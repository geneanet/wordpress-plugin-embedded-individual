<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link http://www.geneanet.org
 * @since 1.0.0
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package Geneanet_Emebedded_Individual
 * @subpackage Geneanet_Emebedded_Individual/public
 * @author Alan Poulain <alan.poulain@geneanet.org>
 */
class Geneanet_Embedded_Individual_Public {

	/**
	 * Contain the shortcodes attributes.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array $shortcodes_atts The shortcodes attributes.
	 */
	protected static $shortcodes_atts = array();

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The URL used to communicate with the Geneanet API.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string $api_url The URL used to communicate with the Geneanet API.
	 */
	private $api_url;

	/**
	 * The admin-specific functionality of the plugin.
	 * Used only for the visual editor.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Geneanet_Embedded_Individual_Admin $plugin_admin The admin-specific functionality of the plugin.
	 */
	private $plugin_admin;

	/**
	 * Initializes the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $api_url     The URL used to communicate with the Geneanet API.
	 */
	public function __construct( $plugin_name, $version, $api_url ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->api_url = $api_url;

	}

	/**
	 * Registers the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/geneanet-embedded-individual-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Registers the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/geneanet-embedded-individual-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add translated strings to a previously enqueued script for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function localize_script() {

		wp_localize_script( $this->plugin_name, 'objectL10n', array(
			'api_url' => $this->get_api_url(),
			'lang' => $this->get_lang(),
			'error_ajax_request' => esc_html__( 'An error has occurred while sending a request to Geneanet', $this->get_plugin_name() ),
			'error_geneanet_api' => esc_html__( 'Geneanet returned an error: ', $this->get_plugin_name() ),
			'error_individual_not_found' => esc_html__( 'Individual has not been found (it may be private in the family tree)', $this->get_plugin_name() ),
			'families_married_male' => esc_html_x( 'Married', 'male', $this->get_plugin_name() ),
			'families_married_female' => esc_html_x( 'Married', 'female', $this->get_plugin_name() ),
			'families_married_unknown' => esc_html_x( 'Married', 'unknown', $this->get_plugin_name() ),
			'families_relation' => esc_html__( 'Relation', $this->get_plugin_name() ),
			'families_engaged_male' => esc_html_x( 'Engaged', 'male', $this->get_plugin_name() ),
			'families_engaged_female' => esc_html_x( 'Engaged', 'female', $this->get_plugin_name() ),
			'families_engaged_unknown' => esc_html_x( 'Engaged', 'unknown', $this->get_plugin_name() ),
			'families_with' => esc_html_x( 'with', 'family', $this->get_plugin_name() ),
			'families_with_one_child' => esc_html__( 'with one child', $this->get_plugin_name() ),
			'families_with_children' => esc_html__( 'with %d children', $this->get_plugin_name() ),
		) );

	}

	/**
	 * Shortcode for an embedded individual main information.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts The shortcode attributes.
	 * @return string
	 */
	public function embed_individual_shortcode( $atts ) {

		$embed_individual_atts = shortcode_atts( array(
			'url' => 'url',
			'link_type' => 'fiche',
			'align' => 'left',
		), $atts );

		// The URL attribute should never be the default one.
		if ( 'url' === $embed_individual_atts['url'] ) {
			return '';
		}

		// Create an ID based on the URL attribute and the type of the link.
		$embed_individual_id = md5( $embed_individual_atts['url'] . $embed_individual_atts['link_type'] . $embed_individual_atts['align'] );

		// Store the attributes in the instance attribute to retrieve them later.
		self::$shortcodes_atts[ $embed_individual_id ] = array_merge( $embed_individual_atts, array( 'post_id' => get_the_ID() ) );

		// Retrieve permanent data.
		$permanent_data = get_option( 'geneanet_embedded_individual_permanent_data' );

		$is_visual_editor = 'wp_ajax_bulk_do_shortcode' === current_filter();

		// In the case of the visual editor, retrieve permanent data from the admin area if the data have not been retrieved yet.
		if ( $is_visual_editor && ! isset( $permanent_data[ $embed_individual_id ] ) ) {
			$this->plugin_admin->retrieve_permanent_data( get_the_ID(), get_post() );

			$permanent_data = get_option( 'geneanet_embedded_individual_permanent_data' );
		}

		// If there was an issue when retrieving permanent data, do not display anything.
		if ( ! isset( $permanent_data[ $embed_individual_id ] ) ) {
			if ( $is_visual_editor ) {
				$error = __( 'An error occurred. Please try another URL or save the post for more information.', $this->plugin_name );

				ob_start();

				require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/geneanet-embedded-individual-public-display-error.php';

				$output = ob_get_clean();

				return $output;
			}

			return '';
		}

		// Create the variables used for the template.
		$plugin_name = $this->get_plugin_name();
		$url = $permanent_data[ $embed_individual_id ]['url'];
		$link_type = $embed_individual_atts['link_type'];
		$align = $embed_individual_atts['align'];
		$basename = $permanent_data[ $embed_individual_id ]['basename'];
		$index = $permanent_data[ $embed_individual_id ]['index'];
		$sex = $permanent_data[ $embed_individual_id ]['sex'];
		$firstname = $permanent_data[ $embed_individual_id ]['firstname'];
		$lastname = $permanent_data[ $embed_individual_id ]['lastname'];

		ob_start();

		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/geneanet-embedded-individual-public-display.php';

		$output = ob_get_clean();

		return $output;

	}

	/**
	 * Get the shortcodes attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_shortcodes_atts() {
		return self::$shortcodes_atts;
	}

	/**
	 * Get the lang (used by Geneanet).
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_lang() {
		return explode( '_', get_locale() )[0];
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
	 * Retrieves the URL used to communicate with the Geneanet API.
	 *
	 * @since 1.0.0
	 *
	 * @return string The URL used to communicate with the Geneanet API.
	 */
	public function get_api_url() {
		return $this->api_url;
	}

	/**
	 * Sets the admin-specific functionality of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param Geneanet_Embedded_Individual_Admin $plugin_admin The admin-specific functionality of the plugin.
	 */
	public function set_plugin_admin( Geneanet_Embedded_Individual_Admin $plugin_admin ) {
		$this->plugin_admin = $plugin_admin;
	}
}
