<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormFactoryInterface;
use Drupal\layout_builder\Controller\LayoutRebuildTrait;
use Drupal\layout_builder\Form\ConfigureSectionForm;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Drupal\layout_builder\Section;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\layoutcomponents\LcLayoutsManager;
use Drupal\layout_builder\Plugin\SectionStorage\DefaultsSectionStorage;

/**
 * Provides a form for configuring a layout section.
 *
 * @internal
 *   Form classes are internal.
 */
class LcConfigureSection extends ConfigureSectionForm {

  use AjaxFormHelperTrait;
  use LayoutRebuildTrait;

  /**
   * RequestStack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * The LC manager.
   *
   * @var \Drupal\layoutcomponents\LcLayoutsManager
   */
  protected $lcLayoutManager;

  /**
   * Is a default section.
   *
   * @var bool
   */
  protected $isDefault;

  /**
   * {@inheritdoc}
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, PluginFormFactoryInterface $plugin_form_manager, RequestStack $request, LcLayoutsManager $layout_manager) {
    parent::__construct($layout_tempstore_repository, $plugin_form_manager);
    $this->request = $request;
    $this->lcLayoutManager = $layout_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('layout_builder.tempstore_repository'),
      $container->get('plugin_form.factory'),
      $container->get('request_stack'),
      $container->get('plugin.manager.layoutcomponents_layouts')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, $plugin_id = NULL) {
    $this->isDefault = 0;
    // Check section type.
    try {
      $section = $section_storage->getSection($delta)->getLayoutSettings();
      if (array_key_exists('section', $section)) {
        $section_overwrite = $section_storage->getSection($delta)->getLayoutSettings()['section']['general']['basic']['section_overwrite'];
        $this->isDefault = (boolval($section_overwrite) && !$section_storage instanceof DefaultsSectionStorage) ? TRUE : FALSE;
      }
    }
    catch (\Exception $e) {
      $this->isDefault = 0;
    }

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
      $plugin_id = NULL;
    }

    $build = parent::buildForm($form, $form_state, $section_storage, $delta, $plugin_id);

    if ($this->isDefault && !boolval($autosave)) {
      // This section cannot be configured.
      $message = 'This section cannot be configured because is configurated as default';
      $build = $this->lcLayoutManager->getDefaultCancel($message);
    }
    else {
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
    }

    // Hidde other configurations.
    $build['layout_settings']['container']['regions']['#access'] = FALSE;
    $build['layout_settings']['container']['section']['#open'] = TRUE;
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getLayoutSettings() {
    return $this->layout;
  }

}
