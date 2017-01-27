<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SA_Email' ) ) :

/**
 *
 * @class       WC_SA_Email
 */
class WC_SA_Email extends WC_Email {

	public $status;
	public $message_text;
	public $order_info;
	/**
	 * Constructor.
	 */
	public function __construct($st_id) {

		$status               = wc_sa_get_status($st_id);
		$this->status         = $status;
        $this->sent_to_admin  = false;
		$this->id             = 'wc_sa_order' . $status->label;
		$this->customer_email = $status->email_recipients === 'customer';
		$this->title          = sprintf( __( 'Order status is set to %s', 'woocommerce_status_actions' ), $status->title);
		$this->description    = sprintf( __( "This email is sent when order status is set to %s", 'woocommerce_status_actions' ), $status->title );

		$this->heading          = stripslashes($status->email_heading);
        $this->subject          = stripslashes($status->email_subject);

		$this->template_html    = WC_SA()->templates_dir . '/wc_custom_status_email_html_template.php';
        $this->template_plain   = WC_SA()->templates_dir . '/wc_custom_status_email_plain_template.php';

		// Triggers for this email
		add_action( 'woocommerce_order_status_'.$status->label.'_notification', array( $this, 'trigger' ), 150, 1 );


		// Call parent constuctor
		parent::__construct();


		// Other settings
        $this->email_type             = $status->email_type;
        $this->from_name              = stripslashes($status->email_from_name);
        $this->from_email             = $status->email_from_address;
        $this->message_text           = $status->email_message == 'yes' ? stripslashes(nl2br($status->email_message_text)) : '';
        $this->order_info             = $status->email_order_info == 'yes' ? true : false;
        $this->custom_email_address   = $status->email_custom_address;
		switch ($status->email_recipients) {
			case 'custom':
				$this->recipient = $status->email_custom_address;
                $this->sent_to_admin  = false;
				break;
			default:
				$this->recipient = get_option('admin_email');
                $this->sent_to_admin  = true;
				break;
		}
	}

	/**
	 * Trigger.
	 *
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {

		if ( $order_id ) {
			$this->object = wc_get_order( $order_id );
			if( $this->customer_email ){
				$this->recipient     = $this->object->billing_email;
                $this->sent_to_admin = false;
			}
        	$this->process_shortcodes($order_id);
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		if( $this->status->email_recipients == 'both' && $order_id ){
			$this->recipient = $this->object->billing_email;
            $this->sent_to_admin = false;
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}
	public  function init_form_fields() {
		return array();
	}
	/**
	 * Checks if this email is enabled and will be sent.
	 * @return bool
	 */
	public function is_enabled() {
		return 'yes' === $this->status->email_notification;
	}

	/**
	 * Get valid recipients.
	 * @return string
	 */
	public function get_recipient() {
		if( isset($_GET['page']) && $_GET['page'] == 'wc-settings' && !$this->customer_email ){
			switch( $this->status->email_recipients ){
                case 'admin':
                    return __('Administrator', 'woocommerce_status_actions');
                    break;
                case 'both':
                	return __('Administrator & Customer', 'woocommerce_status_actions');
                    break;
            }			
		}
		$recipient  = apply_filters( 'woocommerce_email_recipient_' . $this->id, $this->recipient, $this->object );
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients = array_filter( $recipients, 'is_email' );
		return implode( ', ', $recipients );
	}

    public function get_message_text()
    {
        return apply_filters( 'woocommerce_email_custom_info_' . $this->id, $this->format_string( $this->message_text ), $this->object );
    }

