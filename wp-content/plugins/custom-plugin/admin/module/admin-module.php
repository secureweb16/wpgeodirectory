<?php

/**

 * Register all actions and filters for the plugin

 * @link       http://securewebtechnologies.com/

 * @since      1.0.0

 * @package    Custom_Plugin

 * @subpackage Custom_Plugin/includes

 */

/**

 * Register all actions and filters for the plugin.

 * Maintain a list of all hooks that are registered throughout

 * the plugin

 * @package    Custom_Plugin

 * @subpackage Custom_Plugin/includes

 * @author     

 */

class Admin_Module {

	/**

	 * The ID of this plugin.

	 * @since    1.0.0

	 * @access   private

	 * @var      string    $plugin_name    The ID of this plugin.

	 */

	private $plugin_name;

	/**

	 * The version of this plugin.

	 * @since    1.0.0

	 * @access   private

	 * @var      string    $version    The current version of this plugin.

	 */

	private $version;

	/**

	 * Initialize the class and set its properties.

	 * @since    1.0.0

	 * @param      string    $plugin_name       The name of this plugin.

	 * @param      string    $version    The version of this plugin.

	 */

	public function __construct( $plugin_name, $version ) {

		global $wpdb;

		$this->plugin_name = $plugin_name;

		$this->version = $version;

		$this->load_admin_area();	

	}

	public function load_admin_area(){

		$args = array(	
			'post_type'		=>  'dentist_appoinment',
			'posts_per_page' => -1,
		);

		$finalData = get_posts($args);
		
		include_once Custom_Plugin_BASEPATH. 'admin/templates/admin-form-data.php';

	}

}