<?php

namespace Drupal\layoutcomponents\Element;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\layout_builder\Element\LayoutBuilder;
use Drupal\Core\Config\ConfigFactory;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * {@inheritdoc}
 */
class LcElement extends LayoutBuilder {

  /**
   * The theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Config factory object.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LayoutTempstoreRepositoryInterface $layout_tempstore_repository, MessengerInterface $messenger, ThemeHandlerInterface $theme_handler, ConfigFactory $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $layout_tempstore_repository, $messenger);
    $this->themeHandler = $theme_handler;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('layout_builder.tempstore_repository'),
      $container->get('messenger'),
      $container->get('theme_handler'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function layout(SectionStorageInterface $section_storage) {
    $output = parent::layout($section_storage);
    $output['#attached']['library'][] = 'layoutcomponents/layoutcomponents.editform';

    if (!array_key_exists('bootstrap4', $this->themeHandler->listInfo())) {
      $this->messenger->addError($this->t('To use LayoutComponents is completely necessary Bootstrap4 theme.'));
      $output = [];
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function buildAddSectionLink(SectionStorageInterface $section_storage, $delta) {
    $build = parent::buildAddSectionLink($section_storage, $delta);
    /** @var \Drupal\Core\Config\Config $lc_settings */
    $lcSettings = $this->configFactory->getEditable('layoutcomponents.fields');
    $dialogOptions = Json::encode([
      'width' => $lcSettings->get('width'),
    ]);

    $build['link']['#title'] = '';

    // Alter Add Section button.
    /** @var \Drupal\Core\Url $url */
    $url = $build['link']['#url'];

    // Set update_layout if is "Add section".
    $url->setRouteParameter('update_layout', 0);

    // Remove link--add class.
    $options = $url->getOptions();
    $options['attributes']['class'] = [
      'use-ajax',
      'link-rounded',
      'lc_editor-link',
      'layout-builder__link',
      'layout-builder__link-add-section',
    ];
    $options['attributes']['data-dialog-options'] = $dialogOptions;
    $options['attributes']['title'] = $this->t('Add new section');

    // Save new options.
    $url->setOptions($options);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildAdministrativeSection(SectionStorageInterface $section_storage, $delta) {
    $build = parent::buildAdministrativeSection($section_storage, $delta);

    // Remove default section.
    if ($build['layout-builder__section']['#theme'] == 'layout__onecol') {
      return [];
    }

    /** @var \Drupal\Core\Config\Config $lc_settings */
    $lcSettings = $this->configFactory->getEditable('layoutcomponents.fields');
    $dialogOptions = Json::encode([
      'width' => $lcSettings->get('width'),
    ]);

    // Storage settings.
    $storage_type = $section_storage->getStorageType();
    $storage_id = $section_storage->getStorageId();
    $section = $section_storage->getSection($delta);
    $layout = $section->getLayout();
    $layout_definition = $layout->getPluginDefinition();

    // Alter configure button.
    $configure['configure'] = $build['configure'];
    $configure['configure']['#url'] = $this->addTooltip($configure['configure']['#url'], 'Configure this section');
    $configure['configure']['#title'] = '';
    $configure['configure']['#attributes']['data-dialog-options'] = $dialogOptions;
    $configure['configure']['#attributes']['class'] = [
      'use-ajax',
      'lc_editor-link',
      'layout-builder__section_link',
      'layout-builder__section_link-configure',
    ];

    // Alter remove button.
    $remove['remove'] = $build['remove'];
    $remove['remove']['#url'] = $this->addTooltip($remove['remove']['#url'], 'Remove this section');
    $remove['remove']['#title'] = '';
    $remove['remove']['#attributes']['data-dialog-options'] = $dialogOptions;
    $remove['remove']['#attributes']['class'] = [
      'use-ajax',
      'lc_editor-link',
      'layout-builder__section_link',
      'layout-builder__section_link-remove',
    ];

    // Add change layout button.
    $update = [
      'move_layout' => [
        '#type' => 'link',
        '#title' => '',
        '#url' => Url::fromRoute('layout_builder.move_sections_form',
          [
            'section_storage_type' => $storage_type,
            'section_storage' => $storage_id,
          ],
          [
            'attributes' => [
              'class' => [
                'use-ajax',
                'lc_editor-link',
                'layout-builder__section_link',
                'layout-builder__section_link-move',
              ],
              'data-dialog-type' => 'dialog',
              'data-dialog-renderer' => 'off_canvas',
              'data-dialog-options' => $dialogOptions,
              'title' => $this->t('Move section'),
            ],
          ]
        ),
        '#weight' => -1,
      ],
      'update_layout' => [
        '#type' => 'link',
        '#title' => '',
        '#url' => Url::fromRoute('layout_builder.choose_section',
          [
            'section_storage_type' => $storage_type,
            'section_storage' => $storage_id,
            'delta' => $delta,
            'update_layout' => 1,
          ],
          [
            'attributes' => [
              'class' => [
                'use-ajax',
                'lc_editor-link',
                'layout-builder__section_link',
                'layout-builder__section_link-update',
              ],
              'data-dialog-type' => 'dialog',
              'data-dialog-renderer' => 'off_canvas',
              'data-dialog-options' => $dialogOptions,
              'title' => $this->t('Change layout'),
            ],
          ]
        ),
      ],
    ];

    // Reorder section links.
    $new_config = $remove + $configure + $update;
    $new_config['#type'] = 'container';
    $new_config['#attributes']['class'] = 'layout_builder__configure_section_items';
    $new_config['#weight'] = -1;

    // Remove old buttons.
    unset($build['configure']);
    unset($build['remove']);

    $build['layout-builder__configure_section'] = $new_config;

    foreach ($layout_definition->getRegions() as $region => $info) {

      // Blocks.
      $section = &$build['layout-builder__section'];
      if (!empty($section[$region])) {
        foreach (Element::children($section[$region]) as $uuid) {
          if (array_key_exists('#contextual_links', $section[$region][$uuid])) {
            // Implement buildAdministrativeBlock().
            $section[$region][$uuid]['content']['layout_builder-configuration'] = $this->buildAdministrativeBlock($storage_type, $storage_id, $delta, $region, $uuid);
          }
        }
      }

      // Process Add Block button.
      $addBlock = &$build['layout-builder__section'][$region]['layout_builder_add_block'];
      $addBlock['link']['#title'] = '';
      $addBlock['#weight'] = 999;

      /** @var \Drupal\Core\Url $url */
      $url = $addBlock['link']['#url'];

      // Remove link--add class.
      $options = $url->getOptions();
      $options['attributes']['class'] = [
        'use-ajax',
        'lc_editor-link',
        'link-rounded',
        'layout-builder__column_link',
        'layout-builder__column_link-add',
      ];
      $options['attributes']['data-dialog-options'] = $dialogOptions;
      $options['attributes']['title'] = $this->t('Add new block');
      $url->setOptions($options);

      // Column.
      $configureSection = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['layout-builder__configure-column'],
        ],
        '#weight' => 1,
      ];

