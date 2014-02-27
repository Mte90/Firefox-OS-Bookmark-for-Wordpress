<?php
/**
 * Plugin Name.
 *
 * @package   Firefox_OS_Bookmark
 * @author    Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Mte90
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-firefox-os-bookmark-admin.php`
 *
 * @package Firefox_OS_Bookmark
 * @author  Your Name <email@example.com>
 */
class Firefox_OS_Bookmark {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

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
		if ( isset( $check[ 'alert' ][ 'ff' ] ) or isset( $check[ 'alert' ][ 'ffos' ] ) ) {
			add_action( 'wp_head', array( $this, 'inline_script' ) );
		}
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

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

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

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

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
		//Some times this rules not work but the htaccess rule in the readme fix the problem
		$plugin_url = plugins_url() . '/firefox-os-bookmark/manifest.php';
		add_rewrite_rule( 'manifest\.webapp$', $plugin_url, 'top' );
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

	function inline_script() {
		//Javascript code for install the manifest
		//Check with cookie if the alert was showed for not annoying the user
		?>
		<script type="text/javascript">
			function load_manifest() {
				if (document.cookie.replace(/(?:(?:^|.*;\s*)appTime\s*\=\s*([^;]*).*$)|^.*$/, "$1") !== "false") {
					var checkIfInstalled = navigator.mozApps.getSelf();
					checkIfInstalled.onsuccess = function() {
						if (!checkIfInstalled.result) {
							var now = new Date;
							m_app = navigator.mozApps.install('<?php echo get_bloginfo( 'url' ) ?>/manifest.webapp');
							m_app.onsuccess(function(data) {
								now.setDate(now.getDate() + 365);
								document.cookie = 'appTime=false; expires=' + now.toGMTString();
							});
							m_app.onerror(function() {
								now.setDate(now.getDate() + 30);
								console.log("Install failed\n\n:" + installApp.error.name);
								document.cookie = 'appTime=false; expires=' + now.toGMTString();
							});
						}
					};
				}
			}
		<?php
		$check = get_option( 'firefox-os-bookmark' );
		//Show the alert only on firefox/firefox for Android
		if ( isset( $check[ 'alert' ][ 'ff' ] ) ) {
			?>
				if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
					load_manifest();
			<?php
			//Show the alert only on FirefoxOS
			//TODO: better check
			if ( isset( $check[ 'alert' ][ 'ffos' ] ) ) {
				?>
						try {
							new MozActivity({});
						} catch (e) {
							load_manifest();
						}
			<?php } ?>
				}
		<?php } ?>
		</script>
		<?php
	}

}
