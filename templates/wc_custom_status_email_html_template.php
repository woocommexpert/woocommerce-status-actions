<?php
/**
 * Custom status email
 *
 * @author 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<?php echo $custom_info; ?>

<?php if($order_info) { ?>

<?php  /**
  * @hooked WC_Emails::order_details() Shows the order details table.
  * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
  * @since 2.5.0
  */
 do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

 /**
  * @hooked WC_Emails::order_meta() Shows order meta data.
  */
 do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

 /**
  * @hooked WC_Emails::customer_details() Shows customer details
  * @hooked WC_Emails::email_address() Shows email address
  */
 do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
  ?>

<?php } ?>
<?php do_action('woocommerce_email_footer'); ?>