<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       https://wpgeodirectory.com
 * @since      2.0.0
 *
 * @package    GeoDir_Advance_Search_Filters
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    GeoDir_Adv_Search
 * @author     AyeCode Ltd
 */
final class GeoDir_Adv_Search {

    /**
     * GeoDirectory Advance Search Filters instance.
     *
     * @access private
     * @since  2.0.0
     */
    private static $instance = null;

    /**
     * Main GeoDir_Adv_Search Instance.
     *
     * Ensures only one instance of GeoDirectory Advance Search Filters is loaded or can be loaded.
     *
     * @since 2.0.0
     * @static
     * @see GeoDir()
     * @return GeoDir_Adv_Search - Main instance.
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof GeoDir_Adv_Search ) ) {
            self::$instance = new GeoDir_Adv_Search;
            self::$instance->setup_constants();

            add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			if ( ! class_exists( 'GeoDirectory' ) ) {
                add_action( 'admin_notices', array( self::$instance, 'geodirectory_notice' ) );

                return self::$instance;
            }

            if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
                add_action( 'admin_notices', array( self::$instance, 'php_version_notice' ) );

                return self::$instance;
            }

            self::$instance->includes();
            self::$instance->init_hooks();

            do_action( 'geodir_advance_search_filters_loaded' );
        }
 
        return self::$instance;
    }

    /**
     * Setup plugin constants.
     *
     * @access private
     * @since 2.0.0
     * @return void
     */
    private function setup_constants() {
        global $plugin_prefix;

		if ( $this->is_request( 'test' ) ) {
            $plugin_path = dirname( GEODIR_ADV_SEARCH_PLUGIN_FILE );
        } else {
            $plugin_path = plugin_dir_path( GEODIR_ADV_SEARCH_PLUGIN_FILE );
        }

        $this->define( 'GEODIR_ADV_SEARCH_PLUGIN_DIR', $plugin_path );
        $this->define( 'GEODIR_ADV_SEARCH_PLUGIN_URL', untrailingslashit( plugins_url( '/', GEODIR_ADV_SEARCH_PLUGIN_FILE ) ) );
        $this->define( 'GEODIR_ADV_SEARCH_PLUGIN_BASENAME', plugin_basename( GEODIR_ADV_SEARCH_PLUGIN_FILE ) );

		// Database tables
		$this->define( 'GEODIR_ADVANCE_SEARCH_TABLE', $plugin_prefix . 'custom_advance_search_fields' );
		$this->define( 'GEODIR_BUSINESS_HOURS_TABLE', $plugin_prefix . 'business_hours' ); // business hours table
    }
    
    /**
     * Loads the plugin language files
     *
     * @access public
     * @since 2.0.0
     * @return void
     */
    public function load_textdomain() {
        global $wp_version;
        
        $locale = $wp_version >= 4.7 ? get_user_locale() : get_locale();
        
        /**
         * Filter the plugin locale.
         *
         * @since   1.0.0
         */
        $locale = apply_filters( 'plugin_locale', $locale, 'geodiradvancesearch' );

        load_textdomain( 'geodiradvancesearch', WP_LANG_DIR . '/' . 'geodiradvancesearch' . '/' . 'geodiradvancesearch' . '-' . $locale . '.mo' );
        load_plugin_textdomain( 'geodiradvancesearch', FALSE, basename( dirname( GEODIR_ADV_SEARCH_PLUGIN_FILE ) ) . '/languages/' );
    }

	/**
     * Check plugin compatibility and show warning.
     *
     * @static
     * @access private
     * @since 2.0.0
     * @return void
     */
    public static function geodirectory_notice() {
        echo '<div class="error"><p>' . __( 'GeoDirectory plugin is required for the GeoDirectory Advance Search Filters plugin to work properly.', 'geodiradvancesearch' ) . '</p></div>';
    }
    
    /**
     * Show a warning to sites running PHP < 5.3
     *
     * @static
     * @access private
     * @since 2.0.0
     * @return void
     */
    public static function php_version_notice() {
        echo '<div class="error"><p>' . __( 'Your version of PHP is below the minimum version of PHP required by GeoDirectory Advance Search Filters. Please contact your host and request that your version be upgraded to 5.3 or later.', 'geodiradvancesearch' ) . '</p></div>';
    }
    
