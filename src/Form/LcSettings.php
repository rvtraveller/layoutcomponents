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
    return 'layoutcomponents_field_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'layoutcomponents.fields',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('layoutcomponents.fields');

    $form['interfaz'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Interfaz settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['interfaz']['theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Theme'),
      '#options' => [
        'color-dark' => $this->t('Color Dark'),
        'grey-dark' => $this->t('Color Grey Dark'),
      ],
      '#default_value' => $config->get('theme') ?? 'color-dark',
    ];

    $form['menu'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Lateral menu settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['menu']['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#min' => 200,
      '#max' => 1000,
      '#step' => 10,
      '#default_value' => $config->get('width') ?? 200,
    ];

    $form['color'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Color settings'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    $form['color']['colors'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Colors'),
      '#rows' => 5,
      '#cols' => 5,
      '#default_value' => $config->get('colors') ?? '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $theme = $form_state->getValue('theme') ?: 'color-light';
    $colors = $form_state->getValue('colors') ?: '#000000';
    $width = $form_state->getValue('width') ?: 200;

    $this->config('layoutcomponents.fields')
      ->set('theme', $theme)
      ->set('width', $width)
      ->set('colors', $colors)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
