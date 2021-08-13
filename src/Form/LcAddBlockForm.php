<?php

namespace Drupal\layoutcomponents\Form;

use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Form\AddBlockForm;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Provides a form to add a block.
 */
class LcAddBlockForm extends AddBlockForm {

  use AjaxHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, $region = NULL, $plugin_id = NULL) {
    $build = parent::buildForm($form, $form_state, $section_storage, $delta, $region, $plugin_id);

    $admin_label = isset($build['settings']['admin_label']) ? $build['settings']['admin_label'] : NULL;

    if (array_key_exists('block_form', $build['settings'])) {
      /** @var \Drupal\block_content\Entity\BlockContent $block */
      $block = $build['settings']['block_form']['#block'];
      $build['#title'] = $this->t("Add new @title", ['@title' => $block->bundle()]);
    }
    else {
      $admin_label_plain_text = '';
      if (!empty($admin_label)) {
        $admin_label_plain_text = isset($admin_label['#plain_text']) ? $admin_label['#plain_text'] : '';
      }
      $build['#title'] = $this->t("Add field @title", ['@title' => $admin_label_plain_text]);
    }

    // Title and description config.
    if (!empty($admin_label)) {
      $build['settings']['admin_label']['#access'] = FALSE;
    }

    $build['settings']['label']['#title'] = $this->t('<span class="lc-lateral-title">@title</span> <span class="lc-lateral-info" title="@content"/>',
      ['@title' => 'Title', '@content' => 'Set an identifier of this block']);

    unset($build['settings']['label']['#description']);

    // Label display config.
    $build['settings']['label_display']['#default_value'] = FALSE;

    // Add custom libraries.
    $build['#attached']['library'][] = 'layoutcomponents/layoutcomponents.lateral';

    return $build;
  }

}
