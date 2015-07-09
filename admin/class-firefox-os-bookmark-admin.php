<?php

/**
 * Firefox OS Bookmark
 *
 *
 * @package   Firefox_OS_Bookmark_Admin
 * @author    Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.mte90.net
 * @copyright 2014 Mte90
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package Firefox_OS_Bookmark_Admin
 * @author  Mte90 <mte90net@gmail.com>
 */
class Firefox_OS_Bookmark_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Firefox_OS_Bookmark::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add the settings field
		add_action( 'admin_init', array( $this, 'ffos_bookmark_settings' ) );
		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		//Add the javascript for the settings page
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since 1.0.0
	 *
	 * @return null Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			//Loading the media for the media picker
			wp_enqueue_media();
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Firefox_OS_Bookmark::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
				__( 'Firefox OS Bookmark', $this->plugin_slug ), __( 'Firefox OS Bookmark', $this->plugin_slug ), 'manage_options', $this->plugin_slug, array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
				array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
				), $links
		);
	}

	/**
	 * Intiliaze all the field for the setting page.
	 *
	 * @since    1.0.0
	 */
	function ffos_bookmark_settings() {

		add_settings_section(
				'ffos_bookmark_settings_section', __( 'Plugin Settings', $this->plugin_slug ), '__return_false', $this->plugin_slug
		);
		add_settings_field(
				$this->plugin_slug . '_alert_ffos', __( 'Show info box on Firefox OS for ask to install the app (30 days for show again the box)', $this->plugin_slug ), array( $this, 'field_alert_ffos' ), $this->plugin_slug, 'ffos_bookmark_settings_section'
		);
		add_settings_field(
				$this->plugin_slug . '_alert_fffa', __( 'Show info box on Firefox for Android for ask to install the app (30 days for show again the box)', $this->plugin_slug ), array( $this, 'field_alert_fffa' ), $this->plugin_slug, 'ffos_bookmark_settings_section'
		);
		add_settings_field(
				$this->plugin_slug . '_alert_ff', __( 'Show info box on Firefox Desktop for ask to install the app (30 days for show again the box)', $this->plugin_slug ), array( $this, 'field_alert_ff' ), $this->plugin_slug, 'ffos_bookmark_settings_section'
		);
		add_settings_field(
				$this->plugin_slug . '_modal_content', __( 'Text of the installation window', $this->plugin_slug ), array( $this, 'field_modal_content' ), $this->plugin_slug, 'ffos_bookmark_settings_section'
		);

		add_settings_section(
				'ffos_bookmark_settings_manifest_section', __( 'Manifest Settings', $this->plugin_slug ), '__return_false', $this->plugin_slug
		);
		add_settings_field(
				$this->plugin_slug . '_name', __( 'Name', $this->plugin_slug ), array( $this, 'field_name' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_field(
				$this->plugin_slug . '_description', __( 'Description', $this->plugin_slug ), array( $this, 'field_description' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_field(
				$this->plugin_slug . '_developer_name', __( 'Developer Name', $this->plugin_slug ), array( $this, 'field_developer_name' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_field(
				$this->plugin_slug . '_developer_url', __( 'Site', $this->plugin_slug ), array( $this, 'field_developer_url' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_field(
				$this->plugin_slug . '_default_locale', __( 'Default Locale', $this->plugin_slug ), array( $this, 'field_default_locale' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_field(
				$this->plugin_slug . '_version', __( 'Version', $this->plugin_slug ), array( $this, 'field_version' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_field(
				$this->plugin_slug . '_navigation', __( 'Navigation Bar', $this->plugin_slug ), array( $this, 'field_navigation' ), $this->plugin_slug, 'ffos_bookmark_settings_manifest_section'
		);
		add_settings_section(
				'ffos_bookmark_settings_icons_section', __( 'Icons Settings', $this->plugin_slug ), '__return_false', $this->plugin_slug
		);
		add_settings_field(
				$this->plugin_slug . '_icon', __( 'Icon', $this->plugin_slug ), array( $this, 'field_icon' ), $this->plugin_slug, 'ffos_bookmark_settings_icons_section'
		);

		add_settings_section(
				'ffos_bookmark_settings_locales_section', __( 'Locales Settings', $this->plugin_slug ), '__return_false', $this->plugin_slug
		);
		add_settings_field(
				$this->plugin_slug . '_locales', __( 'App data for language', $this->plugin_slug ), array( $this, 'field_locales' ), $this->plugin_slug, 'ffos_bookmark_settings_locales_section'
		);

		register_setting( $this->plugin_slug, $this->plugin_slug );
	}

	/**
	 * Firefox OS Alert
	 *
	 * @since    1.0.0
	 */
	function field_alert_ffos() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'alert' ][ 'ffos' ] ) ) {
			$setting[ 'alert' ][ 'ffos' ] = false;
		}

		echo '<input type="checkbox" name="' . $this->plugin_slug . '[alert][ffos]" ' . checked( $setting[ 'alert' ][ 'ffos' ], 'on', false ) . ' />';
	}

	/**
	 * Firefox alert
	 *
	 * @since    1.0.0
	 */
	function field_alert_ff() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'alert' ][ 'ff' ] ) ) {
			$setting[ 'alert' ][ 'ff' ] = false;
		}

		echo '<input type="checkbox" name="' . $this->plugin_slug . '[alert][ff]" ' . checked( $setting[ 'alert' ][ 'ff' ], 'on', false ) . ' />';
	}

	/**
	 * Firefox for Android alert
	 *
	 * @since    1.0.0
	 */
	function field_alert_fffa() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'alert' ][ 'fffa' ] ) ) {
			$setting[ 'alert' ][ 'fffa' ] = false;
		}

		echo '<input type="checkbox" name="' . $this->plugin_slug . '[alert][fffa]" ' . checked( $setting[ 'alert' ][ 'fffa' ], 'on', false ) . ' />';
	}

	/**
	 * Admin Name
	 *
	 * @since    1.0.0
	 */
	function field_name() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'name' ] ) ) {
			$setting[ 'name' ] = get_bloginfo( 'name' );
		}

		echo '<input type="text" name="' . $this->plugin_slug . '[name]" value="' . esc_attr( $setting[ 'name' ] ) . '" />';
	}

	/**
	 * Site description
	 *
	 * @since    1.0.0
	 */
	function field_description() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'description' ] ) ) {
			$setting[ 'description' ] = get_bloginfo( 'description' );
		}

		echo '<textarea name="' . $this->plugin_slug . '[description]">' . esc_attr( $setting[ 'description' ] ) . '</textarea>';
	}

	/**
	 * Developer Name
	 *
	 * @since    1.0.0
	 */
	function field_developer_name() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'developer' ][ 'name' ] ) ) {
			$setting[ 'developer' ][ 'name' ] = '';
		}

		echo '<input type="text" size="60" name="' . $this->plugin_slug . '[developer][name]" value="' . esc_attr( $setting[ 'developer' ][ 'name' ] ) . '" />';
	}

	/**
	 * Developer Url
	 *
	 * @since    1.0.0
	 */
	function field_developer_url() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'developer' ][ '_url' ] ) ) {
			$setting[ 'developer' ][ 'url' ] = get_bloginfo( 'url' );
		}

		echo '<input type="text" size="60" name="' . $this->plugin_slug . '[developer][url]" value="' . esc_attr( $setting[ 'developer' ][ 'url' ] ) . '" />';
	}

	/**
	 * Locale default
	 *
	 * @since    1.0.0
	 */
	function field_default_locale() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'default_locale' ] ) ) {
			$setting[ 'default_locale' ] = '';
		}

		echo '<input type="text" name="' . $this->plugin_slug . '[default_locale]" value="' . esc_attr( $setting[ 'default_locale' ] ) . '" />';
	}

	/**
	 * Version
	 *
	 * @since    1.0.0
	 */
	function field_version() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'version' ] ) ) {
			$setting[ 'version' ] = '1.0';
		}

		echo '<input type="text" name="' . $this->plugin_slug . '[version]" value="' . esc_attr( $setting[ 'version' ] ) . '" /> ' . __( 'Change if you update this page', $this->plugin_slug );
	}
	/**
	 * Navigation
	 *
	 * @since    1.X
	 */
	function field_navigation() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'chrome' ] ) ) {
			$setting['chrome'] = true;
		}

		echo '<input type="checkbox" name="' . $this->plugin_slug . '[chrome]" ' . checked( $setting[ 'chrome' ], 'on', false ) . ' />';
	}
	/**
	 * Icon
	 *
	 * @since    1.0.0
	 */
	function field_icon() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'icon' ] ) ) {
			$setting[ 'icon' ] = '';
		}

		echo '<div class="uploader">
				<input type="text" name="' . $this->plugin_slug . '[icon]" value="' . esc_attr( $setting[ 'icon' ] ) . '" id="ffos-icon" />
				<input type="button" class="button" name="_unique_name_button" id="_unique_name_button" value="' . __( 'Upload', $this->plugin_slug ) . '" />
			  </div>';
	}

	/**
	 * Locales
	 *
	 * @since    1.0.0
	 */
	function field_locales() {
		$setting = ( array ) get_option( $this->plugin_slug );
		$i = 0;
		if ( isset( $setting[ 'locales' ] ) ) {
			$locales = $setting[ 'locales' ];
			foreach ( $locales as &$locale ) {
				if ( isset( $locale[ 'name' ] ) && !empty( $locale[ 'name' ] ) ) {
					$i++;
					echo '<br>' . __( 'Language', $this->plugin_slug ) . ':<br><input type="text" name="' . $this->plugin_slug . '[locales][' . $i . '][language]" value="' . $locale[ 'language' ] . '" /><br>';
					echo __( 'Name', $this->plugin_slug ) . ':<br><input type="text" name="' . $this->plugin_slug . '[locales][' . $i . '][name]" value="' . $locale[ 'name' ] . '" /><br>';
					echo __( 'Description', $this->plugin_slug ) . ':<br><textarea name="' . $this->plugin_slug . '[locales][' . $i . '][description]">' . $locale[ 'description' ] . '</textarea>';
				}
			}
		}
		echo '<br><a href="#" id="new_language" data-name="' . __( 'Name', $this->plugin_slug ) . '" data-language="' . __( 'Language', $this->plugin_slug ) . '" data-description="' . __( 'Description', $this->plugin_slug ) . '" data-number="' . ($i + 1) . '">' . __( 'Add new language', $this->plugin_slug ) . '</a><br>';
	}
	
	/**
	 * Modal Content
	 *
	 * @since    1.1.0
	 */
	function field_modal_content() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'modal_content' ] ) ) {
			$setting[ 'modal_content' ] = __( 'Do you want to install this site as an application on your system with Firefox/Firefox for Android/Firefox OS?', $this->plugin_slug );
		}

		echo '<textarea name="' . $this->plugin_slug . '[modal_content]">' . esc_attr( $setting[ 'modal_content' ] ) . '</textarea>';
	}

}
