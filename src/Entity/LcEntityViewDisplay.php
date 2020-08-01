<?php

namespace Drupal\layoutcomponents\Entity;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\layoutcomponents\LcDisplayHelperTrait;

/**
 * Provides an entity view display entity that has a layout by LC.
 */
class LcEntityViewDisplay extends LayoutBuilderEntityViewDisplay {

  use LcDisplayHelperTrait;

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
      // Content sections.
      $sections = $this->getOrderedSections($section_storage);

      // Set the rest of defaults.
      foreach ($sections as $delta => $section) {
        if (!isset($section)) {
          continue;
        }
        if ($section->getLayoutId() == 'layout_builder_blank') {
          continue;
        }

        $build[$delta] = $section->toRenderArray($contexts);
      }
    }

    $cacheability->applyTo($build);
    return $build;
  }

}
