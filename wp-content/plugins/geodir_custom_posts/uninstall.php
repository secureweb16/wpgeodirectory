<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    GeoDir_Custom_Posts
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb, $plugin_prefix;

$geodir_settings = get_option( 'geodir_settings' );
 
if ( ( ! empty( $geodir_settings ) && ( ! empty( $geodir_settings['admin_uninstall'] ) || ! empty( $geodir_settings['uninstall_geodir_custom_posts'] ) ) ) || ( defined( 'GEODIR_UNINSTALL_GEODIR_CUSTOM_POSTS' ) && true === GEODIR_UNINSTALL_GEODIR_CUSTOM_POSTS ) ) {
	if ( empty( $plugin_prefix ) ) {
		$plugin_prefix = $wpdb->prefix . 'geodir_';
	}

	$link_posts_table = defined( 'GEODIR_CP_LINK_POSTS' ) ? GEODIR_CP_LINK_POSTS : $plugin_prefix . 'cp_link_posts';
	$attachments_table = defined( 'GEODIR_ATTACHMENT_TABLE' ) ? GEODIR_ATTACHMENT_TABLE : $plugin_prefix . 'attachments';
	$custom_fields_table = defined( 'GEODIR_CUSTOM_FIELDS_TABLE' ) ? GEODIR_CUSTOM_FIELDS_TABLE : $plugin_prefix . 'custom_fields';
	$custom_sort_fields_table = defined( 'GEODIR_CUSTOM_SORT_FIELDS_TABLE' ) ? GEODIR_CUSTOM_SORT_FIELDS_TABLE : $plugin_prefix . 'custom_sort_fields';
	$reviews_table = defined( 'GEODIR_REVIEW_TABLE' ) ? GEODIR_REVIEW_TABLE : $plugin_prefix . 'post_review';
	$search_fields_table = defined( 'GEODIR_ADVANCE_SEARCH_TABLE' ) ? GEODIR_ADVANCE_SEARCH_TABLE : $plugin_prefix . 'custom_advance_search_fields';
	$tabs_layout_table = defined( 'GEODIR_TABS_LAYOUT_TABLE' ) ? GEODIR_TABS_LAYOUT_TABLE : $plugin_prefix . 'tabs_layout';

	// Delete table
	$wpdb->query( "DROP TABLE IF EXISTS {$link_posts_table}" );

	if ( ! empty( $geodir_settings ) ) {
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '{$search_fields_table}'" ) ) {
			$search_fields_table = '';
		}

		$save_settings = $geodir_settings;

		$remove_options = array(
			'linked_post_types',
			'uninstall_geodir_custom_posts',
		);

		$post_types = ! empty( $geodir_settings['post_types'] ) ? $geodir_settings['post_types'] : array();
		$taxonomies = ! empty( $geodir_settings['taxonomies'] ) ? $geodir_settings['taxonomies'] : array();

		foreach ( $post_types as $post_type => $data ) {
			if ( $post_type == 'gd_place' || $post_type == 'gd_event' ) { // Don't remove default post types
				continue;
			}
			$remove_options[] = $post_type . '_dummy_data_type';

			unset( $save_settings['post_types'][ $post_type ] );

			if ( ! empty( $taxonomies ) && isset( $taxonomies[ $post_type . 'category' ] ) ) {
				unset( $save_settings['taxonomies'][ $post_type . 'category' ] );
			}

			if ( ! empty( $taxonomies ) && isset( $taxonomies[ $post_type . '_tags' ] ) ) {
				unset( $save_settings['taxonomies'][ $post_type . '_tags' ] );
			}

			// Delete post table
			$wpdb->query( "DROP TABLE IF EXISTS {$plugin_prefix}{$post_type}_detail" );

			// Delete posts
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = %s", array( $post_type ) ) );

			// Delete post menu
			$wpdb->query( "DELETE posts FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} meta ON posts.ID = meta.post_id WHERE posts.post_type = 'nav_menu_item' AND meta.meta_key = '_menu_item_object' AND meta.meta_value = '{$post_type}'" );
			$wpdb->query( "DELETE posts FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} meta ON posts.ID = meta.post_id WHERE posts.post_type= 'nav_menu_item' AND meta.meta_key = '_menu_item_url' AND meta.meta_value LIKE '%listing_type={$post_type}%'" );

			// Delete term taxonomies
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s OR taxonomy = %s", array( $post_type . 'category', $post_type . '_tags' ) ) );

			delete_option( $post_type . 'category_installed' );
		}

		// Delete orphan attachment.
		$wpdb->query( "DELETE post1 FROM {$wpdb->posts} post1 LEFT JOIN {$wpdb->posts} post2 ON post1.post_parent = post2.ID WHERE post1.post_parent > 0 AND post1.post_type = 'attachment' AND post2.ID IS NULL" );

		// Delete orphan post meta
		$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL" );

		// Delete orphan relationships
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL" );

		// Delete orphan terms
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL" );

		// Delete orphan term meta
		$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL" );
		
		// Delete orphan comments
		$wpdb->query( "DELETE comments FROM {$wpdb->comments} AS comments LEFT JOIN {$wpdb->posts} AS posts ON posts.ID = comments.comment_post_ID WHERE posts.ID IS NULL" );
		$wpdb->query( "DELETE meta FROM {$wpdb->commentmeta} meta LEFT JOIN {$wpdb->comments} comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL" );

		// Delete orphan post attachments
		$wpdb->query( "DELETE attachments FROM {$attachments_table} attachments LEFT JOIN {$wpdb->posts} posts ON posts.ID = attachments.post_id WHERE posts.ID IS NULL" );

		// Delete custom fields
		$wpdb->query( "DELETE FROM {$custom_fields_table} WHERE post_type NOT IN( 'gd_place', 'gd_event' ) OR field_type = 'link_posts'" );

		// Delete custom sort fields
		$wpdb->query( "DELETE FROM {$custom_sort_fields_table} WHERE post_type NOT IN( 'gd_place', 'gd_event' )" );

		// Delete search fields
		if ( $search_fields_table ) {
			$wpdb->query( "DELETE FROM {$search_fields_table} WHERE post_type NOT IN( 'gd_place', 'gd_event' )" );
		}

		// Delete tabs layout
		$wpdb->query( "DELETE FROM {$tabs_layout_table} WHERE post_type NOT IN( 'gd_place', 'gd_event' )" );
			
		// Delete orphan post reviews
		$wpdb->query( "DELETE reviews FROM {$reviews_table} reviews LEFT JOIN {$wpdb->posts} posts ON posts.ID = reviews.post_id WHERE posts.ID IS NULL" );

		foreach ( $remove_options as $option ) {
			if ( isset( $save_settings[ $option ] ) ) {
				unset( $save_settings[ $option ] );
			}
		}

		// Update options.
		update_option( 'geodir_settings', $save_settings );
	}

	// Delete core options
	delete_option( 'geodir_cp_version' );
	delete_option( 'geodir_cp_db_version' );
	delete_option( 'geodir_custom_posts_db_version' );
	
	// Clear any cached data that has been removed.
	wp_cache_flush();
}