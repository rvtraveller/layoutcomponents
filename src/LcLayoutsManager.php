<?php

namespace Drupal\layoutcomponents;

use Drupal\Core\Layout\LayoutPluginManager;
/**
 * Class LCLayoutsManager.
 */
class LcLayoutsManager {

  /**
   * Section options.
   *
   * @var array
   */
  protected $wrapperOptions = [];

  /**
   * Templates.
   *
   * @var array
   */
  protected $templates = [];

  /**
   * Section size.
   *
   * @var array
   */
  protected $wrapperSize = [];

  /**
   * Section top space.
   *
   * @var array
   */
  protected $wrapperTopSpace = [];

  /**
   * Section content align.
   *
   * @var array
   */
  protected $wrapperContentAlign = [];

  /**
   * Column name.
   *
   * @var array
   */
  protected $columnName = [];

  /**
   * Column title align.
   *
   * @var array
   */
  protected $columnTitleAlign = [];

  /**
   * Column border.
   *
   * @var array
   */
  protected $columnBorder = [];

  /**
   * List of ayouts.
   *
   * @var array
   */
  protected $layouts = [];

  /**
   * Layout Plugin Manager.
   *
   * @var \Drupal\Core\Layout\LayoutPluginManager
   */
  protected $LayoutPluginManager;

  /**
   * Constructs a new \Drupal\layoutcomponents\LcLayoutsManager object.
   */
  public function __construct(LayoutPluginManager $layout_plugin_manager) {
    $this->wrapperOptions = [
      'div' => 'Div',
      'span' => 'Span',
      'section' => 'Section',
      'article' => 'Article',
      'header' => 'Header',
      'footer' => 'Footer',
      'aside' => 'Aside',
      'figure' => 'Figure',
    ];

    $this->tagOptions = [
      'div' => 'Div',
      'span' => 'Span',
      'h1' => 'H1',
      'h2' => 'H2',
      'h3' => 'H3',
      'h4' => 'H4',
      'h5' => 'H5',
      'h6' => 'H6',
    ];

    $this->templates = [
      'layoutcomponents-one-column' => 1,
      'layoutcomponents-two-column' => 2,
      'layoutcomponents-three-column' => 3,
      'layoutcomponents-four-column' => 4,
      'layoutcomponents-five-column' => 5,
      'layoutcomponents-six-column' => 6,
    ];

    $this->wrapperSize = [
      'col-md-1' => '1 column',
      'col-md-2' => '2 columns',
      'col-md-3' => '3 columns',
      'col-md-4' => '4 columns',
      'col-md-5' => '5 columns',
      'col-md-6' => '6 columns',
      'col-md-7' => '7 columns',
      'col-md-8' => '8 columns',
      'col-md-9' => '9 columns',
      'col-md-10' => '10 columns',
      'col-md-11' => '11 columns',
      'col-md-12' => '12 columns',
    ];

    $this->wrapperTopSpace = [
      'simple-margin-none' => 'None',
      'simple-margin-small' => 'Small',
      'simple-margin-medium' => 'Medium',
      'simple-margin-large' => 'Large',
    ];

    $this->wrapperContentAlign = [
      'justify-content-start' => 'Left',
      'justify-content-center' => 'Center',
      'justify-content-end' => 'Right',
    ];

    $this->columnName = [
      0 => 'first',
      1 => 'second',
      2 => 'third',
      3 => 'quarter',
      4 => 'fifth',
      5 => 'sixth',
    ];

    $this->columnTitleAlign = [
      'text-left' => 'Left',
      'text-center' => 'Center',
      'text-right' => 'Right',
    ];

    $this->titleBorder = [
      'none' => 'None',
      'left' => 'Left',
      'top' => 'Top',
      'right' => 'Right',
      'bottom' => 'Bottom',
      'all' => 'All',
    ];

    $this->columnBorder = [
      'none' => 'None',
      'left' => 'Left',
      'top' => 'Top',
      'right' => 'Right',
      'bottom' => 'Bottom',
      'all' => 'All',
    ];

    $this->LayoutPluginManager = $layout_plugin_manager;

    $this->getLayoutComponentsLayouts();
  }

