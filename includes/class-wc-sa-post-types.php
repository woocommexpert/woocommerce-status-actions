<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     WC_SA_Post_types
 * @category  Class
 * @author    Actuality Extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_SA_Post_types Class.
 */
class WC_SA_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 105 );
		add_filter('woocommerce_screen_ids', array( __CLASS__, 'woocommerce_screen_ids'), 150);
	}
	public static function woocommerce_screen_ids($screen_ids)
	{
		$screen_ids[] = 'wc_custom_statuses';
		$screen_ids[] = 'edit-wc_custom_statuses';
		return $screen_ids;
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('wc_custom_statuses') ) {
			return;
		}

		do_action( 'wc_sa_register_post_type' );

		register_post_type( 'wc_custom_statuses',
				array(
					'labels'              => array(
							'name'               => __( 'Order Statuses', 'woocommerce_status_actions' ),
							'singular_name'      => __( 'Status', 'woocommerce_status_actions' ),
							'menu_name'          => _x( 'Order Statuses', 'Admin menu name', 'woocommerce_status_actions' ),
							'add_new'            => __( 'Add Status', 'woocommerce_status_actions' ),
							'add_new_item'       => __( 'Add New Status', 'woocommerce_status_actions' ),
							'edit'               => __( 'Edit', 'woocommerce_status_actions' ),
							'edit_item'          => __( 'Edit Status', 'woocommerce_status_actions' ),
							'new_item'           => __( 'New Status', 'woocommerce_status_actions' ),
							'view'               => __( 'View Statuses', 'woocommerce_status_actions' ),
							'view_item'          => __( 'View Status', 'woocommerce_status_actions' ),
							'search_items'       => __( 'Search Statuses', 'woocommerce_status_actions' ),
							'not_found'          => __( 'No Statuses found', 'woocommerce_status_actions' ),
							'not_found_in_trash' => __( 'No Statuses found in trash', 'woocommerce_status_actions' ),
							'parent'             => __( 'Parent Status', 'woocommerce_status_actions' ),
							'featured_image'     => __( 'Icon', 'woocommerce_status_actions' ),
							'set_featured_image' => __( 'Set Icon', 'woocommerce_status_actions' ),
							'remove_featured_image' => __( 'Remove Icon', 'woocommerce_status_actions' ),
						),
					'description'         => __( 'This is where you can add new Statuses that you can use in orders.', 'woocommerce_status_actions' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'supports'            => array( 'title'),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true
				)
		);

	}

}

WC_SA_Post_types::init();
