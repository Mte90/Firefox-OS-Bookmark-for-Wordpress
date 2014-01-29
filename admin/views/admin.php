<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Firefox_OS_Bookmark
 * @author    Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Mte90
 */
?>
<script>
	jQuery(document).ready(function($) {
		var _custom_media = true,
				_orig_send_attachment = wp.media.editor.send.attachment;

		$('.uploader .button').click(function(e) {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment) {
				if (_custom_media) {
					$(".uploader [type='text']").val(attachment.url);
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
</script>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form action="options.php" method="post">
		Manifest path: <?php echo get_bloginfo( 'url' ) ?>/manifest.webapp
		<?php
		settings_fields( $this->plugin_slug );
		do_settings_sections( $this->plugin_slug );
		?>

		<?php submit_button(); ?>
	</form>

</div>
