<?php

namespace Drupal\layoutcomponents\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\path_alias\AliasManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Render\Renderer;

/**
 * Field formatter for Viewsreference Field.
 *
 * @FieldFormatter(
 *   id = "layoutcomponents_entity_formatter",
 *   label = @Translation("Default"),
 *   field_types = {"layoutcomponents_field_reference"}
 * )
 */
class LcFieldReferenceFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The alias manager.
   *
   * @var \Drupal\path_alias\AliasManager
   */
  protected $aliasManager;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, AliasManager $alias_manager, RequestStack $request_stack, Renderer $renderer) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
    $this->aliasManager = $alias_manager;
    $this->requestStack = $request_stack;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('path_alias.manager'),
      $container->get('request_stack'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();
    $options['plugin_types'] = ['block'];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = [];

    $view_mode = 'default';

    foreach ($items as $delta => $item) {
      $entity_type = $items->getValue()[$delta]['entity_type'];
      $entity = $items->getValue()[$delta]['entity_id'];
      $entity_id_context = $items->getValue()[$delta]['entity_id_context'];
      $entity_field = $items->getValue()[$delta]['entity_field'];
      if (!empty($entity_type) && !empty($entity_field)) {
        // If entity id takes from current URL or with direct ID.
        if (!empty($entity_id_context)) {
          $path = parse_url($this->requestStack->getCurrentRequest()->getRequestUri(), PHP_URL_PATH);
          if (!empty($path)) {
            $source_uri = $this->aliasManager->getPathByAlias($path);
            if (!empty($source_uri)) {
              $params = Url::fromUri("internal:" . $source_uri)->getRouteParameters();
              if (!empty($params)) {
                $current_entity_type = key($params);
                if (!empty($current_entity_type)) {
                  // Check if the current node is equal as the entity type.
                  if ($current_entity_type == $entity_type) {
                    $entity_id = isset($params[$entity_type]) ? $params[$entity_type] : NULL;
                    if (!empty($entity_id)) {
                      $entity = $this->entityTypeManager->getStorage($entity_type)->load($params[$entity_type]);
                      if (!empty($entity)) {
                        $bundle = $entity->bundle();
                      }
                    }
                  }
                }
              }
            }
          }
        }
        else {
          if (!empty($entity)) {
            $entity = explode('-', $entity);
            if (count($entity) > 1) {
              $entity_id = isset($entity[0]) ? $entity[0] : NULL;
              $bundle = isset($entity[1]) ? $entity[1] : NULL;
              if (!empty($entity_id) && !empty($bundle)) {
                $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);
              }
            }
          }
        }
        // Once that we have loaded the bundle and entity we build the display.
        if (!empty($entity) && !empty($bundle)) {
          // Load field.
          $field = $entity->get($entity_field);
          if (!empty($field)) {
            // Set the display of the field.
            $display = $this->entityTypeManager->getStorage('entity_view_display')->load($entity_type . '.' . $bundle . '.' . $view_mode);
            if (!empty($display)) {
              // Set display settings of the field.
              $display_options = $display->getComponent($entity_field);
              $display_options['label'] = $items->getValue()[$delta]['entity_field_label'];
              // Build the field.
              $field_view = $field->view($display_options);
              // Render the field.
              $elements[$delta] = [
                '#type' => 'markup',
                '#markup' => $this->renderer->render($field_view),
              ];
            }
          }
        }
      }
    }

    return $elements;
  }

}
