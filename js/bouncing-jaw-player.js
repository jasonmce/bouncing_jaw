(function ($, Drupal, window, document) {

  Drupal.behaviors.bouncingJawPlayer = {
    attach: function (context, settings) {

      let trackIndex = 0;
      let iTimeoutID = 0;
      const player = $('.bouncing-jaw-player');

      function bStartJaw() {
        // Catch runaway timers
        if (player.paused) {
          return bStopJaw;
        }
        $('.jaw-image').toggle();
        iTimeoutID = setTimeout(bStartJaw, ((Math.random() * 3) + 2) * 100);
      }

      function bStopJaw() {
        clearTimeout(iTimeoutID);
        $('.jaw-image').hide();
      }

      $(".bouncing-jaw-player").on("ended", function () {
        bStopJaw();
      });

      // Set the jaw-image top black border dynamically from the image size
      const backgroundWidth = $('.bouncing-jaw .background-image').width();
      $(".jaw-image").css('border-width', ((backgroundWidth * drupalSettings.bouncingJaw.jawMovement) / 100) + 'px 0 0');

      $(".play-me").on("click", function () {
        $(".bouncing-jaw-player").attr({
          "src": drupalSettings.bouncingJaw.tracks[trackIndex],
          "autoplay": "autoplay",
          "onplay": bStartJaw(),
        });
        trackIndex = (trackIndex + 1) % drupalSettings.bouncingJaw.tracks.length;
      });
    }
  }
}(jQuery, Drupal, this, this.document));
