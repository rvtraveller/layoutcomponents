(function ($, Drupal) {
  Drupal.behaviors.VideoVeil = {
    attach: function (context, settings) {

      var stopAllYouTubeVideos = () => {
        var iframes = document.querySelectorAll('iframe');
        Array.prototype.forEach.call(iframes, iframe => {
          iframe.contentWindow.postMessage(JSON.stringify({ event: 'command',
            func: 'stopVideo' }), '*');
        });
      }
      stopAllYouTubeVideos();
      // Fix selector to get one element
      $('.block-inline-blocksimple-video .videoimage', context).once('VideoVeil').each(function () {
        var vid = $(this).find('video').get(0);
        var image = $(this).find('.field--name-field-sv-image').get(0);
        var video = $(this).find('.field--name-field-sv-video').get(0);
        // If have image to show hide the video
        if(typeof image != "undefined") {
          $(this).find('.embed-responsive-item').hide();
          $(video).addClass('absolute');
          // If have image show the veil even is not mark as the veil property
          $(this).find('.embed-responsive').addClass("lc-video-bg");
        } else {
          // If there is not image set video at correct  way
          $(video).addClass('relative');
        }
        // Set the height the image like video to show the veil correct height
        if($(video).height() > $(this).height()) {
          $(image).find('img').height($(video).height());
        }

        var veil = $(this).find('.lc-video-bg').get(0);
        $(veil).on('click', function () {
          // Remove absolute class
          $(video).removeClass('absolute');
          // Stop all playing videos
          $('iframe.lc_playing', context).each(function () {
            $(this)[0].contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
          });
          $('video.lc_playing', context).each(function () {
            $(this).get(0).pause();
          });
          // Show video wrapper.
          $(veil).find('.embed-responsive-item').show();
          //$(veil).find('video').show();
          $(veil).parent().addClass('relative')

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
          // Add playing class to video
          $(vid).addClass("lc_playing");
        });
      });
    }
  };

})(jQuery, Drupal);
