<?php

namespace Drupal\layoutcomponents\Controller;

use Drupal\layout_builder\Controller\ChooseBlockController;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Defines a controller to choose a new block type.
 *
 * @internal
 *   Controller classes are internal.
 */
class LcChooseBlockController extends ChooseBlockController {

  /**
   * Provides the UI for choosing a new block.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   * @param string $region
   *   The region the block is going in.
   *
   * @return array
   *   A render array.
   */
  public function build(SectionStorageInterface $section_storage, $delta, $region) {
    $build = parent::build($section_storage, $delta, $region);

    // Categories.
    $un_categories = [
      'Chaos Tools',
      'System',
      'User fields',
    ];

    // Add class to menu item "Create custom block".
    $build['add_block']['#attributes']['class'][] = 'customblock-menuitem-modal';
    // Alter layoutcomponents blocks names.
    foreach ($build['block_categories'] as $name => $category) {
      // Remove unnecesary categories.
      if (in_array($name, $un_categories)) {
        unset($build['block_categories'][$name]);
        continue;
      }
      // Close category.
      if (is_array($build['block_categories'][$name])) {
        $build['block_categories'][$name]['#open'] = FALSE;
      }
    }

    $build['#title'] = $this->t('Select a block or create new');

    return $build;
  }

  /**
   * Provides the UI for choosing a new inline block.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   * @param string $region
   *   The region the block is going in.
   *
   * @return array
   *   A render array.
   */
  public function inlineBlockList(SectionStorageInterface $section_storage, $delta, $region) {
    // Parent items.
    $build = parent::inlineBlockList($section_storage, $delta, $region);
    // Block definitions.
    $definitions = $this->blockManager->getFilteredDefinitions('layout_builder', $this->getAvailableContexts($section_storage), [
      'section_storage' => $section_storage,
      'region' => $region,
      'list' => 'inline_blocks',
    ]);
    // Block types.
    $blocks_type = $this->blockManager->getGroupedDefinitions($definitions);
    foreach ($build['links']['#links'] as $key => $link) {
      $blockId = [];
      foreach ($blocks_type['Inline blocks'] as $name => $type) {
        if ($type['admin_label'] == $link['title']) {
          $blockId = explode(':', $name);
          $build['links']['#links'][$key]['attributes']['class'][] = $blockId[1];
        }
      }
      // Remove link if in array.
      if (isset($blockId)) {
        if (in_array('item', explode('_', $blockId[1]))) {
          unset($build['links']['#links'][$key]);
          continue;
        }
      }
    }

    $build['#title'] = $this->t('Select a block type');

    // Add custom selector.
    $build['back_button']['#attributes']['data-drupal-selector'] = 'back';

    return $build;
  }

}
