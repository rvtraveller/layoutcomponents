<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\layout_builder\Form\MoveSectionsForm;
use Drupal\layout_builder\Plugin\SectionStorage\DefaultsSectionStorage;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a form for moving a section.
 *
 * @internal
 *   Form classes are internal.
 */
class LcMoveSections extends MoveSectionsForm {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL) {
    $build = parent::buildForm($form, $form_state, $section_storage);
    foreach ($build['sections_wrapper']['sections'] as $delta => $wrapper) {
      if (!is_numeric($delta)) {
        continue;
      }
      // Check section type.
      $section_overwrite = $section_storage->getSection($delta)->getLayoutSettings()['section']['general']['basic']['section_overwrite'];
      $is_default = (boolval($section_overwrite) && !$section_storage instanceof DefaultsSectionStorage) ? TRUE : FALSE;
      $build['sections_wrapper']['warning']['#markup'] = '<div class="layout_builder__add-section-confirm"> ' . $this->t('* Default sections cannot be moved, they always will keep the same position. If you want a free to reorder, you must disable the overwriting in your display settings.') . ' </div>';
      if ($is_default) {
        $build['sections_wrapper']['sections'][$delta]['#attributes']['class'][0] = 'disabled';
        $build['sections_wrapper']['sections'][$delta]['label']['#markup'] = $this->t('Default Section @section', ['@section' => $delta + 1]);
      }
    }
    return $build;
  }

}
