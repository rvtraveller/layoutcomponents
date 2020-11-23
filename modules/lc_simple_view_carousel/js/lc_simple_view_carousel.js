/**
 * @file
 * Provides Simple Home Caoursel integration.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Attaches slick home carousel to change the dot values.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.lcHomeCarrousel = {
    attach: function (context) {
      $(document).once('init').ready(function () {
        var $slick = $('div[data-quickedit-field-id^="' + drupalSettings.homeCarousel.id + '"]').find('.slick__slider');
        var $dots = $slick.find('ul').addClass('slick-dots-tabs').find('li[role="presentation"]');
        $dots.each(function (i) {
          var $text = '<div class="slick-dot-number">(' + i + ') </div>';
          $text += '<div class="slick-dot-text">' + drupalSettings.homeCarousel.items[i].title + '</div>';
          $($dots[i]).find('button').html($text);
        });

        var $isPaused = ($slick.hasClass('is-paused')) ? Drupal.t('Play') : Drupal.t('Pause');

        var $li = $('<li class="slick-dot-control">' + $isPaused + '</li>').on('click', function () {

          // Movement control.
          if ($slick.hasClass('is-paused')) {
            $(this).text(Drupal.t('Pause'));
            $slick.removeClass('is-paused').slick('slickPlay');
          } else {
            $(this).text(Drupal.t('Play'));
            $slick.addClass('is-paused').slick('slickPause');
          }
        })

        // Inlude Play/Pause button.
        $slick.find('ul').once('added').append($li);
      })
    }
  };

})(jQuery, Drupal, drupalSettings);
