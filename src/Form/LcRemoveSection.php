<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Form\RemoveSectionForm;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a form for removing section.
 */
class LcRemoveSection extends RemoveSectionForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL) {
    $build = parent::buildForm($form, $form_state, $section_storage, $delta);

    // Alter description to insert it in a DIV.
    $build['description']['#markup'] = '<div class="layout_builder__remove-description"> ' . $this->t('This action can not be undone') . ' </div>';

    // Add custom libraries.
    $build['#attached']['library'][] = 'layoutcomponents/layoutcomponents.lateral';

    return $build;
  }

}
