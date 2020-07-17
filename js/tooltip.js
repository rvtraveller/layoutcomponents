/**
 * @file
 * Layout Components behaviors.
 */

(function ($, Drupal) {

  'use strict';

  var ajax = Drupal.ajax,
      behaviors = Drupal.behaviors;

  behaviors.lcEditTooltip = {
    attach: function (context) {
      $(".lc_editor-link, .lc-lateral-info").tooltip({
        tooltipClass:"lc-tooltip"
      });
    }
  }

})(jQuery, Drupal);
