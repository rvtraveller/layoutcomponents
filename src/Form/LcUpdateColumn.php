<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Form\ConfigureSectionForm;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a form for configuring section.
 */
class LcUpdateColumn extends ConfigureSectionForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, $plugin_id = NULL, $region = NULL) {
    $build = parent::buildForm($form, $form_state, $section_storage, $delta, $plugin_id);
    $section = $section_storage->getSection($delta);

    // Hide section form.
    $build['layout_settings']['container']['title']['#access'] = FALSE;
    $build['layout_settings']['container']['section']['#access'] = FALSE;

    // Change form title.
    $form['#title'] = $this->t('Configure column');

    // Hide others columns.
    $column_settings = &$build['layout_settings']['container']['regions'];
    $column_settings['#open'] = TRUE;

    // Proccess columns.
    $regions = $section->getLayout()->getPluginDefinition()->getRegionNames();
    foreach ($regions as $key => $name) {
      // Expand columns.
      $column_settings[$name]['#open'] = TRUE;

      if ($region !== $name) {
        $column_settings[$name]['#access'] = FALSE;
      }

    }

    // Set custom classes.
    $build['layout_settings']['container']['#attributes'] = [
      'class' => ['lc-container-column'],
    ];

    // Add custom libraries.
    $build['#attached']['library'][] = 'layoutcomponents/layoutcomponents.lateral-column';

    return $build;
  }

}