	public function get_content_html() {
        ob_start();
        extract(array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'custom_info'   => $this->get_message_text(),
            'order_info'    => $this->order_info,
            'sent_to_admin' => $this->sent_to_admin,
            'plain_text'    => false,
            'email'         => $this
        ) );
        include $this->template_html;
        return ob_get_clean();
    }

    public function get_content_plain() {
        ob_start();
        extract(array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'custom_info'   => $this->get_message_text(),
            'order_info'    => $this->order_info,
            'sent_to_admin' => $this->sent_to_admin,
            'plain_text'    => true,
            'email'         => $this
        ) );
        include $this->template_plain;
        return ob_get_clean();
    }

    public function get_from_address() {
        return sanitize_email( $this->from_email );
    }

    public function get_from_name() {
        return wp_specialchars_decode(stripslashes($this->from_name), ENT_QUOTES);
    }

	 //Process shortcodes
	public function process_shortcodes($order_id)
	{
        //$this->object = wc_get_order( $order_id );
        if( !is_array($this->find)){
            $this->find = array();
        }
        if( !is_array($this->replace)){
            $this->replace = array();
        }

        $this->find['order_date']         = '{order_date}';
        $this->replace['order_date']      = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );

        $this->find['order_number']       = '{order_number}';
        $this->replace['order_number']    = $this->object->get_order_number();

        $this->find['order_value']        = '{order_value}';
        $this->replace['order_value']     = $this->object->order_total;
        
        $this->find['payment_method']     = '{payment_method}';
        $this->replace['payment_method']  = $this->object->payment_method_title;
        
        $this->find['shipping_method']    = '{shipping_method}';
        $this->replace['shipping_method'] = $this->object->get_shipping_method();

        $this->find['billing_address']    = '{billing_address}';
        $this->replace['billing_address'] = $this->object->get_formatted_billing_address();

        $this->find['shipping_address']    = '{shipping_address}';
        $this->replace['shipping_address'] = $this->object->get_formatted_shipping_address();

        $customer_first_name = "";
        $customer_last_name = "";
        $customer_user_object = get_user_by('id', $this->object->customer_user);
        if($customer_user_object){
            $customer_first_name = $customer_user_object->get('first_name');
            $customer_last_name = $customer_user_object->get('last_name');
        }
        $this->find['customer_first_name']    = '{customer_first_name}';
        $this->find['customer_last_name']     = '{customer_last_name}';
        $this->replace['customer_first_name'] = stripslashes( $customer_first_name );
        $this->replace['customer_last_name']  = stripslashes( $customer_last_name );



        $default_fields = array(
            'country',
            'first_name',
            'last_name',
            'company',
            'address_1',
            'address_2',
            'city',
            'state',
            'postcode',
            'email',
            'phone',
            );
        $default_fields = array_flip($default_fields);
        $billing_fields = get_option('wc_fields_billing');
        $sufix = '';
        if( !$billing_fields || !is_array($billing_fields) ){
            $sufix = 'billing_';
            $billing_fields = $default_fields;
        }
        foreach($billing_fields as $name => $value) {
            $key = $sufix.$name;
            $this->find[$key] = '{' . $key . '}';
            $field_val = get_post_meta ($order_id, '_' . $key, true);
            if ($field_val) {
                $this->replace[$key] = $field_val;
            }else{
                $this->replace[$key] = '';
            }
        }
        $billing_fields = get_option('wc_fields_shipping');
        $sufix = '';
        if( !$billing_fields || !is_array($billing_fields) ){
            $sufix = 'shipping_';
            $billing_fields = $default_fields;
        }
        foreach($billing_fields as $name => $value) {
            $key = $sufix.$name;
            $this->find[$key] = '{' . $key . '}';
            $field_val = get_post_meta ($order_id, '_' . $key, true);
            if ($field_val) {
                $this->replace[$key] = $field_val;
            }else{
                $this->replace[$key] = '';
            }
        }

        $default_tracking_fields = array(
            'tracking_provider',
            'custom_tracking_provider',
            'tracking_number',
            'custom_tracking_link',
            'date_shipped',
            );
        $default_tracking_fields = array_flip($default_tracking_fields);
        $hipment_tracking_items  = get_post_meta($order_id, '_wc_shipment_tracking_items', true);
        $items                   = !is_array($hipment_tracking_items) ? array() : $hipment_tracking_items;

        foreach ($default_tracking_fields as $key => $value) {
            $this->find[$key]    = '{'.$key.'}';
            $this->replace[$key] = isset($items[$key]) ? $items[$key] : get_post_meta($order_id, '_'.$key, true);
        }
        
        
		
        $custom_fields = get_option('wc_fields_additional');
        if ($custom_fields) {
            foreach($custom_fields as $name => $value) {
                if( !empty($name) ){
                    $val = get_post_meta($order_id, $name, true);
                    if( is_string($val) ){
                        $this->find[$name]    = '{' . $name . '}';
                        $this->replace[$name] = $val;
                    }
                }
            }
        }

        $advanced_fields = wc_sa_get_acf_editor_btns();
        if ($advanced_fields && !empty($advanced_fields) && is_array($advanced_fields)) {
            foreach($advanced_fields as $name => $value) {
                if( !empty($name) ){
                    $val = get_post_meta($order_id, $name, true);
                    if( is_string($val) ){
                        $this->find[$name]    = '{' . $name . '}';
                        $this->replace[$name] = $val;
                    }
                }
            }
        }
        
        $aftership_tracking_provider = get_post_custom_values('_aftership_tracking_provider_name', $order_id);
	    
        $this->find['aftership_tracking_provider_name']    = '{aftership_tracking_provider_name}';
	    $this->replace['aftership_tracking_provider_name'] = isset($aftership_tracking_provider[0]) ? $aftership_tracking_provider[0] : '';
		
		$aftership_tracking_number = get_post_custom_values('_aftership_tracking_number', $order_id);
		
	    $this->find['aftership_tracking_number']    = '{aftership_tracking_number}';
	    $this->replace['aftership_tracking_number'] = isset($aftership_tracking_number[0]) ? $aftership_tracking_number[0] : '';
    
        
        /*$this->message_text = str_replace($shortcodes,$replacements, $this->message_text);

        $this->heading = str_replace($shortcodes,$replacements,$this->heading);
        $this->subject = str_replace($shortcodes,$replacements,$this->subject);*/
	}
}

endif;