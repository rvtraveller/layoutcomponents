<?php

/**
 * @file
 * Contains layoutcomponents.theme.inc.
 */

use Drupal\layoutcomponents\LcLayout;
use Drupal\layoutcomponents\Event\LcPreprocessLayoutEvent;

/**
 * Preprocess lc theme.
 *
 * @param array $variables
 *   The array with variables.
 */
function _layoutcomponents_preprocess_layout(array &$variables) {
  /** @var \Drupal\Core\Layout\LayoutDefinition $layout_definition */
  $layout_definition = $variables['layout'];

  // Remove quick edit as it doesn't make sense here. It also points to the view
  // for instance, which doesn't make sense at all.
  if (isset($variables['attributes']['data-quickedit-entity-id'])) {
    unset($variables['attributes']['data-quickedit-entity-id']);
  }

  /** @var \Drupal\layoutcomponents\LcLayoutRender $layout */
  $layout = \Drupal::service('layoutcomponents.render');

  // Dispatch the event.
  $event = new LcPreprocessLayoutEvent($layout);
  $event_dispatcher = \Drupal::service('event_dispatcher');
  $event_dispatcher->dispatch(LcPreprocessLayoutEvent::LC_LAYOUT, $event);

  $variables['lc_admin'] = FALSE;
  $route = \Drupal::routeMatch()->getRouteObject();
  if ($route->hasOption('parameters')) {
    $variables['lc_admin'] = array_key_exists('section_storage', $route->getOption('parameters'));
  }

  $render = $layout->render(
    [
      'id' => $layout_definition->id(),
      'settings' => $variables['settings'],
      'content' => $variables['content'],
      'default' => $variables['attributes'],
    ]
  );

  if (isset($render) && !empty($render)) {
    // Set new variables.
    $variables['lc_data'] = $render;
    $variables['#attached']['drupalSettings']['lc'] = $layout->getJsSettings();
  }
}
