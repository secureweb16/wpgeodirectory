<?php

/**
 * GeoDir_Location_Widget_Locations class.
 *
 * @since 2.0.0
 */
class GeoDir_Location_Widget_Locations extends WP_Super_Duper {

	public $arguments;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		$options = array(
			'textdomain'     => 'geodirlocation',
			'block-icon'     => 'location-alt',
			'block-category' => 'geodirectory',
			'block-keywords' => "['geodirlocation','location','locations']",
			'class_name'     => __CLASS__,
			'base_id'        => 'gd_locations',
			'name'           => __( 'GD > Locations', 'geodirlocation' ),
			'widget_ops'     => array(
				'classname'     => 'geodir-lm-locations ' . geodir_bsui_class(),
				'description'   => esc_html__( 'Displays the locations.', 'geodirlocation' ),
				'gd_wgt_restrict' => '',
                'geodirectory' => true,
			)
		);

		parent::__construct( $options );
	}

	/**
	 * Set widget arguments.
	 *
	 */
	public function set_arguments() {
		$design_style = geodir_design_style();

		$arguments = array(
			'title'  => array(
				'title' => __('Title:', 'geodirlocation'),
				'desc' => __('The widget title.', 'geodirlocation'),
				'type' => 'text',
				'default'  => '',
				'desc_tip' => true,
				'advanced' => false
			),
			'what' => array(
				'type' => 'select',
				'title' => __( 'Show Locations:', 'geodirlocation' ),
				'desc' => __( 'Select which locations to show in a list. Default: Cities', 'geodirlocation' ),
				'placeholder' => '',
				'default' => 'city',
				'options' =>  array(
					"city" => __( 'Cities', 'geodirlocation' ),
					"region" => __( 'Regions', 'geodirlocation' ),
					"country" => __( 'Countries', 'geodirlocation' ),
					"neighbourhood" => __( 'Neighbourhoods', 'geodirlocation' ),
				),
				'desc_tip' => true,
				'advanced' => false,
			),
			'slugs'  => array(
				'type' => 'text',
				'title' => __( 'Location slugs:', 'geodirlocation' ),
				'desc' => __( 'To show specific locations, enter comma separated location slugs for the option selected in "Show Locations". Ex: new-york,london', 'geodirlocation' ),
				'placeholder' => '',
				'default' => '',
				'desc_tip' => true,
				'advanced' => false
			),
			'output_type'  => array(
				'type' => 'select',
				'title' => __('Output type', 'geodirlocation'),
				'desc' => __('This determines the style of the output list.', 'geodirlocation'),
				'placeholder' => '',
				'default' => '',
				'options' =>  array(
					"" => __('List', 'geodirlocation'),
					"grid" => __('Image Grid', 'geodirlocation'),
				),
				'desc_tip' => true,
				'advanced' => true,
			),
			'fallback_image' => array(
				'type' => 'checkbox',
				'title' => __( "Show post image as a fallback?", 'geodirlocation' ),
				'desc' => __( "If location image not available then show last post image added under this location.", 'geodirlocation' ),
				'desc_tip' => true,
				'value'  => '1',
				'default'  => '0',
				'advanced' => true,
				'element_require' => '[%output_type%]=="grid"',
			),
			'per_page'  => array(
				'type' => 'number',
				'title' => __('Number of locations:', 'geodirlocation'),
				'desc' => __('Number of locations to be shown on each page. Use 0(zero) or ""(blank) to show all locations.', 'geodirlocation'),
				'placeholder' => '',
				'default' => '',
				'desc_tip' => true,
				'advanced' => false,
				'element_require' => '![%slugs%]'
			),
			'pagi_t'  => array(
				'title' => __("Show pagination on top?", 'geodirlocation'),
				'type' => 'checkbox',
				'desc_tip' => false,
				'value'  => '1',
				'default'  => '0',
				'advanced' => true,
				'element_require' => '![%slugs%]'
			),
			'pagi_b'  => array(
				'title' => __("Show pagination at bottom?", 'geodirlocation'),
				'type' => 'checkbox',
				'desc_tip' => false,
				'value'  => '1',
				'default'  => '0',
				'advanced' => true,
				'element_require' => '![%slugs%]'
			),
			'pagi_info'  => array(
				'type' => 'select',
				'title' => __('Show advanced pagination details:', 'geodirlocation'),
				'desc' => __('This will add extra pagination info like "Showing locations x-y of z" after/before pagination.', 'geodirlocation'),
				'placeholder' => '',
				'default' => '',
				'options' =>  array(
					"" => __('Never Display', 'geodirlocation'),
					"after" => __('After pagination', 'geodirlocation'),
					"before" => __('Before pagination', 'geodirlocation')
				),
				'desc_tip' => true,
				'advanced' => true,
				'element_require' => '![%slugs%]'
			),
			'no_loc'  => array(
				'title' => __("Disable location filter?", 'geodirlocation'),
				'desc' => __("Don't filter results for current location.", 'geodirlocation'),
				'type' => 'checkbox',
				'desc_tip' => true,
				'value'  => '1',
				'default'  => '0',
				'advanced' => false,
				'element_require' => '![%slugs%]'
			),
			'show_current' => array(
				'title' => __( 'Show current location only', 'geodirlocation' ),
				'desc' => __( 'Tick to show only current country / region / city / neighbourhood when location filter is active & country / region / city / neighbourhood is set.', 'geodirlocation' ),
				'type' => 'checkbox',
				'desc_tip' => true,
				'value' => '1',
				'default' => '0',
				'advanced' => false,
				'element_require' => '( ! ( ( typeof form != "undefined" && jQuery( form ).find( "[data-argument=no_loc]" ).find( "input[type=checkbox]" ).is( ":checked" ) ) || ( typeof props == "object" && props.attributes && props.attributes.no_loc ) ) ) && ![%slugs%]',
			),
			'country' => array(
				'type' => 'text',
				'title' => __( 'Country slug', 'geodirlocation' ),
				'desc' => __( 'Filter the locations by country slug when location filter enabled. Default: current country.', 'geodirlocation' ),
				'placeholder' => '',
				'desc_tip' => true,
				'value' => '',
				'default' => '',
				'advanced' => true,
				'element_require' => '[%what%]!="country" && ![%slugs%]',
			),
			'region' => array(
				'type' => 'text',
				'title' => __( 'Region slug', 'geodirlocation' ),
				'desc' => __( 'Filter the locations by region slug when location filter enabled. Default: current region.', 'geodirlocation' ),
				'placeholder' => '',
				'desc_tip' => true,
				'value' => '',
				'default' => '',
				'advanced' => true,
				'element_require' => '( [%what%]=="city" || [%what%]=="neighbourhood" ) && ![%slugs%]',
			),
			'city' => array(
				'type' => 'text',
				'title' => __( 'City slug', 'geodirlocation' ),
				'desc' => __( 'Filter the locations by city slug when location filter enabled. Default: current city.', 'geodirlocation' ),
				'placeholder' => '',
				'desc_tip' => true,
				'value' => '',
				'default' => '',
				'advanced' => true,
				'element_require' => '[%what%]=="neighbourhood" && ![%slugs%]',
			)
		);

		if ( $design_style ) {

			$arguments['type'] = array(
				'title' => __('Type', 'geodirectory'),
				'desc' => __('Select the badge type.', 'geodirectory'),
				'type' => 'select',
				'options'   =>  array(
					"" => __('Badge', 'geodirectory'),
					"pill" => __('Pill', 'geodirectory'),
					"link" => __('Button Link', 'geodirectory'),
					"button" => __('Button', 'geodirectory'),
				),
				'default'  => '',
				'desc_tip' => true,
				'advanced' => false,
				'group'     => __("Design","geodirectory")
			);

			$arguments['icon_class']  = array(
				'type' => 'text',
				'title' => __('Icon class:', 'geodirectory'),
				'desc' => __('You can show a font-awesome icon here by entering the icon class.', 'geodirectory'),
				'placeholder' => 'fas fa-caret-right',
				'default' => '',
				'desc_tip' => true,
				'group'     => __("Design","geodirectory")
			);

			$arguments['shadow'] = array(
				'title' => __('Shadow', 'geodirectory'),
				'desc' => __('Select the shadow badge type.', 'geodirectory'),
				'type' => 'select',
				'options'   =>  array(
					"" => __('None', 'geodirectory'),
					"small" => __('small', 'geodirectory'),
					"medium" => __('medium', 'geodirectory'),
					"large" => __('large', 'geodirectory'),
				),
				'default'  => '',
				'desc_tip' => true,
				'advanced' => false,
				'group'     => __("Design","geodirectory")
			);

			$arguments['color'] = array(
				'title' => __('Color', 'geodirectory'),
				'desc' => __('Select the the color.', 'geodirectory'),
				'type' => 'select',
				'options'   =>  array(
					                "" => __('Custom colors', 'geodirectory'),
				                )+geodir_aui_colors(true, true, true),
				'default'  => '',
				'desc_tip' => true,
				'advanced' => false,
				'group'     => __("Design","geodirectory")
			);


			$arguments['bg_color']  = array(
				'type' => 'color',
				'title' => __('Background color:', 'geodirectory'),
				'desc' => __('Color for the background.', 'geodirectory'),
				'placeholder' => '',
				'default' => '#0073aa',
				'desc_tip' => true,
				'group'     => __("Design","geodirectory"),
				'element_require' => $design_style ?  '[%color%]==""' : '',
			);
			$arguments['txt_color']  = array(
				'type' => 'color',
//			'disable_alpha'=> true,
				'title' => __('Text color:', 'geodirectory'),
				'desc' => __('Color for the text.', 'geodirectory'),
				'placeholder' => '',
				'desc_tip' => true,
				'default'  => '#ffffff',
				'group'     => __("Design","geodirectory"),
				'element_require' => $design_style ?  '[%color%]==""' : '',
			);
			$arguments['size']  = array(
				'type' => 'select',
				'title' => __('Size', 'geodirectory'),
				'desc' => __('Size of the item.', 'geodirectory'),
				'options' =>  array(
					"" => __('Default', 'geodirectory'),
					"h6" => __('XS (badge)', 'geodirectory'),
					"h5" => __('S (badge)', 'geodirectory'),
					"h4" => __('M (badge)', 'geodirectory'),
					"h3" => __('L (badge)', 'geodirectory'),
					"h2" => __('XL (badge)', 'geodirectory'),
					"h1" => __('XXL (badge)', 'geodirectory'),
					"btn-lg" => __('L (button)', 'geodirectory'),
					"btn-sm" => __('S (button)', 'geodirectory'),
				),
				'default' => '',
				'desc_tip' => true,
				'group'     => __("Design","geodirectory"),
			);

			$arguments['mt']  = geodir_get_sd_margin_input('mt');
			$arguments['mr']  = geodir_get_sd_margin_input('mr');
			$arguments['mb']  = geodir_get_sd_margin_input('mb');
			$arguments['ml']  = geodir_get_sd_margin_input('ml');
		}

		return $arguments;
	}

	public function output( $args = array(), $widget_args = array(), $content = '' ) {
		extract( $widget_args, EXTR_SKIP );

		$params = $args;

		/**
		 * Filter the widget title.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title The widget title. Default empty.
		 * @param array  $args An array of the widget's settings.
		 * @param mixed  $id_base The widget ID.
		 */
		$title = apply_filters('geodir_popular_location_widget_title', !empty($args['title']) ? $args['title'] : '', $args, $this->id_base);
		
		/**
		 * Filter the no. of locations to shows on each page.
		 *
		 * @since 1.5.0
		 *
		 * @param int   $per_page No. of locations to be displayed.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['per_page'] = apply_filters('geodir_popular_location_widget_per_page', !empty($args['per_page']) ? absint($args['per_page']) : '', $args, $this->id_base);
		
		/**
		 * Whether to show pagination on top of widget content.
		 *
		 * @since 1.5.0
		 *
		 * @param bool  $pagi_t If true then pagination displayed on top. Default false.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['pagi_t'] = apply_filters('geodir_popular_location_widget_pagi_top', !empty($args['pagi_t']) ? true : false, $args, $this->id_base);
		
		/**
		 * Whether to show pagination on bottom of widget content.
		 *
		 * @since 1.5.0
		 *
		 * @param bool  $pagi_b If true then pagination displayed on bottom. Default false.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['pagi_b'] = apply_filters('geodir_popular_location_widget_pagi_bottom', !empty($args['pagi_b']) ? true : false, $args, $this->id_base);
		
		/**
		 * Filter the position to display advanced pagination info.
		 *
		 * @since 1.5.0
		 *
		 * @param string  $pagi_info Position to display advanced pagination info.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['pagi_info'] = apply_filters('geodir_popular_location_widget_pagi_info', !empty($args['pagi_info']) ? $args['pagi_info'] : '', $args, $this->id_base);
		
		/**
		 * Whether to disable filter results for current location.
		 *
		 * @since 1.5.0
		 *
		 * @param bool  $no_loc If true then results not filtered for current location. Default false.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['no_loc'] = apply_filters('geodir_popular_location_widget_no_location_filter', !empty($args['no_loc']) ? true : false, $args, $this->id_base);

		/**
		 * Whether to show current country / region / city / neighbourhood only.
		 *
		 * @since 2.0.0.24
		 *
		 * @param bool  $show_current If true then it will show only current location. Default false.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['show_current'] = apply_filters( 'geodir_popular_location_widget_show_current_filter', ! empty( $args['show_current'] ) ? true : false, $args, $this->id_base );

		/**
		 * Whether to disable filter results for current location.
		 *
		 * @since 1.5.0
		 *
		 * @param bool  $output_type If true then results not filtered for current location. Default false.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['output_type'] = apply_filters('geodir_popular_location_widget_output_type_filter', !empty($args['output_type']) ? $args['output_type'] : 'list', $args, $this->id_base);

		/**
		 * Whether to show post image as a fallback image.
		 *
		 * @since 2.0.0.25
		 *
		 * @param bool  $fallback_image If true then show post image when location image not available. Default false.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['fallback_image'] = apply_filters( 'geodir_popular_location_widget_fallback_image_filter', ( ! empty( $args['fallback_image'] ) ? true : false ), $args, $this->id_base );
		
		$what = ! empty( $args['what'] ) && in_array( $args['what'], array( 'country', 'region', 'city', 'neighbourhood' ) ) ? $args['what'] : 'city';
		/**
		 * Filter which location to show in a list.
		 *
		 * @since 2.0.0.22
		 *
		 * @param string $what The locations to show. Default city.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['what'] = apply_filters( 'geodir_popular_location_widget_what_filter', $what, $args, $this->id_base );

		/**
		 * Filter location slugs.
		 *
		 * @since 2.1.0.4
		 *
		 * @param string $slugs Comma separated location slugs.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['slugs'] = apply_filters( 'geodir_popular_location_widget_slugs_filter', ( isset( $args['slugs'] ) ? trim( $args['slugs'] ) : '' ), $args, $this->id_base );

		/**
		 * Filter the locations by country.
		 *
		 * @since 2.0.0.22
		 *
		 * @param string $country The country.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['country'] = apply_filters( 'geodir_popular_location_widget_country_filter', ( ! empty( $args['country'] ) ? $args['country'] : '' ), $args, $this->id_base );

		/**
		 * Filter the locations by region.
		 *
		 * @since 2.0.0.22
		 *
		 * @param string $region The region.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['region'] = apply_filters( 'geodir_popular_location_widget_region_filter', ( ! empty( $args['region'] ) ? $args['region'] : '' ), $args, $this->id_base );

		/**
		 * Filter the locations by city.
		 *
		 * @since 2.0.0.22
		 *
		 * @param string $city The city.
		 * @param array $args An array of the widget's settings.
		 * @param mixed $id_base The widget ID.
		 */
		$params['city'] = apply_filters( 'geodir_popular_location_widget_city_filter', ( ! empty( $args['city'] ) ? $args['city'] : '' ), $args, $this->id_base );

		$design_style = geodir_design_style();

		if ( $design_style ) {
			$params['css_class'] = '';
			$params['css_class'] .= !empty( $params['button_class'] ) ? $params['button_class'] : '';
			// margins
			if ( !empty( $params['mt'] ) ) { $params['css_class'] .= " mt-".sanitize_html_class($params['mt'])." "; }
			if ( !empty( $params['mr'] ) ) { $params['css_class'] .= " mr-".sanitize_html_class($params['mr'])." "; }
			if ( !empty( $params['mb'] ) ) { $params['css_class'] .= " mb-".sanitize_html_class($params['mb'])." "; }
			if ( !empty( $params['ml'] ) ) { $params['css_class'] .= " ml-".sanitize_html_class($params['ml'])." "; }

			if(!empty($params['size'])){
				switch ($params['size']) {
					case 'h6': $params['size'] = 'h6';break;
					case 'h5': $params['size'] = 'h5';break;
					case 'h4': $params['size'] = 'h4';break;
					case 'h3': $params['size'] = 'h3';break;
					case 'h2': $params['size'] = 'h2';break;
					case 'h1': $params['size'] = 'h1';break;
					case 'btn-lg': $params['size'] = ''; $params['css_class'] = 'btn-lg';break;
					case 'btn-sm':$params['size'] = '';  $params['css_class'] = 'btn-sm';break;
					default:
						$params['size'] = '';
				}
			}
		}

		$params['widget_atts'] = $params;

		ob_start();
		?>
		<div class="geodir-category-list-in clearfix geodir-location-lity-type-<?php echo esc_attr( $params['output_type'] ); ?>">
		    <?php geodir_popular_location_widget_output( $params ); ?>
		</div>
		<?php
		$output = ob_get_clean();

		return $output;
	}	
}

