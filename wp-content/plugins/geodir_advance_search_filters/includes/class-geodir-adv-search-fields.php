<?php
/**
 * Advance search field class
 *
 * @class    GeoDir_Adv_Search_Fields
 * @author   AyeCode
 * @package  GeoDir_Advance_Search_Filters/Classes
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * GeoDir_Adv_Search_Fields class.
 */
class GeoDir_Adv_Search_Fields {

    public function __construct() {
    }

	public static function init() {
		add_action( 'geodir_advance_search_field_in_main_search_bar', array( __CLASS__, 'main_search_bar_setting' ), 10, 3 );

		add_action( 'geodir_before_search_form', array( __CLASS__, 'enqueue_search_scripts' ), 0 );
		add_action( 'geodir_before_search_form', 'geodir_search_add_to_main', 0 );
		add_action( 'geodir_after_search_form', 'geodir_advance_search_more_filters' );

		// advance search button
		add_action( 'geodir_after_search_button', array( __CLASS__, 'advance_search_button' ) );

		// output main search by fields
		add_filter( 'geodir_search_output_to_main_business_hours', array( __CLASS__, 'output_main_business_hours' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_categories', array( __CLASS__, 'output_main_categories' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_checkbox', array( __CLASS__, 'output_main_checkbox' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_datepicker', array( __CLASS__, 'output_main_datepicker' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_select', array( __CLASS__, 'output_main_select' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_address', array( __CLASS__, 'output_main_text' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_text', array( __CLASS__, 'output_main_text' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_email', array( __CLASS__, 'output_main_text' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_phone', array( __CLASS__, 'output_main_text' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_url', array( __CLASS__, 'output_main_text' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_time', array( __CLASS__, 'output_main_time' ), 10, 3 );
		add_filter( 'geodir_search_output_to_main_radio', array( __CLASS__, 'output_main_select' ), 10, 3 );

		// search form show more filters
		add_action( 'geodir_search_fields', 'geodir_show_filters_fields' );

		// output more filters fields
		add_filter( 'geodir_search_filter_field_output_business_hours', array( __CLASS__, 'output_field_business_hours' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_categories', array( __CLASS__, 'output_field_categories' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_checkbox', array( __CLASS__, 'output_field_checkbox' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_datepicker', array( __CLASS__, 'output_field_datepicker' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_distance', array( __CLASS__, 'output_field_distance' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_fieldset', array( __CLASS__, 'output_field_fieldset' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_multiselect', array( __CLASS__, 'output_field_multiselect' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_radio', array( __CLASS__, 'output_field_radio' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_select', array( __CLASS__, 'output_field_select' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_text', array( __CLASS__, 'output_field_text' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_email', array( __CLASS__, 'output_field_text' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_phone', array( __CLASS__, 'output_field_text' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_url', array( __CLASS__, 'output_field_text' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_address', array( __CLASS__, 'output_field_text' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_textarea', array( __CLASS__, 'output_field_textarea' ), 10, 3 );
		add_filter( 'geodir_search_filter_field_output_time', array( __CLASS__, 'output_field_time' ), 10, 3 );

		if ( is_admin() ) {
			add_action( 'geodir_after_post_type_deleted', array( __CLASS__, 'post_type_deleted' ), 10, 1 );
			add_action( 'geodir_after_custom_field_deleted', array( __CLASS__, 'custom_field_deleted' ), 1, 3 );
		}
	}

	public static function get_custom_field_meta( $column, $htmlvar_name, $post_type ) {
		global $wpdb;

		if ( empty( $column ) || empty( $htmlvar_name ) || empty( $post_type ) ) {
			return NULL;
		}

		if ( $htmlvar_name == 'sale_status' && ( $features = geodir_get_classified_statuses( $post_type ) ) ) {
			$options = __( 'Select Status', 'geodirectory' ) . '/';

			foreach ( $features as $value => $label ) {
				$options .= ',' . $label . '/' . $value;
			}

			return $options;
		}

		return $wpdb->get_var( $wpdb->prepare( "SELECT {$column} FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type = %s AND htmlvar_name = %s LIMIT 1", $post_type,$htmlvar_name ) );
	}

	public static function get_field_by_name( $htmlvar_name, $post_type = '' ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . GEODIR_ADVANCE_SEARCH_TABLE . " WHERE htmlvar_name = %s AND post_type = %s LIMIT 1", $htmlvar_name, $post_type ) );
	}

	public static function get_search_fields( $post_type = '' ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GEODIR_ADVANCE_SEARCH_TABLE . " WHERE post_type = %s ORDER BY sort_order ASC", $post_type ) );
	}

	public static function get_main_search_fields( $post_type = '' ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GEODIR_ADVANCE_SEARCH_TABLE . " WHERE post_type = %s ANd main_search = %d ORDER BY sort_order ASC", $post_type, '1' ) );
	}

	public static function get_search_custom_fields( $post_type = '' ) {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type = %s AND is_active = %d ORDER BY sort_order ASC", $post_type, '1' ) );

		$fields = array();
		if ( ! empty( $results ) ) {
			$checkbox_fields = self::checkbox_fields( $post_type );
		
			foreach( $results as $key => $field ) {
				$allow = false;
				if ( in_array( $field->field_type, array( 'address', 'categories', 'checkbox', 'datepicker', 'email', 'multiselect', 'phone', 'radio', 'select', 'text', 'textarea', 'time', 'url', 'business_hours' ) ) ) {
					$allow = true;
				}

				$allow = apply_filters( 'geodir_search_fields_setting_allow_var_' . $field->htmlvar_name, $allow, $field );
				$allow = apply_filters( 'geodir_search_fields_setting_allow', $allow, $field );

				if ( $allow ) {
					// Show special offers, video as a checkbox field.
					if ( ! empty( $checkbox_fields ) && in_array( $field->htmlvar_name, $checkbox_fields ) ) {
						$field->field_type = 'checkbox';
						$field->input_type = 'SINGLE';
						$field->data_type = 'TEXT';
						$field->search_condition = 'SINGLE';
					}

					if ( $field->htmlvar_name == 'business_hours' ) {
						$field->admin_title = __( 'Open Hours', 'geodiradvancesearch');
						$field->frontend_title = __( 'Open Hours', 'geodiradvancesearch');
					}

					$fields[] = $field;
				}
			}
		}

		return $fields;
	}

	public static function advance_search_button() {
		global $wpdb, $geodir_search_post_type;
		$stype = $geodir_search_post_type;
	
		if (empty($stype)){
			$stype = geodir_get_default_posttype();
		}

		$where = '';

		// Don't show distance field when service_distance active.
		if ( GeoDir_Post_types::supports( $stype, 'service_distance' ) ) {
			$where .= " AND `htmlvar_name` != 'distance'";
		}

		$rows = $wpdb->get_var($wpdb->prepare("SELECT count(id) FROM " . GEODIR_ADVANCE_SEARCH_TABLE . " where post_type= %s AND main_search!='1'{$where}",$stype) );
		if ($rows > 0) {
			$default_btn_value = '<i class="fas fa-cog" aria-hidden="true"></i>';
			$btn_value = apply_filters('gd_adv_search_btn_value', $default_btn_value);
			$fa_class = '';
			if(strpos($btn_value, '&#') !== false){
				$fa_class = 'fas';
			}

			$design_style = geodir_design_style();
			if($design_style){
				echo '<div class="gd-search-field-search gd-search-field-search-filters col-auto flex-grow-1">';
					echo '<div class="form-group">';
						echo aui()->button(
							array(
								'type'       => 'button',
								'class'      => 'geodir-show-filters btn btn-primary w-100 ',
								'content'    => $btn_value,
								'aria-label' => __( 'Advanced Filters', 'geodiradvancesearch' ),
								'extra_attributes'  => array(
									'onclick'   => 'jQuery(this).closest(\'.geodir-listing-search\').find(\'.geodir-more-filters\').collapse(\'toggle\')',
//									'data-toggle'   => 'collapse',
//									'data-target'   => '.geodir-more-filters',
								)
							)
						);
					echo "</div>";
				echo "</div>";
			}else{
				echo '<button class="geodir-show-filters ' . $fa_class . '" aria-label="' . esc_attr( $btn_value ) . '" onclick="geodir_search_show_filters(this); return false;">' . $btn_value . '</button>';
			}
		}
	}

	public static function main_search_bar_setting( $show, $field, $cf = array() ) {
		if ( ! empty( $field ) && ( $field->field_type == 'categories' || $field->field_type == 'select' || $field->field_type == 'radio' || $field->field_type == 'checkbox' || $field->field_type == 'datepicker' || $field->field_type == 'time' || ( $field->field_type == 'text' && $field->data_type == 'FLOAT' ) || $field->field_type == 'business_hours' ) ) { 
			$show = true;
		}

		return $show;
	}

	public static function order_terms_heretically($terms,$parent = '0',$level = 0){
		$terms_temp = array();
		$level++;

		foreach ( $terms as $term ) {
			if ( $term->parent == $parent && $term->term_id != $parent) {
				$terms_temp[] = $term;


				$child_terms = self::order_terms_heretically($terms,$term->term_id,$level);

				if(!empty($child_terms)){
					foreach($child_terms as $child_term){
						$child_term->name  = str_repeat("- ", $level)  . $child_term->name;
						$terms_temp[] = $child_term;
					}
				}

			}

		}


		return $terms_temp;

	}

	public static function output_main_categories( $html, $cf, $post_type ) {
		$design_style = geodir_design_style();

		$cf->input_type = 'SELECT';

		$args = array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true );

		$args = apply_filters( 'geodir_filter_term_args', $args, $post_type . 'category' );

		$terms = apply_filters( 'geodir_filter_terms', get_terms( $post_type . 'category', $args ) );

		// let's order the child categories below the parent.
		$terms = self::order_terms_heretically( $terms );

		$frontend_title = ! empty( $cf->frontend_title ) ? stripslashes( __( $cf->frontend_title, 'geodirectory' ) ) : __( 'Category', 'geodirectory' );

		if ( $design_style ) {
			$html .= "<div class='gd-search-field-taxonomy gd-search-field-categories col-auto flex-fill'>";

			$cats = array( '' => esc_attr( $frontend_title ) );

			if(!empty($terms)){
				foreach($terms as $term){
					$cats[$term->term_id] = __($term->name, 'geodirectory' );
				}
			}

			$value = !empty($_REQUEST['spost_category']) ? absint($_REQUEST['spost_category'][0]) : '';

			$html .=  aui()->select( array(
				'id'               => "geodir_search_post_category",
				'name'             => "spost_category[]",
				'class'            => 'mw-100',
				'label'            => $frontend_title,
				'placeholder'      => esc_attr( $frontend_title ),
				'value'            => $value ,
				'options'          => $cats,
			) );
			$html .= "</div>";
		} else {
			$html .= "<div class='gd-search-input-wrapper gd-search-field-cpt gd-search-field-taxonomy gd-search-field-categories'>";
			$html .= str_replace( array( '<li>', '</li>' ), '', geodir_advance_search_options_output( $terms, $cf, $post_type, $frontend_title ) );
			$html .= "</div>";
		}

		return $html;
	}

	public static function output_main_checkbox( $html, $cf, $post_type) {
		$cf->input_type = 'SELECT';

		$terms = array();
		$terms[] = array(
			'label' => __('Yes','geodiradvancesearch'),
			'value' => 1,
			'optgroup' => ''
		);

		$design_style = geodir_design_style();

		$main_class = $design_style && !empty($cf->main_search) ? 'col-auto flex-fill' : '';

		$html .= "<div class='gd-search-input-wrapper gd-search-field-cpt gd-search-" . $cf->htmlvar_name . " $main_class'>";
		$output = $design_style ? geodir_advance_search_options_output_aui( $terms, $cf, $post_type, stripslashes( __( $cf->frontend_title, 'geodirectory' ) ) ) : geodir_advance_search_options_output( $terms, $cf, $post_type, stripslashes( __( $cf->frontend_title, 'geodirectory' ) ) );
		$html .= str_replace(array('<li>','</li>'),'',$output );
		$html .= "</div>";


		return $html;
	}

	public static function output_main_datepicker( $html, $field, $post_type ) {
		global $as_fieldset_start;

		$pt_name = geodir_post_type_singular_name( $post_type, true );
		$htmlvar_name = $field->htmlvar_name;
		$field_label = $field->frontend_title ? stripslashes( __( $field->frontend_title, 'geodirectory' ) ) : '';
		$field_value = isset( $_REQUEST[ $htmlvar_name ] ) ? $_REQUEST[ $htmlvar_name ] : '';
		$has_fieldset = empty( $field->main_search ) && $as_fieldset_start > 0 ? true : false;

		$cf = geodir_get_field_infoby( 'htmlvar_name', $field->htmlvar_name, $post_type );
		$extra_fields = ! empty( $cf->extra_fields ) ? maybe_unserialize( $cf->extra_fields ) : NULL;

		$date_format = ! empty( $extra_fields['date_format'] ) ? $extra_fields['date_format'] : geodir_date_format();

		// Convert to jQuery UI datepicker format.
		$jqueryui_date_format  = geodir_date_format_php_to_jqueryui( $date_format  );

		$design_style = geodir_design_style();

		ob_start();
		if($design_style ){
			if ( $field->search_condition == 'FROM' ) {
				$field_value_from = '';
				$field_value_to = '';
				$field_value_from_display = '';
				$field_value_to_display = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $date_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $date_format, strtotime( $field_value_to ) );
					}
				}

				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Dates', 'geodiradvancesearch' ), $pt_name );
				}
				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Date', 'geodiradvancesearch' ), $pt_name );
				$field_label_to = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Date', 'geodiradvancesearch' ), $pt_name );
				$aria_label_from = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label_from ) . '"' : '';
				$aria_label_to = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label_to ) . '"' : '';
				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-date gd-search-<?php echo $htmlvar_name; ?> from-to col-auto flex-fill">
					<?php if ( ! empty( $field_label ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>" class="sr-only"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = $date_format;
					$extra_attributes['data-date-format'] = 'Y-m-d';

					// range
					$extra_attributes['data-mode'] = 'range';
					echo aui()->input(
						array(
							'id'                => $htmlvar_name,
							'name'              => $htmlvar_name,
							'type'              => 'datepicker',
							'placeholder'       => $field_label,
							'class'             => '',
							'value'             => esc_attr($field_value),
							'extra_attributes'  => $extra_attributes
						)
					);
					?>
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Date', 'geodiradvancesearch' ), $pt_name );
				}
				$aria_label = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label ) . '"' : '';
				$field_value = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				$field_value_display = ! empty( $field_value ) ? date_i18n( $date_format, strtotime( $field_value ) ) : '';


				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-date gd-search-<?php echo $htmlvar_name; ?> col-auto flex-fill">
					<?php if ( ! empty( $field_label ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>" class="sr-only"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = $date_format;
					$extra_attributes['data-date-format'] = 'Y-m-d';
					echo aui()->input(
						array(
							'id'                => $htmlvar_name,
							'name'              => $htmlvar_name,
							'type'              => 'datepicker',
							'placeholder'       => $field_label,
							'class'             => '',
							'value'             => esc_attr($field_value),
							'extra_attributes'  => $extra_attributes
						)
					);

					?>
				</div>
				<?php
			}
		}else{
			if ( $field->search_condition == 'FROM' ) {
				$field_value_from = '';
				$field_value_to = '';
				$field_value_from_display = '';
				$field_value_to_display = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $date_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $date_format, strtotime( $field_value_to ) );
					}
				}

				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Date', 'geodiradvancesearch' ), $pt_name );
				$field_label_to = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Date', 'geodiradvancesearch' ), $pt_name );
				?>
				<div class="gd-search-input-wrapper gd-search-field-cpt gd-search-has-date gd-search-<?php echo esc_attr( $htmlvar_name ); ?>-from">
					<input type="text" value="<?php echo esc_attr( $field_value_from_display ); ?>" placeholder="<?php echo esc_attr( $field_label_from ); ?>" class="cat_input gd-search-date-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[from]" data-date-format="<?php echo esc_attr( $jqueryui_date_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>" aria-label="<?php echo esc_attr( $field_label_from ); ?>"/><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[from]" value="<?php echo esc_attr( $field_value_from ); ?>">
				</div>
				<div class="gd-search-input-wrapper gd-search-field-cpt gd-search-has-date gd-search-<?php echo esc_attr( $htmlvar_name ); ?>-to">
					<input type="text" value="<?php echo esc_attr( $field_value_to_display ); ?>" placeholder="<?php echo esc_attr( $field_label_to ); ?>" class="cat_input gd-search-date-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[to]" data-date-format="<?php echo esc_attr( $jqueryui_date_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>" aria-label="<?php echo esc_attr( $field_label_to ); ?>"/><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[to]" value="<?php echo esc_attr( $field_value_to ); ?>">
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Date', 'geodiradvancesearch' ), $pt_name );
				}
				$field_value = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				$field_value_display = ! empty( $field_value ) ? date_i18n( $date_format, strtotime( $field_value ) ) : '';
				?>
				<div class="gd-search-input-wrapper gd-search-field-cpt gd-search-has-date gd-search-<?php echo esc_attr( $htmlvar_name ); ?>">
					<input type="text" value="<?php echo esc_attr( $field_value_display ); ?>" placeholder="<?php echo esc_attr( $field_label ); ?>" class="cat_input gd-search-date-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>" data-date-format="<?php echo esc_attr( $jqueryui_date_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>" aria-label="<?php echo esc_attr( $field_label ); ?>"><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
				</div>
				<?php
			}
		}


		$html .= ob_get_clean();

		return $html;
	}

	public static function output_main_text( $html, $cf, $post_type ) {
		$design_style = geodir_design_style();
		$terms = array( 1 );
		$main_class = $design_style && ! empty( $cf->main_search ) ? ' col-auto flex-fill' : '';
		if ( $cf->htmlvar_name == 'address' ) {
			$cf->htmlvar_name = 'street';
		}

		$html .= "<div class='gd-search-input-wrapper gd-search-field-cpt gd-search-" . $cf->htmlvar_name . $main_class . "'>";
		$output = $design_style ? geodir_advance_search_options_output_aui( $terms, $cf, $post_type, stripslashes( __( $cf->frontend_title, 'geodirectory' ) ) ) : geodir_advance_search_options_output( $terms, $cf, $post_type, stripslashes( __( $cf->frontend_title, 'geodirectory' ) ) );
		$html .= str_replace( array( '<li>', '</li>' ), '', $output );
		$html .= "</div>";

		return $html;
	}

	public static function output_main_time( $html, $field, $post_type ) {
		global $as_fieldset_start;

		$pt_name = geodir_post_type_singular_name( $post_type, true );
		$htmlvar_name = $field->htmlvar_name;
		$field_label = $field->frontend_title ? stripslashes( __( $field->frontend_title, 'geodirectory' ) ) : '';
		$field_value = isset( $_REQUEST[ $htmlvar_name ] ) ? $_REQUEST[ $htmlvar_name ] : '';
		$has_fieldset = empty( $field->main_search ) && $as_fieldset_start > 0 ? true : false;

		$cf = geodir_get_field_infoby( 'htmlvar_name', $field->htmlvar_name, $post_type );
		$extra_fields = ! empty( $cf->extra_fields ) ? maybe_unserialize( $cf->extra_fields ) : NULL;
		
		$time_format = ! empty( $extra_fields['time_format'] ) ? $extra_fields['time_format'] : geodir_time_format();

		// Convert to jQuery UI timepicker format.
		$jqueryui_time_format  = geodir_date_format_php_to_jqueryui( $time_format  );

		$design_style = geodir_design_style();

		ob_start();

		if($design_style){
			if ( $field->search_condition == 'FROM' ) {
				$field_value_from         = '';
				$field_value_to           = '';
				$field_value_from_display = '';
				$field_value_to_display   = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from         = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $time_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to         = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $time_format, strtotime( $field_value_to ) );
					}
				}

				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Time', 'geodiradvancesearch' ), $pt_name );
				$field_label_to   = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Time', 'geodiradvancesearch' ), $pt_name );

				// flatpickr attributes
				$extra_attributes['data-enable-time'] = 'true';
				$extra_attributes['data-no-calendar'] = 'true';
				$extra_attributes['data-date-format'] = 'Hi';

				$extra_attributes['data-alt-input'] = 'true';
				$extra_attributes['data-alt-format'] = geodir_search_input_time_format( true );
				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-time gd-search-<?php echo $htmlvar_name; ?> from-to col-auto flex-fill">
					<?php if ( ! empty( $field_label) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>" class="sr-only"><?php echo $field_label; ?></label>
					<?php }
					echo aui()->input(
						array(
							'id'                => $htmlvar_name . "_from",
							'name'              => $htmlvar_name . "[from]",
							'type'              => 'timepicker',
							'placeholder'       => esc_attr( $field_label_from ),
							'class'             => 'rounded-left',
							'value'             => esc_attr( $field_value_from ),
							'extra_attributes'  => $extra_attributes,
							'input_group_right'        => '<div class="input-group-text px-2 bg-transparent border-0x" onclick="jQuery(this).parent().parent().find(\'input\').val(\'\');"><i class="fas fa-times geodir-search-input-label-clear text-muted c-pointer" title="' . __( 'Clear field', 'geodiradvancesearch' ) . '" ></i></div>',
						)
					);
					?>
				</div>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-time gd-search-<?php echo $htmlvar_name; ?> from-to col-auto flex-fill">
					<?php
					echo aui()->input(
						array(
							'id'                => $htmlvar_name . "_to",
							'name'              => $htmlvar_name . "[to]",
							'type'              => 'timepicker',
							'placeholder'       => esc_attr( $field_label_to ),
							'class'             => 'rounded-left',
							'value'             => esc_attr( $field_value_to ),
							'extra_attributes'  => $extra_attributes,
							'input_group_right'        => '<div class="input-group-text px-2 bg-transparent border-0x" onclick="jQuery(this).parent().parent().find(\'input\').val(\'\');"><i class="fas fa-times geodir-search-input-label-clear text-muted c-pointer" title="' . __( 'Clear field', 'geodiradvancesearch' ) . '" ></i></div>',
						)
					);
					?>
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_value         = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-time gd-search-<?php echo $htmlvar_name; ?> col-auto flex-fill">
					<?php if ( ! empty( $field_label) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>" class="sr-only"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-enable-time'] = 'true';
					$extra_attributes['data-no-calendar'] = 'true';
					$extra_attributes['data-date-format'] = 'Hi';

					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = geodir_search_input_time_format( true );

					echo aui()->input(
						array(
							'id'                => $htmlvar_name,
							'name'              => $htmlvar_name,
							'required'          => !empty($cf['is_required']) ? true : false,
							'type'              => 'timepicker',
							'placeholder'       => esc_attr( $field_label ),
							'class'             => '',
							'value'             => esc_attr( $field_value ),
							'extra_attributes'  => $extra_attributes
						)
					);
					?>
				</div>
				<?php
			}
		}else{
			if ( $field->search_condition == 'FROM' ) {
				$field_value_from = '';
				$field_value_to = '';
				$field_value_from_display = '';
				$field_value_to_display = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $time_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $time_format, strtotime( $field_value_to ) );
					}
				}

				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Time', 'geodiradvancesearch' ), $pt_name );
				$field_label_to = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Time', 'geodiradvancesearch' ), $pt_name );
				?>
				<div class="gd-search-input-wrapper gd-search-field-cpt gd-search-has-time gd-search-<?php echo $htmlvar_name; ?>-from">
					<input type="text" value="<?php echo esc_attr( $field_value_from_display ); ?>" placeholder="<?php echo esc_attr( $field_label_from ); ?>" class="cat_input gd-search-time-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[from]" data-time-format="<?php echo esc_attr( $jqueryui_time_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"/><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[from]" value="<?php echo esc_attr( $field_value_from ); ?>">
				</div>
				<div class="gd-search-input-wrapper gd-search-field-cpt gd-search-has-time gd-search-<?php echo $htmlvar_name; ?>-to">
					<input type="text" value="<?php echo esc_attr( $field_value_to_display ); ?>" placeholder="<?php echo esc_attr( $field_label_to ); ?>" class="cat_input gd-search-time-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[to]" data-time-format="<?php echo esc_attr( $jqueryui_time_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"/><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[to]" value="<?php echo esc_attr( $field_value_to ); ?>">
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_value = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				$field_value_display = ! empty( $field_value ) ? date_i18n( $time_format, strtotime( $field_value ) ) : '';
				?>
				<div class="gd-search-input-wrapper gd-search-field-cpt gd-search-has-time gd-search-<?php echo esc_attr( $htmlvar_name ); ?>">
					<input type="text" value="<?php echo esc_attr( $field_value_display ); ?>" placeholder="<?php echo esc_attr( $field_label ); ?>" class="cat_input gd-search-time-input" field_type="text" data-default-value="<?php echo esc_attr( $field_value_display ); ?>" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>" data-time-format="<?php echo esc_attr( $jqueryui_time_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
				</div>
				<?php
			}
		}


		$html .= ob_get_clean();

		return $html;
	}

	public static function output_main_select($html,$cf,$post_type){

		$cf->input_type = 'SELECT';

		global $wpdb;
		$select_fields_result = $wpdb->get_row( $wpdb->prepare( "SELECT option_values  FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type = %s and htmlvar_name=%s  ORDER BY sort_order", array(
			$post_type,
			$cf->htmlvar_name
		) ) );
		if ( in_array( $cf->input_type, array(
			'CHECK',
			'SELECT',
			'LINK',
			'RADIO'
		) ) ) {
			if ( $cf->htmlvar_name == 'sale_status' && ( $features = geodir_get_classified_statuses( $cf->post_type ) ) ) {
				$options = __( 'Select Status', 'geodirectory' ) . '/';
				foreach ( $features as $feature_value => $feature_label ) {
					$options .= ',' . $feature_label . '/' . $feature_value;
				}
				$select_fields_result->option_values = $options;
			}

			// optgroup
			$terms = geodir_string_values_to_options( $select_fields_result->option_values, true );
		} else {
			$terms = explode( ',', $select_fields_result->option_values );
		}

		$design_style = geodir_design_style();

		$main_class = $design_style && !empty($cf->main_search) ? 'col-auto flex-fill' : '';

		$html .= "<div class='gd-search-input-wrapper gd-search-field-cpt gd-search-" . $cf->htmlvar_name . " $main_class'>";
		$output = $design_style ? geodir_advance_search_options_output_aui( $terms, $cf, $post_type, stripslashes( __( $cf->frontend_title, 'geodirectory' ) )) : geodir_advance_search_options_output( $terms, $cf, $post_type, stripslashes( __( $cf->frontend_title, 'geodirectory' ) ));
		$html .= str_replace(array('<li>','</li>'),'',$output);
		$html .= "</div>";

		return $html;
	}

	public static function output_main_business_hours( $html, $cf, $post_type ) {
		$options = array(
			$options[] = array(
				'label' => __( 'Yes', 'geodiradvancesearch' ),
				'value' => 1,
				'optgroup' => ''
			) 
		);

		$frontend_title = ! empty( $cf->frontend_title ) ? __( stripslashes( $cf->frontend_title ), 'geodirectory' ) : '';
		$design_style = geodir_design_style();

		if ( $design_style ) {
			$html .= "<div class='gd-search-input-wrapper gd-search-field-cpt gd-search-" . $cf->htmlvar_name . " col-auto flex-fill'>";
			$html .=  aui()->select( array(
				'id'               => "geodir_search_open_now",
				'name'             => "sopen_now",
				'class'             => 'mw-100',
				'label'             => $frontend_title,
				'placeholder'      => esc_attr( $frontend_title ),
				'value'            => !empty($_REQUEST['sopen_now']) ? esc_attr($_REQUEST['sopen_now']) : '',
				'options'          => GeoDir_Adv_Search_Business_Hours::business_hours_options( $frontend_title ),
			) );
			$html .= "</div>";
		} else {
			$html .= "<div class='gd-search-input-wrapper gd-search-field-cpt gd-search-" . $cf->htmlvar_name . "'>";
			$html .= str_replace( array( '<li>', '</li>' ), '', geodir_advance_search_options_output( $options, $cf, $post_type, $frontend_title ) );
			$html .= "</div>";
		}

		return $html;
	}

	// more filters fields

	public static function field_wrapper_start( $field_info ) {
		global $as_fieldset_start;

		$design_style = geodir_design_style();

		if ( $as_fieldset_start > 0 ) {
			$html = '';
		} else {
			if ( $design_style ) {
				$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
				$htmlvar     = $field_info->htmlvar_name;
				$html        = '<div class="geodir-filter-cat gd-type-single gd-field-t' . esc_attr( $field_info->field_type ) . ' gd-field-' . esc_attr( $htmlvar ) . ' col mb-3" style="min-width:200px;">';
				if ( $field_info->field_type != 'checkbox' ) {
					$label_for   =  'for="geodir_search_' . $htmlvar . '"';
					if ( $htmlvar == 'business_hours' || $htmlvar == 'distance' ) {
						$label_for = '';
					}
					$html .= '<label ' . $label_for . ' class="text-muted">' . stripslashes( __( $field_label, 'geodirectory' ) ) . '</label>';
				}
			} else {
				$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
				$htmlvar     = $field_info->htmlvar_name;
				$html  = '<div class="geodir-filter-cat gd-type-single gd-field-t' . esc_attr( $field_info->field_type ) . ' gd-field-' . esc_attr( $htmlvar ) . '">';
				if ( $field_info->field_type != 'checkbox' ) {
					$html .= '<span>' . stripslashes( __( $field_label, 'geodirectory' ) ) . '</span>';
				}
				$html .= '<ul>';
			}
		}

		return $html;
	}

	public static function field_wrapper_end( $field_info ) {
		global $as_fieldset_start;

		$design_style = geodir_design_style();

		if ( $as_fieldset_start > 0 ) {
			$html = '';
		} else {
			if($design_style){
				$html = '</div>';
			}else{
				$html = '</ul></div>';
			}

		}

		return $html;
	}

	/**
	 * Get the html output for the custom search field: categories.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_categories( $html, $field_info, $post_type ) {

		if ( $field_info->input_type == 'SELECT' ) {
			$args = array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true );
		} else {
			$args = array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true );
		}

		/**
		 * Filters the `$args` get_terms function.
		 *
		 * @since 1.4.0
		 *
		 * @param array $args        Args array for get_terms function.
		 * @param string $field_info ->htmlvar_name Taxonomy name for get_terms function.
		 *
		 * @return array Modified $args array
		 */
		$args = apply_filters( 'geodir_filter_term_args', $args, $field_info->htmlvar_name );
		/**
		 * Filters the array returned by get_terms function.
		 *
		 * @since 1.0.0
		 *
		 * @param string $field_info ->htmlvar_name Taxonomy name for get_terms function.
		 *
		 * @return array|int|WP_Error List of WP_Term instances and their children.
		 */
		$terms = apply_filters( 'geodir_filter_terms', get_terms( $post_type.'category', $args ) );

		// let's order the child categories below the parent.
		$terms_temp = array();

		foreach ( $terms as $term ) {
			if ( $term->parent == '0' ) {
				$terms_temp[] = $term;

				foreach ( $terms as $temps ) {
					if ( $temps->parent != '0' && $temps->parent == $term->term_id ) {
						$temps->name  = '- ' . $temps->name;
						$terms_temp[] = $temps;
					}
				}
			}
		}

		$terms = $terms_temp;

		$html .= self::field_wrapper_start( $field_info );
		$html .= geodir_design_style() ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type ) : geodir_advance_search_options_output( $terms, $field_info, $post_type );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_categories', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: checkbox.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_checkbox( $html, $field_info, $post_type ) {
		global $as_fieldset_start;

		$search_val = geodir_search_get_field_search_param( $field_info->htmlvar_name );

		$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
		$field_label = stripslashes( __( $field_label, 'geodirectory' ) ); // via db translation.
		$has_fieldset = empty( $field_info->main_search ) && $as_fieldset_start > 0 ? true : false;
		$field_class = 'gd-search-li-' . (int) $has_fieldset . ' gd-search-li-' . $field_info->htmlvar_name . ' gd-field-t' . $field_info->field_type;;

		$checked = '';
		if ( $search_val == '1' ) {
			$checked = 'checked="checked"';
		}

		$field_label_text = __( $field_label, 'geodirectory' );

		$design_style = geodir_design_style();

		$html .= self::field_wrapper_start( $field_info );
		if ( $design_style ) {
			if ( $has_fieldset ) {
				$field_class .= ' form-group';
			}
			$html .= '<div class="' . esc_attr( $field_class ) . '"><div class="form-check"><input ' . $checked . ' type="' . esc_attr( $field_info->field_type ) . '" class="form-check-input" name="s' . esc_attr( $field_info->htmlvar_name ) . '"  value="1" id="geodir_search_' . esc_attr( $field_info->htmlvar_name ) . '" /> <label for="geodir_search_' . esc_attr( $field_info->htmlvar_name ) . '" class="form-check-label text-muted">' . $field_label_text . '</label></div></div>';
		} else {
			$html .= '<li class="' . esc_attr( $field_class ) . '"><input ' . $checked . ' type="' . esc_attr( $field_info->field_type ) . '" class="cat_input" name="s' . esc_attr( $field_info->htmlvar_name ) . '"  value="1" id="geodir_search_' . esc_attr( $field_info->htmlvar_name ) . '" /> <label for="geodir_search_' . esc_attr( $field_info->htmlvar_name ) . '">' . $field_label_text . '</label></li>';
		}
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_checkbox', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: datepicker.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_datepicker( $html, $field, $post_type ) {
		global $as_fieldset_start;

		$pt_name = geodir_post_type_singular_name( $post_type, true );
		$htmlvar_name = $field->htmlvar_name;
		$field_label = $field->frontend_title ? stripslashes( __( $field->frontend_title, 'geodirectory' ) ) : '';
		$field_value = isset( $_REQUEST[ $htmlvar_name ] ) ? $_REQUEST[ $htmlvar_name ] : '';
		$has_fieldset = empty( $field->main_search ) && $as_fieldset_start > 0 ? true : false;

		$cf = geodir_get_field_infoby( 'htmlvar_name', $htmlvar_name, $post_type );
		$extra_fields = ! empty( $cf->extra_fields ) ? maybe_unserialize( $cf->extra_fields ) : NULL;
		
		$date_format = ! empty( $extra_fields['date_format'] ) ? $extra_fields['date_format'] : geodir_date_format();

		// Convert to jQuery UI datepicker format.
		$jqueryui_date_format  = geodir_date_format_php_to_jqueryui( $date_format  );

		$design_style = geodir_design_style();
		
		$html .= self::field_wrapper_start( $field );

		ob_start();
		if($design_style){

			if ( $field->search_condition == 'FROM' ) {
				$field_value_from = '';
				$field_value_to = '';
				$field_value_from_display = '';
				$field_value_to_display = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $date_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $date_format, strtotime( $field_value_to ) );
					}
				}

				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Dates', 'geodiradvancesearch' ), $pt_name );
				}
				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Date', 'geodiradvancesearch' ), $pt_name );
				$field_label_to = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Date', 'geodiradvancesearch' ), $pt_name );
				$aria_label_from = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label_from ) . '"' : '';
				$aria_label_to = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label_to ) . '"' : '';
				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-date gd-search-<?php echo $htmlvar_name; ?> from-to">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>" class="text-muted"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = $date_format;
					$extra_attributes['data-date-format'] = 'Y-m-d';

					// range
					$extra_attributes['data-mode'] = 'range';
					echo aui()->input(
						array(
							'id'                => $htmlvar_name,
							'name'              => $htmlvar_name,
							'type'              => 'datepicker',
							'placeholder'       => $field_label,
							'class'             => '',
							'value'             => esc_attr($field_value),
							'extra_attributes'  => $extra_attributes
						)
					);
					?>
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Date', 'geodiradvancesearch' ), $pt_name );
				}
				$aria_label = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label ) . '"' : '';
				$field_value = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				$field_value_display = ! empty( $field_value ) ? date_i18n( $date_format, strtotime( $field_value ) ) : '';


				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-date gd-search-<?php echo esc_attr( $htmlvar_name ); ?>">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = $date_format;
					$extra_attributes['data-date-format'] = 'Y-m-d';
					echo aui()->input(
						array(
							'id'                => $htmlvar_name,
							'name'              => $htmlvar_name,
							'type'              => 'datepicker',
							'placeholder'       => $field_label,
							'class'             => '',
							'value'             => esc_attr($field_value),
							'extra_attributes'  => $extra_attributes
						)
					);

					?>
				</div>
				<?php
			}

		}else{


			?><li class="gd-search-row-<?php echo esc_attr( $htmlvar_name ); ?>"><?php
			if ( $field->search_condition == 'FROM' ) {
				$field_value_from = '';
				$field_value_to = '';
				$field_value_from_display = '';
				$field_value_to_display = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $date_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $date_format, strtotime( $field_value_to ) );
					}
				}

				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Dates', 'geodiradvancesearch' ), $pt_name );
				}
				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Date', 'geodiradvancesearch' ), $pt_name );
				$field_label_to = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Date', 'geodiradvancesearch' ), $pt_name );
				$aria_label_from = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label_from ) . '"' : '';
				$aria_label_to = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label_to ) . '"' : '';
				?>
				<div class="gd-search-has-date gd-search-<?php echo $htmlvar_name; ?> from-to">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>_from"><?php echo $field_label; ?></label>
					<?php } ?>
					<input type="text" id="<?php echo esc_attr( $htmlvar_name ); ?>_from" value="<?php echo esc_attr( $field_value_from_display ); ?>" placeholder="<?php echo esc_attr( $field_label_from ); ?>" class="cat_input gd-search-date-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[from]" data-date-format="<?php echo esc_attr( $jqueryui_date_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"<?php echo $aria_label_from; ?>/>
					<input type="text" id="<?php echo esc_attr( $htmlvar_name ); ?>_to" value="<?php echo esc_attr( $field_value_to_display ); ?>" placeholder="<?php echo esc_attr( $field_label_to ); ?>" class="cat_input gd-search-date-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[to]" data-date-format="<?php echo esc_attr( $jqueryui_date_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"<?php echo $aria_label_to; ?>/>
					<input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[from]" value="<?php echo esc_attr( $field_value_from ); ?>"><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[to]" value="<?php echo esc_attr( $field_value_to ); ?>">
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Date', 'geodiradvancesearch' ), $pt_name );
				}
				$aria_label = empty( $as_fieldset_start ) ? ' aria-label="' . esc_attr( $field_label ) . '"' : '';
				$field_value = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				$field_value_display = ! empty( $field_value ) ? date_i18n( $date_format, strtotime( $field_value ) ) : '';
				?>
				<div class="gd-search-has-date gd-search-<?php echo esc_attr( $htmlvar_name ); ?>">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>"><?php echo $field_label; ?></label>
					<?php } ?>
					<input type="text" id="<?php echo esc_attr( $htmlvar_name ); ?>" value="<?php echo esc_attr( $field_value_display ); ?>" placeholder="<?php echo esc_attr( $field_label ); ?>" class="cat_input gd-search-date-input" field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>" data-date-format="<?php echo esc_attr( $jqueryui_date_format ); ?>" data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"<?php echo $aria_label; ?>/><input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
				</div>
				<?php
			}
			?></li><?php

		}

		$html .= ob_get_clean();
		
		$html .= self::field_wrapper_end( $field );
		
		return apply_filters( 'geodir_search_filter_field_html_output_datepicker', $html, $field, $post_type );
	}

	/**
	 * Get the html output for the custom search field: distance.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_distance( $html, $field_info, $post_type ) {
		global $as_fieldset_start;

		// Don't show distance field when service_distance active.
		if ( GeoDir_Post_types::supports( $post_type, 'service_distance' ) ) {
			return $html;
		}

		$field_label = $field_info->frontend_title ? __( $field_info->frontend_title, 'geodirectory' ) : __( $field_info->admin_title, 'geodirectory' );
		$has_fieldset = empty( $field_info->main_search ) && $as_fieldset_start > 0 ? true : false;
		$field_class = 'gd-search-li-' . (int) $has_fieldset . ' gd-search-li-' . $field_info->htmlvar_name . ' gd-field-t' . $field_info->field_type;

		$terms = array( 1 );

		$html .= self::field_wrapper_start( $field_info );

		ob_start();
		if ( $field_info->search_condition == "RADIO" ) {

			if ( $field_info->htmlvar_name == 'distance' && $field_info->extra_fields != '' ) {

				$display_label = $has_fieldset ? '<label for="geodir_search_' . esc_attr( $field_info->htmlvar_name ) . '">' . $field_label . '</label>' : '';
				$extra_fields = maybe_unserialize( $field_info->extra_fields );

				$sort_options = '';

				if ( $extra_fields['is_sort'] == '1' ) {

					if ( $extra_fields['asc'] == '1' ) {

						$name     = ( ! empty( $extra_fields['asc_title'] ) ) ? stripslashes( __( $extra_fields['asc_title'], 'geodirectory' ) ) : __( 'Nearest', 'geodiradvancesearch' );
						$selected = '';
						if ( isset( $_REQUEST['sort_by'] ) && $_REQUEST['sort_by'] == 'nearest' ) {
							$selected = 'selected="selected"';
						}

						$sort_options .= '<option ' . $selected . ' value="nearest">' . $name . '</option>';
					}

					if ( $extra_fields['desc'] == '1' ) {
						$name     = ( ! empty( $extra_fields['desc_title'] ) ) ? stripslashes( __( $extra_fields['desc_title'], 'geodirectory' ) ) : __( 'Farthest', 'geodiradvancesearch' );
						$selected = '';
						if ( isset( $_REQUEST['sort_by'] ) && $_REQUEST['sort_by'] == 'farthest' ) {
							$selected = 'selected="selected"';
						}

						$sort_options .= '<option ' . $selected . ' value="farthest">' . $name . '</option>';
					}

				}

				if ( $sort_options != '' ) {
					echo '<ul><li class="' . esc_attr( $field_class ) . '">' . $display_label . '<select id="" class="cat_select" name="sort_by">';
					echo '<option value="">' . __( 'Select Option', 'geodiradvancesearch' ) . '</option>';
					echo $sort_options;
					echo '</select></li></ul>';
				}
			}
		}
		$html .= ob_get_clean();

		$html .= geodir_design_style() ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type ) : geodir_advance_search_options_output( $terms, $field_info, $post_type );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_distance', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: checkbox.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_fieldset( $html, $field_info, $post_type ) {

		global $as_fieldset_start;

		$design_style = geodir_design_style();

		if( $design_style ){
			if ( $as_fieldset_start == 0 ) {
				$as_fieldset_start ++;
				$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
				$htmlvar     = $field_info->htmlvar_name;
				$html        = '<div class="geodir-filter-cat gd-type-single gd-field-t' . esc_attr( $field_info->field_type ) . ' gd-field-' . esc_attr( $htmlvar ) . ' col mb-3">';
				$html .= '<label class="text-muted">' . stripslashes( __( $field_label, 'geodirectory' ) ) . '</label>';
			} else {
				$as_fieldset_start ++;
				$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
				$htmlvar     = $field_info->htmlvar_name;
				$html        = '</div>'; //end the prev
				$html .= '<div class="geodir-filter-cat gd-type-single gd-field-t' . esc_attr( $field_info->field_type ) . ' gd-field-' . esc_attr( $htmlvar ) . '-' . $as_fieldset_start . ' col mb-3">';
				$html .= '<label class="text-muted">' . stripslashes( __( $field_label, 'geodirectory' ) ) . '</label>';
			}
		}else{
			if ( $as_fieldset_start == 0 ) {
				$as_fieldset_start ++;
				$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
				$htmlvar     = $field_info->htmlvar_name;
				$html        = '<div class="geodir-filter-cat gd-type-single gd-field-t' . esc_attr( $field_info->field_type ) . ' gd-field-' . esc_attr( $htmlvar ) . '">';
				$html .= '<span>' . stripslashes( __( $field_label, 'geodirectory' ) ) . '</span>';
				$html .= '<ul>';
			} else {
				$as_fieldset_start ++;
				$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
				$htmlvar     = $field_info->htmlvar_name;
				$html        = '</ul></div>'; //end the prev
				$html .= '<div class="geodir-filter-cat gd-type-single gd-field-t' . esc_attr( $field_info->field_type ) . ' gd-field-' . esc_attr( $htmlvar ) . '-' . $as_fieldset_start . '">';
				$html .= '<span>' . stripslashes( __( $field_label, 'geodirectory' ) ) . '</span>';
				$html .= '<ul>';
			}
		}



		return apply_filters( 'geodir_search_filter_field_html_output_fieldset', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: multiselect
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_multiselect( $html, $field_info, $post_type ) {

		global $wpdb;
		$select_fields_result = $wpdb->get_row( $wpdb->prepare( "SELECT option_values  FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type = %s and htmlvar_name=%s  ORDER BY sort_order", array(
			$post_type,
			$field_info->htmlvar_name
		) ) );
		if ( in_array( $field_info->input_type, array(
			'CHECK',
			'SELECT',
			'LINK',
			'RADIO'
		) ) ) {
			if ( $field_info->htmlvar_name == 'sale_status' && ( $features = geodir_get_classified_statuses( $field_info->post_type ) ) ) {
				$options = __( 'Select Status', 'geodirectory' ) . '/';
				foreach ( $features as $feature_value => $feature_label ) {
					$options .= ',' . $feature_label . '/' . $feature_value;
				}
				$select_fields_result->option_values = $options;
			}

			// optgroup
			$terms = geodir_string_values_to_options( $select_fields_result->option_values, true );
		} else {
			$terms = explode( ',', $select_fields_result->option_values );
		}

		global $as_fieldset_start;

		$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
		if ( $as_fieldset_start > 0 ) {
			$title = stripslashes( __( $field_label, 'geodirectory' ) );
		} else {
			$title = '';
		}

		$html .= self::field_wrapper_start( $field_info );
		$html .= geodir_design_style() ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type,$title ) : geodir_advance_search_options_output( $terms, $field_info, $post_type,$title );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_multiselect', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: radio
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_radio( $html, $field_info, $post_type ) {

		global $wpdb;
		$select_fields_result = $wpdb->get_row( $wpdb->prepare( "SELECT option_values  FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type = %s and htmlvar_name=%s  ORDER BY sort_order", array(
			$post_type,
			$field_info->htmlvar_name
		) ) );
		if ( in_array( $field_info->input_type, array(
			'CHECK',
			'SELECT',
			'LINK',
			'RADIO'
		) ) ) {
			// optgroup
			$terms = geodir_string_values_to_options( $select_fields_result->option_values, true );
		} else {
			$terms = explode( ',', $select_fields_result->option_values );
		}

		$html .= self::field_wrapper_start( $field_info );
		$html .= geodir_design_style() ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type ) : geodir_advance_search_options_output( $terms, $field_info, $post_type );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_radio', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: select
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_select( $html, $field_info, $post_type ) {

		global $wpdb;
		$select_fields_result = $wpdb->get_row( $wpdb->prepare( "SELECT option_values  FROM " . GEODIR_CUSTOM_FIELDS_TABLE . " WHERE post_type = %s and htmlvar_name=%s  ORDER BY sort_order", array(
			$post_type,
			$field_info->htmlvar_name
		) ) );
		if ( in_array( $field_info->input_type, array(
			'CHECK',
			'SELECT',
			'LINK',
			'RADIO'
		) ) ) {
			if ( $field_info->htmlvar_name == 'sale_status' && ( $features = geodir_get_classified_statuses( $field_info->post_type ) ) ) {
				$options = __( 'Select Status', 'geodirectory' ) . '/';
				foreach ( $features as $feature_value => $feature_label ) {
					$options .= ',' . $feature_label . '/' . $feature_value;
				}
				$select_fields_result->option_values = $options;
			}

			// optgroup
			$terms = geodir_string_values_to_options( $select_fields_result->option_values, true );
		} else {
			$terms = explode( ',', $select_fields_result->option_values );
		}

		global $as_fieldset_start;

		$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
		if ( $as_fieldset_start > 0 ) {
			$title = stripslashes( __( $field_label, 'geodirectory' ) );
		} else {
			$title = '';
		}

		$design_style = geodir_design_style();

		$html .= self::field_wrapper_start( $field_info );
		$html .= $design_style ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type,$title ) : geodir_advance_search_options_output( $terms, $field_info, $post_type,$title );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_select', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: text.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_text( $html, $field_info, $post_type ) {
		if ( $field_info->htmlvar_name == 'distance' ) {
			return '';
		}

		if ( $field_info->htmlvar_name == 'address' ) {
			$field_info->htmlvar_name = 'street';
		}

		$design_style = geodir_design_style();

		$terms = array( 1 );

		$html .= self::field_wrapper_start( $field_info );
		$html .= $design_style ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type ) : geodir_advance_search_options_output( $terms, $field_info, $post_type );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_text', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: textarea.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.4
	 * @return string The html to output.
	 */
	public static function output_field_textarea( $html, $field_info, $post_type ) {
		if ( $field_info->htmlvar_name == 'distance' ) {
			return '';
		}

		$terms = array( 1 );

		$html .= self::field_wrapper_start( $field_info );
		$html .= geodir_design_style() ? geodir_advance_search_options_output_aui( $terms, $field_info, $post_type ) : geodir_advance_search_options_output( $terms, $field_info, $post_type );
		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_textarea', $html, $field_info, $post_type );
	}

	/**
	 * Get the html output for the custom search field: time.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 1.4.2
	 * @return string The html to output.
	 */
	public static function output_field_time( $html, $field, $post_type ) {
		global $as_fieldset_start;

		$pt_name = geodir_post_type_singular_name( $post_type, true );
		$htmlvar_name = $field->htmlvar_name;
		$field_label = $field->frontend_title ? stripslashes( __( $field->frontend_title, 'geodirectory' ) ) : '';
		$field_value = isset( $_REQUEST[ $htmlvar_name ] ) ? $_REQUEST[ $htmlvar_name ] : '';
		$has_fieldset = empty( $field->main_search ) && $as_fieldset_start > 0 ? true : false;

		$cf = geodir_get_field_infoby( 'htmlvar_name', $htmlvar_name, $post_type );
		$extra_fields = ! empty( $cf->extra_fields ) ? maybe_unserialize( $cf->extra_fields ) : NULL;
		
		$time_format = ! empty( $extra_fields['time_format'] ) ? $extra_fields['time_format'] : geodir_time_format();

		// Convert to jQuery UI timepicker format.
		$jqueryui_time_format  = geodir_date_format_php_to_jqueryui( $time_format  );
		
		$html .= self::field_wrapper_start( $field );

		$design_style = geodir_design_style();

		ob_start();

		if ( $design_style ) {

			if ( $field->search_condition == 'FROM' ) {
				$field_value_from         = '';
				$field_value_to           = '';
				$field_value_from_display = '';
				$field_value_to_display   = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from         = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $time_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to         = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $time_format, strtotime( $field_value_to ) );
					}
				}

				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Time', 'geodiradvancesearch' ), $pt_name );
				$field_label_to   = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Time', 'geodiradvancesearch' ), $pt_name );
				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-time gd-search-<?php echo $htmlvar_name; ?> from-to">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>_from"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-enable-time'] = 'true';
					$extra_attributes['data-no-calendar'] = 'true';
					$extra_attributes['data-date-format'] = 'Hi';

					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = geodir_search_input_time_format( true );

					echo aui()->input(
						array(
							'id'                => $htmlvar_name . "_from",
							'name'              => $htmlvar_name . "[from]",
							'type'              => 'timepicker',
							'placeholder'       => esc_attr( $field_label_from ),
							'class'             => 'rounded-left',
							'value'             => esc_attr( $field_value_from ),
							'extra_attributes'  => $extra_attributes,
							'input_group_right'        => '<div class="input-group-text px-2 bg-transparent border-0x" onclick="jQuery(this).parent().parent().find(\'input\').val(\'\');"><i class="fas fa-times geodir-search-input-label-clear text-muted c-pointer" title="' . __( 'Clear field', 'geodiradvancesearch' ) . '" ></i></div>',
						)
					);
					echo aui()->input(
						array(
							'id'                => $htmlvar_name . "_to",
							'name'              => $htmlvar_name . "[to]",
							'type'              => 'timepicker',
							'placeholder'       => esc_attr( $field_label_to ),
							'class'             => 'rounded-left',
							'value'             => esc_attr( $field_value_to ),
							'extra_attributes'  => $extra_attributes,
							'input_group_right'        => '<div class="input-group-text px-2 bg-transparent border-0x" onclick="jQuery(this).parent().parent().find(\'input\').val(\'\');"><i class="fas fa-times geodir-search-input-label-clear text-muted c-pointer" title="' . __( 'Clear field', 'geodiradvancesearch' ) . '" ></i></div>',
						)
					);
					?>
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_value         = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				?>
				<div class="<?php echo ( $has_fieldset ? 'form-group ' : '' ); ?>gd-search-has-time gd-search-<?php echo esc_attr( $htmlvar_name ); ?>">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>"><?php echo $field_label; ?></label>
					<?php }

					// flatpickr attributes
					$extra_attributes['data-enable-time'] = 'true';
					$extra_attributes['data-no-calendar'] = 'true';
					$extra_attributes['data-date-format'] = 'Hi';

					$extra_attributes['data-alt-input'] = 'true';
					$extra_attributes['data-alt-format'] = geodir_search_input_time_format( true );

					echo aui()->input(
						array(
							'id'                => $htmlvar_name,
							'name'              => $htmlvar_name,
							'required'          => !empty($cf['is_required']) ? true : false,
							'type'              => 'timepicker',
							'placeholder'       => esc_attr( $field_label ),
							'class'             => '',
							'value'             => esc_attr( $field_value ),
							'extra_attributes'  => $extra_attributes
						)
					);
					?>
				</div>
				<?php
			}

		}else {
			?>
			<li class="gd-search-row-<?php echo esc_attr( $htmlvar_name ); ?>"><?php
			if ( $field->search_condition == 'FROM' ) {
				$field_value_from         = '';
				$field_value_to           = '';
				$field_value_from_display = '';
				$field_value_to_display   = '';

				if ( is_array( $field_value ) && ! empty( $field_value ) ) {
					if ( ! empty( $field_value['from'] ) ) {
						$field_value_from         = sanitize_text_field( $field_value['from'] );
						$field_value_from_display = date_i18n( $time_format, strtotime( $field_value_from ) );
					}

					if ( ! empty( $field_value['to'] ) ) {
						$field_value_to         = sanitize_text_field( $field_value['to'] );
						$field_value_to_display = date_i18n( $time_format, strtotime( $field_value_to ) );
					}
				}

				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_label_from = ! empty( $field_label ) ? wp_sprintf( __( 'From: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s Start Time', 'geodiradvancesearch' ), $pt_name );
				$field_label_to   = ! empty( $field_label ) ? wp_sprintf( __( 'To: %s', 'geodiradvancesearch' ), $field_label ) : wp_sprintf( __( '%s End Time', 'geodiradvancesearch' ), $pt_name );
				?>
				<div class="gd-search-has-time gd-search-<?php echo $htmlvar_name; ?> from-to">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>_from"><?php echo $field_label; ?></label>
					<?php } ?>
					<input type="text" id="<?php echo esc_attr( $htmlvar_name ); ?>_from"
					       value="<?php echo esc_attr( $field_value_from_display ); ?>"
					       placeholder="<?php echo esc_attr( $field_label_from ); ?>"
					       class="cat_input gd-search-time-input" field_type="text"
					       data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[from]"
					       data-time-format="<?php echo esc_attr( $jqueryui_time_format ); ?>"
					       data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"/>
					<input type="text" id="<?php echo esc_attr( $htmlvar_name ); ?>_to"
					       value="<?php echo esc_attr( $field_value_to_display ); ?>"
					       placeholder="<?php echo esc_attr( $field_label_to ); ?>"
					       class="cat_input gd-search-time-input" field_type="text"
					       data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>[to]"
					       data-time-format="<?php echo esc_attr( $jqueryui_time_format ); ?>"
					       data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"/>
					<input type="hidden" name="<?php echo esc_attr( $htmlvar_name ); ?>[from]"
					       value="<?php echo esc_attr( $field_value_from ); ?>"><input type="hidden"
					                                                                   name="<?php echo esc_attr( $htmlvar_name ); ?>[to]"
					                                                                   value="<?php echo esc_attr( $field_value_to ); ?>">
				</div>
				<?php
			} else {
				if ( empty( $field_label ) ) {
					$field_label = wp_sprintf( __( '%s Time', 'geodiradvancesearch' ), $pt_name );
				}
				$field_value         = ! empty( $field_value ) && ! is_array( $field_value ) ? sanitize_text_field( $field_value ) : '';
				$field_value_display = ! empty( $field_value ) ? date_i18n( $time_format, strtotime( $field_value ) ) : '';
				?>
				<div class="gd-search-has-time gd-search-<?php echo esc_attr( $htmlvar_name ); ?>">
					<?php if ( ! empty( $as_fieldset_start ) ) { ?>
						<label for="<?php echo esc_attr( $htmlvar_name ); ?>"><?php echo $field_label; ?></label>
					<?php } ?>
					<input type="text" id="<?php echo esc_attr( $htmlvar_name ); ?>"
					       value="<?php echo esc_attr( $field_value_display ); ?>"
					       placeholder="<?php echo esc_attr( $field_label ); ?>" class="cat_input gd-search-time-input"
					       field_type="text" data-alt-field="<?php echo esc_attr( $htmlvar_name ); ?>"
					       data-time-format="<?php echo esc_attr( $jqueryui_time_format ); ?>"
					       data-field-key="<?php echo esc_attr( $htmlvar_name ); ?>"/><input type="hidden"
					                                                             name="<?php echo esc_attr( $htmlvar_name ); ?>"
					                                                             value="<?php echo esc_attr( $field_value ); ?>">
				</div>
				<?php
			}
			?></li><?php
		}

		$html .= ob_get_clean();
		
		$html .= self::field_wrapper_end( $field );
		
		return apply_filters( 'geodir_search_filter_field_html_output_timepicker', $html, $field, $post_type );
	}

	/**
	 * Get the html output for the custom search field: business_hours.
	 *
	 * @param string $html       The html to be filtered.
	 * @param object $field_info The field object info.
	 * @param string $post_type  The post type being called.
	 *
	 * @since 2.0.1.0
	 * @return string The html to output.
	 */
	public static function output_field_business_hours( $html, $field_info, $post_type ) {
		$htmlvar_name = 'open_now';

		$minutes = geodir_hhmm_to_bh_minutes( gmdate( 'H:i' ), gmdate( 'N' ) );
		$field_label = $field_info->frontend_title ? $field_info->frontend_title : $field_info->admin_title;
		$field_label = __( stripslashes( $field_label ), 'geodirectory' );
		$aria_label = $field_label ? ' aria-label="' . esc_attr( $field_label ) . '"' : '';
		$search_value = geodir_search_get_field_search_param( $htmlvar_name );
		$options = GeoDir_Adv_Search_Business_Hours::business_hours_options( $field_label );

		global $as_fieldset_start;

		$design_style = geodir_design_style();

		$html .= self::field_wrapper_start( $field_info );

		if($design_style){
			$html .= '<select data-minutes="' . $minutes . '" name="s' . esc_attr( $htmlvar_name ) . '" class="geodir-advs-open-now cat_select custom-select form-control" id="geodir_search_' . esc_attr( $htmlvar_name ) . '"' . $aria_label . '>';
			foreach ( $options as $option_value => $option_label ) {
				$selected = selected( $search_value == $option_value, true, false );

				if ( $option_value == 'now' ) {
					if ( ! $selected && ( $search_value === 0 || $search_value === '0' || ( ! empty( $search_value ) && $search_value > 0 ) ) && ! in_array( $search_value, array_keys( $options ) ) ) {
						$selected = selected( true, true, false );
					}
				}

				$html .= '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . $option_label . '</option>';
			}
			$html .= '</select>';
		}else{
			$html .= '<li><select data-minutes="' . $minutes . '" name="s' . esc_attr( $htmlvar_name ) . '" class="geodir-advs-open-now cat_select" id="geodir_search_' . esc_attr( $htmlvar_name ) . '"' . $aria_label . '>';
			foreach ( $options as $option_value => $option_label ) {
				$selected = selected( $search_value == $option_value, true, false );

				if ( $option_value == 'now' ) {
					if ( ! $selected && ( $search_value === 0 || $search_value === '0' || ( ! empty( $search_value ) && $search_value > 0 ) ) && ! in_array( $search_value, array_keys( $options ) ) ) {
						$selected = selected( true, true, false );
					}
				}

				$html .= '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . $option_label . '</option>';
			}
			$html .= '</select></li>';
		}


		$html .= self::field_wrapper_end( $field_info );

		return apply_filters( 'geodir_search_filter_field_html_output_business_hours', $html, $field_info, $post_type );
	}

	/*
	 * Delete search fields after post type deleted
	 */
	public static function post_type_deleted( $post_type = '' ) {
		global $wpdb;
		if ( $post_type != '' ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM " . GEODIR_ADVANCE_SEARCH_TABLE . " WHERE post_type = %s", array( $post_type ) ) );
		}
	}

	/*
	 * Delete search field after custom field deleted
	 */
	public static function custom_field_deleted( $id, $htmlvar_name, $post_type ) {
		global $wpdb;

		if ( $htmlvar_name != '' && $post_type != '' ) {
			$wpdb->query($wpdb->prepare( "DELETE FROM " . GEODIR_ADVANCE_SEARCH_TABLE . " WHERE htmlvar_name = %s AND post_type = %s", array( $htmlvar_name, $post_type ) ) );
		}
	}

	public static function checkbox_fields( $post_type = '' ) {
		$fields = array( 'video', 'special_offers' );

		return apply_filters( 'geodir_search_checkbox_fields', $fields, $post_type );
	}

	/**
	 * Enqueue flatpickr scripts.
	 *
	 * @since 2.1.0.7
	 */
	public static function enqueue_search_scripts() {
		if ( geodir_design_style() ) {
			$aui_settings = AyeCode_UI_Settings::instance();
			$aui_settings->enqueue_flatpickr();
		}
	}
}