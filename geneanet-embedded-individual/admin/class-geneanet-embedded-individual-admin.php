<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link http://www.geneanet.org
 * @since 1.0.0
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/admin
 * @author Alan Poulain <alan.poulain@geneanet.org>
 */
class Geneanet_Embedded_Individual_Admin {

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
	 * The public-facing functionality of the plugin.
	 * Used only for the visual editor.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var Geneanet_Embedded_Individual_Public $plugin_public The public-facing functionality of the plugin.
	 */
	private $plugin_public;

	/**
	 * Initializes the class and sets its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $api_url     The URL used to communicate with the Geneanet API.
	 */
	public function __construct( $plugin_name, $version, $api_url ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->api_url = $api_url;

	}

	/**
	 * Registers styles for the visual editor.
	 *
	 * @since 1.0.0
	 */
	public function editor_styles() {

		add_editor_style( plugin_dir_url( __FILE__ ) . '../public/css/geneanet-embedded-individual-public.css' );

	}

	/**
	 * Describes shortcode for Shortcake UI.
	 *
	 * @since 1.0.0
	 *
	 * @link https://github.com/fusioneng/Shortcake Shortcake UI
	 */
	public function embed_individual_shortcode_ui() {
		// Shortcake is required.
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		shortcode_ui_register_for_shortcode(
			'geneanet-embed-individual',
			array(
				'label' => esc_html__( 'Geneanet Embedded Individual', $this->get_plugin_name() ),
				'listItemImage' => '<img src="' . plugin_dir_url( __FILE__ ) . 'img/gnt_logo.svg"></img>',
				'attrs' => array(
					array(
						'label' => __( 'URL of the individual', $this->get_plugin_name() ),
						'attr' => 'url',
						'type' => 'url',
						'meta' => array(
							'required' => true,
							'style' => 'width: 600px;',
						),
					),
					array(
						'label' => __( 'Select link', $this->get_plugin_name() ),
						'description' => __( 'Select if visitor will view the individual page or the family tree.', $this->get_plugin_name() ),
						'attr' => 'link_type',
						'type' => 'select',
						'options' => array(
							'fiche' => __( 'Individual', $this->get_plugin_name() ),
							'tree' => __( 'Family Tree', $this->get_plugin_name() ),
						),
						'meta' => array(
							'style' => 'width: 200px;',
						),
					),
					array(
						'label' => __( 'Select alignment', $this->get_plugin_name() ),
						'attr' => 'align',
						'type' => 'select',
						'options' => array(
							'left' => __( 'Left', $this->get_plugin_name() ),
							'center' => __( 'Center', $this->get_plugin_name() ),
							'right' => __( 'Right', $this->get_plugin_name() ),
						),
						'meta' => array(
							'style' => 'width: 200px;',
						),
					),
				),
			)
		);
	}

	/**
	 * Retrieves permanent data (not retrieved in Ajax).
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id ID of the post.
	 * @param WP_Post $post    Post.
	 * @return bool
	 */
	public function retrieve_permanent_data( $post_id, $post ) {

		$is_visual_editor = 'wp_ajax_bulk_do_shortcode' === current_filter();

		// Only if user has clicked on 'Save' or 'Publish'.
		if ( $is_visual_editor || ! ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
			add_option( 'geneanet_embedded_individual_permanent_data', array() );

			$permanent_data = get_option( 'geneanet_embedded_individual_permanent_data' );

			// Execute the shortcodes hooks.
			if ( ! $is_visual_editor ) {
				do_shortcode( $post->post_content );
			}

			$shortcodes_atts = Geneanet_Embedded_Individual_Public::get_shortcodes_atts();

			// Delete the data not used anymore.
			// In case of the visual editor, the data are not deleted because the post will not be necessarily modified.
			foreach ( $permanent_data as $permanent_data_id => $permanent_data_shortcode ) {
				if ( $post_id === $permanent_data_shortcode['post_id'] &&
					! in_array( $permanent_data_id, array_keys( $shortcodes_atts ), true ) &&
					! $is_visual_editor ) {
					unset( $permanent_data[ $permanent_data_id ] );
				}
			}

			// Retrieve the data from the API.
			foreach ( $shortcodes_atts as $embed_individual_id => $shortcode_atts ) {
				$basename_index = $this->retrieve_basename_index_individual( $shortcode_atts['url'] );
				if ( false === $basename_index ) {
					continue;
				}

				$parsed_url_query = $this->parse_url_query( $shortcode_atts['url'] );
				if ( false === $parsed_url_query ) {
					continue;
				}

				$data = $this->get_geneanet_api_individual_data( $this->get_api_url() . '?basename=' . $basename_index[0] . '&i=' . $basename_index[1], $shortcode_atts['url'] );
				if ( false === $data ) {
					return false;
				}
				list( $parsed_url, $parsed_query ) = $parsed_url_query;
				// The type of the link is chosen by the user.
				$parsed_query['type'] = $shortcode_atts['link_type'];
				// Keep only selected parameters.
				$query_parameters_to_keep = array( 'n', 'p', 'oc', 'i', 'type' );
				$parsed_query = array_intersect_key( $parsed_query, array_flip( $query_parameters_to_keep ) );
				$permanent_data[ $embed_individual_id ]['post_id'] = $shortcode_atts['post_id'];
				$permanent_data[ $embed_individual_id ]['url'] = $this->build_geneanet_url( $parsed_url, $parsed_query, $basename_index[0] );
				$permanent_data[ $embed_individual_id ]['basename'] = $basename_index[0];
				$permanent_data[ $embed_individual_id ]['index'] = $basename_index[1];
				$permanent_data[ $embed_individual_id ]['firstname'] = $data['firstname'];
				$permanent_data[ $embed_individual_id ]['lastname'] = $data['lastname'];
				$permanent_data[ $embed_individual_id ]['sex'] = $data['sex'];
			}

			update_option( 'geneanet_embedded_individual_permanent_data', $permanent_data );

			return true;
		}

		return false;

	}