  /**
   * Set the layouts filtered by LC class.
   */
  protected function getLayoutComponentsLayouts() {
    $layoutList = $this->LayoutPluginManager->getDefinitions();
    foreach ($layoutList as $name => $layout) {
      /** @var \Drupal\Core\Layout\LayoutDefinition $layout */
      if ($layout->getClass() === 'Drupal\layoutcomponents\Plugin\Layout\LcBase') {
        $this->layouts[] = $layout;
      }
    }
  }

  /**
   * Get the wrapper options.
   *
   * @return array
   *   The wrapper options.
   */
  public function getWrapperOptions() {
    return $this->wrapperOptions;
  }

  /**
   * Get the tag options.
   *
   * @return array
   *   The tag options.
   */
  public function getTagOptions() {
    return $this->tagOptions;
  }

  /**
   * Get the wrapper size.
   *
   * @return array
   *   The wrapper size.
   */
  public function getWrapperSize() {
    return $this->wrapperSize;
  }

  /**
   * Get the wrapper top space.
   *
   * @return array
   *   The wrapper top space.
   */
  public function getWrappetTopSpace() {
    return $this->wrapperTopSpace;
  }

  /**
   * Get the wrapper content align.
   *
   * @return array
   *   The wrapper content align.
   */
  public function getWrappetContentAlign() {
    return $this->wrapperContentAlign;
  }

  /**
   * Get the wrapper title align.
   *
   * @return array
   *   The wrapper title align.
   */
  public function getColumnTitleAlign() {
    return $this->columnTitleAlign;
  }

  /**
   * Get the title border.
   *
   * @return array
   *   The title border
   */
  public function getTitleBorder() {
    return $this->titleBorder;
  }

  /**
   * Get the column border.
   *
   * @return array
   *   The wrapper column border.
   */
  public function getColumnBorder() {
    return $this->columnBorder;
  }

  /**
   * Get the column name.
   *
   * @return array
   *   The column name.
   */
  public function getColumName($index) {
    return $this->columnName[$index];
  }

  /**
   * Get the column options.
   *
   * @return array
   *   The column options.
   */
  public function getColumnOptions($type) {
    if (isset($type)) {
      $options = ["12" => "100"];
      switch ($type) {
        case 1:
          $options = [
            "1" => '1 Column',
            "2" => '2 Columns',
            "3" => '3 Columns',
            "4" => '4 Columns',
            "5" => '5 Columns',
            "6" => '6 Columns',
            "7" => '7 Columns',
            "8" => '8 Columns',
            "9" => '9 Columns',
            "10" => '10 Columns',
            "11" => '11 Columns',
            "12" => '12 Columns',
          ];
          break;

        case 2:
          $options = [
            "4/8" => '35/65%',
            "8/4" => '65/35%',
            "5/7" => '40/60%',
            "7/5" => '60/40%',
            "6/6" => '50/50%',
            "9/3" => '75/25%',
            "3/9" => '25/75%',
          ];
          break;

        case 3:
          $options = [
            "3/6/3" => '25/50/25%',
            "4/4/4" => '33/33/33%',
            "3/3/6" => '25/25/50%',
            "6/3/3" => '50/25/25%',
          ];
          break;

        case 4:
          $options = [
            "3/3/3/3" => '25/25/25/25%',
            "6/2/2/2" => '50/25/25/25%',
            "2/6/2/2" => '25/50/25/25%',
            "2/2/6/2" => '25/25/50/25%',
            "2/2/2/6" => '25/25/25/50%',
          ];
          break;

        case 5:
          $options = [
            "3/2/2/2/3" => '25/22/22/22/25%',
          ];
          break;

        case 6:
          $options = [
            "2/2/2/2/2/2" => '16.6/16.6/16.6/16.6/16.6/16.6%',
          ];
          break;
      }
    }
    return $options;
  }

  /**
   * Get the number of columns.
   *
   * @return array
   *   The number of column.
   */
  public function getNumberOfColumns($template) {
    return $this->templates[$template];
  }

  /**
   * Convert hex color to rgba.
   *
   * @param string $hex
   *   The color as hex.
   * @param string $opacity
   *   The opacity.
   *
   * @return string
   *   The color converted to rgb|rgba.
   */
  public function hexToRgba($hex, $opacity = NULL) {
    if (isset($hex) && isset($opacity)) {
      list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
      $background_color = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
    }
    else {
      list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
      $background_color = 'rgb(' . $r . ',' . $g . ',' . $b . ')';
    }
    return $background_color;
  }

}
