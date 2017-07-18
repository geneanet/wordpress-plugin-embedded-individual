<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link http://www.geneanet.org
 * @since 1.0.0
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since 1.0.0
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/includes
 * @author Alan Poulain <alan.poulain@geneanet.org>
 */
class Geneanet_Embedded_Individual {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var Geneanet_Embedded_Individual_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The URL used to communicate with the Geneanet API.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string $api_url The URL used to communicate with the Geneanet API.
	 */
	protected $api_url;

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * Sets the plugin name and the plugin version that can be used throughout the plugin.
	 * Loads the dependencies, defines the locale, and sets the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'geneanet-embedded-individual';
		$this->version = '1.1.1';
		$this->api_url = '//api.geneanet.org/geneweb/modelperson';

		$this->load_dependencies();
		$this->set_locale();


		$plugin_admin = new Geneanet_Embedded_Individual_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_api_url() );
		$plugin_public = new Geneanet_Embedded_Individual_Public( $this->get_plugin_name(), $this->get_version(), $this->get_api_url() );
		$plugin_admin->set_plugin_public( $plugin_public );
		$plugin_public->set_plugin_admin( $plugin_admin );

		$this->define_admin_hooks( $plugin_admin );
		$this->define_public_hooks( $plugin_public );

	}

	/**
	 * Loads the required dependencies for this plugin.
	 *
	 * Includes the following files that make up the plugin:
	 *
	 * - Geneanet_Embedded_Individual_Loader. Orchestrates the hooks of the plugin.
	 * - Geneanet_Embedded_Individual_i18n. Defines internationalization functionality.
	 * - Geneanet_Embedded_Individual_Admin. Defines all hooks for the admin area.
	 * - Geneanet_Embedded_Individual_Public. Defines all hooks for the public side of the site.
	 *
	 * Creates an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geneanet-embedded-individual-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-geneanet-embedded-individual-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-geneanet-embedded-individual-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-geneanet-embedded-individual-public.php';

		$this->loader = new Geneanet_Embedded_Individual_Loader();

	}

	/**
	 * Defines the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function set_locale() {

		$plugin_i18n = new Geneanet_Embedded_Individual_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Registers all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param Geneanet_Embedded_Individual_Admin $plugin_admin The admin-specific functionality of the plugin.
	 */
	private function define_admin_hooks( Geneanet_Embedded_Individual_Admin $plugin_admin ) {

		// For the visual editor.
		global $pagenow;
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			$plugin_public = $plugin_admin->get_plugin_public();

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_public, 'localize_script' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'editor_styles' );
		}

		$this->loader->add_action( 'save_post', $plugin_admin, 'retrieve_permanent_data', 10, 2 );
		$this->loader->add_action( 'register_shortcode_ui', $plugin_admin, 'embed_individual_shortcode_ui' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_notices' );

	}

	/**
	 * Registers all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param Geneanet_Embedded_Individual_Public $plugin_public The public-facing functionality of the plugin.
	 */
	private function define_public_hooks( Geneanet_Embedded_Individual_Public $plugin_public ) {

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'localize_script' );

		$this->loader->add_shortcode( 'geneanet-embed-individual', $plugin_public, 'embed_individual_shortcode' );

	}

	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Geneanet_Embedded_Individual_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieves the URL used to communicate with the Geneanet API.
	 *
	 * @since 1.0.0
	 *
	 * @return string The URL used to communicate with the Geneanet API.
	 */
	public function get_api_url() {
		return $this->api_url;
	}
}
