<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormFactoryInterface;
use Drupal\layout_builder\Form\ConfigureSectionForm;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Drupal\layout_builder\Section;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a form for configuring a layout section.
 *
 * @internal
 *   Form classes are internal.
 */
class LcConfigureSection extends ConfigureSectionForm {

  /**
   * RequestStack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, PluginFormFactoryInterface $plugin_form_manager, RequestStack $request) {
    parent::__construct($layout_tempstore_repository, $plugin_form_manager);
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('layout_builder.tempstore_repository'),
      $container->get('plugin_form.factory'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, $plugin_id = NULL) {

    // Get custom params.
    $update_layout = $this->request->getCurrentRequest()->query->get('update_layout');
    $autosave = $this->request->getCurrentRequest()->query->get('autosave');

    // Do we need update the layout?
    if (boolval($update_layout)) {
      // Old Section.
      $section = $section_storage->getSection($delta);
      // Store old components.
      $components = $section->getComponents();
      // All componentes should be in first region.
      foreach ($components as $key => $component) {
        $component->set('region', 'first');
      }
      // Store old layout settings.
      $layoutSettings = $section->getLayoutSettings();
      // New section with old values.
      $newSection = new Section($plugin_id, $layoutSettings, $components);
      // Remove old section to not get conflicts.
      $section_storage->removeSection($delta);
      // Register new section in SectionStorage $section_storage.
      $section_storage->insertSection($delta, $newSection);
      // Remove plugin id to parent form detect new section as old section.
      unset($plugin_id);
    }

    $build = parent::buildForm($form, $form_state, $section_storage, $delta, $plugin_id);

    // Add new step if is new section or is a update.
    if (boolval($autosave)) {
      $build['new_section'] = [
        '#type' => 'help',
        '#markup' => '<div class="layout_builder__add-section-confirm">' . $this->t("Are you sure to add a new section?") . '</div>',
        '#weight' => -1,
      ];

      if (boolval($update_layout)) {
        $build['new_section']['#markup'] = '<div class="layout_builder__add-section-confirm">' . $this->t("Are you sure to change layout?") . '</div>';
      }

      $build['layout_settings']['container']['#prefix'] = '<div class="lc-lateral-container hidden">';
      $build['layout_settings']['container']['#suffix'] = '</div>';
    }

    // Hidde other configurations.
    $build['layout_settings']['container']['regions']['#access'] = FALSE;
    $build['layout_settings']['container']['section']['#open'] = TRUE;

    return $build;
  }

}
