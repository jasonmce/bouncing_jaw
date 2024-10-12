(function ($, Drupal, window, document) {

  Drupal.behaviors.bouncingJawAdmin = {
    attach: function (context, settings) {

      // Fit the canvas to the image
      const face_image = $('.face-info-wrapper img')[0];
      const face_image_rect = face_image.getBoundingClientRect();
      $('canvas.jaw_rectangle').width(face_image_rect.width).height(face_image_rect.height);

      // Grab context now so it is ready
      const canvas = document.getElementsByClassName("jaw_rectangle")[0];
      const ctx = canvas.getContext("2d");

      // Clear the canvas and redraw the jaw rect from the form values
      redrawJawRect = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.beginPath();
        ctx.rect(
          ($('#edit-face-info-jaw-left').val() * canvas.width) / 100,
          canvas.height * ($('#edit-face-info-jaw-top').val() / 100),
          ($('#edit-face-info-jaw-width').val() * canvas.width) / 100,
          ($('#edit-face-info-jaw-height').val() * canvas.height) / 100,
        );
        ctx.strokeStyle = 'red';
        ctx.stroke();
      };

      // Use keyup to signal a jaw box redraw is needed
      $('#edit-face-info-jaw-left, #edit-face-info-jaw-top, #edit-face-info-jaw-width, #edit-face-info-jaw-height').keyup(() => {
        redrawJawRect();
      });

      // First draw of jaw_rectangle
      redrawJawRect();

      // Draw the jaw into the canvas while the Test button is pressed
      $('.btn-test-jaw')[0].addEventListener('mousedown', e => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        // Black box atop the animated jaw
        ctx.fillStyle = 'black';
        ctx.fillRect(
          (canvas.width * $('#edit-face-info-jaw-left').val()) / 100,
          (canvas.height * $('#edit-face-info-jaw-top').val()) / 100,
          (canvas.width * $('#edit-face-info-jaw-width').val()) / 100,
          (canvas.height * $('#edit-face-info-jaw-move').val()) / 100,
        );
        // Blit the jaw section of the face onto the canvas
        ctx.drawImage(face_image,
          (face_image.width * $('#edit-face-info-jaw-left').val()) / 100,
          (face_image.height * $('#edit-face-info-jaw-top').val()) / 100,
          (face_image.width * $('#edit-face-info-jaw-width').val()) / 100,
          (face_image.height * $('#edit-face-info-jaw-height').val()) / 100,
          (canvas.width * $('#edit-face-info-jaw-left').val()) / 100,
          (canvas.height * (+$('#edit-face-info-jaw-top').val() + +$('#edit-face-info-jaw-move').val())) / 100,
          (canvas.width * $('#edit-face-info-jaw-width').val()) / 100,
          (canvas.height * $('#edit-face-info-jaw-height').val()) / 100
        );
      })

      // Draw the jaw sqaure into the canvas when the Test button is released
      $('.btn-test-jaw')[0].addEventListener('mouseup', e => {
        redrawJawRect();
      })

    }
  }
}(jQuery, Drupal, this, this.document));