      $configureSection['configure'] = [
        '#type' => 'link',
        '#title' => '',
        '#url' => Url::fromRoute('layoutcomponents.update_column',
          [
            'section_storage_type' => $storage_type,
            'section_storage' => $storage_id,
            'delta' => $delta,
            'region' => $region,
          ],
          [
            'attributes' => [
              'class' => [
                'use-ajax',
                'lc_editor-link',
                'layout-builder__column_link',
                'layout-builder__column_link-configure',
              ],
              'data-dialog-type' => 'dialog',
              'data-dialog-renderer' => 'off_canvas',
              'data-dialog-options' => $dialogOptions,
              'title' => $this->t('Configure column'),
            ],
          ]
        ),
      ];

      // Reorder block links.
      $build['layout-builder__section'][$region][] = $configureSection;
    }

    return $build;
  }

  /**
   * Builds the render array for the layout block while editing.
   *
   * @param string $storage_type
   *   The section storage.
   * @param string $storage_id
   *   The storage id.
   * @param int $delta
   *   The delta of the section.
   * @param string $region
   *   The region.
   * @param string $uuid
   *   The uuid.
   *
   * @return array
   *   The render array for a given block.
   */
  public function buildAdministrativeBlock($storage_type, $storage_id, $delta, $region, $uuid) {
    /** @var \Drupal\Core\Config\Config $lc_settings */
    $lcSettings = $this->configFactory->getEditable('layoutcomponents.fields');

    $dialogOptions = Json::encode([
      'width' => $lcSettings->get('width'),
    ]);

    $configureBlock = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['layout-builder__configure-block'],
      ],
      '#weight' => -2,
    ];

    $configureBlock['move'] = [
      '#type' => 'link',
      '#title' => '',
      '#url' => Url::fromUserInput('#',
        [
          'attributes' => [
            'class' => [
              'use-ajax',
              'lc_editor-link',
              'layout-builder__block_link',
              'layout-builder__block_link-move',
            ],
            'data-dialog-type' => 'dialog',
            'data-dialog-renderer' => 'off_canvas',
            'data-dialog-options' => $dialogOptions,
            'title' => $this->t('Move block'),
          ],
        ]
      ),
    ];

    $configureBlock['remove'] = [
      '#type' => 'link',
      '#title' => '',
      '#url' => Url::fromRoute('layout_builder.remove_block',
        [
          'section_storage_type' => $storage_type,
          'section_storage' => $storage_id,
          'delta' => $delta,
          'region' => $region,
          'uuid' => $uuid,
        ],
        [
          'attributes' => [
            'class' => [
              'use-ajax',
              'lc_editor-link',
              'layout-builder__block_link',
              'layout-builder__block_link-remove',
            ],
            'data-dialog-type' => 'dialog',
            'data-dialog-renderer' => 'off_canvas',
            'data-dialog-options' => $dialogOptions,
            'title' => $this->t('Remove block'),
          ],
        ]
      ),
    ];

    $configureBlock['configure'] = [
      '#type' => 'link',
      '#title' => '',
      '#url' => Url::fromRoute('layout_builder.update_block',
        [
          'section_storage_type' => $storage_type,
          'section_storage' => $storage_id,
          'delta' => $delta,
          'region' => $region,
          'uuid' => $uuid,
        ],
        [
          'attributes' => [
            'class' => [
              'use-ajax',
              'lc_editor-link',
              'layout-builder__block_link',
              'layout-builder__block_link-configure',
            ],
            'data-dialog-type' => 'dialog',
            'data-dialog-renderer' => 'off_canvas',
            'data-dialog-options' => $dialogOptions,
            'title' => $this->t('Configure block'),
            'resizable' => TRUE,
          ],
        ]
      ),
    ];

    return $configureBlock;
  }

  /**
   * Provide tooltip for Url elements.
   *
   * @param \Drupal\Core\Url $url
   *   The section storage.
   * @param string $text
   *   The text.
   *
   * @return \Drupal\Core\Url
   *   The url preprocessed.
   */
  public function addTooltip(Url $url, $text) {
    $options = $url->getOptions();
    $options['attributes']['title'] = $text;
    $url->setOptions($options);

    return $url;
  }

}
