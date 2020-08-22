<?php

namespace Drupal\lc_commands\Commands;

use Drush\Commands\DrushCommands;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Symfony\Component\Yaml\Parser;
use Drupal\block_content\Entity\BlockContent;
use Symfony\Component\Serializer\SerializerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\layout_builder\Plugin\Block\InlineBlock;

/**
 * LC commands.
 */

class LcCommands extends DrushCommands {

  /**
   * The folder path.
   *
   * @var string
   */
  protected $folder = DRUPAL_ROOT;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The serializer.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $serializer;

  /**
   * The Config factory object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, SerializerInterface $serializer, ConfigFactoryInterface $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->serializer = $serializer;
    $this->configFactory = $config_factory;
    $this->folder .= $this->configFactory->getEditable('layoutcomponents.general')->get('folder') . '/';
  }

  /**
   * Delete all content_blocks.
   *
   *
   * @command lc:delete
   * @aliases lcd
   */
  public function delete() {
    $blocks = \Drupal::entityTypeManager()->getStorage('block_content')->loadMultiple();
    /** @var \Drupal\block_content\Entity\BlockContent $block */
    foreach ($blocks as $block) {
      $this->output->writeln('Removing: ' . $block->uuid());
      $block->delete();
    }

    $this->output->writeln( count($blocks) . ' blocks deleted');
  }

  /**
   * Export the content_blocks.
   *
   * @command lc:export
   * @aliases lce
   */
  public function export() {
    $storage = $this->entityTypeManager->getStorage('block_content');
    $blocks = $storage->loadMultiple();

    if (!$this->prepareFolder()) {
      return FALSE;
    }

    $this->clearDirectory();

    /** @var \Drupal\block_content\Entity\BlockContent $block */
    foreach ($blocks as $block) {
      $revision = $storage->getLatestRevisionId($block->id());
      $block_revision = $storage->loadRevision($revision);
      $item = $this->prepareFile($block_revision);
      $this->output->writeln('Exporting: ' . $block_revision->uuid());
      if (!$this->writeFile($this->folder . $block_revision->uuid() . '.json', $item)) {
        return FALSE;
      }
    }

    $this->output->writeln( count($blocks) . ' blocks exported');

    return TRUE;
  }

  /**
   * Import the content_blocks.
   *
   * @command lc:import
   * @aliases lci
   */
  public function import() {
    // Remove the current blocks.
    $this->delete();

    // Check the directory.
    $files = $this->readDirectory();
    if (empty($files)) {
      $this->output->writeln('The directory is empty');
      return FALSE;
    }

    foreach ($files as $file) {
      $this->output->writeln('Importing: ' . $file);

      /** @var \Drupal\block_content\Entity\BlockContent $n_block */
      $n_block = $this->readFile($file);

      $uuid = $n_block['uuid'][0];

      /** @var \Drupal\block_content\Entity\BlockContent $d_block */
      $d_block = $this->getBlock($uuid);

      $references = $n_block['_embedded'];
      if (isset($references)) {
        foreach ($references as $link => $reference) {
          /** @var \Drupal\block_content\Entity\BlockContent $ref */
          $ref = $this->getDependencie($n_block, $link);
          if (!empty($ref)) {
            $n_block[$this->getEmbedded($link)][0]['target_id'] = $ref->id();
            $n_block[$this->getEmbedded($link)][0]['target_revision_id'] = $ref->getRevisionId();
          }
        }
      }

      if (!empty($d_block)) {
        $this->updateBlock($d_block, $n_block);
      }
      else {
        BlockContent::create($n_block)->save();
      }
    }

    $this->output->writeln( count($files) . ' blocks imported');

    return TRUE;
  }

