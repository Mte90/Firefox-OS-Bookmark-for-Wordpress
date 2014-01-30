(function ( $ ) {
	"use strict";

	$(function () {

		var _custom_media = true,
				_orig_send_attachment = wp.media.editor.send.attachment;

		$('.uploader .button').click(function(e) {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment) {
				if (_custom_media) {
					$("#ffos-icon").val(attachment.url);
				} else {
					return _orig_send_attachment.apply(this, [props, attachment]);
				}
				;
			}

			wp.media.editor.open($(this));
			return false;
		});

		$('.add_media').on('click', function() {
			_custom_media = false;
		});

	});

}(jQuery));