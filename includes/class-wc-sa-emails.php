<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Transactional Emails Controller
 *
 * Emails Class which handles the sending on transactional emails and email templates. This class loads in available emails.
 *
 */
class WC_SA_Emails {

	/** @var array Array of email notification classes */
	public $emails;

	/** @var WC_SA_Emails The single instance of the class */
	protected static $_instance = null;

	/**
	 * Main WC_SA_Emails Instance.
	 *
	 * Ensures only one instance of WC_SA_Emails is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_SA_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '2.1' );
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 *
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array($this , 'email_classes') );
		add_filter( 'woocommerce_email_actions', array($this , 'email_actions') );
	}

	/**
	 * Init email classes.
	 */
	public function email_classes($emails) {
		include( 'emails/class-wc-sa-email.php' );
		$statuses = wc_sa_get_statuses();
		foreach ($statuses as $id => $value) {
			$emails['WC_SA_Email_'.$value->label] = new WC_SA_Email($id);
		}
		return $emails;
	}

	/**
	 * Hook in all transactional emails.
	 */
	public function email_actions($actions) {
		$statuses = wc_sa_get_statuses();
		foreach ($statuses as $id => $value) {
			$actions[] = 'woocommerce_order_status_' . $value->label;
		}
		return $actions;
	}
}

return new WC_SA_Emails();