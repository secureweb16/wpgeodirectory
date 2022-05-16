<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Loads the plugin 
 *
 * @since GeoDirectory Converter 1.0.0
 */
class GDCONVERTER_Loarder {
    
	/**
	 * @var string Path to the includes directory
	 */
	public $includes_dir = '';

	/**
	 * @var string URL to the includes directory
	 */
    public $includes_url = '';
    
	/**
	 * The main class constructor
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	public function __construct() {
        
        //Setup class globals
        $this->includes_dir = plugin_dir_path( GEODIR_CONVERTER_PLUGIN_FILE ) . 'includes/';
        $this->includes_url = plugin_dir_url( GEODIR_CONVERTER_PLUGIN_FILE ) . 'includes/';

		//Load textdomain
		$this->load_text_domain();

        //Include plugin files
        $this->includes();

        //Setup hooks
        $this->setup_hooks();

        //Init the Admin
        new GDCONVERTER_Admin();

        //Init PMD
		new GDCONVERTER_PMD();

		// Inist the listify converter.
		new GDCONVERTER_Listify();
    }
    
    /**
	 * Loads text domain
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	private function load_text_domain() {
        load_plugin_textdomain( 'geodirectory-converter', false, basename( dirname( GEODIR_CONVERTER_PLUGIN_FILE ) ) . '/languages' ); 
	}
	
	/**
	 * Includes plugin files and dependancies
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	private function includes() {
        require_once( $this->includes_dir . 'admin/admin.php' );
		require_once( $this->includes_dir . 'directories/pmd/pmd.php' );
		require_once( $this->includes_dir . 'directories/listify/listify.php' );
    }
    
    /**
	 * Attaches handlers to various hooks
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	private function setup_hooks() {

		//Handles ajax requests for data import
		add_action( 'wp_ajax_gdconverter_handle_import', array( $this, 'handle_import' ) );
		
		//Maybe redirect the user to the plugin's admin page
		add_action( 'admin_init', array( $this, 'maybe_redirect' ) );

		//Add a link to the plugin's admin page on the plugins overview screen
		add_filter( 'plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );
	}

	/**
	 * Maybe redirect the user to the plugin's admin page
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	public function maybe_redirect() {
		if( '1' == get_transient( '_geodir_converter_installed' ) ){
			delete_transient( '_geodir_converter_installed' );

			// Bail if activating from network, or bulk
  			if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
    			return;
  			}

			// Redirect to the converter page
  			wp_redirect( esc_url( $this->import_page_url() ) );
			exit;
		}
	}

	/**
	 * Adds a link to the plugin's admin page on the plugins overview screen
	 *
	 * @since GeoDirectory Converter 1.0.0
	 */
	public function modify_plugin_action_links( $links, $file ) {

		if ( plugin_basename( GEODIR_CONVERTER_PLUGIN_FILE )  == $file ) {
			$url 				= esc_url( $this->import_page_url() );
			$attr				= esc_attr__( 'Convert', 'geodirectory-converter' );
			$title				= esc_html__( 'Convert', 'geodirectory-converter' );
			$links['convert']  = "<a href='$url' aria-label='$attr'> $title </a>";
		}
		return $links;

	}

	/**
	 * returns a url to the plugin's admin page
	 *
	 * @since GeoDirectory Converter 1.0.0
	 */
	public function import_page_url() {

		return add_query_arg( 
			array( 
				'page' => 'geodir-converter'
			),
			admin_url( 'tools.php' )
		);

	}

    /**
	 * Retrieves a list of all registerd importers
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	public static function get_importers() {
        return apply_filters( 'geodir_converter_importers', array());
    }

    /**
	 * Sends ajax response to the browser
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	public static function send_response( $action, $body ) {
		wp_send_json( array(
            'action' => $action,
            'body'	 => $body,	
        ) );
    }
    
    /**
	 * Processes the import
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	public function handle_import() {
		
        //Abort if the current user does not have enough rights to run this import
	    if ( !current_user_can( 'manage_options' )  ) {
		    $error = esc_html__( 'You are not allowed to run imports on this site.', 'geodirectory-converter' );
		    self::send_response( 'error', $error );
        }

        //Basic security check
	    if ( empty( $_REQUEST['gdconverter_nonce_field'] ) || ! wp_verify_nonce( $_REQUEST['gdconverter_nonce_field'], 'gdconverter_nonce_action' ) ) {
		    $error = esc_html__( 'An unknown error occured! Please refresh the page and try again.', 'geodirectory-converter' );
		    self::send_response( 'error', $error );
        }

        //Ensure that an importer has been selected...
        if( empty( $_REQUEST['gd-converter'] ) ){
            $error = esc_html__( 'Error: Please select a converter.', 'geodirectory-converter' );
		    self::send_response( 'error', $error );
        }

        //...and is registered
        $importer = sanitize_text_field( $_REQUEST['gd-converter'] );
        $importers= self::get_importers();
        if( !array_key_exists( $importer, $importers ) ){
            $error = esc_html__( 'Error: The converter you selected is not registered on this site.', 'geodirectory-converter' );
		    self::send_response( 'error', $error );
		}
		
		if ( ! defined( 'GEODIR_DOING_IMPORT' ) ) {
			define( 'GEODIR_DOING_IMPORT', TRUE );
		}
		
		//What step are we on
		$current_step = 1;
		if($_REQUEST['step'] ) {
			$current_step = intval( $_REQUEST['step'] );
		}

        //Let's fetch the next step
        $next_step = '';

        /**
	     * Filters the response returned to the user after selecting an importer
	     *
	     * @since 1.0.0
	     *
	     */
        $next_step = apply_filters( 'geodirectory_importer_fields', $next_step, $current_step );
        
        /**
	     * Filters the response returned to the user after selecting an importer
	     *
	     * @since 1.0.0
	     *
	     */
        $next_step = apply_filters( "geodirectory_{$importer}_importer_fields", $next_step, $current_step );

        if( empty($next_step) ){
            $next_step = esc_html__('The selected converter is incorrectly configured', 'geodirectory-converter');
        }

		$return = $this->next_step_to_html( $next_step, $current_step );

        //Return our response
        self::send_response( 'success', $return );
	}

	/**
	 * Fetches the HTML for the next step
	 *
	 * @since GeoDirectory Converter 1.0.0
	 *
	 */
	public function next_step_to_html( $next_step, $current_step ) {

		$importer = esc_attr( $_REQUEST['gd-converter'] );
		$html = '
				<form method="post" action="" class="geodir-converter-form geodir-converter-form1">
				<input type="hidden" name="action" value="gdconverter_handle_import">
		';

		$step = $current_step + 1;
		$html .= "<input type='hidden' name='gd-converter' value='$importer'>";
		$html .= "<input type='hidden' name='step' value='$step'>";
		$html .= $next_step;
		$html .= wp_nonce_field( 'gdconverter_nonce_action', 'gdconverter_nonce_field', true, false );
		$html .= '</form>';		

		return $html;

	}
}
