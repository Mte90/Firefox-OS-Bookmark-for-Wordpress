<?php

/**
 * Firefox OS Bookmark
 *
 *
 * @package   Firefox_OS_Bookmark_Admin
 * @author    Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Mte90
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-firefox-os-bookmark.php`
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
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Firefox_OS_Bookmark::VERSION );
			wp_enqueue_media();
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

	function ffos_bookmark_settings() {

		add_settings_section(
				'ffos_bookmark_settings_manifest_section', __( 'Manifest Settings', $this->plugin_slug ), function () {
			
		}, $this->plugin_slug
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

		add_settings_section(
				'ffos_bookmark_settings_icons_section', __( 'Icons Settings', $this->plugin_slug ), function () {
			
		}, $this->plugin_slug
		);
		add_settings_field(
				$this->plugin_slug . '_icon', __( 'Icon', $this->plugin_slug ), array( $this, 'field_icon' ), $this->plugin_slug, 'ffos_bookmark_settings_icons_section'
		);

		add_settings_section(
				'ffos_bookmark_settings_locales_section', __( 'Locales Settings', $this->plugin_slug ), function () {
			
		}, $this->plugin_slug
		);
		add_settings_field(
				$this->plugin_slug . '_locales', __( 'Locales', $this->plugin_slug ), array( $this, 'field_locales' ), $this->plugin_slug, 'ffos_bookmark_settings_locales_section'
		);

		register_setting( $this->plugin_slug, $this->plugin_slug );
	}

	function field_name() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'name' ] ) ) {
			$setting[ 'name' ] = get_bloginfo( 'name' );
		}

		echo '<input type="text" name="' . $this->plugin_slug . '[name]" value="' . esc_attr( $setting[ 'name' ] ) . '" />';
	}

	function field_description() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'description' ] ) ) {
			$setting[ 'description' ] = get_bloginfo( 'description' );
		}

		echo '<textarea name="' . $this->plugin_slug . '[description]">' . esc_attr( $setting[ 'description' ] ) . '</textarea>';
	}

	function field_developer_name() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'developer' ][ 'name' ] ) ) {
			$setting[ 'developer' ][ 'name' ] = '';
		}

		echo '<input type="text" size="60" name="' . $this->plugin_slug . '[developer][name]" value="' . esc_attr( $setting[ 'developer' ][ 'name' ] ) . '" />';
	}

	function field_developer_url() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'developer' ][ '_url' ] ) ) {
			$setting[ 'developer' ][ 'url' ] = get_bloginfo( 'url' );
		}

		echo '<input type="text" size="60" name="' . $this->plugin_slug . '[developer][url]" value="' . esc_attr( $setting[ 'developer' ][ 'url' ] ) . '" />';
	}

	function field_default_locale() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'default_locale' ] ) ) {
			$setting[ 'default_locale' ] = '';
		}

		echo '<input type="text" name="' . $this->plugin_slug . '[default_locale]" value="' . esc_attr( $setting[ 'default_locale' ] ) . '" />';
	}

	function field_version() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'version' ] ) ) {
			$setting[ 'version' ] = '1.0';
		}

		echo '<input type="text" name="' . $this->plugin_slug . '[version]" value="' . esc_attr( $setting[ 'version' ] ) . '" /> ' . __( 'Change if you update this page', $this->plugin_slug );
	}

	function field_icon() {
		$setting = ( array ) get_option( $this->plugin_slug );

		if ( !isset( $setting[ 'icon' ] ) ) {
			$setting[ 'icon' ] = '';
		}

		echo '<div class="uploader">
				<input type="text" name="' . $this->plugin_slug . '[icon]" value="' . esc_attr( $setting[ 'icon' ] ) . '" id="ffos-icon" />
				<input type="button" class="button" name="_unique_name_button" id="_unique_name_button" value="' . __( 'Upload', $this->plugin_slug ) . '" />
			  </div>
			  ';
	}

	function field_locales() {
		$setting = ( array ) get_option( $this->plugin_slug );
		$i = 0;
		if ( isset( $setting[ 'locales' ] ) ) {
			$locales = $setting[ 'locales' ];
			foreach ( $locales as &$locale ) {
				if ( isset($locale[ 'name' ]) && !empty($locale[ 'name' ]) ) {
					$i++;
					echo '<br>' . __( 'Language', $this->plugin_slug ) . ':<br><input type="text" name="' . $this->plugin_slug . '[locales][' . $i . '][name]" value="' . $locale[ 'name' ] . '" /><br>';
					echo __( 'Description', $this->plugin_slug ) . ':<br><textarea name="' . $this->plugin_slug . '[locales][' . $i . '][description]">' . $locale[ 'description' ] . '</textarea>';
				}
			}
		}
		echo '<br><a href="#" id="new_language" data-language="' . __( 'Language', $this->plugin_slug ) . '" data-description="' . __( 'Description', $this->plugin_slug ) . '" data-number="' . ($i + 1) . '">' . __( 'Add new language', $this->plugin_slug ) . '</a><br>';
	}

}
