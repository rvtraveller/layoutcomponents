<?php

namespace Drupal\layoutcomponents\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\layoutcomponents\LcLayoutsManager;
use Drupal\Core\Config\ConfigFactory;
use Drupal\layoutcomponents\Api\Text;
use Drupal\layoutcomponents\Api\Color;
use Drupal\layoutcomponents\Api\Select;
use Drupal\layoutcomponents\Api\Slider;
use Drupal\layoutcomponents\Api\Checkbox;
use Drupal\layoutcomponents\Api\Media;

/**
 * Layout class for all Layoutcomponents.
 */
class LcBase extends LayoutDefault implements ContainerFactoryPluginInterface {

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\LcLayoutsManager
   */
  protected $manager;

  /**
   * Config factory object.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\Api\Text
   */
  protected $lcApiText;

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\Api\Color
   */
  protected $lcApiColor;

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\Api\Select
   */
  protected $lcApiSelect;

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\Api\Slider
   */
  protected $lcApiSlider;

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\Api\Checkbox
   */
  protected $lcApiCheckbox;

  /**
   * Layoutcomponents manager.
   *
   * @var \Drupal\layoutcomponents\Api\Media
   */
  protected $lcApiMedia;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LcLayoutsManager $manager, ConfigFactory $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->manager = $manager;
    $this->configFactory = $config_factory;
    $this->lcApiText = new Text();
    $this->lcApiColor = new Color($this->configFactory);
    $this->lcApiSelect = new Select();
    $this->lcApiSlider = new Slider();
    $this->lcApiCheckbox = new Checkbox();
    $this->lcApiMedia = new Media();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.layoutcomponents_layouts'),
      $container->get('config.factory')
    );
  }

  /**
   * Provides a default region definition.
   *
   * @return array
   *   Default region array.
   */
  protected function getRegionDefaults() {
    return [
      'title' => [
        'title' => '',
      ],
      'styles' => [
        'title' => [
          'type' => 'div',
          'color' => [
            'settings' => [
              'color' => '#ffffff',
              'opacity' => 0,
            ],
          ],
          'size' => (int) 0,
          'align' => 'left',
          'border' => 'none',
          'border_size' => (int) 0,
          'border_color' => [
            'settings' => [
              'color' => '#ffffff',
              'opacity' => 0,
            ],
          ],
        ],
        'border' => [
          'border' => 'none',
          'size' => (int) 0,
          'color' => [
            'settings' => [
              'color' => '#ffffff',
              'opacity' => 0,
            ],
          ],
          'radius_top_left' => (int) 0,
          'radius_top_right' => (int) 0,
          'radius_bottom_left' => (int) 0,
          'radius_bottom_right' => (int) 0,
        ],
        'background' => [
          'color' => [
            'settings' => [
              'color' => '#ffffff',
              'opacity' => 0,
            ],
          ],
        ],
        'spacing' => [
          'paddings' => FALSE,
          'paddings_left' => FALSE,
          'paddings_right' => FALSE,
        ],
        'misc' => [
          'extra_class' => '',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration += [
      'title' => [
        'general' => [
          'title' => '',
        ],
        'styles' => [
          'design' => [
            'title_color' => [
              'settings' => [
                'color' => '#ffffff',
                'opacity' => 1,
              ],
            ],
            'title_type' => 'h2',
            'title_align' => 'text-left',
          ],
          'sizing' => [
            'title_size' => (int) 0,
          ],
          'border' => [
            'title_border' => 'left',
            'title_border_size' => (int) 0,
            'title_border_color' => [
              'settings' => [
                'color' => '#ffffff',
                'opacity' => 0,
              ],
            ],
          ],
          'spacing' => [
            'title_margin_top' => (int) 0,
            'title_margin_bottom' => (int) 0,
          ],
        ],
      ],
      'section' => [
        'general' => [
          'basic' => [
            'section_type' => 'div',
          ],
          'structure' => [
            'section_structure' => 12,
          ],
        ],
        'styles' => [
          'background' => [
            'image' => '',
            'backgroud_color' => [
              'settings' => [
                'color' => '#ffffff',
                'opacity' => 0,
              ],
            ],
          ],
          'sizing' => [
            'full_width' => (int) 0,
            'full_width_container' => (int) 0,
            'full_width_container_title' => (int) 0,
            'height' => (int) 0,
            'height_size' => (int) 1,
          ],
          'spacing' => [
            'no_top_padding' => (int) 1,
            'top_padding' => (int) 0,
            'no_bottom_padding' => (int) 1,
            'bottom_padding' => (int) 1,
          ],
          'misc' => [
            'extra_class' => '',
            'extra_attributes' => '',
            'parallax' => (int) 0,
          ],
        ],
      ],
      'regions' => [],
    ];

    // Set config in each region.
    foreach ($this->getPluginDefinition()->getRegions() as $region => $info) {
      $configuration['regions'][$region] = $this->getRegionDefaults();
    }

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $complete_form_state = $form_state instanceof SubformStateInterface ? $form_state->getCompleteFormState() : $form_state;

    // Merge default configuration.
    $this->getConfiguration();

    // Section container.
    $form['container'] = [
      '#type' => 'horizontal_tabs',
      '#title' => t('Settings'),
      '#prefix' => '<div class="lc-lateral-container">',
      '#suffix' => '</div>',
    ];

    // Build Title.
    $form['container']['title'] = [
      '#type' => 'details',
      '#title' => t('Title'),
      '#group' => 'container',
    ];

    $form['container']['title']['container'] = [
      '#type' => 'horizontal_tabs',
      '#title' => t('Title'),
      '#group' => 'title',
    ];

    $form['container']['title']['container'][] = $this->setAdministrativeTitle($form, $form_state);

    // Build Section.
    $form['container']['section'] = [
      '#type' => 'details',
      '#title' => t('Section'),
      '#group' => 'container',
    ];

    $form['container']['section']['container'] = [
      '#type' => 'horizontal_tabs',
      '#title' => t('Section'),
      '#group' => 'section',
    ];

    $form['container']['section']['container'][] = $this->setAdminsitrativeSection($form, $form_state);

    // Build Regions.
    $form['container']['regions'] = [
      '#type' => 'details',
      '#title' => t('Regions'),
      '#group' => 'container',
    ];

    foreach ($this->getPluginDefinition()->getRegionNames() as $region) {
      $form['container']['regions'][$region] = [
        '#type' => 'horizontal_tabs',
        '#title' => t('Regions'),
        '#group' => 'regions',
        '#prefix' => '<div class="lc-lateral-regions">',
        '#suffix' => '</div>',
      ];

      $form['container']['section'][$region][] = $this->setAdminsitrativeRegion($form, $form_state, $region);
    }

    return $form;
  }

  /**
   * Provide the region configuration.
   *
   * @param array $form
   *   The complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The FormStateInterface object.
   * @param string $region
   *   The region.
   */
  public function setAdminsitrativeRegion(array &$form, FormStateInterface $form_state, $region) {
    $config = $this->getConfiguration()['regions'][$region];
    $general = $config['general'];
    $styles = $config['styles'];
    $container = &$form['container']['regions'][$region];

    $container['general'] = [
      '#type' => 'details',
      '#title' => t('General'),
      '#group' => 'regions',
      'title' => $this->lcApiText->plainText(
        [
          'id' => 'column_' . $region . '-title',
          'title' => $this->t('Title'),
          'description' => $this->t('Set the title of this section'),
          'default_value' => $general['title'],
          'attributes' => [
            'placeholder' => $this->t('Title'),
            'lc' => [
              'type' => 'text',
            ],
          ],
        ]
      ),
    ];

    $container['styles'] = [
      '#type' => 'details',
      '#title' => t('Styles'),
      '#group' => 'regions',
      'title' => [
        '#type' => 'details',
        '#title' => t('Text'),
        '#group' => 'title',
        'type' => $this->lcApiSelect->normal(
          [
            'id' => 'column_' . $region . '-title',
            'title' => $this->t('Title type'),
            'description' => $this->t('Set the type of title'),
            'default_value' => $styles['title']['type'],
            'options' => $this->manager->getTagOptions(),
            'attributes' => [
              'lc' => [
                'type' => 'element',
              ],
            ],
            'class' => 'type',
          ]
        ),
        'color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'column_' . $region . '-title',
            'title' => $this->t('Title Color'),
            'description' => $this->t('Set the title color'),
            'default_value' =>
              [
                'color' => $styles['title']['color']['settings']['color'],
                'opacity' => $styles['title']['color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'style' => 'color',
                'depend' => [
                  'opacity' => [
                    'color' => 'lc-inline_column_' . $region . '-title-color',
                  ],
                ],
              ],
            ],
            'class' => 'color',
          ]
        ),
        'size' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region . '-title',
            'title' => $this->t('Title size'),
            'description' => $this->t('Set the size of title'),
            'default_value' => $styles['title']['size'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'font-size',
              ],
            ],
            'class' => 'size',
          ]
        ),
        'align' => $this->lcApiSelect->normal(
          [
            'id' => 'column_' . $region . '-title',
            'title' => $this->t('Title align'),
            'description' => $this->t('Set the align of title'),
            'default_value' => $styles['title']['align'],
            'options' => $this->manager->getColumnTitleAlign(),
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'align',
                'class_remove' => 'text-*',
              ],
            ],
            'class' => 'align',
          ]
        ),
        'border' => $this->lcApiSelect->normal(
          [
            'id' => 'column_' . $region . '-container-title',
            'title' => $this->t('Title border'),
            'description' => $this->t('Set the border of title'),
            'default_value' => $styles['title']['border'],
            'options' => $this->manager->getTitleBorder(),
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border',
                'style-value' => 1,
                'lc-after-value' => 'px solid',
                'depend' => [
                  'size' => 'lc-inline_column_' . $region . '-container-title-border-size',
                  'color' => 'lc-inline_column_' . $region . '-container-title-border-color',
                  'opacity' => 'lc-inline_column_' . $region . '-container-title-border-color-opacity',
                ],
              ],
            ],
            'class' => 'border-type',
          ]
        ),
        'border_size' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region . '-container-title',
            'title' => $this->t('Title border size'),
            'description' => $this->t('Set the border size of title'),
            'default_value' => $styles['title']['border_size'],
            'min' => 0,
            'max' => 50,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-size',
                'depend' => [
                  'type' => 'lc-inline_column_' . $region . '-container-title-border-type',
                  'color' => 'lc-inline_column_' . $region . '-container-title-border-color',
                  'opacity' => 'lc-inline_column_' . $region . '-container-title-border-color-opacity',
                ],
              ],
            ],
            'class' => 'border-size',
          ]
        ),
        'border_color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'column_' . $region . '-container-title',
            'title' => $this->t('Title border color'),
            'description' => $this->t('Set the border color of title'),
            'default_value' =>
              [
                'color' => $styles['title']['border_color']['settings']['color'],
                'opacity' => $styles['title']['border_color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-color',
                'depend' => [
                  'type' => 'lc-inline_column_' . $region . '-container-title-border-type',
                  'size' => 'lc-inline_column_' . $region . '-container-title-border-size',
                  'opacity' => [
                    'color' => 'lc-inline_column_' . $region . '-container-title-border-color',
                    'type' => 'lc-inline_column_' . $region . '-container-title-border-type',
                    'size' => 'lc-inline_column_' . $region . '-container-title-border-size',
                  ],
                ],
              ],
            ],
            'class' => 'border-color',
          ]
        ),
      ],
      'border' => [
        '#type' => 'details',
        '#title' => t('Border'),
        '#group' => 'regions',
        'border' => $this->lcApiSelect->normal(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Type'),
            'description' => $this->t('Set the type of border'),
            'default_value' => $styles['border']['border'],
            'options' => $this->manager->getColumnBorder(),
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border',
                'style-value' => 1,
                'lc-after-value' => 'px solid',
                'depend' => [
                  'size' => "lc-inline_column_$region-border-size",
                  'color' => "lc-inline_column_$region-border-color",
                  'opacity' => "lc-inline_column_$region-border-color-opacity",
                ],
              ],
            ],
            'class' => 'border-type',
          ]
        ),
        'size' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Size'),
            'description' => $this->t('Set the border size of column'),
            'default_value' => $styles['border']['size'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-size',
                'depend' => [
                  'type' => "lc-inline_column_$region-border-type",
                  'color' => "lc-inline_column_$region-border-color",
                  'opacity' => "lc-inline_column_$region-border-color-opacity",
                ],
              ],
            ],
            'class' => 'border-size',
          ]
        ),
        'color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Color'),
            'description' => $this->t('Set the border color of column'),
            'default_value' =>
              [
                'color' => $styles['border']['color']['settings']['color'],
                'opacity' => $styles['border']['color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-color',
                'depend' => [
                  'type' => "lc-inline_column_$region-border-type",
                  'size' => "lc-inline_column_$region-border-size",
                  'opacity' => [
                    'color' => "lc-inline_column_$region-border-color",
                    'type' => "lc-inline_column_$region-border-type",
                    'size' => "lc-inline_column_$region-border-size",
                  ],
                ],
              ],
            ],
            'class' => 'border-color',
          ]
        ),
        'radius_top_left' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Radius top - left'),
            'description' => $this->t('Set the border radius top - left'),
            'default_value' => $styles['border']['radius_top_left'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-top-left-radius',
              ],
            ],
            'class' => $region . '-border-radius-top-left',
          ]
        ),
        'radius_top_right' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Radius top - right'),
            'description' => $this->t('Set the border radius top - right'),
            'default_value' => $styles['border']['radius_top_right'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-top-right-radius',
              ],
            ],
            'class' => $region . '-border-radius-top-right',
          ]
        ),
        'radius_bottom_left' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Radius bottom - left'),
            'description' => $this->t('Set the border radius bottom - left'),
            'default_value' => $styles['border']['radius_bottom_left'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-bottom-left-radius',
              ],
            ],
            'class' => $region . '-border-radius-bottom_left',
          ]
        ),
        'radius_bottom_right' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Radius bottom - right'),
            'description' => $this->t('Set the border radius bottom - right'),
            'default_value' => $styles['border']['radius_bottom_right'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-bottom-right-radius',
              ],
            ],
            'class' => $region . '-border-radius-bottom_right',
          ]
        ),
      ],
      'background' => [
        '#type' => 'details',
        '#title' => t('Background'),
        '#group' => 'regions',
        'color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Color'),
            'description' => $this->t('Set the background color of column'),
            'default_value' =>
              [
                'color' => $styles['background']['color']['settings']['color'],
                'opacity' => $styles['background']['color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'background-color',
                'depend' => [
                  'opacity' => [
                    'color' => "lc-inline_column_$region-background-color",
                  ],
                ],
              ],
            ],
            'class' => 'background-color',
          ]
        ),
      ],
      'spacing' => [
        '#type' => 'details',
        '#title' => t('Spacing'),
        '#group' => 'regions',
        'remove_paddings' => $this->lcApiCheckbox->normal(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('No paddings'),
            'description' => $this->t('Remove the spaces betwen columns'),
            'default_value' => $styles['spacing']['remove_paddings'],
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'checkbox',
                'class_checkbox_active' => 'p-0',
                'class_checkbox_disable' => '',
              ],
            ],
            'class' => "$region-remove-paddings",
          ]
        ),
        'remove_padding_left' => $this->lcApiCheckbox->normal(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('No left padding'),
            'description' => $this->t('Remove left padding'),
            'default_value' => $styles['spacing']['remove_padding_left'],
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'checkbox',
                'class_checkbox_active' => 'pl-0',
                'class_checkbox_disable' => '',
              ],
            ],
            'class' => "$region-remove-left_paddings",
          ]
        ),
        'remove_padding_right' => $this->lcApiCheckbox->normal(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('No right padding'),
            'description' => $this->t('Remove right padding'),
            'default_value' => $styles['spacing']['remove_padding_right'],
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'checkbox',
                'class_checkbox_active' => 'pr-0',
                'class_checkbox_disable' => '',
              ],
            ],
            'class' => "$region-remove-right_paddings",
          ]
        ),
      ],
      'misc' => [
        '#type' => 'details',
        '#title' => t('Misc'),
        '#group' => 'regions',
        'extra_class' => $this->lcApiText->plainText(
          [
            'id' => 'column_' . $region,
            'title' => $this->t('Extra class'),
            'description' => $this->t('Set extra classes in this column, ej. myClass1,myClass2'),
            'default_value' => $styles['misc']['extra_class'],
            'attributes' => [
              'placeholder' => $this->t('Ej. myclass1 myclass2'),
              'lc' => [
                'type' => 'class',
                'style' => 'extra_class',
              ],
            ],
            'class' => '-extra_class',
          ]
        ),
      ],
    ];
  }

  /**
   * Provide the title configuration.
   *
   * @param array $form
   *   The complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The FormStateInterface object.
   */
  public function setAdministrativeTitle(array &$form, FormStateInterface $form_state) {
    $config = $this->getConfiguration()['title'];
    $general = $config['general'];
    $styles = $config['styles'];

    $container = &$form['container']['title']['container'];

    $container['general'] = [
      '#type' => 'details',
      '#title' => t('General'),
      '#group' => 'title',
      'title' => $this->lcApiText->plainText(
        [
          'id' => 'title',
          'title' => $this->t('Title'),
          'description' => $this->t('Set the title of this section'),
          'default_value' => $general['title'],
          'attributes' => [
            'placeholder' => $this->t('My title'),
            'lc' => [
              'type' => 'text',
            ],
          ],
          'class' => 'title',
        ]
      ),
    ];

    $container['styles'] = [
      '#type' => 'details',
      '#title' => t('Styles'),
      '#group' => 'title',
      'design' => [
        '#type' => 'details',
        '#title' => t('Text'),
        '#group' => 'title',
        'title_color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'title',
            'title' => $this->t('Color'),
            'description' => $this->t('Set the title color'),
            'default_value' =>
              [
                'color' => $styles['design']['title_color']['settings']['color'],
                'opacity' => $styles['design']['title_color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'style' => 'color',

              ],
            ],
            'class' => 'color',
          ]
        ),
        'title_type' => $this->lcApiSelect->normal(
          [
            'id' => 'title',
            'title' => $this->t('Title type'),
            'description' => $this->t('Set the type of title'),
            'default_value' => $styles['design']['title_type'],
            'options' => $this->manager->getTagOptions(),
            'attributes' => [
              'lc' => [
                'type' => 'element',
              ],
            ],
            'class' => 'title-type',
          ]
        ),
        'title_align' => $this->lcApiSelect->normal(
          [
            'id' => 'title',
            'title' => $this->t('Title align'),
            'description' => $this->t('Set the align of title'),
            'default_value' => $styles['design']['title_align'],
            'options' => $this->manager->getColumnTitleAlign(),
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'align',
                'class_remove' => 'text-*',
              ],
            ],
            'class' => 'title-align',
          ]
        ),
      ],
      'sizing' => [
        '#type' => 'details',
        '#title' => t('Sizing'),
        '#group' => 'title',
        'title_size' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'title',
            'title' => $this->t('Font size'),
            'description' => $this->t('Set the size of title'),
            'default_value' => $styles['sizing']['title_size'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'font-size',
              ],
            ],
            'class' => 'title-size',
          ]
        ),
      ],
      'border' => [
        '#type' => 'details',
        '#title' => t('Border'),
        '#group' => 'title',
        'title_border' => $this->lcApiSelect->normal(
          [
            'id' => 'title',
            'title' => $this->t('Type'),
            'description' => $this->t('Set the border type of title'),
            'default_value' => $styles['border']['title_border'],
            'options' => $this->manager->getTitleBorder(),
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border',
                'style-value' => 1,
                'lc-after-value' => 'px solid',
                'depend' => [
                  'size' => 'lc-inline_title-border-size',
                  'color' => 'lc-inline_title-border-color',
                  'opacity' => 'lc-inline_title-border-color-opacity',
                ],
              ],
            ],
            'class' => 'border-type',
          ]
        ),
        'title_border_size' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'title',
            'title' => $this->t('Size'),
            'description' => $this->t('Set the border size of title'),
            'default_value' => $styles['border']['title_border_size'],
            'min' => 0,
            'max' => 100,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'border-size',
                'depend' => [
                  'type' => 'lc-inline_title-border-type',
                  'color' => 'lc-inline_title-border-color',
                  'opacity' => 'lc-inline_title-border-color-opacity',
                ],
              ],
            ],
            'class' => 'border-size',
          ]
        ),
        'title_border_color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'title',
            'title' => $this->t('Color'),
            'description' => $this->t('Set the border color of title'),
            'default_value' =>
              [
                'color' => $styles['border']['title_border_color']['settings']['color'],
                'opacity' => $styles['border']['title_border_color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'style' => 'border-color',
                'depend' => [
                  'type' => 'lc-inline_title-border-type',
                  'size' => 'lc-inline_title-border-size',
                  'opacity' => [
                    'type' => 'lc-inline_title-border-type',
                    'size' => 'lc-inline_title-border-size',
                  ],
                ],
              ],
            ],
            'class' => 'border-color',
          ]
        ),
      ],
      'spacing' => [
        '#type' => 'details',
        '#title' => $this->t('Spacing'),
        '#group' => 'title',
        'title_margin_top' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'title-container',
            'title' => $this->t('Margin top'),
            'description' => $this->t('Set px of title margin top'),
            'default_value' => $styles['spacing']['title_margin_top'],
            'min' => 0,
            'max' => 500,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'padding-top',
              ],
            ],
            'class' => 'margin-top',
          ]
        ),
        'title_margin_bottom' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'title-container',
            'title' => $this->t('Margin Bottom'),
            'description' => $this->t('Set px of title margin bottom'),
            'default_value' => $styles['spacing']['title_margin_bottom'],
            'min' => 0,
            'max' => 500,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'padding-bottom',
              ],
            ],
            'class' => 'margin-bottom',
          ]
        ),
      ],
    ];
  }

  /**
   * Provide the section configuration.
   *
   * @param array $form
   *   The complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The FormStateInterface object.
   */
  public function setAdminsitrativeSection(array &$form, FormStateInterface $form_state) {
    $config = $this->getConfiguration()['section'];
    $general = $config['general'];
    $styles = $config['styles'];
    $container = &$form['container']['section']['container'];

    $container['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General'),
      '#group' => 'section',
      'basic' => [
        '#type' => 'details',
        '#title' => t('Basic'),
        '#group' => 'section',
        'section_type' => $this->lcApiSelect->normal(
          [
            'id' => 'section',
            'title' => $this->t('Type'),
            'description' => $this->t('Set the type of this section'),
            'default_value' => $general['basic']['section_type'],
            'options' => $this->manager->getWrapperOptions(),
            'attributes' => [
              'lc' => [
                'type' => 'element',
              ],
            ],
            'class' => 'container-type',
          ]
        ),
      ],
      'structure' => [
        '#type' => 'details',
        '#title' => t('Structure'),
        '#group' => 'section',
        'section_structure' => $this->lcApiSelect->normal(
          [
            'id' => 'row',
            'title' => $this->t('Type'),
            'description' => $this->t('Set the size of each column'),
            'default_value' => $general['structure']['section_structure'],
            'options' => $this->manager->getColumnOptions(count($this->getPluginDefinition()->getRegionNames())),
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'class_remove' => 'col-md-*',
                'style' => 'column_size',
              ],
            ],
            'class' => 'column-size',
          ]
        ),
      ],
    ];

    $container['styles'] = [
      '#type' => 'details',
      '#title' => t('Styles'),
      '#group' => 'section',
      'background' => [
        '#type' => 'details',
        '#title' => t('Background'),
        '#group' => 'section',
        'image' => $this->lcApiMedia->mediaLibrary(
          [
            'id' => 'section',
            'title' => $this->t('Image'),
            'description' => $this->t('Upload a background image'),
            'default_value' => $styles['background']['image'],
            'allowed_bundles' => ['image'],
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'background',
                'depend' => [
                  'color' => 'lc-inline_section-background-color',
                  'opacity' => 'lc-inline_section-background-color-opacity',
                ],
              ],
            ],
            'class' => 'background-image',
          ]
        ),
        'background_color' => $this->lcApiColor->colorPicker(
          [
            'id' => 'section',
            'title' => $this->t('Color'),
            'description' => $this->t('Set the background color of this setion'),
            'default_value' =>
              [
                'color' => $styles['background']['background_color']['settings']['color'],
                'opacity' => $styles['background']['background_color']['settings']['opacity'],
              ],
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'background-color',
                'depend' => [
                  'background' => 'lc-inline_section-background-image',
                  'opacity' => [
                    'background' => 'lc-inline_section-background-image',
                  ],
                ],
              ],
            ],
            'class' => 'background-color',
          ]
        ),
      ],
      'sizing' => [
        '#type' => 'details',
        '#title' => t('Sizing'),
        '#group' => 'section',
        'full_width' => $this->lcApiCheckbox->normal(
          [
            'id' => 'section',
            'title' => $this->t('Full width'),
            'description' => $this->t('Enable full width'),
            'default_value' => $styles['sizing']['full_width'],
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'checkbox',
                'class_checkbox_active' => 'container-fluid',
                'class_checkbox_disable' => 'container',
              ],
            ],
            'class' => 'section-full_width',
          ]
        ),
        'full_width_container' => $this->lcApiCheckbox->normal(
          [
            'id' => 'container-section',
            'title' => $this->t('+ "Container" class'),
            'description' => $this->t('Include the class -Container- in this section'),
            'default_value' => $styles['sizing']['full_width_container'],
            'states' => 'layout_settings[container][section][container][styles][sizing][full_width]',
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'checkbox',
                'class_checkbox_active' => 'container',
                'class_checkbox_disable' => '',
              ],
            ],
            'class' => 'full_width-section-container',
          ]
        ),
        'full_width_container_title' => $this->lcApiCheckbox->normal(
          [
            'id' => 'container-title',
            'title' => $this->t('Title + "Container" class 2'),
            'description' => $this->t('Include the class -Container- in the title'),
            'default_value' => $styles['sizing']['full_width_container_title'],
            'states' => 'layout_settings[container][section][container][styles][sizing][full_width]',
            'attributes' => [
              'lc' => [
                'type' => 'class',
                'style' => 'checkbox',
                'class_checkbox_active' => 'container',
                'class_checkbox_disable' => '',
              ],
            ],
            'class' => 'full_width-title-section',
          ]
        ),
        'height' => $this->lcApiSelect->normal(
          [
            'id' => 'section',
            'title' => $this->t('Height Type'),
            'description' => $this->t('Set the height type'),
            'default_value' => $styles['sizing']['height'],
            'options' =>
              [
                'auto' => $this->t('Auto'),
                'manual' => $this->t('Manual'),
                '100vh' => $this->t('Full'),
                '50vh' => $this->t('Medium'),
              ],
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'height',
                'depend' => [
                  'size' => 'lc-inline_section-height-size',
                ],
              ],
            ],
            'class' => 'height',
          ]
        ),
        'height_size' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'section',
            'title' => $this->t('Height size'),
            'description' => $this->t('Set height of the section'),
            'default_value' => $styles['sizing']['height_size'],
            'min' => 0,
            'max' => 1000,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'height-size',
                'depend' => [
                  'type' => 'lc-inline_section-height',
                ],
              ],
            ],
            'class' => 'height-size',
          ]
        ),
      ],
      'spacing' => [
        '#type' => 'details',
        '#title' => t('Spacing'),
        '#group' => 'section',
        'top_padding' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'section',
            'title' => $this->t('Top padding size'),
            'description' => $this->t('Set the size of top padding'),
            'default_value' => $styles['spacing']['top_padding'],
            'min' => 0,
            'max' => 500,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'padding-top',
                'depend' => [
                  'size' => 'lc-inline_section-top-padding',
                ],
              ],
            ],
            'class' => 'top-padding-size',
          ]
        ),
        'bottom_padding' => $this->lcApiSlider->sliderWidget(
          [
            'id' => 'section',
            'title' => $this->t('Bottom padding size'),
            'description' => $this->t('Set the size of bottom padding'),
            'default_value' => $styles['spacing']['bottom_padding'],
            'min' => 0,
            'max' => 500,
            'attributes' => [
              'lc' => [
                'type' => 'style',
                'style' => 'padding-bottom',
                'depend' => [
                  'size' => 'lc-inline_section-bottom-padding',
                ],
              ],
            ],
            'class' => 'bottom-padding-size',
          ]
        ),
      ],
      'misc' => [
        '#type' => 'details',
        '#title' => t('Misc'),
        '#group' => 'section',
        'extra_class' => $this->lcApiText->plainText(
          [
            'id' => 'section',
            'title' => $this->t('Additional classes'),
            'description' => $this->t('Set extra classes, ilegal character will be removed automatically'),
            'default_value' => $styles['misc']['extra_class'],
            'attributes' => [
              'placeholder' => $this->t('Ej. myclass1 myclass2'),
              'lc' => [
                'type' => 'class',
                'style' => 'extra_class',
              ],
            ],
            'class' => 'extra_class',
          ]
        ),
        'extra_attributes' => $this->lcApiText->plainText(
          [
            'id' => 'section',
            'title' => $this->t('Additional attributes'),
            'description' => $this->t('Set extra attributes, ilegal character will be removed automatically'),
            'default_value' => $styles['misc']['extra_attributes'],
            'attributes' => [
              'placeholder' => $this->t('Ej. id|custom-id role|navigation'),
              'lc' => [
                'type' => 'attribute',
                'style' => 'extra_attributes',
              ],
            ],
            'class' => 'extra_attributes',
          ]
        ),
        'parallax' => $this->lcApiCheckbox->normal(
          [
            'id' => 'section',
            'title' => $this->t('Parallax'),
            'description' => $this->t('Set this section with parallax effect'),
            'default_value' => $styles['misc']['parallax'],
            'attributes' => [
              'lc' => [],
            ],
            'class' => 'section-parallax',
          ]
        ),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('container');
    $this->configuration['title'] = $values['title']['container'];
    $this->configuration['section'] = $values['section']['container'];
    $this->configuration['regions'] = $values['regions'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

}
