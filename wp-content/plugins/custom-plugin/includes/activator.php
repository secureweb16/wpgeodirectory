<?php



/**

 * Register all actions and filters for the plugin

 *

 * @link       http://securewebtechnologies.com/

 * @since      1.0.0

 *

 * @package    Custom_Plugin

 * @subpackage Custom_Plugin/includes

 */



/**

 * Register all actions and filters for the plugin.

 *

 * Maintain a list of all hooks that are registered throughout

 * the plugin

 *

 * @package    Custom_Plugin

 * @subpackage Custom_Plugin/includes

 * @author     

 */

class Custom_Plugin_Activator {



	/**

	 * Short Description. (use period)

	 *

	 * Long Description.

	 *

	 * @since    1.0.0

	 */

	public static function activate() {

            

	    global $wpdb;

    	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $create_table_query = "";



    	dbDelta( $create_table_query );

	}



}

