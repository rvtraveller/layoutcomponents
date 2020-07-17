<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Form\UpdateBlockForm;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a form to update a block.
 */
class LcUpdateBlockForm extends UpdateBlockForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, $region = NULL, $uuid = NULL) {
    $build = parent::buildForm($form, $form_state, $section_storage, $delta, $region, $uuid);

    /** @var \Drupal\block_content\Entity\BlockContent $block */
    $block = $build['settings']['block_form']['#block'];

    if (array_key_exists('block_form', $build['settings'])) {
      /** @var \Drupal\block_content\Entity\BlockContent $block */
      $block = $build['settings']['block_form']['#block'];
      $build['#title'] = $this->t("Edit @title", ['@title' => $block->get("info")->getString()]);
    }
    else {
      $build['#title'] = $this->t("Edit field @title", ['@title' => $build['settings']['admin_label']['#plain_text']]);
    }

    // Hidde block config.
    $build['settings']['label']['#access'] = FALSE;
    $build['settings']['admin_label']['#access'] = FALSE;
    $build['settings']['label_display']['#access'] = FALSE;

    return $build;
  }

}
