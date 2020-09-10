<?php

namespace Drupal\layoutcomponents;

use Drupal\Core\Render\ElementInfoManager;
use Drupal\layoutcomponents\Element\LcElement;

/**
 * LcElementManager extended to alter LayoutBuilder Element.
 */
class LcElementManager extends ElementInfoManager {

  /**
   * {@inheritdoc}
   */
  protected function alterDefinitions(&$definitions) {
    parent::alterDefinitions($definitions);
    // Replace LayoutBuilder element class.
    if (isset($definitions['layout_builder'])) {
      if ()
      $definitions['layout_builder']['class'] = LcElement::class;
      $definitions['layout_builder']['provider'] = 'layoutcomponents';
    }
  }

}
