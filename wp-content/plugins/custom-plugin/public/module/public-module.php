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

		$responce = array();

		if(isset($_POST['cs_submit']) && $_POST['cs_submit'] != ''){
			$responce = $this->save_form_submited($_POST);			
		}

		if(isset($responce['status']) && $responce['status'] === 200) {			
			echo "<script>swal('Data is submited successfuly!',
				  '',
				  'success'
				)</script>";
			$responce = array();
		}

		ob_start();
		require_once Custom_Plugin_BASEPATH . 'public/templates/dental-lab.php';
		$output = ob_get_clean();
		return $output;
	}

	function save_form_submited($data){
		
		
		$path = plugin_dir_path( dirname( __FILE__ ) ).'logs/log_'.date('d_m_Y').'.log';
		$filename = fopen($path, 'a');

		fwrite($filename, "\n\r========================Time ====================\n\r");
		fwrite($filename, print_r(date('d-m-Y h:i:s'),true));

		fwrite($filename, "\n\r======================== Post Data ====================\n\r");
		fwrite($filename, print_r($data,true));

		$validation = $this->sumnit_validation($data);
	
		
		$response['status'] = 200;

		if(count($validation) > 0 ){

			$response['status'] = 403;

			$response['error'] = $validation;

			$response['value'] = $data;

			return $response;

		}

		fwrite($filename, "\n\r======================== validation ====================\n\r");
		fwrite($filename, print_r($validation,true));


		$array_data = array(

			'post_title'      =>  $data['cs_dentist_name'],

			'post_content'    =>  $data['cs_dentist_name'],

			'post_status'     => 'publish',

			'post_type'       => 'dentist_appoinment',

			'post_author'   => 1,

		); 

		$post_id = wp_insert_post( $array_data );

		$meta_value['cs_post_id']						= $data['cs_post_id'];
		$meta_value['cs_dentist_name']			= $data['cs_dentist_name'];
		$meta_value['cs_clinic_name']				= $data['cs_clinic_name'];
		$meta_value['cs_email']							= $data['cs_email'];
		$meta_value['cs_phone']							= $data['cs_phone'];
		$meta_value['cs_date']							= $data['cs_date'];
		$meta_value['cs_patient_name']			= $data['cs_patient_name'];
		$meta_value['cs_patient_age']				= $data['cs_patient_age'];
		$meta_value['cs_gender']						= $data['cs_gender'];
		$meta_value['cs_restoration_type']	= $data['cs_restoration_type'];
		$meta_value['cs_implant_system']		= $data['cs_implant_system'];
		$meta_value['cs_material_type']			= $data['cs_material_type'];

		foreach ($meta_value as $metakey => $metavalue) {

			update_post_meta($post_id,$metakey,$metavalue);

		}

		fwrite($filename, "\n\r======================== Success ====================\n\r");
		fclose($filename);

		return $response;

	}

	function sumnit_validation($data){

		$check_validation = array();
		

		if($data['cs_email'] == ''){

			$check_validation['cs_email'] = 'Email is required';

		}

		if($data['cs_phone'] == ''){

			$check_validation['cs_phone'] = 'Phone is required';

		}

		if($data['cs_date'] == ''){

			$check_validation['cs_date'] = 'Date is required';

		}

		if($data['cs_patient_name'] == ''){

			$check_validation['cs_patient_name'] = 'Patient name is required';

		}

		if($data['cs_patient_age'] == ''){

			$check_validation['cs_patient_age'] = 'Patient age is required';

		}

		if($data['cs_gender'] == ''){

			$check_validation['cs_gender'] = 'Gender is required';

		}

		if($data['cs_restoration_type'] == ''){

			$check_validation['cs_restoration_type'] = 'Restoration is required';

		}

		if($data['cs_implant_system'] == ''){

			$check_validation['cs_implant_system'] = 'Implant system is required';

		}

		if($data['cs_material_type'] == ''){

			$check_validation['cs_material_type'] = 'Material is required';

		}

		return $check_validation;	

	}

}