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

  });

}(jQuery));