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

class Custom_Plugin_Admin {

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

    private $loader;

    /**

     * Initialize the class and set its properties.

     * @since    1.0.0

     * @param      string    $plugin_name       The name of this plugin.

     * @param      string    $version    The version of this plugin.

     */

    public function __construct( $plugin_name, $version ) {

      $this->plugin_name = $plugin_name;

      $this->version = $version;

      $this->load_files();

    }

    /**

     * Register the hooks for the admin area.

     * @since    1.0.0

     */

    public function admin_menu() {

      add_menu_page(
        __( 'Wp Form', $this->plugin_name ),
        __( 'Wp Form', $this->plugin_name ),
        'manage_options',
        'wp-form',
        array($this,'load_view_form_submited_list'),
        'dashicons-images-alt',
        10
      );
      
      

    }

    function load_view_form_submited_list(){

      $class_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/module/admin-module.php';
      require_once $class_file;
      
      $admin_module = new Admin_Module( $this->plugin_name, $this->version );
      return $admin_module;
    }

    function load_files(){

    }

    /**

     * Register the hooks for the admin area.

     * @since    1.0.0

     */

    public function load_view_coin_booking($params) {

      $class_file = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/module/booking/cc-booking.php';

      require_once $class_file;

      $cc_all_booking = new Cc_All_Booking( $this->plugin_name, $this->version );

      return $cc_all_booking;

    }

    function save_post_meta_data(){

      if(isset($_POST['post_type']) && $_POST['post_type'] == 'gd_place') {

        $post_id=$_POST['post_ID'];

        $doctor_name = $_POST['doctor_name'];

        update_post_meta($post_id, 'cs_doctor_name', $doctor_name );
        
      }
    }


    /**

     * Register the stylesheets for the admin area.

     * @since    1.0.0

     */

    public function enqueue_styles() {

      wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/admin_style.css?v='.time(), array(), 'all');

    }

    public function init_hook() {
      // add_filter('use_block_editor_for_post', '__return_false', 10);
    //  register_post_type( 'dentist_appoinment',      
    //   array(
    //     'labels' => array(
    //       'name' => __( 'Blog Post' ),
    //       'singular_name' => __( 'Blog Post' )
    //     ),
    //     'public' => true,
    //     'has_archive' => true,
    //     'rewrite' => array('slug' => 'blog-post'),
    //     'show_in_rest' => true,
    //     'supports' => array('title','editor','thumbnail'),
    //     'taxonomies'    => array('blog-category', 'Category' ),
    //   )
    // );
    }

    


  /**

  * Register the JavaScript for the admin area.

  * @since    1.0.0

  */

  public function enqueue_scripts() 
  {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin-custom.js?v='.time(), array( 'jquery' ), false );
    wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'js/my-ajax-script.js', array('jquery') );
    wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
  }

}

