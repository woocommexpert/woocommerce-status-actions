<?php
/**
 * Edit WooCommerce Order page
 *
 * @author   Actuality Extensions
 * @category Admin
 * @package  WC_SA/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SA_Order' ) ) :

/**
 * WC_SA_Order.
 */
class WC_SA_Order {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 99 );
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'admin_order_actions' ), 199, 2 );
			add_action( 'wc_order_is_editable', array($this, 'order_is_editable'), 50, 2 );
		}
		add_action( 'woocommerce_order_status_changed', array($this, 'order_status_changed'), 777, 3 );
		add_action( 'woocommerce_order_is_download_permitted', array($this, 'order_is_download_permitted'), 777, 2 );
		add_filter('woocommerce_payment_complete_order_status', array($this, 'payment_complete_order_status'));
		add_action( 'woocommerce_thankyou', array($this, 'change_payments_method_status' ), 777, 1 );
	}
	/**
	 * Add extra bulk action options to mark orders as complete or processing.
	 *
	 */
	public function bulk_admin_footer() {
		global $post_type;

		if ( 'shop_order' == $post_type ) {
			$default_statuses  = wc_sa_get_default_order_statuses();
			$order_statuses    = wc_sa_get_statuses();
       		$modifed_def_st    = get_option( 'wc_custom_status_edit_existing_status');
       		$need_remove       = array('processing', 'completed', 'on-hold');
			?>
			<script type="text/javascript" id="sa-status-bulk-actions">
			jQuery(function() {
                var $optgroup = jQuery('<optgroup>').attr('label', '<?php _e('WooCommerce statuses', 'woocommerce_status_actions'); ?>');

				<?php foreach($need_remove as $slug){ ?>
                    jQuery('select[name="action"] option[value="mark_<?php echo $slug; ?>"], select[name="action2"] option[value="mark_<?php echo $slug; ?>"]').remove();
                <?php } ?>
				<?php foreach ($default_statuses as $key => $value) {
					if( isset($modifed_def_st[$key]) && isset($modifed_def_st[$key]['hide_bulk']) && $modifed_def_st[$key]['hide_bulk'] == 'yes') continue;
					$st = substr( $key, 3 );
					$v = $modifed_def_st[$key]['label'];
					?>
					jQuery('<option>').val('mark_<?php echo $st; ?>').text('<?php printf(__( 'Mark %s', 'woocommerce_status_actions' ), strtolower($v) ); ?>').appendTo($optgroup);
				<?php } ?>

				if( $optgroup.find('option').length ){
					$optgroup.appendTo('select[name="action"], select[name="action2"]');
				}
				var $optgroup = jQuery('<optgroup>').attr('label', '<?php _e('Custom statuses', 'woocommerce_status_actions'); ?>');

				<?php foreach ($order_statuses as $st_id => $status) {
					$hide = get_post_meta($st_id, '_hide_bulk_actions', true);
					if( $hide == 'yes') continue;
					?>
					jQuery('<option>').val('mark_<?php echo $status->label; ?>').text('<?php printf(__( 'Mark %s', 'woocommerce_status_actions' ), strtolower($status->title) ); ?>').appendTo($optgroup);
				<?php } ?>
				if( $optgroup.find('option').length ){
					$optgroup.appendTo('select[name="action"], select[name="action2"]');
				}
			});
			</script>
			<?php
			$this->note_promt();
		}
	}

	public function admin_order_actions($actions, $the_order)
	{
		$new_actions	= array();
		$statuses       = wc_sa_get_statuses();
		$order_statuses = wc_get_order_statuses();
		if ($statuses) {
			foreach ($statuses as $st_id => $value) {
				$status      = new WC_SA_Status($st_id);
				$display_for = $status->action_visibility;
					$o_st = 'wc-' . $the_order->status;
				if(in_array( $o_st, $display_for )){

					$aicod = !empty($status->action_icon) ? $status->action_icon : 'e014';
					$_a    = array( $status->label, 'wc-sa-action-icon', 'wc-sa-icon-uni'.$aicod);
					if( $status->order_note_prompt == 'yes'){
                        $_a[] = 'sa_note_prompt';
                    }

					$new_actions[$status->label] = array(
						'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status='.$status->label.'&order_id=' . $the_order->id ), 'woocommerce-mark-order-status' ),
						'name'      => $status->title,
						'action'    => implode(' ', $_a )
					);
				}
				if( $the_order->status == $status->label ) {
					if( !empty($status->show_action_buttons)){
						foreach ($status->show_action_buttons as $st_key) {
							$_key = substr($st_key, 3);
							if( isset($order_statuses[$st_key]) ){
								$_action = $_key;
								$name    = $order_statuses[$st_key];
								switch ($_key) {
									case 'completed':
										$_action = 'complete';
										$name    = __( 'Complete', 'woocommerce' );
										break;
								}
								$new_actions[$_action] = array(
									'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status='.$_key.'&order_id=' . $the_order->id ), 'woocommerce-mark-order-status' ),
									'name'      => $name,
									'action'    => $_action
								);								
							}
						}
					}
				}
			}
		}
		return array_merge($new_actions, $actions);
	}

	private function note_promt()
	{
		?>
	     <script type="text/html" id="tmpl-wc_as_note_prompt-modal">
	        <div class="media-frame-title">
	            <h1><?php _e('Add note', 'woocommerce_status_actions'); ?></h1>
	        </div>
	        <form class="wc_as_note_prompt_form" method="post">
	            <div class="media-frame-content" data-columns="10">
	                <?php _e('Add a note for your reference, or add a customer note (the user will be notified).', 'woocommerce_status_actions'); ?>
	                <textarea id="add_order_note" class="input-text" rows="5" name="sa_order_note" type="text"></textarea>
	                <p>
	                    <select id="order_note_type" name="sa_order_note_type">
	                        <option value=""><?php _e('Private note', 'woocommerce_status_actions'); ?></option>
	                        <option value="customer"><?php _e('Note to customer', 'woocommerce_status_actions');?></option>
	                    </select>
	                    
	                </p>
	            </div>
	            <div class="media-frame-toolbar">
	                <div class="media-toolbar">
	                    <div class="media-toolbar-primary search-form">
	                        <button type="submit" class="button  button-primary media-button"><?php _e('Add', 'woocommerce_status_actions'); ?></button>
	                    </div>
	                </div>
	            </div>
	        </form>
	    </script>
	     <?php
	}

	public function order_status_changed( $order_id, $old_status, $new_status )
    {
        $note  = isset($_POST['sa_order_note']) ? trim($_POST['sa_order_note']) : '' ;
        $order = wc_get_order( $order_id );
        $note  = apply_filters('bulk_handler_custom_action_note', $note, $new_status, $order );
        if( !empty($note) ){
            $is_customer_note  = isset($_POST['sa_order_note_type']) && $_POST['sa_order_note_type'] == 'customer' ? true : false;
            $order->add_order_note( $note, $is_customer_note, true );            
        }

        $custom = wc_sa_get_statusesList();
        if( isset($custom['wc-'.$new_status] )){
        	$status = wc_sa_get_status_by_name($new_status);
        
			if($status->stock_status == 'reduce'){
                $order->reduce_order_stock();
            }
            else if( $status->stock_status == 'restore'){
                $status->restore_order_stock($order);
            }

            if($status->automatic_trigger === 'yes'){
	            $triggered_status = $status->triggered_status;
	            $time_period      = (int)$status->time_period;
	            $time_period_type = $status->time_period_type;
	            $time = 0;
	            if($time_period > 0){
	                $time = strtotime( $time_period.' '.$time_period_type, 0);
	                wp_schedule_single_event( time() + $time, 'wc_sa_run_automatic_trigger', array( $order_id, $triggered_status, $new_status ) );
	            }
	        }


        }
        wc_delete_shop_order_transients( $order_id );
    }

    public function order_is_download_permitted($permitted, $order)
    {
    	$order_status = $order->get_status();
    	$statuses     = wc_sa_get_statuses_by_meta('_downloads_permitted', 'yes');
    	if( in_array($order_status, $statuses) ){
    		$permitted = true;
    	}

        return $permitted;
    }

    public function order_is_editable($editable, $order)
    {
    	$order_status    = $order->get_status();
    	$statuses        = wc_sa_get_statuses_by_meta('_item_editing', 'yes');
    	$existing_status = get_option('wc_custom_status_edit_existing_status', array());
    	if( in_array($order_status, $statuses) ){
    		$editable = true;
    	}else if( isset($existing_status['wc-'.$order_status] )) {
    		if( isset($existing_status['wc-'.$order_status]['item_editing'] ) && $existing_status['wc-'.$order_status]['item_editing'] == 'yes' ){
				$editable = true;    			
    		}else{
    			$editable = false;
    		}
    	}

    	return $editable;
    }

    public function payment_complete_order_status($new_order_status)
    {
        if($new_order_status == 'processing'){
            $complete_status = get_option('wc_custom_status_payment_complete_status');
            if($complete_status && !empty($complete_status))
                $new_order_status = substr($complete_status, 3);
        }
        return $new_order_status;
    }

    public function change_payments_method_status($order_id )
    {
         if ( !$order_id )
            return;

        $saved_st = get_option('wc_sa_payment_gateway_statuses');
        if( !is_array($saved_st) ){
            $saved_st = array();
        }

        $order  = new WC_Order( $order_id );
        $pm_id = $order->payment_method;

        if( $pm_id && !empty($pm_id) && isset($saved_st[$pm_id]) ){
            $new_st = $saved_st[$pm_id];
            if( !empty($new_st)){
                $new_st = 'wc-' === substr( $new_st, 0, 3 ) ? substr( $new_st, 3 ) : $new_st;
                $order->update_status( $new_st );                
            }
        }

    }


}

endif;

new WC_SA_Order();