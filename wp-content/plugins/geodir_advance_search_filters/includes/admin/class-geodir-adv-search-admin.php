<?php
/**
 * GeoDirectory Advance Search Admin
 *
 * @class    GeoDir_Adv_Search_Admin
 * @author   AyeCode
 * @package  GeoDir_Advance_Search_Filters/Admin
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GeoDir_Adv_Search_Admin class.
 */
class GeoDir_Adv_Search_Admin {
    
    /**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_filter( 'geodir_get_settings_pages', array( $this, 'load_settings_page' ), 10.3, 1 );
		add_filter( 'geodir_search_options', array( __CLASS__, 'general_search_settings' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_styles' ), 10 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ), 10 );

		add_filter( 'geodir_uninstall_options', 'geodir_search_uninstall_settings', 10, 1 );
		add_action( 'geodir_clear_version_numbers', 'geodir_search_clear_version_number', 20 );
		add_filter( 'geodir_diagnose_multisite_conversion', 'geodir_search_diagnose_multisite_conversion', 20, 1 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( GEODIR_ADV_SEARCH_PLUGIN_DIR . 'includes/admin/admin-functions.php' );
	}

	/**
	 * Enqueue styles.
	 */
	public static function admin_styles() {
	}

	/**
	 * Enqueue scripts.
	 */
	public static function admin_scripts() {
		global $wp_query, $post, $pagenow;

		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		$gd_screen_id = sanitize_title( __( 'GeoDirectory', 'geodirectory' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$post_type   = isset($_REQUEST['post_type']) && $_REQUEST['post_type'] ? sanitize_text_field($_REQUEST['post_type']) : '';
		$page 		  = ! empty( $_GET['page'] ) ? $_GET['page'] : '';

		// Register scripts
		wp_register_script( 'geodir-adv-search', GEODIR_ADV_SEARCH_PLUGIN_URL . '/assets/js/admin' . $suffix . '.js', array( 'jquery', 'geodir-admin-script' ), GEODIR_ADV_SEARCH_VERSION );

		// Admin scripts for GD pages only
		if ( in_array( $screen_id, geodir_get_screen_ids() ) ) {
			wp_enqueue_script( 'geodir-adv-search' );
			wp_localize_script( 'geodir-adv-search', 'geodir_advance_search_admin_params', geodir_adv_search_admin_params() );
		}
	}

	public static function load_settings_page( $settings_pages ) {
		$post_type = ! empty( $_REQUEST['post_type'] ) ? sanitize_title( $_REQUEST['post_type'] ) : 'gd_place';
		if ( !( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == $post_type.'-settings' ) ) {
			$settings_pages[] = include( 'settings/class-geodir-adv-search-settings-advance-search.php' );

		} else {
			$settings_pages[] = include( 'settings/class-geodir-adv-search-settings-cpt-search.php' );

		}

		return $settings_pages;
	}

	public static function general_search_settings( $settings = array() ) {
		$search_settings = array( 
			array( 
				'type' => 'title', 
				'id' => 'adv_search_general_settings', 
				'name' => __( 'Site settings', 'geodiradvancesearch' ),
			),
			array(
				'type' => 'checkbox',
				'id' => 'advs_search_display_searched_params',
				'name' => __( 'Display searched parameters with title?', 'geodiradvancesearch' ),
				'desc' => __( 'Enable to display searched parameters with title when searching for a custom field.', 'geodiradvancesearch' ),
				'std' => '0',
			),
			array(
				'type' => 'sectionend', 
				'id' => 'adv_search_general_settings'
			)
		);
		$settings = array_merge( $settings, $search_settings );

		return $settings;
	}
}
