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

 * @package           Custom_Plugin

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

 */



class Custom_Plugin {



	/**

	 * The loader that's responsible for maintaining and registering all hooks that power

	 * the plugin.

	 *

	 * @since    1.0.0

	 * @access   public

	 * @var      Custom_Plugin_Loader    $loader    Maintains and registers all hooks for the plugin.

	 */

	public $loader;



	/**

	 * The unique identifier of this plugin.

	 *

	 * @since    1.0.0

	 * @access   protected

	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.

	 */

	protected $plugin_name;



	/**

	 * The current version of the plugin.

	 *

	 * @since    1.0.0

	 * @access   protected

	 * @var      string    $version    The current version of the plugin.

	 */

	protected $version;



	/**

	 * Define the core functionality of the plugin.

	 *

	 * Set the plugin name and the plugin version that can be used throughout the plugin.

	 * Load the dependencies, define the locale, and set the hooks for the admin area and

	 * the public-facing side of the site.

	 *

	 * @since    1.0.0

	 */

	public function __construct() {

		if ( defined( 'Custom_Plugin_VERSION' ) ) {

			$this->version = Custom_Plugin_VERSION;

		} else {

			$this->version = '1.0.0';

		}

		$this->plugin_name = 'custom-plugin';

		$this->load_dependencies();		

		$this->define_admin_hooks();

		$this->define_public_hooks();

	}



	/**

	 * Load the required dependencies for this plugin.

	 *

	 * Include the following files that make up the plugin:

	 *

	 * - Ss_Booking_Loader. Orchestrates the hooks of the plugin.

	 * - Ss_Products_i18n. Defines internationalization functionality.

	 * - Ss_Booking_Admin. Defines all hooks for the admin area.

	 * - Ss_Products_Public. Defines all hooks for the public side of the site.

	 *

	 * Create an instance of the loader which will be used to register the hooks

	 * with WordPress.

	 *

	 * @since    1.0.0

	 * @access   private

	 */

	private function load_dependencies() {		

		/**

		 * The class responsible for orchestrating the actions and filters of the

		 * core plugin.

		 */

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/loader.php';

		/**

		 * The class responsible for defining all actions that occur in the admin area.

		 */

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.php';

		/**

		 * The class responsible for defining all actions that occur in the public-facing

		 * side of the site.

		 */

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/public.php';

		$this->loader = new Custom_Plugin_Loader();

	}

	/**

	 * Register all of the hooks related to the admin area functionality

	 * of the plugin.

	 *

	 * @since    1.0.0

	 * @access   private

	 */

	private function define_admin_hooks() {

		$plugin_admin = new Custom_Plugin_Admin( $this->get_plugin_name(), $this->get_version() );		

		$this->loader->add_action( 'init', $plugin_admin, 'init_hook' );
		
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post_meta_data' );
		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );		

	}



	/**

	 * Register all of the hooks related to the public-facing functionality

	 * of the plugin.

	 *

	 * @since    1.0.0

	 * @access   private

	 */

	private function define_public_hooks() {

		$plugin_public = new Custom_Plugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_footer', $plugin_public, 'wp_footer_hook' );

		$this->loader->add_action( 'init', $plugin_public, 'wp_init_hook' );


		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );		

	}



	/**

	 * Run the loader to execute all of the hooks with WordPress.

	 *

	 * @since    1.0.0

	 */

	public function run() {

		$this->loader->run();

	}



	/**

	 * The name of the plugin used to uniquely identify it within the context of

	 * WordPress and to define internationalization functionality.

	 *

	 * @since     1.0.0

	 * @return    string    The name of the plugin.

	 */

	public function get_plugin_name() {

		return $this->plugin_name;

	}



	/**

	 * The reference to the class that orchestrates the hooks with the plugin.

	 *

	 * @since     1.0.0

	 * @return    Ss_Booking_Loader    Orchestrates the hooks of the plugin.

	 */

	public function get_loader() {

		return $this->loader;

	}



	/**

	 * Retrieve the version number of the plugin.

	 *

	 * @since     1.0.0

	 * @return    string    The version number of the plugin.

	 */

	public function get_version() {

		return $this->version;

	}



}

