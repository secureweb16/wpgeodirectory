<?php

/**

 * The plugin bootstrap file

 *

 * This file is read by WordPress to generate the plugin information in the plugin

 * admin area. This file also includes crypto cruncy form frant-end,

 * registers the activation and deactivation functions, and defines a function

 * that starts the plugin.

 *

 * @link              http://securewebtechnologies.com/

 * @since             1.0.0 

 *

 * @wordpress-plugin

 * Plugin Name:       Custom Plugin

 * Plugin URI:        http://securewebtechnologies.com/

 * Description:       An alternative to woocommrece specially developed for secureweb. Handles symbols and related content for secureweb.

 * Version:           1.0.0

 * Author:            Secureweb

 * Author URI:        http://securewebtechnologies.com/

 * License:           GPL-2.0+

 * License URI:       

 * Text Domain:      custom-plugin

 */



// If this file is called directly, abort.

if ( ! defined( 'WPINC' ) ) {

	die;

}



/**

 * Currently plugin version. 

 * Rename this for your plugin and update it as you release new versions.

 */

define( 'Custom_Plugin_VERSION', '1.0.0' );

define( 'Custom_Plugin_BASEPATH', plugin_dir_path( __FILE__ ) );

define( 'Custom_Plugin_BASEURL', plugin_dir_url( __FILE__ ) );

// define( 'SS_ENABLE_CACHE', 0 );

include_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');
/**

 * The code that runs during plugin activation.

 * This action is documented in includes/activator.php

 */

function activate_Custom_Plugin() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';

	Custom_Plugin_Activator::activate();

}

/**

 * The code that runs during plugin deactivation.

 * This action is documented in includes/deactivator.php

 */

function deactivate_Custom_Plugin() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';

	Custom_Plugin_Deactivator::deactivate();

}

function load_media_files() {

    wp_enqueue_media();

}

add_action( 'admin_enqueue_scripts', 'load_media_files' );

register_activation_hook( __FILE__, 'activate_Custom_Plugin' );

register_deactivation_hook( __FILE__, 'deactivate_Custom_Plugin' );



/**

 * The core plugin class that is used to define internationalization,

 * admin-specific hooks, and public-facing site hooks.

 */

require plugin_dir_path( __FILE__ ) . 'includes/custom-plugin.php';

require plugin_dir_path( __FILE__ ) . 'ajax/admin-ajax.php';
require plugin_dir_path( __FILE__ ) . 'ajax/public-ajax.php';


/**

 * Begins execution of the plugin.

 *

 * Since everything within the plugin is registered via hooks,

 * then kicking off the plugin from this point in the file does

 * not affect the page life cycle.

 *

 * @since    1.0.0

 */

function run_Custom_Plugin() {

    $Custom_Plugin = new Custom_Plugin();

    $Custom_Plugin->run();

}



run_Custom_Plugin();