    /**
     * Include required files.
     *
     * @access private
     * @since 2.0.0
     * @return void
     */
    private function includes() {
        /**
         * Class autoloader.
         */
        include_once( GEODIR_ADV_SEARCH_PLUGIN_DIR . 'includes/class-geodir-adv-search-autoloader.php' );

		GeoDir_Adv_Search_AJAX::init();
		GeoDir_Adv_Search_Business_Hours::init(); // Business Hours
		GeoDir_Adv_Search_Fields::init();

        require_once( GEODIR_ADV_SEARCH_PLUGIN_DIR . 'includes/functions.php' );
		require_once( GEODIR_ADV_SEARCH_PLUGIN_DIR . 'includes/template-functions.php' );

        if ( $this->is_request( 'admin' ) || $this->is_request( 'test' ) || $this->is_request( 'cli' ) ) {
            new GeoDir_Adv_Search_Admin();

	        require_once( GEODIR_ADV_SEARCH_PLUGIN_DIR . 'includes/admin/admin-functions.php' );

			GeoDir_Adv_Search_Admin_Install::init();

			require_once( GEODIR_ADV_SEARCH_PLUGIN_DIR . 'upgrade.php' );
        }

		$this->query = new GeoDir_Adv_Search_Query();
    }
    
    /**
     * Hook into actions and filters.
     * @since  2.0.0
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ), 0 );

	    add_filter( 'wp_super_duper_options_gd_search', 'geodir_search_widget_options' );
		add_filter( 'geodir_register_block_pattern_search_attrs', 'geodir_search_block_pattern_attrs', 10, 1 );

	    if ( $this->is_request( 'frontend' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ), 10 );
		    add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ), 10 );
		    
		    // aui
		    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_aui' ), 10 );
		    
			add_filter( 'wp_super_duper_div_classname_gd_search', 'geodir_search_widget_add_class', 10, 3 );
			add_filter( 'wp_super_duper_div_attrs_gd_search', 'geodir_search_widget_add_attr', 10, 3 );
			add_filter( 'wp_super_duper_before_widget_gd_search', 'geodir_search_before_widget_content', 10, 4 );
			add_filter( 'wp_footer' , 'geodir_search_form_add_script' , 10 );

			add_filter( 'body_class', 'geodir_search_body_class' ); // let's add a class to the body so we can style the new addition to the search
			
			if ( geodir_get_option( 'advs_search_display_searched_params' ) ) {
				add_action( 'geodir_extra_loop_actions', 'geodir_search_show_searched_params', 9999, 1 );
			}
		}
    }
    
    /**
     * Initialise plugin when WordPress Initialises.
     */
    public function init() {
        // Before init action.
        do_action( 'geodir_adv_search_before_init' );

        // Init action.
        do_action( 'geodir_adv_search_init' );
    }
    
    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }
    
    /**
     * Request type.
     *
     * @param  string $type admin, frontend, ajax, cron, test or CLI.
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
                break;
            case 'ajax' :
                return wp_doing_ajax();
                break;
            case 'cli' :
                return ( defined( 'WP_CLI' ) && WP_CLI );
                break;
            case 'cron' :
                return wp_doing_cron();
                break;
            case 'frontend' :
                return ( ! is_admin() || wp_doing_ajax() ) && ! wp_doing_cron();
                break;
            case 'test' :
                return defined( 'GD_TESTING_MODE' );
                break;
        }
        
        return null;
    }
	
	/**
	 * Enqueue styles.
	 */
	public function add_styles() {
		$design_style = geodir_design_style();
		if(!$design_style){
			// Register stypes
			wp_register_style( 'geodir-adv-search', GEODIR_ADV_SEARCH_PLUGIN_URL . '/assets/css/style.css', array(), GEODIR_ADV_SEARCH_VERSION );

			wp_enqueue_style( 'geodir-adv-search' );
		}

	}

	/**
	 * Enqueue scripts.
	 */
	public function add_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$design_style = geodir_design_style();
		if(!$design_style) {
			// Register scripts
			wp_register_script( 'geodir-adv-search', GEODIR_ADV_SEARCH_PLUGIN_URL . '/assets/js/script' . $suffix . '.js', array(
				'jquery',
				'geodir',
				'geodir-jquery-ui-timepicker'
			), GEODIR_ADV_SEARCH_VERSION );

			wp_enqueue_script( 'geodir-adv-search' );
		}
		$script = $design_style ? 'geodir' : 'geodir-adv-search';
		wp_localize_script($script , 'geodir_search_params', geodir_adv_search_params() );
	}

	public function enqueue_aui(){
		// core
		wp_add_inline_script( 'geodir', $this->add_scripts_aui() );
	}


	public function add_scripts_aui(){
		ob_start();
			if(0){ ?><script><?php }?>

			document.addEventListener("DOMContentLoaded", function() {

				//setup advanced search form on load
				geodir_search_setup_advance_search();

				//setup advanced search form on form ajax load
				jQuery("body").on("geodir_setup_search_form", function() {
					geodir_search_setup_advance_search();
				});

				if (jQuery('.geodir-search-container form').length) {
					geodir_search_setup_searched_filters();
				}

				/* Refresh Open Now time */
				if (jQuery('.geodir-search-container select[name="sopen_now"]').length) {
					setInterval(function(e) {
						geodir_search_refresh_open_now_times();
					}, 60000);
					geodir_search_refresh_open_now_times();
				}
			});

			function geodir_search_setup_advance_search() {
				jQuery('.geodir-search-container.geodir-advance-search-searched').each(function() {
					var $this = this;

					if (jQuery($this).attr('data-show-adv') == 'search') {
						jQuery('.geodir-show-filters', $this).trigger('click');
					}
				});

				jQuery('.geodir-more-filters', '.geodir-filter-container').each(function() {
					var $cont = this;
					var $form = jQuery($cont).closest('form');
					var $adv_show = jQuery($form).closest('.geodir-search-container').attr('data-show-adv');
					if ($adv_show == 'always' && typeof jQuery('.geodir-show-filters', $form).html() != 'undefined') {
						jQuery('.geodir-show-filters', $form).remove();
						if (!jQuery('.geodir-more-filters', $form).is(":visible")) {
							jQuery('.geodir-more-filters', $form).slideToggle(500);
						}
					}
				});
			}


			function geodir_search_setup_searched_filters() {
				jQuery('.gd-adv-search-labels .gd-adv-search-label').on('click', function(e) {
					var $this = jQuery(this), $form = jQuery('.geodir-search-container form'), name, to_name,
						name = $this.data('name');
					to_name = $this.data('names');

					if ((typeof name != 'undefined' && name) || $this.hasClass('gd-adv-search-near')) {
						if ($this.hasClass('gd-adv-search-near')) {
							name = 'snear';
							// if we are clearing the near then we need to clear up a few more things
							jQuery('.sgeo_lat,.sgeo_lon,.geodir-location-search-type', $form).val('');
						}

						geodir_search_deselect(jQuery('[name="' + name + '"]', $form));

						if (typeof to_name != 'undefined' && to_name) {
							geodir_search_deselect(jQuery('[name="' + to_name + '"]', $form));
						}

						jQuery('.geodir_submit_search', $form).trigger('click');
					}
				});
			}

			function geodir_search_refresh_open_now_times() {
				jQuery('.geodir-search-container select[name="sopen_now"]').each(function() {
					geodir_search_refresh_open_now_time(jQuery(this));
				});
			}

			function geodir_search_refresh_open_now_time($this) {
				var $option = $this.find('option[value="now"]'), label, value, d, date_now, time, $label, open_now_format = geodir_search_params.open_now_format;
				if ($option.length && open_now_format) {
					if ($option.data('bkp-text')) {
						label = $option.data('bkp-text');
					} else {
						label = $option.text();
						$option.attr('data-bkp-text', label);
					}
					d = new Date();
					date_now = d.getFullYear() + '-' + (("0" + (d.getMonth()+1)).slice(-2)) + '-' + (("0" + (d.getDate())).slice(-2)) + 'T' + (("0" + (d.getHours())).slice(-2)) + ':' + (("0" + (d.getMinutes())).slice(-2)) + ':' + (("0" + (d.getSeconds())).slice(-2));
					time = geodir_search_format_time(d);
					open_now = geodir_search_params.open_now_format;
					open_now = open_now.replace("{label}", label);
					open_now = open_now.replace("{time}", time);
					$option.text(open_now);
					$option.closest('select').data('date-now',date_now);
					/* Searched label */
					$label = jQuery('.gd-adv-search-open_now .gd-adv-search-label-t');
					if (jQuery('.gd-adv-search-open_now').length && jQuery('.gd-adv-search-open_now').data('value') == 'now') {
						if ($label.data('bkp-text')) {
							label = $label.data('bkp-text');
						} else {
							label = $label.text();
							$label.attr('data-bkp-text', label);
						}
						open_now = geodir_search_params.open_now_format;
						open_now = open_now.replace("{label}", label);
						open_now = open_now.replace("{time}", time);
						$label.text(open_now);
					}
				}
			}

			function geodir_search_format_time(d) {
				var format = geodir_search_params.time_format, am_pm = eval(geodir_search_params.am_pm), hours, aL, aU;

				hours = d.getHours();
				if (hours < 12) {
					aL = 0;
					aU = 1;
				} else {
					hours = hours > 12 ? hours - 12 : hours;
					aL = 2;
					aU = 3;
				}

				time = format.replace("g", hours);
				time = time.replace("G", (d.getHours()));
				time = time.replace("h", ("0" + hours).slice(-2));
				time = time.replace("H", ("0" + (d.getHours())).slice(-2));
				time = time.replace("i", ("0" + (d.getMinutes())).slice(-2));
				time = time.replace("s", '');
				time = time.replace("a", am_pm[aL]);
				time = time.replace("A", am_pm[aU]);

				return time;
			}

			function geodir_search_deselect(el) {
				var fType = jQuery(el).prop('type');
				switch (fType) {
					case 'checkbox':
					case 'radio':
						jQuery(el).prop('checked', false);
						break;
				}
				jQuery(el).val('');
			}

			<?php if(0){ ?></script><?php }

		return ob_get_clean();
	}
}
