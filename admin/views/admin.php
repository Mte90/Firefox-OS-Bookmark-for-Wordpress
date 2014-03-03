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

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php
	if ( !get_option( 'permalink_structure' ) ) {
		echo '<div class="error"><p>' . __( 'The permalink are required for use this plugin.', $this->plugin_slug ) . '</p></div>';
	}
	?>
	<form action="options.php" method="post">
		<?php _e( 'Manifest path', $this->plugin_slug ) ?>: <?php echo get_bloginfo( 'url' ) ?>/manifest.webapp
		<?php
		settings_fields( $this->plugin_slug );
		do_settings_sections( $this->plugin_slug );
		submit_button(); 
		?>
	</form>

</div>