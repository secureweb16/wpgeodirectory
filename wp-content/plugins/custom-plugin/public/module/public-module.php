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

class Custom_Plugin_Public_Module {

	/**

	 * The ID of this plugin.

	 *

	 * @since    1.0.0

	 * @access   private

	 * @var      string    $plugin_name    The ID of this plugin.

	 */

	private $plugin_name;

	/**

	 * The version of this plugin.

	 *

	 * @since    1.0.0

	 * @access   private

	 * @var      string    $version    The current version of this plugin.

	 */

	private $version;

	/**

	 * Initialize the class and set its properties.

	 *

	 * @since    1.0.0

	 * @param      string    $plugin_name       The name of this plugin.

	 * @param      string    $version    The version of this plugin.

	 */ 	

	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;

		$this->version = $version;

		$this->add_shortcodes();

	}

	function add_shortcodes() {

		add_shortcode( 'DENTAL_LAB_FORM', array( $this,'dental_lab_form') );

	}


	function dental_lab_form(){

		if(isset($_POST['cs_submit']) && $_POST['cs_submit'] != ''){
			$this->save_form_submited($_POST);
		}

		ob_start();
		require_once Custom_Plugin_BASEPATH . 'public/templates/dental-lab.php';
		$output = ob_get_clean();
		return $output;
	}

	function save_form_submited($data){
		print_r($data);		
	}
}