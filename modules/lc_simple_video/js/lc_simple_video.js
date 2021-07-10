(function ($, Drupal) {
  Drupal.behaviors.VideoVeil = {
    attach: function (context, settings) {
      $('.block-inline-blocksimple-video', context).once('VideoVeil').each(function () {
        var vid = $(this).find('video').get(0);
        var image = $(this).find('.field--name-field-sv-image').get(0);
        var veil = $(this).find('.lc-video-bg').get(0);
        $(veil).on('click', function () {
          $(image).addClass('hidden');
          // Remove overlay.
          $(this).addClass("no-bg");
          // Check if is a HTML video or an iframe.
          if (typeof vid === "undefined") {
            // If is iframe.
            vid = $(this).find('iframe');
            var src = vid.prop('src');
            // Remove the parameters.
            src = src.replace('autoplay=0&start=0&rel=0', '');
            // Add autoplay.
            src += '&autoplay=1&enablejsapi=1'
            vid.prop('src', '');
            // From chrome 83 is necessary apply this attribute.
            vid.attr('allow', 'autoplay');
            // Add new parameters.
            vid.prop('src', src);
          } else {
            // If is a HTML video.
            vid.play();
          }
        });
      });
    }
  };

})(jQuery, Drupal);
