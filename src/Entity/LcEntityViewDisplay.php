<?php

namespace Drupal\layoutcomponents\Entity;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\layout_builder\Plugin\SectionStorage\DefaultsSectionStorage;

/**
 * Provides an entity view display entity that has a layout by LC.
 */
class LcEntityViewDisplay extends LayoutBuilderEntityViewDisplay {

  /**
   * Gets the section storage manager.
   *
   * @return \Drupal\layout_builder\SectionStorage\SectionStorageManagerInterface
   *   The section storage manager.
   */
  private function sectionStorageManager() {
    return \Drupal::service('plugin.manager.layout_builder.section_storage');
  }

  /**
   * Gets the LC layout manager.
   *
   * @return \Drupal\layoutcomponents\LcLayoutsManager
   *   The LC layout manager.
   */
  private function lcLayoutManager() {
    return \Drupal::service('plugin.manager.layoutcomponents_layouts');
  }

  /**
   * {@inheritdoc}
   */
  public function buildSections(FieldableEntityInterface $entity) {
    // @todo This method has been overwrited to control the default sections configurated in each display.
    $contexts = $this->getContextsForEntity($entity);
    $label = new TranslatableMarkup('@entity being viewed', [
      '@entity' => $entity->getEntityType()->getSingularLabel(),
    ]);
    $contexts['layout_builder.entity'] = EntityContext::fromEntity($entity, $label);

    $cacheability = new CacheableMetadata();
    $section_storage = $this->sectionStorageManager()->findByContext($contexts, $cacheability);

    $build = [];
    if ($section_storage) {
      if (!$section_storage instanceof DefaultsSectionStorage) {
        // Default sections.
        $defaults = $section_storage->getDefaultSectionStorage()->getSections();

        // Content sections.
        $sections = $section_storage->getSections();

        // Reset sections.
        $section_storage->removeAllSections(FALSE);

        foreach ($sections as $delta => $section) {
          $settings = $section->getLayoutSettings();
          $section_label = $settings['section']['general']['basic']['section_label'];
          $section_overwrite = $settings['section']['general']['basic']['section_overwrite'];
          if (boolval($section_overwrite)) {
            if ($this->lcLayoutManager()->checkDefaultExists($defaults, $section_label)) {
              // Remplace if the section is a defualt.
              $d_delta = $delta;
              unset($sections[$delta]);
              $default = $this->lcLayoutManager()->getDefault($defaults, $section_label, $d_delta);
              if (isset($default)) {
                $this->lcLayoutManager()->arrayInsert($sections, $d_delta, $default);
              }
            }
            else {
              // Remove the section if is default and not exists.
              unset($sections[$delta]);
            }
          }
        }

        // Store the rest of defaults.
        /** @var \Drupal\layout_builder\Section $default */
        foreach ($defaults as $delta => $default) {
          $settings = $default->getLayoutSettings();
          $default_delta = $settings['section']['general']['basic']['section_delta'];
          $this->lcLayoutManager()->arrayInsert($sections, $default_delta, $default);
          unset($defaults[$delta]);
        }

        ksort($sections);

        // Set the rest of defaults.
        foreach ($sections as $delta => $section) {
          $build[$delta] = $section->toRenderArray($contexts);
        }
      }
      else {
        foreach ($section_storage->getSections() as $delta => $section) {
          $build[$delta] = $section->toRenderArray($contexts);
        }
      }
    }

    $cacheability->applyTo($build);
    return $build;
  }

}
