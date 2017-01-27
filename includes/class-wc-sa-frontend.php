<?php
/**
 * @author   Actuality Extensions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SA_Frontend' ) ) :

/**
 * WC_SA_Frontend Class.
 */
class WC_SA_Frontend {

	public $allow_product_reviews;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->allow_product_reviews = wc_sa_get_statuses_by_meta('_product_reviews', 'yes');
		add_filter( 'woocommerce_valid_order_statuses_for_payment', array( &$this, 'pay_button_handler' ) );
		add_filter( 'woocommerce_valid_order_statuses_for_cancel', array( &$this, 'cancel_button_handler' ) );
		add_filter( 'woocommerce_my_account_my_orders_actions', array( &$this, 'my_account_my_orders_actions' ), 10, 2 );
		add_filter('query', array($this, 'wc_customer_bought_product'), 50, 1);


	}

	public function pay_button_handler($statuses)
	{
		$st = wc_sa_get_pay_button_statuses();
		if( $st ){
			$statuses = array_merge($statuses, $st);
		}
		return $statuses;
	}

	public function cancel_button_handler($statuses)
	{
		$cancels = wc_sa_get_can_cancel_statuses();
		if( $cancels ){
			$statuses = array_merge($statuses, $cancels);
		}
		return $statuses;
	}

	public function my_account_my_orders_actions($actions, $order)
	{
		$st_actions      = wc_sa_get_statuses_by_meta('_customer_account', 'yes', true);
		$confirm_prompt  = wc_sa_get_statuses_by_meta('_customer_confirm_prompt', 'yes', true);
		$confirm_prompt  = array_intersect($confirm_prompt, $st_actions);

		if( $st_actions ){
			$o_status     = $order->get_status();
			$order_status = 'wc-'.$o_status;
			foreach ($st_actions as $st_id => $label) {
				$status       = wc_sa_get_status($st_id);
				$visibility   = $status->customer_account_visibility;
				if( in_array($order_status, $visibility) ){
					$button_label = $status->customer_account_button_label;
					$url = wp_nonce_url( 
								admin_url( "admin-ajax.php?action=wc_sa_mark_order_status&order_id={$order->id}&status={$label}" ), "wc-sa-mark-order-status" 
							);
					$key = !empty($confirm_prompt) && in_array($label, $confirm_prompt) ? 'prompt_mark_custom_status_'.$label : 'mark_custom_status_'.$label ;
					$actions[$key] = array(
							'url'  => $url,
							'name' => !empty($button_label) ? $button_label : $status->title
						);
				}
			}
		}

		return $actions;
	}

    public function wc_customer_bought_product($query){
    	global $wpdb;
        $query = trim($query);
        $pos  = strpos($query, "SELECT im.meta_value FROM {$wpdb->posts} AS p");
        if($pos === 0 && !empty($this->allow_product_reviews)){
        	$query = trim(preg_replace('/\s+/', ' ', $query));
        	$pos2 = strpos($query, "p.post_status IN ( 'wc-completed', 'wc-processing' ) AND");
        	if( $pos2 !== false ){
        		$statuses = "'wc-" . implode("', 'wc-", $this->allow_product_reviews) . "'";
        		$query = str_replace("p.post_status IN ( 'wc-completed', 'wc-processing' ) AND", "p.post_status IN ( 'wc-completed', 'wc-processing', {$statuses} ) AND", $query);
        	}
        	
        }
        
        return $query;
    }
}

endif;

new WC_SA_Frontend();