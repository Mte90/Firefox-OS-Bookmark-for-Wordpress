<?php

/**
 * Firefox OS Bookmark
 *
 * @package   Firefox_OS_Bookmark
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
 * public-facing side of the WordPress site.
 *
 * @package Firefox_OS_Bookmark
 * @author  Mte90 <mte90net@gmail.com>
 */
class Firefox_OS_Bookmark {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.1.3';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'firefox-os-bookmark';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing JavaScript after check the settings
		$check = get_option( 'firefox-os-bookmark' );
		if ( isset( $check[ 'alert' ][ 'ff' ] ) or isset( $check[ 'alert' ][ 'fffa' ] ) or isset( $check[ 'alert' ][ 'ffos' ] ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
		//Add fake page for the manifest
		add_filter( 'the_posts', array( $this, 'fakepage_manifest' ), -10 );
		add_action( 'template_redirect', array( $this, 'fakepage_manifest_render' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {

			// Get all blog ids
			$blog_ids = self::get_blog_ids();

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::single_activate();
			}

			restore_current_blog();
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {

			// Get all blog ids
			$blog_ids = self::get_blog_ids();

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::single_deactivate();
			}

			restore_current_blog();
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		//Insert the redirect
		flush_rewrite_rules();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_PLUGIN_DIR . '/' . $domain . '/languages/' . $domain . '-' . $locale . '.mo' );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$check = get_option( 'firefox-os-bookmark' );
		$ffos_bookmark = array();
		/* Show the alert only on FirefoxOS */
		if ( isset( $check[ 'alert' ][ 'ffos' ] ) ) {
			$ffos_bookmark[ 'ffos' ] = true;
		}
		/* Show the alert only on Firefox for Android */
		if ( isset( $check[ 'alert' ][ 'fffa' ] ) ) {
			$ffos_bookmark[ 'fffa' ] = true;
		}
		/* Show the alert only on Firefox */
		if ( isset( $check[ 'alert' ][ 'ff' ] ) ) {
			$ffos_bookmark[ 'ff' ] = true;
		}
		$ffos_bookmark[ 'host' ] = get_bloginfo( 'url' );
		if ( isset( $check[ 'alert' ][ 'modal_content' ] ) ) {
			$ffos_bookmark[ 'content' ] = $check[ 'alert' ][ 'modal_content' ];
		} else {
			$ffos_bookmark[ 'content' ] = __( 'Do you want to install this site as an application on your system with Firefox/Firefox for Android/Firefox OS?', $this->plugin_slug );
		}
		$ffos_bookmark[ 'close' ] = __( 'Close' );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), null, self::VERSION );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'ffos_bookmark', $ffos_bookmark );
	}

	function fakepage_manifest( $posts ) {
		global $wp;
		global $wp_query;

		global $fakepage_manifest; // used to stop double loading
		$fakepage_manifest_url = "manifest.webapp"; // URL of the fake page
		if ( !$fakepage_manifest && (strtolower( $wp->request ) == $fakepage_manifest_url) ) {
			//Stop interferring with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page)
			$fakepage_manifest = true;
			//Create a fake virtual page
			$post = new stdClass;
			$post->post_author = 1;
			$post->post_name = $fakepage_manifest_url;
			$post->guid = get_bloginfo( 'wpurl' ) . '/' . $fakepage_manifest_url;
			$post->post_title = "manifest.webapp";
			$post->post_content = '';
			$post->ID = -999;
			$post->post_type = 'page';
			$post->post_status = 'static';
			$post->comment_status = 'closed';
			$post->ping_status = 'open';
			$post->comment_count = 0;
			$post->post_date = current_time( 'mysql' );
			$post->post_date_gmt = current_time( 'mysql', 1 );
			$posts = NULL;
			$posts[] = $post;
			//Make wpQuery believe this is a real page too
			$wp_query->is_page = true;
			$wp_query->is_singular = true;
			$wp_query->is_home = false;
			$wp_query->is_archive = false;
			$wp_query->is_category = false;
			unset( $wp_query->query[ "error" ] );
			$wp_query->query_vars[ "error" ] = "";
			$wp_query->is_404 = false;
		}
		return $posts;
	}

	function fakepage_manifest_render() {
		global $wp;

		if ( (strtolower( $wp->request ) == "manifest.webapp" ) ) {
			//Get options
			$manifest = ( array ) get_option( 'firefox-os-bookmark' );

			//Execute the resize
			if ( isset( $manifest[ 'icon' ] ) ) {
				//Local path
				$clean_url = ABSPATH . str_replace( get_bloginfo( 'url' ), '', $manifest[ 'icon' ] );
				//Absolute url for icon
				$url = parse_url( dirname( $manifest[ 'icon' ] ) );
				$img = wp_get_image_editor( $clean_url );
				unset( $manifest[ 'icon' ] );
				$manifest[ 'icons' ] = array();

				//Resize the icon
				if ( !is_wp_error( $img ) ) {

					$sizes_array = array(
						array( 'width' => 16, 'height' => 16, 'crop' => true ),
						array( 'width' => 32, 'height' => 32, 'crop' => true ),
						array( 'width' => 48, 'height' => 48, 'crop' => true ),
						array( 'width' => 60, 'height' => 60, 'crop' => true ),
						array( 'width' => 64, 'height' => 64, 'crop' => true ),
						array( 'width' => 90, 'height' => 90, 'crop' => true ),
						array( 'width' => 120, 'height' => 120, 'crop' => true ),
						array( 'width' => 128, 'height' => 128, 'crop' => true ),
						array( 'width' => 256, 'height' => 256, 'crop' => true ),
					);

					$resize = $img->multi_resize( $sizes_array );

					foreach ( $resize as $row ) {
						$manifest[ 'icons' ][ $row[ 'width' ] ] = $url[ 'path' ] . '/' . $row[ 'file' ];
					}
				}
			}
			unset( $manifest[ 'alert' ] );
			unset( $manifest[ 'modal_content' ] );
			$manifest[ 'installs_allowed_from' ] = "*";
			//Get locales info
			if ( isset( $manifest[ 'locales' ] ) ) {
				$locales = $manifest[ 'locales' ];
				unset( $manifest[ 'locales' ] );
				$locales_clean = array();
				foreach ( $locales as $value ) {
					$locales_clean[ $value[ 'language' ] ] = array( 'name' => $value[ 'name' ], 'description' => $value[ 'description' ] );
				}
				$manifest[ 'locales' ] = $locales_clean;
			}

			//Replace the "
			$manifest[ 'developer' ][ 'name' ] = str_replace( '"', "'", $manifest[ 'developer' ][ 'name' ] );
			$relative_path = parse_url( get_bloginfo( 'url' ) );
			if ( empty( $relative_path[ 'path' ] ) ) {
				$relative_path[ 'path' ] = "/";
			}
			$manifest[ 'launch_path' ] = $relative_path[ 'path' ];

			//Replace now the chrome entry
			if (isset( $manifest[ 'chrome'])) {
				$chrome = $manifest[ 'chrome'];
				unset($manifest[ 'chrome']);
				$manifest[ 'chrome' ] = array('navigation' => true);
			}
			

			//Clean JSON
			$manifest_ready = str_replace( '\\', '', json_encode( $manifest ) );

			//Set the mime type
			header( 'Content-type: application/x-web-app-manifest+json' );
			header( 'HTTP/1.1 200 OK' );
			//Clean and print
			echo str_replace( '"installs_allowed_from":"*"', '"installs_allowed_from":["*"]', $manifest_ready );
			//Kill the execution of the template
			exit();
		}
	}

}
