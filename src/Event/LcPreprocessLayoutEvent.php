<?php

namespace Drupal\layoutcomponents\Event;

use Drupal\layoutcomponents\LcLayout;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event that is fired when a section is preprocesed.
 */
class LcPreprocessLayoutEvent extends Event {

  const LC_LAYOUT = 'layoutcomponents_preprocess_layout';

  /**
   * The layout array.
   *
   * @var \Drupal\layoutcomponents\LcLayout
   */
  protected $layout;

  /**
   * Constructs the object.
   *
   * @param \Drupal\layoutcomponents\LcLayout $layout
   *   The complete object with the data.
   */
  public function __construct(LcLayout $layout) {
    $this->layout = $layout;
  }

  /**
   * Get the layout object.
   *
   * @return \Drupal\layoutcomponents\LcLayout
   */
  public function getLayout() {
    return $this->layout;
  }

  /**
   * Set the layout object.
   *
   * @param \Drupal\layoutcomponents\LcLayout $layout
   *   The complete layout object.
   */
  public function setLayout(LcLayout $layout) {
    $this->layout = $layout;
  }

}
