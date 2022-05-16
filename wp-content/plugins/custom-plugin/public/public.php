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

class Custom_Plugin_Public {

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

    public $symbol;

    /**

     * Initialize the class and set its properties.

     *

     * @since    1.0.0

     * @param      string    $plugin_name       The name of the plugin.

     * @param      string    $version    The version of this plugin.

     */

    public function __construct($plugin_name, $version) {

      $this->plugin_name = $plugin_name;

      $this->version = $version;  

      $class_file = plugin_dir_path( dirname( __FILE__ ) ) . 'public/module/public-module.php';

      require_once $class_file;

      $Custom_Plugin_Public_Module = new Custom_Plugin_Public_Module($this->plugin_name, $this->version);

    }    

    /**

     * Register the stylesheets for the public-facing side of the site.

     *

     * @since    1.0.0

     */

    public function enqueue_styles() {
 
      wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/style.css?v='.time(), array(), 'all'); 

    }



    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */

    public function enqueue_scripts() {
      wp_enqueue_script( 'custom_js', plugin_dir_url(__FILE__) . 'js/custom.js?v='.time(), array('jquery') );
      wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'js/my-ajax-script.js', array('jquery') );
      wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
    

    public function wp_init_hook() {
      $user = wp_get_current_user();
      if (!in_array( 'administrator', (array) $user->roles )) {
        add_filter('show_admin_bar', '__return_false');
      }
    }

    public function wp_footer_hook(){
      echo do_shortcode('[DENTAL_LAB_FORM]');
    }

  }