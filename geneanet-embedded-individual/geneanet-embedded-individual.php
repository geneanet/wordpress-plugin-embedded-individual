<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link http://www.geneanet.org
 * @since 1.0.0
 * @package Geneanet_Embedded_Individual
 *
 * @wordpress-plugin
 * Plugin Name: Geneanet Embedded Individual
 * Description: Allows to insert a short embedded sheet containing the main information of an individual from a Geneanet family tree to a post.
 * Version: 1.1.1
 * Author: Geneanet
 * Author URI: http://www.geneanet.org
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: geneanet-embedded-individual
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-geneanet-embedded-individual-activator.php
 */
function activate_geneanet_embedded_individual() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geneanet-embedded-individual-activator.php';
	Geneanet_Embedded_Individual_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-geneanet-embedded-individual-deactivator.php
 */
function deactivate_geneanet_embedded_individual() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geneanet-embedded-individual-deactivator.php';
	Geneanet_Embedded_Individual_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_geneanet_embedded_individual' );
register_deactivation_hook( __FILE__, 'deactivate_geneanet_embedded_individual' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-geneanet-embedded-individual.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_geneanet_embedded_individual() {

	$plugin = new Geneanet_Embedded_Individual();
	$plugin->run();

}
run_geneanet_embedded_individual();
