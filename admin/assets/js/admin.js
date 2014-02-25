(function($) {
  "use strict";

  $(function() {

    var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;

    $('.uploader .button').click(function(e) {
      var send_attachment_bkp = wp.media.editor.send.attachment;
      _custom_media = true;
      wp.media.editor.send.attachment = function(props, attachment) {
        $("#ffos-icon").val(attachment.url);
      };

      wp.media.editor.open($(this));
      return false;
    });

    $('.add_media').on('click', function() {
      _custom_media = false;
    });
    
    jQuery('#new_language').click(function(e) {
      jQuery('#new_language').after('<br/>' + jQuery('#new_language').data('language') +':<br/><input type="text" name="firefox-os-bookmark[locales][' + jQuery('#new_language').data('number') +'][language]" value="" /><br/>' + jQuery('#new_language').data('name') +':<br/><input type="text" name="firefox-os-bookmark[locales][' + jQuery('#new_language').data('number') +'][name]" value="" /><br/>' + jQuery('#new_language').data('description') +':<br/><textarea name="firefox-os-bookmark[locales][' + jQuery('#new_language').data('number') +'][description]"></textarea>');
      e.preventDefault();
    });
  
  });
  
}(jQuery));