	/**
	 * Retrieves basename and index from an URL.
	 * When index is not present directly in the URL, retrieve it from the API.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $url URL of the individual.
	 * @return array {
	 *     @type string $basename Basename retrieved.
	 *     @type string $index    Index retrieved.
	 * }|false
	 */
	protected function retrieve_basename_index_individual( $url ) {
		$parsed_url_query = $this->parse_url_query( $url );

		if ( false === $parsed_url_query ) {
			return false;
		}

		list( $parsed_url, $parsed_query ) = $parsed_url_query;

		$match_result = preg_match( '@^/([^_][a-z0-9]+)(_[fw])?$@', $parsed_url['path'], $matched_basename );
		if ( false === $match_result || 0 === $match_result ) {
			$this->add_admin_notice_error_parsed_url( $url );
			return false;
		}

		$basename = $matched_basename[1];
		if ( isset( $parsed_query['i'] ) ) {
			$index = $parsed_query['i'];
		} else {
			// Index is not present in the URL.
			// Try to retrieve it from the API using NPOC.
			if ( ! isset( $parsed_query['n'] ) || ! isset( $parsed_query['p'] ) ) {
				$this->add_admin_notice_error_parsed_url( $url );
				return false;
			}
			$n = str_replace( ' ', '+', $parsed_query['n'] );
			$p = str_replace( ' ', '+', $parsed_query['p'] );
			$oc = isset( $parsed_query['oc'] ) ? $parsed_query['oc'] : '0';

			$data = $this->get_geneanet_api_individual_data( $this->get_api_url() . '?basename=' . $basename . '&n=' . $n . '&p=' . $p . '&oc=' . $oc, $url );
			if ( false === $data ) {
				return false;
			}
			$index = $data['index'];
		}

		return array( $basename, $index );
	}

	/**
	 * Parses an URL and its query.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $url The URL to parse.
	 * @return array {
	 *     @type string $parsed_url   The components of the URL.
	 *     @type string $parsed_query The components of the query.
	 * }|false
	 */
	protected function parse_url_query( $url ) {
		// Parse the given URL.
		if ( function_exists( 'wp_parse_url' ) ) {
			$parsed_url = wp_parse_url( $url );
		} else {
			$parsed_url = parse_url( $url );
		}

		if ( false === $parsed_url ) {
			$this->add_admin_notice_error_parsed_url( $url );
			return false;
		}

		if ( ! isset( $parsed_url['path'] ) || ! isset( $parsed_url['query'] ) ) {
			$this->add_admin_notice_error_parsed_url( $url );
			return false;
		}

		parse_str( html_entity_decode( $parsed_url['query'] ), $parsed_query );

		return array( $parsed_url, $parsed_query );
	}

