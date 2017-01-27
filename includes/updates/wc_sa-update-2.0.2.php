<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$wpdb->hide_errors();
$table_name = $wpdb->prefix . 'woocommerce_order_status_action';
$tables = $wpdb->query("SHOW TABLES LIKE '{$table_name}'");
if( $tables ){

	$st = wc_sa_get_statuses();
	foreach ($st as $st_id => $s) {
		$status_action = $wpdb->get_row("SELECT * FROM {$table_name} WHERE status_name = '{$s->title}' LIMIT 1");
		if( $status_action ) {
			if( $status_action->status_slug != $s->label ){
				$orders = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'wc-{$status_action->status_slug}' AND post_type = 'shop_order' ");
				if( $orders ){
					$order_ids = array();
					foreach ($orders as $order) {
						$order_ids[] = $order->ID;
					}
					$order_ids = implode(',', $order_ids);
					$wpdb->query("UPDATE {$wpdb->posts} SET post_status = 'wc-{$s->label}' WHERE ID IN({$order_ids}) ");
				}
			}
		}
		update_post_meta($st_id, '__label', $s->label);
	}
	wc_delete_shop_order_transients();

}