  /**
   * Get the dependencie or create if not exists.
   *
   * @param \Drupal\block_content\Entity\BlockContent $n_block
   *   The block.
   * @param string $reference
   *   The new reference.
   * @return \Drupal\block_content\Entity\BlockContent
   *   The new block.
   */
  public function getDependencie(&$n_block, $reference) {
    // Get the embed reference.
    $embed = $this->getEmbedded($reference);
    if (!empty($embed) && strpos($embed, 'field') !== FALSE) {
      $n_uuid = $n_block['_embedded'][$reference][0]['uuid'][0]['value'];
      // Check if the file xists.
      if (!file_exists($this->folder . $n_uuid . '.json')) {
        return null;
      }
      // Get the reference block.
      $new_block = $this->readFile($n_uuid . '.json');
      $uuid = $new_block['uuid'][0];
      // Get the current block.
      $block = $this->getBlock($uuid);
      if (empty($block)) {
        // Create if not exists.
        BlockContent::create($new_block)->save();
        return $this->getBlock($uuid);
      }
      else {
        // Update the block with the new data.
        return $this->updateDependencie($block, $n_uuid);
      }
    }
  }

  /**
   * Get the decoded file content.
   *
   * @param string $file
   *   The file.
   * @return string
   *   The content.
   */
  public function readFile($file) {
    return $this->serializer->decode($this->parseFile($file), 'hal_json');
  }

  /**
   * Get the current block.
   *
   * @param string $uuid
   *   The uuid.
   * @return \Drupal\block_content\Entity\BlockContent
   *   The new block.
   */
  public function getBlock($uuid) {
    $block = $this->entityTypeManager
      ->getStorage('block_content')
      ->loadByProperties(['uuid' => $uuid]);

    /** @var \Drupal\block_content\Entity\BlockContent $block */
    $block = reset($block);

    return $block;
  }

  /**
   * Update the dependencie of the block.
   *
   * @param \Drupal\block_content\Entity\BlockContent $block
   *   The block.
   * @param string $uuid
   *   The uuid.
   * @return \Drupal\block_content\Entity\BlockContent
   *   The new block.
   */
  public function updateDependencie($block, $uuid) {
    $new_block = $this->readFile($uuid . '.json');
    return $this->updateBlock($block, $new_block);
  }

  /**
   * Get the uuid of the file.
   *
   * @param  string $link
   *   The uuid file.
   * @return string
   *   The string.
   */
  public function getEmbedded($link) {
    $parts = explode('/', $link);
    return $parts[count($parts) - 1];
  }

  /**
   * Update each block with the new data.
   *
   * @return \Drupal\block_content\Entity\BlockContent
   *   The new block.
   */
  public function updateBlock($block, $n_block) {
    foreach ($block->getFields() as $name => $field) {
      $block->set($name, $n_block[$name]);
    }
    $block->save();
    return $block;
  }

  /**
   * Check the folder.
   *
   * @return bool
   *   If exists and is writable.
   */
  public function prepareFolder() {
    if (!is_dir($this->folder)) {
      $this->output->writeln('The folder not exists');
      return FALSE;
    }

    if (!is_writable($this->folder)) {
      $this->output->writeln('The folder is not writabble');
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Serialize the file content.
   *
   * @return string
   *   The file content serialized.
   */
  public function prepareFile($item) {
    return $this->serializer->serialize($item, 'hal_json', ['json_encode_options' => 128]);
  }

  /**
   * Write the file.
   *
   * @return bool
   *   If the file has been written correctly.
   */
  public function writeFile($file, $item) {
    if (!file_put_contents($file, $item)) {
      $this->output->writeln('An error encoured writing the file');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Get the file content.
   *
   * @return string|false
   *   The function returns the read data or false on failure.
   */
  protected function parseFile($file) {
    return file_get_contents($this->folder . $file);
  }

  /**
   * Read the files of directory.
   *
   * @return array
   *   The array with the files.
   */
  public function readDirectory() {
    return array_diff(scandir($this->folder), array('..', '.'));
  }

  /**
   * Remove the files of directory.
   */
  public function clearDirectory() {
    $files = $this->readDirectory();
    foreach ($files as $file) {
      unlink($this->folder . $file);
    }
  }

}