	/**
	 * Builds a Geneanet URL from components.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $parsed_url   The components of the URL to build.
	 * @param string $parsed_query The components of the query to build.
	 * @param string $basename     The basename to use for the URL.
	 * @return string
	 */
	protected function build_geneanet_url( $parsed_url, $parsed_query, $basename ) {
		$scheme = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
		$host = 'gw.geneanet.org';
		$path = '/' . $basename;
		$query = isset( $parsed_url['query'] ) ? '?' . http_build_query( $parsed_query ) : '';

		return "$scheme$host$path$query";
	}

	/**
	 * Add admin notices.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Message to use for the notice.
	 * @param string $type    Type of the notice ('success', 'info', 'warning', 'error').
	 */
	public function add_admin_notice( $message, $type ) {

		$is_visual_editor = 'wp_ajax_bulk_do_shortcode' === current_filter();
		// In the case of the visual editor, do not add notice.
		if ( $is_visual_editor ) {
			return;
		}

		if ( ! in_array( $type, array( 'success', 'info', 'warning', 'error' ), true ) ) {
			return;
		}

		$notices = get_transient( 'geneanet_embedded_individual_admin_notices' );
		if ( false === $notices ) {
			$notices = array( array( $type, $message ) );
		} else {
			$notices[] = array( $type, $message );
		}
		set_transient( 'geneanet_embedded_individual_admin_notices', $notices, 120 );

	}

	/**
	 * Show admin notices.
	 *
	 * @since 1.0.0
	 */
	public function show_admin_notices() {

		$notices = get_transient( 'geneanet_embedded_individual_admin_notices' );
		if ( false !== $notices ) {
			foreach ( $notices as $notice ) {
				?>
				<div class="notice notice-<?php esc_attr_e( $notice[0] ); ?> is-dismissible">
					<p><?php esc_html_e( $notice[1], $this->get_plugin_name() ); ?></p>
				</div>
				<?php
			}

			delete_transient( 'geneanet_embedded_individual_admin_notices' );
		}

	}

	/**
	 * Add admin notice error for the parsed URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The parsed URL.
	 */
	protected function add_admin_notice_error_parsed_url( $url ) {

		$this->add_admin_notice( sprintf( __( 'The URL may not be correctly formatted: %s', $this->get_plugin_name() ), $url ), 'error' );

	}

	/**
	 * Get the data of an individual using Geneanet API.
	 * Handle all possible errors.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_url The remote Geneanet API URL to call.
	 * @param string $url     The URL used in the shortcode.
	 * @return array|false The data of the individual or false if an error has occurred.
	 */
	protected function get_geneanet_api_individual_data( $api_url, $url ) {

		$response = wp_remote_get( 'https:' . $api_url . '&lang=' . $this->get_lang() . '&origin=geneanet-embedded-individual' );

		if ( is_array( $response ) && 200 === $response['response']['code'] ) {
			$body = json_decode( $response['body'], true );
			// Data validation.
			if ( ! is_array( $body ) ||
				! isset( $body['index'] ) || ! is_numeric( $body['index'] ) ||
				! isset( $body['firstname'] ) || ! is_string( $body['firstname'] ) ||
				! isset( $body['lastname'] ) || ! is_string( $body['lastname'] ) ||
				! isset( $body['sex'] ) || ! is_numeric( $body['sex'] ) ) {
				$this->add_admin_notice( sprintf( __( 'Geneanet has returned an unexpected response for the URL %s. Please try again in a few moments.', $this->get_plugin_name() ), $url ), 'error' );
				return false;
			}

			return $body;
		} else {
			if ( is_array( $response ) ) {
				if ( 404 === $response['response']['code'] ) {
					$this->add_admin_notice( sprintf( __( 'No individual found with the URL: %s', $this->get_plugin_name() ), $url ), 'error' );
				} else {
					$this->add_admin_notice( sprintf( __( 'Geneanet returned an error for the URL %s: %d', $this->get_plugin_name() ), $url, $response['response']['code'] ), 'error' );
				}
			} else {
				$this->add_admin_notice( sprintf( __( 'An error has occurred while sending a request to the URL %s: %d', $this->get_plugin_name() ), $url, $response->get_error_code() ), 'error' );
			}

			return false;
		}

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
	 * Sets the public-facing functionality of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param Geneanet_Embedded_Individual_Public $plugin_public The public-facing functionality of the plugin.
	 */
	public function set_plugin_public( Geneanet_Embedded_Individual_Public $plugin_public ) {
		$this->plugin_public = $plugin_public;
	}

	/**
	 * Retrieves the public-facing functionality of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Geneanet_Embedded_Individual_Public The public-facing functionality of the plugin.
	 */
	public function get_plugin_public() {
		return $this->plugin_public;
	}
}
