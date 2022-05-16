<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GeoDirectory Advance Search Filters Query class.
 *
 * AJAX Event Handler.
 *
 * @class    GeoDir_Adv_Search_Query
 * @package  GeoDir_Advance_Search_Filters/Classes
 * @category Class
 * @author   AyeCode
 */
class GeoDir_Adv_Search_Query {

	function __construct() {

		add_filter( 'geodir_posts_fields', array( __CLASS__, 'posts_fields' ), 1, 2 );
		add_filter( 'geodir_posts_join', array( __CLASS__, 'posts_join' ), 1, 2 );
		add_filter( 'geodir_posts_where', array( __CLASS__, 'posts_where' ), 1, 2 );
		add_filter( 'geodir_posts_order_by_sort', array( __CLASS__, 'posts_orderby' ), 1, 4 );
		add_filter( 'geodir_posts_groupby', array( __CLASS__, 'posts_groupby' ), 1, 2 );

		// Distance sort by
		add_filter( 'geodir_posts_order_by_sort', array( __CLASS__, 'sory_by_distance' ), 10, 4 );

		// Classifieds filter
		add_filter( 'geodir_get_post_stati', array( __CLASS__, 'filter_post_stati' ), 9, 3 );
	}

	public static function posts_fields( $fields, $wp_query = array() ) {
		global $geodir_post_type;

		return $fields;
	}

	public static function posts_join( $join, $wp_query = array() ) {
		global $wpdb, $geodir_post_type, $table;

		if ( ! geodir_is_page('search') ) {
			return $join;
		}

		if ( ! ( ! empty( $wp_query ) && $wp_query->is_main_query() ) ) {
			return $join;
		}

		// Current post type
		$post_type = geodir_get_search_post_type();

		return apply_filters( 'geodir_search_posts_join', $join, $post_type, $wp_query );
	}

