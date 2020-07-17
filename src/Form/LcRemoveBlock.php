<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Form\RemoveBlockForm;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a form for removing blocks.
 */
class LcRemoveBlock extends RemoveBlockForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, $region = NULL, $uuid = NULL) {
    $build = parent::buildForm($form, $form_state, $section_storage, $delta, $region, $uuid);
    $build['#attached']['library'][] = 'layoutcomponents/layoutcomponents.lateral';

    // Alter description to insert it in a DIV.
    $build['description']['#markup'] = '<div class="layout_builder__remove-description"> ' . $this->t('This action can not be undone') . ' </div>';
    return $build;
  }

}
