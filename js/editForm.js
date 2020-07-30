/**
 * @file
 * Layout Components behaviors.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  var ajax = Drupal.ajax,
      behaviors = Drupal.behaviors;

  // Focus of all links.
  behaviors.removeFocus = {
    attach: function (context) {
      $(".layout-builder a").on('click', function () {
        this.blur();
        this.hideFocus = true;
        this.style.outline = 'none';
      });
    }
  };

  // Control the configuration of the blocks.
  behaviors.configureBlock = {
    attach: function (context) {
      $('div[data-layout-block-uuid]').hover(function () {
        $(this).find('.layout-builder__configure-block').css('visibility', 'initial');
      },function () {
        $(this).find('.layout-builder__configure-block').css('visibility', 'hidden');
      });
    }
  };

  // Control the movement of the blocks.
  behaviors.moveBlock = {
    attach: function (context) {
      var selector = 'div[data-layout-block-uuid]';
      $(".layout-builder__configure-block").next().hover(function () {
        $(this).parents(selector).removeClass('js-layout-builder-block layout-builder-block');
      },function () {
        var currentClasses = $(this).parents(selector).prop("class");
        $(this).parents(selector).removeClass(currentClasses).addClass('js-layout-builder-block layout-builder-block' + " " + currentClasses);
      });
    }
  };

  behaviors.editInlineElements = {
    attach: function (context) {

      let $context = $(context);

      Array.prototype.forEach.call(context.querySelectorAll('*[input="text"]'), function (text) {
        $(text).on('focus', function () {
          // Store old value
          $(this).attr('lc-prev-value', $(this).val());
        }).on('keyup', function (e, clickedIndex, newValue, oldValue) {
          behaviors.inlineElement(this);
          // Store old value after change.
          $(this).attr('lc-prev-value', $(this).val());
        })
      });

      let selectors = [
        '*[input="select"]',
        '*[input="slider"]',
        'input[input="color"]',
        'input[input="opacity"]',
        '*[input="checkbox"]',
      ];

      Array.prototype.forEach.call(context.querySelectorAll(selectors), function (select) {
        $(select).on('change', function (e) {
          behaviors.inlineElement(this);
        })
      });

      Array.prototype.forEach.call(context.querySelectorAll('*[input="image"]'), function (image) {
        $(image).trigger('change');
        $(image).on('change', function (e) {
          behaviors.inlineElement(this);
        })
      });

      Array.prototype.forEach.call(context.querySelectorAll('*[input="media"]'), function (image) {
        image = $(image);
        let info = $.parseJSON(image.attr('lc'));
        let $input = $(image).find('input[lc-media="' + info.id + '"]');
        image.val($input.val());
        behaviors.inlineElement(image);
      });
    }
  };

  behaviors.editInlineCkeditor = {
    attach: function (context) {
      for(let instanceName in CKEDITOR.instances){
        CKEDITOR.instances[instanceName].on('change', function () {
          let item = $(this.element.$);
          item.val(this.getData());
          behaviors.inlineElement(item);
        });
      }
    }
  };

  behaviors.editInlineSlider = {
    attach: function (context) {
      $('.lc_inline-slider').each(function () {
        let item = $(this);
        let slider = $(this).parents('.sliderwidget', context);
        $(slider).on('DOMSubtreeModified', ".sliderwidget-display-values-field", function () {
          let value = parseInt($(this).html());
          if (value > 0) {
            item.val(value);
          }
        });
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