	public static function posts_where( $where, $wp_query = array() ) {
		global $wpdb, $geodir_post_type, $table;

		if ( ! geodir_is_page('search') ) {
			return $where;
		}

		if ( ! ( ! empty( $wp_query ) && $wp_query->is_main_query() ) ) {
			return $where;
		}
		
		// Current post type
		$post_type = geodir_get_search_post_type();

		if ( empty( $table ) ) {
			$table = geodir_db_cpt_table( $post_type );
		}

		// Search fields
		$fields = GeoDir_Adv_Search_Fields::get_search_fields( $post_type );

		if ( ! empty( $fields ) ) {
			$checkbox_fields = GeoDir_Adv_Search_Fields::checkbox_fields( $post_type );
			$active_features = geodir_classified_active_statuses( $post_type );

			$fields_where = array();

			foreach ( $fields as $key => $field ) {
				$field = stripslashes_deep( $field );
				if ( $field->htmlvar_name == 'address' ) {
					$field->htmlvar_name = 'street';
				}

				$skip = isset( $field->htmlvar_name ) && in_array( $field->htmlvar_name, array( '_sold' ) ) ? true : false;
	
				if ( $field->htmlvar_name == 'sale_status' && ! empty( $active_features ) ) {
					$skip = true;
				}

				$skip = apply_filters( 'geodir_search_posts_where_skip_field', $skip, $field );
				if ( $skip ) {
					continue;
				}

				$htmlvar_name = $field->htmlvar_name;
				$extra_fields = ! empty( $field->extra_fields ) ? maybe_unserialize( $field->extra_fields ) : NULL;

				$field_where = array();

				switch ( $field->input_type ) {
					case 'RANGE': {
						switch ( $field->search_condition ) {
							case 'SINGLE': {
								$value = isset( $_REQUEST['s' . $htmlvar_name ] ) ? sanitize_text_field( $_REQUEST['s' . $htmlvar_name ] ) : '';

								if ( $value !== '' ) {
									$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} = %s", array( $value ) );
								}
							}
							break;
							case 'FROM': {
								$min_value = isset( $_REQUEST['smin' . $htmlvar_name ] ) ? sanitize_text_field( $_REQUEST['smin' . $htmlvar_name ] ) : '';
								$max_value = isset( $_REQUEST['smax' . $htmlvar_name ] ) ? sanitize_text_field( $_REQUEST['smax' . $htmlvar_name ] ) : '';

								// min range
								if ( $min_value !== '' ) {
									$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} >= %s", array( $min_value ) );
								}

								// max range
								if ( $max_value !== '' ) {
									$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} <= %s", array( $max_value ) );
								}
							}
							break;
							case 'RADIO': {
								// This code in main geodirectory listing filter
							}
							break;
							default: {
								$value = isset( $_REQUEST['s' . $htmlvar_name ] ) ? sanitize_text_field( $_REQUEST['s' . $htmlvar_name ] ) : '';

								if ( $value !== '' ) {
									$values = explode( '-', $value );

									$min_value = trim( $values[0] );
									$max_value = isset( $values[1] ) ? trim( $values[1] ) : '';

									$compare = substr( $max_value, 0, 4 );

									if ( $compare == 'Less' || $compare == 'less' ) {
										if ( $min_value !== '' ) {
											$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} < %s", array( $min_value ) );
										}
									} else if ( $compare == 'More' || $compare == 'more' ) {
										if ( $min_value !== '' ) {
											$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} > %s", array( $min_value ) );
										}
									} else {
										if ( $min_value !== '' ) {
											$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} >= %s", array( $min_value ) );
										}

										if ( $max_value !== '' ) {
											$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} <= %s", array( $max_value ) );
										}
									}
								}
							}
							break;
						}
					}
					break;
					case 'DATE': {
						if ( ! empty( $_REQUEST[ $htmlvar_name ] ) ) {
							$value = $_REQUEST[ $htmlvar_name ];

							// new one field range picker
							$design_style = geodir_design_style();
							if(!is_array($value) && $design_style && strpos($value, ' ') !== false){
								$parts = explode(" ",$value);
								if(!empty($parts[2])){
									$value = array();
									$value['from'] = $parts[0];
									$value['to'] = $parts[2];
								}
							}

							if ( $field->data_type == 'DATE' ) {
								if ( is_array( $value ) ) {
									$value_from = ! empty( $value['from'] ) ? date_i18n( 'Y-m-d', strtotime( sanitize_text_field( $value['from'] ) ) ) : '';
									$value_to = ! empty( $value['to'] ) ? date_i18n( 'Y-m-d', strtotime( sanitize_text_field( $value['to'] ) ) ) : '';

									if ( ! empty( $value_from ) ) {
										$field_where[] = $wpdb->prepare( "UNIX_TIMESTAMP( {$table}.{$htmlvar_name} ) >= UNIX_TIMESTAMP( %s )", array( $value_from ) );
									}

									if ( ! empty ( $value_to ) ) {
										$field_where[] = $wpdb->prepare( "UNIX_TIMESTAMP( {$table}.{$htmlvar_name} ) <= UNIX_TIMESTAMP( %s )", array( $value_to ) );
									}
								} else {
									$value = date_i18n( 'Y-m-d', strtotime( sanitize_text_field( $value ) ) );
									$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} = %s", array( $value ) );
								}
							} else if ( $field->data_type == 'TIME' ) {
								if ( is_array( $value ) ) {
									$value_from = isset( $value['from'] ) && $value['from'] != '' ? date_i18n( 'H:i:s', strtotime( sanitize_text_field( $value['from'] ) ) ) : '';
									$value_to = isset( $value['to'] ) && $value['to'] != '' ? date_i18n( 'H:i:s', strtotime( sanitize_text_field( $value['to'] ) ) ) : '';

									if ( ! empty( $value_from ) ) {
										$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} >= %s", array( $value_from ) );
									}

									if ( ! empty ( $value_to ) ) {
										$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} <= %s", array( $value_to ) );
									}
								} else {
									$value = date_i18n( 'H:i:s', strtotime( sanitize_text_field( $value ) ) ); // old style
									$value2 = date_i18n( 'H:i', strtotime( sanitize_text_field( $value ) ) ); // new style
									$field_where[] = $wpdb->prepare( " ( {$table}.{$htmlvar_name} = %s || {$table}.{$htmlvar_name} = %s) ", array( $value,$value2  ) );
								}
							}
						}
					}
					break;
					default: {
						if ( isset( $_REQUEST['s' . $htmlvar_name ] ) ) {
							$value = $_REQUEST['s' . $htmlvar_name ];

							if ( is_array( $value ) ) {
								$search_operator = !empty( $extra_fields ) && !empty( $extra_fields['search_operator'] ) && $extra_fields['search_operator'] == 'OR' ? 'OR' : 'AND';

								$loops = array();
								foreach ( $value as $v ) {
									$v = sanitize_text_field( $v );
									if ( $v !== '' ) {
										$terms_loop = '';
										if ( $htmlvar_name == 'post_category' ) {
											$terms_loop = self::query_terms_children( absint( $v ), $post_type, $htmlvar_name, $table );
										}

										if ( ! empty( $terms_loop ) ) {
											$loops[] = $terms_loop;
										} else {
											$loops[] = $wpdb->prepare( "FIND_IN_SET( %s, {$table}.{$htmlvar_name} )", array( $v ) );
										}
									}
								}

								if ( ! empty ( $loops ) ) {
									$field_where[] = ( count( $loops ) > 1 ? '( ' : '' ) . implode( " {$search_operator} ", $loops ) . ( count( $loops ) > 1 ? ' )' : '' );
								}
							} else {
								$value = sanitize_text_field( $value );

								if ( $value !== '' ) {
									// Show special offers, video as a checkbox field.
									if ( ! empty( $checkbox_fields ) && in_array( $htmlvar_name, $checkbox_fields ) && (int)$value == 1 ) {
										$field_where[] = "{$table}.{$htmlvar_name} IS NOT NULL AND {$table}.{$htmlvar_name} != '' AND {$table}.{$htmlvar_name} != '0'";
									} else {
										if ( $field->data_type == 'VARCHAR' || $field->data_type == 'TEXT' ) {
											$operator = 'LIKE';
											if ( ! empty( $value ) ) {
												$value = '%' . $value . '%';
											}
										} else {
											$operator = '=';
										}
										$field_where[] = $wpdb->prepare( "{$table}.{$htmlvar_name} {$operator} %s", array( $value ) );
									}
								}
							}
						}
					}
					break;
				}

				$field_where = ! empty( $field_where ) ? implode( " AND ", $field_where ) : '';
				$field_where = apply_filters( 'geodir_search_posts_field_where', $field_where, $htmlvar_name, $field, $post_type, $wp_query );

				if ( ! empty( $field_where ) ) {
					$fields_where[] = $field_where;
				}
			}

			$fields_where = ! empty( $fields_where ) ? implode( " AND ", $fields_where ) : '';
			$fields_where = apply_filters( 'geodir_search_posts_fields_where', $fields_where, $where, $wp_query );

			if ( ! empty( $fields_where ) ) {
				$where = rtrim( $where ) . " AND {$fields_where}";
			}
		}

		return apply_filters( 'geodir_search_posts_where', $where, $wp_query );
	}

	public static function posts_groupby( $groupby, $wp_query = array() ) {
		if ( ! geodir_is_page('search') ) {
			return $groupby;
		}

		if ( ! ( ! empty( $wp_query ) && $wp_query->is_main_query() ) ) {
			return $groupby;
		}

		// Current post type
		$post_type = geodir_get_search_post_type();

		return apply_filters( 'geodir_search_posts_groupby', $groupby, $post_type, $wp_query );
	}

	public static function posts_orderby( $orderby, $sortby, $table, $wp_query = array() ) {
		global $geodir_post_type;

		return $orderby;
	}

	public static function sory_by_distance( $orderby, $sort_by, $table, $query ) {
		global $geodir_post_type;

		if ( ! empty( $sort_by ) && ( $sort_by == 'nearest' || $sort_by == 'farthest' ) ) {
			$support_location = $geodir_post_type && GeoDir_Post_types::supports( $geodir_post_type, 'location' );

			if ( $support_location && ( ! empty( $_REQUEST['snear'] ) || ( get_query_var( 'user_lat' ) && get_query_var( 'user_lon' ) ) ) && geodir_is_page( 'search' ) ) {
				$orderby = $sort_by == 'nearest' ? "distance ASC" : "distance DESC";
				$_orderby = GeoDir_Query::search_sort( '', $sort_by, $query );
				if ( trim( $_orderby ) != '' ) {
					$orderby .= ", " . $_orderby;
				}
			}
		}

		return $orderby;
	}

	public static function filter_terms_children( $term_id, $taxonomy ) {
		if ( ! function_exists( 'geodir_get_term_children' ) ) {
			return NULL;
		}

		$children = geodir_get_term_children( $term_id, $taxonomy );

		if ( ! empty( $children ) ) {
			foreach ( $children as $id => $term ) {
				if ( ! empty( $term->count ) ) {
					$terms[] = $term->term_id;
				}
			}
		}

		return $terms;
	}

	public static function query_terms_children( $term_id, $post_type, $column = 'post_category', $alias = '' ) {
		global $wpdb;

		if ( empty( $term_id ) || empty( $post_type ) || $column != 'post_category' || ! geodir_get_option( 'advs_search_in_child_cats' ) ) {
			return NULL;
		}

		$taxonomy = $post_type . 'category';

		$terms = self::filter_terms_children( $term_id, $taxonomy );
		if ( $alias != '' ) {
			$alias .= '.';
		}

		$loops = array();
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $search_id ) {
				$loops[] = $wpdb->prepare( "FIND_IN_SET( %s, {$alias}{$column} )", array( $search_id ) );
			}
		}

		$loops = ! empty( $loops ) ? "( " . implode( " OR ", $loops ) . " )" : "";

		return $loops;
	}

	public static function filter_post_stati( $statuses, $context, $args ) {
		if ( ! empty( $args['post_type'] ) ) {
			// Search
			if ( $context == 'search' && ( $sale_status = GeoDir_Query::get_query_var( 'ssale_status' ) ) && ( $active_statuses = geodir_classified_active_statuses( $args['post_type'] ) ) ) {
				if ( ! is_array( $sale_status ) ) {
					$sale_status = array( $sale_status );
				}

				$_statuses = array();
				foreach ( $sale_status as $_sale_status ) {
					if ( in_array( $_sale_status, $active_statuses ) ) {
						$_statuses = array( strip_tags( $_sale_status ) );
					}
				}

				if ( ! empty( $_statuses ) ) {
					$statuses = $_statuses;
				}
			}

			if ( $context == 'search' && GeoDir_Query::get_query_var( 's_sold' ) ) {
				$statuses[] = 'gd-sold';
			}

			// Map
			if ( $context == 'map' && ! empty( $args['post'] ) && is_array( $args['post'] ) ) {
				$statuses[] = 'gd-sold';
			}
		}

		return $statuses;
	}
}