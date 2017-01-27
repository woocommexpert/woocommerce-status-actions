<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$wpdb->hide_errors();
$table_name = $wpdb->prefix . 'woocommerce_order_status_action';
$tables = $wpdb->query("SHOW TABLES LIKE '{$table_name}'");
if( $tables )
{
	$status_actions = $wpdb->get_results("SELECT * FROM {$table_name} ");

	if($status_actions){
		$wc_icons = array(
			'e000' => 'e4f3',
			'e001' => 'e4f4',
			'e002' => 'e4f5',
			'e003' => 'e4f6',
			'e004' => 'e4f7',
			'e005' => 'e4f8',
			'e006' => 'e4f9',
			'e007' => 'e4fa',
			'e008' => 'e4fb',
			'e009' => 'e4fc',
			'e00a' => 'e4fd',
			'e00b' => 'e4fe',
			'e00c' => 'e4ff',
			'e00d' => 'e500',
			'e00e' => 'e501',
			'e00f' => 'e502',
			'e010' => 'e503',
			'e011' => 'e504',
			'e012' => 'e505',
			'e013' => 'e506',
			'e014' => 'e507',
			'e015' => 'e508',
			'e016' => 'e509',
			'e017' => 'e50a',
			'e018' => 'e50b',
			'e019' => 'e50c',
			'e01a' => 'e50d',
			'e01b' => 'e50e',
			'e01c' => 'e50f',
			'e01d' => 'e510',
			'e01e' => 'e511',
			'e01f' => 'e512',
			'e020' => 'e513',
			'e021' => 'e514',
			'e022' => 'e515',
			'e023' => 'e516',
			'e024' => 'e517',
			'e025' => 'e518',
			'e026' => 'e519',
			'e027' => 'e51a',
			'e028' => 'e51b',
			'e029' => 'e51c',
			'e02a' => 'e51d',
			'e02b' => 'e51e',
			'e02c' => 'e51f',
			'e02d' => 'e520',
			'e02e' => 'e521',
			'e02f' => 'e522',
			'e030' => 'e523',
			'e031' => 'e524',
			'e032' => 'e525',
			'e033' => 'e526',
			'e034' => 'e527',
			'e035' => 'e528',
			'e036' => 'e529',
			'e037' => 'e52a',
			'e038' => 'e52b',
			'e039' => 'e52c',
			'e03a' => 'e52d',
			'e03b' => 'e52e',
			'e03c' => 'e52f',
			'e03d' => 'e530',
			'e600' => 'e600',
			'e601' => 'e601',
			'e602' => 'e602',
			'e603' => 'e603',
			'e604' => 'e604	'
		);

		$wpdb->wc_osmeta = $wpdb->prefix . "wc_osmeta";
		$wpdb->wc_osa    = $wpdb->prefix . "woocommerce_order_status_action";
		foreach ($status_actions as $st) {
			$icon_style = 'icon-outline';
			switch ($st->status_color_type) {
				case '1':
					$icon_style = 'icon-color';
					break;
				case '2':
					$icon_style = 'text-color';
					break;
				case '3':
					$icon_style = 'text-outline';
					break;			
				default:
					$icon_style = 'icon-outline';
					break;
			}
			$show_action_buttons = array();
			if( $st->display_completed == 1){
				$show_action_buttons[] = 'wc-completed';
			}
			if( $st->display_processing == 1){
				$show_action_buttons[] = 'wc-processing';
			}
			$note_prompt       = get_metadata('wc_os', $st->id, 'note_prompt', true);
			$automatic_trigger = get_metadata('wc_os', $st->id, 'automatic_trigger', true);
			$triggered_status  = get_metadata('wc_os', $st->id, 'triggered_status', true);

			$status_font_family = get_metadata('wc_os', $st->id, 'status_icon_font_family', true);
			$action_font_family = get_metadata('wc_os', $st->id, 'action_icon_font_family', true);

			$status_icon = $st->status_icon;
			$action_icon = $st->action_icon;
			switch ($status_font_family) {
				case 'woocommerce':
					if( isset($wc_icons[$st->status_icon]) ){
						$status_icon = $wc_icons[$st->status_icon];
					}
					break;
				case 'dashicons':
					$status_icon = 'a' . substr($st->status_icon, 1);
					break;
			}
			switch ($action_font_family) {
				case 'woocommerce':
					if( isset($wc_icons[$st->action_icon]) ){
						$action_icon = $wc_icons[$st->action_icon];
					}
					break;
				case 'dashicons':
					$action_icon = 'a' . substr($st->action_icon, 1);
					break;
			}

			$meta_data = array(
				'display_in_reports'            => $st->display_in_reports == 1 ? 'yes': 'no',
				'dashboard_widget'              => get_metadata('wc_os', $st->id, 'dashboard_widget', true),
				'status_icon'                   => $status_icon,
				'status_colour'                 => $st->status_color,
				'icon_style'                    => $icon_style,
				'email_notification'            => $st->sends_email == 1 ? 'yes': 'no',
				'email_type'                    => get_metadata('wc_os', $st->id, 'email_type', true),
				'email_recipients'              => $st->email_to,
				'email_custom_address'          => $st->custom_email_address,
				'email_from_name'               => $st->from_name,
				'email_from_address'            => $st->from_address,
				'email_subject'                 => $st->email_subject,
				'email_heading'                 => $st->custom_title,
				'email_order_info'              => $st->display_essential_info == 1 ? 'yes': 'no',
				'email_message'                 => $st->display_custom_info == 1 ? 'yes': 'no',
				'email_message_text'            => stripslashes($st->custom_info),
				'customer_cancel_orders'        => $st->can_cancel == 1 ? 'yes': 'no',
				'customer_account'              => $st->trigger_status == 1 ? 'yes': 'no',
				'customer_account_visibility'   => maybe_unserialize($st->statuses_trigger),
				'customer_account_button_label' => $st->trigger_button_label,
				'product_reviews'               => get_metadata('wc_os', $st->id, 'dashboard_widget', true),
				'downloads_permitted'           => $st->download_permitted == 1 ? 'yes': 'no',
				'item_editing'                  => get_metadata('wc_os', $st->id, 'order_is_editable', true),
				'stock_status'                  => !empty($st->stock_status) ? $st->stock_status : 'no_affection',
				'action_icon'                   => $action_icon,
				'action_visibility'             => !empty($st->display_for) ? explode(',', $st->display_for ) : array(),
				'show_action_buttons'           => $show_action_buttons,
				'hide_bulk_actions'             => $st->hide_bulk_action == 1 ? 'yes': 'no',
				'order_note_prompt'             => $note_prompt == 1 ? 'yes': 'no',
				'automatic_trigger'             => $automatic_trigger == 1 ? 'yes': 'no',
				'triggered_status'              => !empty($triggered_status) ? 'wc-'.$triggered_status : '',
				'time_period'                   => get_metadata('wc_os', $st->id, 'time_period', true),
				'time_period_type'              => get_metadata('wc_os', $st->id, 'time_period_type', true)
			);

			$post_data = array(
			  'post_title'    => wp_strip_all_tags( $st->status_name ),
			  'post_name'     => $st->status_slug,
			  'post_content'  => '',
			  'post_status'   => 'publish',
			  'post_type'     => 'wc_custom_statuses',
			  'menu_order'    => $st->menu_order,
			  'post_author'   => get_current_user_id(),
			);

			 
			// Insert the status into the database
			$st_id = wp_insert_post( $post_data );
			if( $st_id ){
				update_post_meta($st_id, '__label', $st->status_slug);
				foreach ($meta_data as $meta_key => $meta_value) {
					update_post_meta($st_id, '_'.$meta_key, $meta_value);
				}
			}
			WC_SA_Meta_Box_Status_Data::generate_styles($st_id);

		}

		wc_delete_shop_order_transients();
	}
}