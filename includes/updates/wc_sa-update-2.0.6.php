<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$wpdb->hide_errors();
$post_type = 'wc_custom_statuses';

$all_statuses = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = %s", $post_type ) );

if( $all_statuses ){
	foreach ($all_statuses as $post) {
		
		if( strlen($post->post_name) > 17){
			$post_id       = $post->ID;
			$original_slug = $post->post_name;
			$slug          = _truncate_post_slug( $original_slug, 17);

			$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND ID != %d LIMIT 1";
			$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_type, $post_id ) );

			if( $post_name_check ){
				$suffix = 2;
				do {
					$alt_post_name = _truncate_post_slug( $slug, 17 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
					$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $post_type, $post_id ) );
					$suffix++;
				} while ( $post_name_check );
				$slug = $alt_post_name;				
			}

			$query = "UPDATE {$wpdb->posts} SET post_name = %s WHERE ID = %d";
            $wpdb->query($wpdb->prepare( $query, $slug, $post_id ));
			
				
			$new_label = 'wc-'.$slug;
			$old_label = 'wc-'.$original_slug;
			$query = "UPDATE {$wpdb->posts} SET post_status = '{$new_label}' WHERE post_status = '{$old_label}'";
	        $wpdb->query($query);
			
			update_post_meta($post_id, '__label', $slug);

			WC_SA_Meta_Box_Status_Data::generate_styles($post_id);
		}
	}
	wc_delete_shop_order_transients();
}