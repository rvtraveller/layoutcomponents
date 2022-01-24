<?php

namespace Drupal\layoutcomponents\Event;

use Drupal\layoutcomponents\LcLayoutRender;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event that is fired when a section is preprocesed.
 */
class LcPreprocessLayoutEvent extends Event {

  const LC_LAYOUT = 'layoutcomponents_preprocess_layout';

  /**
   * The layout array.
   *
   * @var \Drupal\layoutcomponents\LcLayoutRender
   */
  protected $layout;

  /**
   * Constructs the object.
   *
   * @param \Drupal\layoutcomponents\LcLayoutRender $layout
   *   The complete object with the data.
   */
  public function __construct(LcLayoutRender $layout) {
    $this->layout = $layout;
  }

  /**
   * Get the layout object.
   *
   * @return \Drupal\layoutcomponents\LcLayoutRender
   */
  public function getLayout() {
    return $this->layout;
  }

  /**
   * Set the layout object.
   *
   * @param \Drupal\layoutcomponents\LcLayoutRender $layout
   *   The complete layout object.
   */
  public function setLayout(LcLayoutRender $layout) {
    $this->layout = $layout;
  }

}
