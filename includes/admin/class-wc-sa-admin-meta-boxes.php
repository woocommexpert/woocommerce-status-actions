<?php
/**
 * WC_SA Meta Boxes
 *
 * Sets up the write panels used by statuses (custom post types).
 *
 * @author      Actuality Extensions
 * @category    Admin
 * @package     WC_SA/Admin/Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_SA_Admin_Meta_Boxes.
 */
class WC_SA_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Form Meta Boxes
		add_action( 'wc_sa_process_wc_custom_statuses_meta', 'WC_SA_Meta_Box_Status_Data::save', 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'wc_sa_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'wc_sa_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'wc_sa_meta_box_errors' );
		}
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen = get_current_screen();

		// data
		add_meta_box( 'wc-sa-options', __( 'Status Data', 'wc_custom_statuses' ), 'WC_SA_Meta_Box_Status_Data::output_settings', 'wc_custom_statuses', 'normal', 'high' );

	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'commentstatusdiv', 'wc_custom_statuses', 'normal' );
		remove_meta_box( 'slugdiv', 'wc_custom_statuses', 'normal' );
	}


	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['wc_sa_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wc_sa_meta_nonce'], 'wc_sa_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		self::$saved_meta_boxes = true;

		// Check the post type
		if ( in_array( $post->post_type, array(  'wc_custom_statuses' ) ) ) {
			do_action( 'wc_sa_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}

}

new WC_SA_Admin_Meta_Boxes();