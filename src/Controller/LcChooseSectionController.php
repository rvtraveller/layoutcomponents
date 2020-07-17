<?php

namespace Drupal\layoutcomponents\Controller;

use Drupal\Core\Layout\LayoutPluginManagerInterface;
use Drupal\layout_builder\Controller\ChooseSectionController;
use Drupal\layout_builder\SectionStorageInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines a controller to add a new section.
 *
 * @internal
 *   Controller classes are internal.
 */
class LcChooseSectionController extends ChooseSectionController {

  /**
   * RequestStack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * LcChooseSectionController constructor.
   *
   * @param \Drupal\Core\Layout\LayoutPluginManagerInterface $layout_manager
   *   The layout manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   The requestStack.
   */
  public function __construct(LayoutPluginManagerInterface $layout_manager, RequestStack $request) {
    parent::__construct($layout_manager);
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.core.layout'),
      $container->get('request_stack')
    );
  }

  /**
   * Adds the new section.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   *
   * @return array
   *   The render array.
   */
  public function build(SectionStorageInterface $section_storage, $delta) {

    // Get update layout param.
    $updateLayout = $this->request->getCurrentRequest()->query->get('update_layout');

    $build = parent::build($section_storage, $delta);

    $layoutcomponents = [];
    $others = [];
    $plugin_id = '';

    foreach ($build['layouts']['#items'] as $key => $item) {
      if (isset($item['#title']['#attributes'])) {
        $classes = $item['#title']['#attributes']['class'][1];
      }
      else {
        $classes = [];
      }
      /** @var \Drupal\Core\Url $url */
      $url = $item['#url'];
      $item['#url'] = Url::fromRoute('layout_builder.configure_section',
        [
          'section_storage_type' => $section_storage->getStorageType(),
          'section_storage' => $section_storage->getStorageId(),
          'delta' => $delta,
          'plugin_id' => $url->getRouteParameters()['plugin_id'],
          'update_layout' => $updateLayout,
          'autosave' => 1,
        ]);

      $plugin_id = $url->getRouteParameters()['plugin_id'];
      if (array_key_exists('plugin_id', $url->getRouteParameters())) {
        $plugin_id = $url->getRouteParameters()['plugin_id'];
        if (strpos($plugin_id, 'layoutcomponents_') !== FALSE) {
          $layoutcomponents[] = $item;
        }
        else {
          $others[] = $item;
        }
      }
    }
    // $output['layouts']['#items'] = array_merge($layoutcomponents, $others);.
    // Only layoutcomponents layouts.
    $build['layouts']['#items'] = $layoutcomponents;
    return $build;
  }

}
