<?php
/**
 * Status Data
 *
 * Display the status data meta box.
 *
 * @author      Actuality Extensions
 * @category    Admin
 * @package     WC_SA/Admin/Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_SA_Meta_Box_Status_Data Class.
 */
class WC_SA_Meta_Box_Status_Data {

	public static function output_settings( $post ){
		wp_nonce_field( 'wc_sa_save_data', 'wc_sa_meta_nonce' );
		$status         = new WC_SA_Status($post);
		$order_statuses = wc_get_order_statuses();
		include_once 'views/html-status-settings.php';
	}


	public static function save( $post_id, $post ){
		self::save_options($post_id, $post);
		self::generate_styles($post_id);
		wc_delete_shop_order_transients();
	}
	private static function save_options( $post_id, $post ){
		$sa = new WC_SA_Status(0);
		
		foreach ($sa->get_defaults() as $meta_key => $default_val ) {
			$meta_val = isset($_POST[$meta_key]) ? $_POST[$meta_key] : $default_val;
			update_post_meta($post_id, '_'.$meta_key, $meta_val);
		}
		$label = get_post_meta($post_id, '__label', true);	

		if( strlen($post->post_name) > 17){

			$original_slug = $post->post_name;
			$slug          = _truncate_post_slug( $original_slug, 17);
			$post_type     = 'wc_custom_statuses';

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

			$post->post_name = $slug;
			$query = "UPDATE {$wpdb->posts} SET post_name = %s WHERE ID = %d";
            $wpdb->query($wpdb->prepare( $query, $slug, $post_type, $post_id ));
			
		}
		
		if( !empty($label) && $post->post_name != $label ){
			global $wpdb;
			$new_label = 'wc-'.$post->post_name;
			$old_label = 'wc-'.$label;
			$query = "UPDATE {$wpdb->posts} SET post_status = '{$new_label}' WHERE post_status = '{$old_label}'";
            $wpdb->query($query);
		}
		update_post_meta($post_id, '__label', $post->post_name);
	}

	public static function generate_styles( $post_id ){
		$sa        = new WC_SA_Status($post_id);
		$file_path = WC_SA()->uploads_dir . '/dynamic-font-icons.css';

		$content   = '';
		if( file_exists( $file_path ) ) {
            $content = file_get_contents( $file_path );
        }		

        $delimiter  = '#';
		$startTag   = '/*status-'.$post_id.'-start*/';
		$endTag     = '/*status-'.$post_id.'-end*/';
		$regex = $delimiter . preg_quote($startTag, $delimiter) 
		                    . '(.*?)' 
		                    . preg_quote($endTag, $delimiter) 
		                    . $delimiter 
		                    . 's';
		preg_match($regex,$content,$matches);

		$icod   = !empty($sa->status_icon) ? $sa->status_icon : 'e014';
		$aicod  = !empty($sa->action_icon) ? $sa->action_icon : 'e014';
		$icolor = !empty($sa->status_colour) ? $sa->status_colour : '#aa2727';
		$new_style = '';
		switch ($sa->icon_style) {
			case 'icon-outline':
				$new_style = '
				.widefat .column-order_status mark.'.$sa->label.'::after{
					color: '.$icolor.';
					content: "\\'.$icod.'";
					font-family: \'WC-SA-Icons\' !important;
					font-variant: normal;
				    font-weight: 400;
				    height: 100%;
				    left: 0;
				    margin: 0;
				    position: absolute;
				    text-indent: 0;
				    text-transform: none;
				    top: 0;
				    width: 100%;
				    font-size: 47%;
				    line-height: 17px;
				}
				.widefat .column-order_status mark.'.$sa->label.'{
					color:'.$icolor.';
					border: 1px solid '.$icolor.';
					width: 11px;
    				height: 11px;
    				padding: 3px;
    				border-radius: 100%;
    				text-align: center;
				}				
				';
				break;
			case 'text-color':
				$new_style = '
				.widefat .column-order_status mark.'.$sa->label.'{
					background-color:'.$icolor.';
					border: 1px solid '.$icolor.';
					color: #fff;
				    display: block;
				    border-radius: 16px;
				    font-size: 0px;
				    font-weight: normal;
				    line-height: 0px;
				    min-width: 80px;
				    padding: 0;
				    text-align: center;
				    width: auto;
				    height: auto;
				}
				.widefat .column-order_status mark.'.$sa->label.':after{
					content: "'.$sa->label.'";
				    display: block;
				    font-size: 9px;
				    line-height: 17px;
				    text-transform: uppercase;
				    font-weight: bold;
				    text-indent: 1px !important;
				}
				.widefat tr .column-order_status{
					width: 80px;
				}
				';
				break;
			case 'text-outline':
				$new_style = '
				.widefat .column-order_status mark.'.$sa->label.'{
					color:'.$icolor.';
					border: 2px solid '.$icolor.';
				    display: block;
				    border-radius: 16px;
				    font-size: 0px;
				    line-height: 0px;
				    min-width: 80px;
				    padding: 0;
				    text-align: center;
				    text-indent: 1px;
				    width: auto;
				    height: auto;
				}
				.widefat .column-order_status mark.'.$sa->label.':after{
					content: "'.$sa->label.'";
				    display: block;
				    font-size: 9px;
				    line-height: 15px;
				    text-indent: 1px !important;
				    font-weight: bold;
				    text-transform: uppercase;
				}
				.widefat tr .column-order_status{
					width: 80px;
				}
				';
				break;
			
			default:
				$new_style = '
				.widefat .column-order_status mark.'.$sa->label.'::after{
					color: #fff;
					content: "\\'.$icod.'";
					font-family: \'WC-SA-Icons\' !important;
					font-variant: normal;
				    font-weight: 400;
				    height: 100%;
				    left: 0;
				    margin: 0;
				    position: absolute;
				    text-indent: 0;
				    text-transform: none;
				    top: 0;
				    width: 100%;
				    font-size: 47%;
				    line-height: 17px;
				}
				.widefat .column-order_status mark.'.$sa->label.'{
					background-color:'.$icolor.';
					border: 1px solid '.$icolor.';
					width: 11px;
    				height: 11px;
    				padding: 3px;
    				border-radius: 100%;
    				text-align: center;
				}
				';
				break;
		}


		switch ($sa->icon_style) {
			case 'icon-color':
				$new_style .= '
					#woocommerce_dashboard_status .wc_status_list li.'.$sa->label.'-orders a:before{
						color: #fff;
						content: "\\'.$icod.'";
						font-family: \'WC-SA-Icons\' !important;
						font-size: 12px;
						background-color:'.$icolor.';
						border: 1px solid '.$icolor.';
						width: 18px;
	    				height: 18px;
	    				line-height: 18px;
	    				padding: 3px;
	    				border-radius: 100%;
	    				text-align: center;
					}';
				break;
			default:
				$new_style .= '
					#woocommerce_dashboard_status .wc_status_list li.'.$sa->label.'-orders a:before{
						color: '.$icolor.';
						content: "\\'.$icod.'";
						font-family: \'WC-SA-Icons\' !important;
						font-size: 12px;
						color:'.$icolor.';
						border: 1px solid '.$icolor.';
						width: 18px;
	    				height: 18px;
	    				line-height: 18px;
	    				padding: 3px;
	    				border-radius: 100%;
	    				text-align: center;
					}';
				break;
		}

		$new_style = $startTag . $new_style . $endTag;
		
		if( $matches ){
			$content = str_replace($matches[0], $new_style, $content);
		}else{
			$content .= $new_style;
		}

		$fp = fopen($file_path, "w+");
		fwrite($fp, $content);
		fclose($fp);
	}
}
