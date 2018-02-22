import 'tinymce/tinymce';
import 'tinymce/themes/modern/theme';

var charsLeft = document.getElementById('charsLeft');

window.tinymce.init({
  selector: 'textarea',
  extended_valid_elements: 'strong',
  skin: false,
  theme: 'modern',
  content_style: 'body {font-family: Arial; font-size: 10pt;}',
  height: 175,
  menubar: false,
  toolbar: [
    'bold',
  ],
  branding: false,
  image_advtab: true,
  setup: function (editor) {
    editor.on('change', function () {
      editor.save();
    });

    editor.on('keypress keyup change', function () {
      var body = editor.getBody(),
        content = tinymce.trim(body.innerText || body.textContent),
        length = content.length,
        rest = 250 - length;

      if (rest > 0) {
        charsLeft.innerHTML = ' - '+rest+' restants.';
      } else {
        charsLeft.innerHTML = ' - <br /><strong>Vous dépassez le nombre maximum autorisé, '+(-1 * rest)+' de trop.</strong>';
      }

      editor.save();
    });
  }
});
