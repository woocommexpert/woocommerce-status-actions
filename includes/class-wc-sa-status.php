<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Status
 *
 * @class    WC_SA_Status
 * @package  WC_SA/Classes
 * @category Class
 * @author   Actuality Extensions
 */
class WC_SA_Status {

	/** @public int Status (post) ID. */
	public $id                          = 0;

	/** @var $post WP_Post. */
	public $post                        = null;

	/** @public array Array of child status/posts. */
	public $children = null;


	public $defaults                    = array();

	/**
	 * Constructor gets the post object and sets the ID for the loaded status.
	 *
	 * @param int|WC_SA_Status|object $status Status ID, post object, or status object
	 */
	public function __construct( $status = 0 ) {
		if ( is_numeric( $status ) ) {
			$this->id   = absint( $status );
			$this->post = get_post( $this->id );
		} elseif ( $status instanceof WC_SA_Status ) {
			$this->id   = absint( $status->id );
			$this->post = $status->post;
		} elseif ( isset( $status->ID ) ) {
			$this->id   = absint( $status->ID );
			$this->post = $status;
		}
	}

	/**
	 * __isset function.
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->id, '_' . $key );
	}

	/**
	 * __get function.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		$value = get_post_meta( $this->id, '_' . $key, true );
		switch ($key) {
			case 'action_visibility':
			case 'show_action_buttons':
			case 'customer_account_visibility':
				if( !is_array($value) )
					$value = array();
				break;
			case 'title':
				$value = $this->post->post_title;
			break;
			case 'label':
				$value = $this->post->post_name;
			break;
			case 'menu_order':
				$value = $this->post->menu_order;
			break;
		}

		if ( false !== $value ) {
			$this->$key = $value;
		}
		

		return $value;
	}

	/**
	 * Get the status's post data.
	 *
	 * @return object
	 */
	public function get_post_data() {
		return $this->post;
	}


	/**
	 * Get the title of the post.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->post ? $this->post->post_title : '';
	}

	public function get_defaults()
	{
		return array(
			'display_in_reports'            => 'no',
			'dashboard_widget'              => 'no',
			'status_icon'                   => 'e014',
			'status_colour'                 => '#aa2727',
			'icon_style'                    => 'icon-color',
			'email_notification'            => 'no',
			'email_type'                    => 'html',
			'email_recipients'              => 'customer',
			'email_custom_address'          => '',
			'email_from_name'               => get_option( 'blogname' ),
			'email_from_address'            => get_option( 'admin_email' ),
			'email_subject'                 => '',
			'email_heading'                 => '',
			'email_order_info'              => 'no',
			'email_message'                 => 'no',
			'email_message_text'            => '',
			'customer_pay_button'           => 'no',
			'customer_cancel_orders'        => 'no',
			'customer_confirm_prompt'       => 'no',
			'customer_account'              => 'no',
			'customer_account_visibility'   => array(),
			'customer_account_button_label' => '',
			'product_reviews'               => 'no',
			'downloads_permitted'           => 'no',
			'item_editing'                  => 'no',
			'stock_status'                  => 'no_affection',
			'action_icon'                   => 'e014',
			'action_visibility'             => array(),
			'show_action_buttons'           => array(),
			'hide_bulk_actions'             => 'no',
			'order_note_prompt'             => 'no',
			'automatic_trigger'             => 'no',
			'triggered_status'              => '',
			'time_period'                   => '',
			'time_period_type'              => 'minutes'
		);
	}

	public function restore_order_stock( $order ) {
        $order_items    = $order->get_items();

        if ( ! get_option('woocommerce_manage_stock') == 'yes' && ! empty( $order_items ) ) {
            return;
        }

        foreach ( $order_items as $item ) {

            if ( $item['product_id'] > 0 ) {
                $_product = $order->get_product_from_item( $item );

                if ( $_product && $_product->exists() && $_product->managing_stock() ) {

                    $old_stock = $_product->stock;

                    $qty = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );

                    $new_quantity = $_product->increase_stock( $qty );

                    do_action( 'woocommerce_auto_stock_restored', $_product, $item );

                    $order->add_order_note( sprintf( __( 'Item #%s stock incremented from %s to %s.', 'woocommerce_status_actions' ), $item['product_id'], $old_stock, $new_quantity) );

                    $order->send_stock_notifications( $_product, $new_quantity, $item['qty'] );
                }
            }
        }
    } // End restore_order_stock()

}