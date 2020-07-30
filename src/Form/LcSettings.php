<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Basic fields settings for LayoutComponents.
 */
class LcSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'layoutcomponents_settings_general';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'layoutcomponents.general',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('layoutcomponents.general');

    $form['general'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Provide the general configuration'),
      'menu' => [
        '#type' => 'details',
        '#title' => $this->t('Lateral menu'),
        '#group' => 'general',
        'width' => [
          '#type' => 'number',
          '#title' => $this->t('Width'),
          '#min' => 200,
          '#max' => 1000,
          '#step' => 10,
          '#default_value' => $config->get('width') ?? 200,
          '#description' => $this->t('Select the width of the lateral menu'),
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $width = $form_state->getValue('width') ?: 200;

    $this->config('layoutcomponents.general')
      ->set('width', $width)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
