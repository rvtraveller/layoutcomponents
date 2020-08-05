/**
 * @file
 * Layout Components behaviors.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  var ajax = Drupal.ajax,
    behaviors = Drupal.behaviors;


  function carouselController(){
    let $lc_responsive = drupalSettings.lc.responsive;
    $.each($lc_responsive, function (index, value) {
      let $slick = '.' + index;
      let $columns = $($slick).parents('.' + value.parent).find('*[class^="lc-inline_column_"]');
      $.each(value.options, function (index, value) {
        if ($(window).width() <= value.breakpoint) {
          if (value.unslick === true) {
            $($slick).addClass('hidden');
            $columns.removeClass('hidden');
          }
          else {
            $($slick).removeClass('hidden');
            $columns.addClass('hidden');
          }
        }
      })
    })
  }

  behaviors.sectionCarousel = {
    attach: function (context, settings) {
      carouselController();
      $(window).resize(function () {
        carouselController();
      })
    }
  };

})(jQuery, Drupal, drupalSettings);
