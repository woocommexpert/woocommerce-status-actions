<?php
/**
 *
 * @category Admin
 * @package  WC_SA/Classes
 * @version  1.0.0
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_SA_Delete Class
 */
class WC_SA_Delete {


	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action('admin_menu',array(__CLASS__,'add_admin_pages'));

		add_action( 'wp_loaded', array( __CLASS__, 'status_actions_delete' ), 20 );

	}

	public static function add_admin_pages($value='')
	{
		if( isset($_GET['page']) && $_GET['page'] == 'wc_sa_delete_status' ){
			add_submenu_page('woocommerce', __('Delete Status', 'woocommerce_status_actions'),  __('Delete Status', 'woocommerce_status_actions') , 'manage_woocommerce', 'wc_sa_delete_status', array(__CLASS__, 'output_delete_status'));
		}
	}

	/**
     * Renders the delete page on bulk action and per single delete
     * @return [type] [description]
     */
    public static function output_delete_status() {
        // If status_id is not submitted view an error message
        if( ( empty( $_GET['status_id'] ) && empty( $_POST['status_id'] ) ) ) {
            ?>
            <div id='message' class='updated below-h2'>
                <p>
                    Something is wrong. Please go back and try again.
                </p>
            </div>
            <?php
        } else {
            // If the get parameter is set, it's a single deletion
            $arr = $_GET['status_id'];
            // Important for ajax handling
            $class = 'linen_confirm_multi';
            $select_statuses    = array();            
            $order_statuses     = wc_get_order_statuses();
            $custom_statuses    = wc_sa_get_statuses();
            
            foreach ($arr as $status_id) {
                if( array_key_exists ($status_id, $custom_statuses) ){
            		$status_slug = 'wc-'. $custom_statuses[$status_id]->label;
                    $select_statuses[ $status_id] = $custom_statuses[$status_id]->title;
                    unset($order_statuses[ $status_slug ]);
                }
            }
            
            $order_select = '';
            foreach( $order_statuses as $o => $o_name ) {
                $order_select .= '<option value="' . $o . '">' . $o_name . '</option>';
            }
           
            
            // Build the selectbox and the confirmation button
            
            ?>
            <div class="wrap">
                <h2>Delete Status</h2>
                <form method="POST">
            <?php
            foreach( $select_statuses as $status_id => $status_name ) {
                ?>
                <div id="wrapper_for_<?php echo $status_id; ?>">
                    <fieldset>
                    <ul style="list-style:none;">
                        <li>
                            <div id="select_for_<?php echo $status_id; ?>">
	                            <label for="delete_option1">Attribute all <?php echo $status_name; ?> orders to:</label>
                                <select name="wc_sa_delete_statuses[<?php echo $status_id; ?>]" class="linen_order_statuses" data-status_id="<?php echo $status_id; ?>">
                                    <?php echo $order_select; ?>
                                </select>
                            </div>
                        </li>
                    </ul>
                    </fieldset>
                    </div>
                <?php
            }
            wp_nonce_field( 'delete_custom_status', 'nonce' );
        ?>
        <p class="submit"><button type="submit" class="button" ><?php _e('Confirm Deletion', 'woocommerce_status_actions'); ?></button></p>
        </form>
        </div>
        <?php
        }
    }

    public static function status_actions_delete()
    {
    	global $wpdb;
        // If our required post parameters are set
        $i = 0;
        if( isset( $_POST['wc_sa_delete_statuses'] ) && is_array($_POST['wc_sa_delete_statuses']) && !empty($_POST['wc_sa_delete_statuses']) ) {
            // If not an administrator disallow it
            if ( !is_admin() ) return;
            // If user can not edit shop orders disallow it
            if ( !current_user_can('edit_shop_orders') ) return;
            // If nonce is not set or wrong, disallow it
            if( empty( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'delete_custom_status' ) ) return;

            $custom_statuses    = wc_sa_get_statuses();
            foreach ($_POST['wc_sa_delete_statuses'] as $status_id => $new_label) {
            	$delete_label = 'wc-' . $custom_statuses[$status_id]->label;
                $query = "UPDATE {$wpdb->posts} SET post_status = '{$new_label}' WHERE post_status = '{$delete_label}'";
                $wpdb->query($query);
        		wp_delete_post( $status_id, true );
                $i++;
            }

            $sendback = admin_url( "edit.php?post_type=wc_custom_statuses" );
            if ($i > 0) {
                $sendback = add_query_arg( array('deleted' => $i), $sendback );
            }
            wp_redirect($sendback);
        }
    }
	
}

WC_SA_Delete::init();