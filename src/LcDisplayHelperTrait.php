<?php

namespace Drupal\layoutcomponents;

use Drupal\layout_builder\SectionStorageInterface;
use Drupal\layout_builder\Plugin\SectionStorage\DefaultsSectionStorage;
use Drupal\layout_builder\Section;

/**
 * Methods to help with section storages using LC.
 */
trait LcDisplayHelperTrait {

  /**
   * Gets revision IDs for layout sections.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage object.
   *
   * @return array
   *   The array of sections.
   */
  protected function getOrderedSections(SectionStorageInterface $section_storage) {
    $n_sections = [];

    // Content sections.
    $sections = $section_storage->getSections();

    // Reset sections.
    $section_storage->removeAllSections(FALSE);

    if ($section_storage instanceof DefaultsSectionStorage) {
      foreach ($sections as $delta => $section) {
        $d_delta = $section->getLayoutSettings()['section']['general']['basic']['section_delta'];
        $this->arrayInsert($n_sections, $d_delta, $section);
      }
      ksort($n_sections);
      return $n_sections;
    }

    // Default sections.
    $defaults = $section_storage->getDefaultSectionStorage()->getSections();

    foreach ($sections as $delta => $section) {
      $settings = $section->getLayoutSettings();
      $section_label = $settings['section']['general']['basic']['section_label'];
      $section_overwrite = $settings['section']['general']['basic']['section_overwrite'];
      if (boolval(!$section_overwrite)) {
        if ($this->checkDefaultExists($defaults, $section_label)) {
          // Remplace if the section is a defualt.
          $default = $this->getDefault($defaults, $section_label);
          if (isset($default)) {
            $d_delta = $default->getLayoutSettings()['section']['general']['basic']['section_delta'];
            $d_overwrite = $default->getLayoutSettings()['section']['general']['basic']['section_overwrite'];
            if (boolval($d_overwrite)) {
              $this->arrayInsert($n_sections, $d_delta, $default);
            }
            else {
              $n_sections[] = $sections[$delta];
            }
          }
        }
        else {
          $n_sections[] = $sections[$delta];
        }
        unset($sections[$delta]);
      }
    }

    foreach ($sections as $delta => $section) {
      $settings = $section->getLayoutSettings();
      $section_label = $settings['section']['general']['basic']['section_label'];
      $section_overwrite = $settings['section']['general']['basic']['section_overwrite'];
      if (boolval($section_overwrite)) {
        if ($this->checkDefaultExists($defaults, $section_label)) {
          // Remplace if the section is a defualt.
          $default = $this->getDefault($defaults, $section_label);
          if (isset($default)) {
            $d_delta = $default->getLayoutSettings()['section']['general']['basic']['section_delta'];
            $this->arrayInsert($n_sections, $d_delta, $default);
          }
        }
      }
    }

    // Store the rest of defaults.
    /** @var \Drupal\layout_builder\Section $default */
    foreach ($defaults as $delta => $default) {
      if ($default->getLayoutId() == 'layout_builder_blank') {
        continue;
      }
      $d_delta = $defaults[$delta]->getLayoutSettings()['section']['general']['basic']['section_delta'];
      $this->arrayInsert($n_sections, $d_delta, $defaults[$delta]);

    }

    ksort($n_sections);

    return $n_sections;
  }

  /**
   * Check if the section exists on default sections.
   *
   * @param array $defaults
   *   The array.
   * @param string $label
   *   The label of default section.
   *
   * @return bool
   *   If the default exists.
   */
  public function checkDefaultExists(array $defaults, $label) {
    /** @var \Drupal\layout_builder\Section $default */
    foreach ($defaults as $delta => $default) {
      if ($default->getLayoutSettings()['section']['general']['basic']['section_label'] == $label) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Get the default section.
   *
   * @param array $defaults
   *   The array.
   * @param string $label
   *   The label of default section.
   *
   * @return array|null
   *   The default array.
   */
  public function getDefault(array &$defaults, $label) {
    foreach ($defaults as $delta => $default) {
      $settings = $default->getLayoutSettings();
      $d_label = $settings['section']['general']['basic']['section_label'];
      if ($d_label == $label) {
        unset($defaults[$delta]);
        return $default;
      }
    }
    return NULL;
  }

  /**
   * Insert element in array by position.
   *
   * @param array $array
   *   The array.
   * @param int $position
   *   The delta of the section.
   * @param \Drupal\layout_builder\Section $new
   *   The new element.
   */
  public function arrayInsert(array &$array, $position, Section $new) {
    $first = array_slice($array, 0, $position, TRUE);
    $second = array_slice($array, $position);
    $first[$position] = $new;
    foreach ($second as $item) {
      $first[] = $item;
    }
    $array = $first;
  }

}
