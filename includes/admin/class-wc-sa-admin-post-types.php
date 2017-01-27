<?php
/**
 * Post Types Admin
 *
 * @author   Actuality Extensions
 * @category Admin
 * @package  WC_SA/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SA_Admin_Post_Types' ) ) :

/**
 * WC_SA_Admin_Post_Types Class.
 *
 */
class WC_SA_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// WP List table columns. Defined here so they are always available for events such as inline editing.
		add_filter( 'manage_wc_custom_statuses_posts_columns', array( $this, 'status_columns' ) );
		add_action( 'manage_wc_custom_statuses_posts_custom_column', array( $this, 'render_status_columns' ), 2 );
		add_filter( 'manage_edit-wc_custom_statuses_sortable_columns', array( $this, 'form_sortable_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 2, 100 );
		add_filter( 'disable_months_dropdown', array( $this, 'months_dropdown' ), 10, 2 );
		add_filter( 'edit_posts_per_page', array( $this, 'edit_posts_per_page' ), 10, 2 );
		add_action( 'pre_get_posts', array($this, 'status_default_order'), 9 );


		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );

		add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 99 );
		add_action('load-edit.php', array(&$this, 'custom_bulk_action'));

		// Meta-Box Class
		include_once( 'class-wc-sa-admin-meta-boxes.php' );
	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['wc_custom_statuses'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Status updated.', 'woocommerce_status_actions' ),
			2 => __( 'Custom field updated.', 'woocommerce_status_actions' ),
			3 => __( 'Custom field deleted.', 'woocommerce_status_actions' ),
			4 => __( 'Status updated.', 'woocommerce_status_actions' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Status restored to revision from %s', 'woocommerce_status_actions' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Status updated.', 'woocommerce_status_actions' ),
			7 => __( 'Status saved.', 'woocommerce_status_actions' ),
			8 => __( 'Status submitted.', 'woocommerce_status_actions' ),
			9 => sprintf( __( 'Status scheduled for: <strong>%1$s</strong>.', 'woocommerce_status_actions' ),
			  date_i18n( __( 'M j, Y @ G:i', 'woocommerce_status_actions' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Status draft updated.', 'woocommerce_status_actions' )
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 * @param  array $bulk_messages
	 * @param  array $bulk_counts
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['wc_custom_statuses'] = array(
			'updated'   => _n( '%s status updated.', '%s statues updated.', $bulk_counts['updated'], 'woocommerce_status_actions' ),
			'locked'    => _n( '%s status not updated, somebody is editing it.', '%s statuses not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce_status_actions' ),
			'deleted'   => _n( '%s status permanently deleted.', '%s statuses permanently deleted.', $bulk_counts['deleted'], 'woocommerce_status_actions' ),
			'trashed'   => _n( '%s status moved to the Trash.', '%s statuses moved to the Trash.', $bulk_counts['trashed'], 'woocommerce_status_actions' ),
			'untrashed' => _n( '%s status restored from the Trash.', '%s statuses restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce_status_actions' ),
		);

		return $bulk_messages;
	}


	/**
	 * Define custom columns for statuses.
	 * @param  array $existing_columns
	 * @return array
	 */
	public function status_columns( $existing_columns ) {
		$columns                       = array();
		$columns['sort']               = '';
		$columns['cb']                 = $existing_columns['cb'];
		$columns['title']              = __( 'Name', 'woocommerce_status_actions' );
		$columns['label']              = __( 'Label', 'woocommerce_status_actions' );
		$columns['order_status']       = __( 'Icon', 'woocommerce_status_actions' );
		$columns['order_actions']      = __( 'Action', 'woocommerce_status_actions' );
		$columns['email_notification'] = __( 'Email', 'woocommerce_status_actions' );
		$columns['display_in_reports'] = __( 'Reports', 'woocommerce_status_actions' );
		$columns['item_editing']       = __( 'Editing', 'woocommerce_status_actions' );
		$columns['automatic_trigger']  = __( 'Trigger', 'woocommerce_status_actions' );
		$columns['orders']             = __( 'Orders', 'woocommerce_status_actions' );

		return $columns;
	}

	/**
	 * Output custom columns for statuses.
	 *
	 * @param string $column
	 */
	public function render_status_columns( $column ) {
		global $post, $the_status;
		if( !$the_status || $the_status->id != $post->ID){
			$the_status = new WC_SA_Status($post);
		}

		switch ( $column ) {
			case 'sort' :
				printf( '<input type="hidden" class="column_sort_hidden" name="status_sort[%d]" value="%d" />', $the_status->id, $the_status->menu_order );
			break;
			case 'label' :
				echo $the_status->label;
			break;
			case 'order_status' :
				printf( '<mark class="%s tips" data-tip="%s">%s</mark>', sanitize_title( $the_status->label ), $the_status->title, $the_status->label );
			break;
			case 'order_actions' :
				$aicod = !empty($the_status->action_icon) ? $the_status->action_icon : 'e014';
				$_a    = array( $the_status->label, 'wc-sa-action-icon', 'wc-sa-icon-uni'.$aicod);
				printf( '<button type="button" class="button %s"></button>', implode(' ', $_a ) );
			break;
			case 'email_notification' :
				if( $the_status->email_notification == 'yes' && $the_status->email_recipients == 'both' ){
					printf( '<span class="status-enabled tips" data-tip="%s"></span>', __('Administrator & Customer', 'woocommerce_status_actions') );
				}else if( $the_status->email_notification == 'yes' && $the_status->email_recipients == 'customer' ){
					printf( '<span class="status-enabled tips" data-tip="%s"></span>', __('Customer', 'woocommerce_status_actions') );
				}else if( $the_status->email_notification == 'yes' && $the_status->email_recipients == 'admin' ){
					printf( '<span class="status-enabled tips" data-tip="%s"></span>', __('Administrator', 'woocommerce_status_actions') );
				}else if( $the_status->email_notification == 'yes' && $the_status->email_recipients == 'custom' ){
					printf( '<span class="status-enabled tips" data-tip="%s"></span>', $the_status->email_custom_address );
				}else{
					printf( '<span class="status-disabled tips" data-tip="%s"></span>', __('No', 'woocommerce_status_actions') );
				}
			break;
			case 'display_in_reports' :
				if( $the_status->display_in_reports == 'yes' ){
					printf( '<span class="status-enabled tips" data-tip="%s"></span>', __('Included In Reports', 'woocommerce_status_actions') );
				}else{
					printf( '<span class="status-disabled tips" data-tip="%s"></span>', __('Not Included In Reports', 'woocommerce_status_actions') );
				}
			break;
			case 'item_editing' :
				if( $the_status->item_editing == 'yes' ){
					printf( '<span class="status-enabled tips" data-tip="%s"></span>', __('Item Editing Enabled', 'woocommerce_status_actions') );
				}else{
					printf( '<span class="status-disabled tips" data-tip="%s"></span>', __('Item Editing Disabled', 'woocommerce_status_actions') );
				}
			break;
			case 'automatic_trigger' :
				if( $the_status->automatic_trigger == 'yes'){
					printf( '<span class="status-enabled tips" data-tip="%s %s %s %s %s"></span>', __('Automatic Trigger To', 'woocommerce_status_actions'), ucwords( substr($the_status->triggered_status, 3) ) , __('After', 'woocommerce_status_actions'), $the_status->time_period, ucfirst( $the_status->time_period_type));
				}else{
					printf( '<span class="status-disabled tips" data-tip="%s"></span>', __('Automatic Trigger Disabled', 'woocommerce_status_actions') );
				}
			break;
			case 'orders' :
				global $wpdb;
		        $sql = "SELECT COUNT(DISTINCT ID) FROM {$wpdb->posts} WHERE post_status = 'wc-{$the_status->label}' ";
		        $count = $wpdb->get_var($sql);
		        echo $count;
       		break;
		}
	}

	/**
	 * Make columns sortable.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function form_sortable_columns( $columns ) {
		return array();
	}
	


	/**
	 * Set row actions for statuses.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function row_actions( $actions, $post ) {

		if ( 'wc_custom_statuses' === $post->post_type ) {
			
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
				$actions['trash'] = sprintf('<a href="%1$s" aria-label="%2$s" class="submitdelete">%2$s</a>', admin_url('admin.php?page=wc_sa_delete_status&status_id[]=' . $post->ID), __('Delete permanently', 'woocommerce_status_actions') );
			}
		}

		return $actions;
	}

	/**
	 * Remove the 'Months' drop-down from the statuses table.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function months_dropdown( $action, $post_type ) {

		if ( 'wc_custom_statuses' === $post_type ) {
			$action = true;
		}

		return $action;
	}

	/**
	 * Show all items when specifically listing "statuses".
	 *
	 * @param int    $posts_per_page Number of posts to be displayed. Default 20.
	 * @param string $post_type      The post type.
	 */
	public function edit_posts_per_page($per_page, $post_type)
	{
		if( $post_type === 'wc_custom_statuses'){
			$per_page = 20;
		}
		return $per_page;
	}

	public function status_default_order( $query )
	{
		// Nothing to do:  
	    if( ! $query->is_main_query() || 'wc_custom_statuses' != $query->get( 'post_type' )  )
	        return;

	    //-------------------------------------------  
	    // Modify the 'orderby' and 'meta_key' parts
	    //-------------------------------------------  
	    $orderby = $query->get( 'orderby');

	    switch ( $orderby ) 
	    {
	        case '':  // <-- The default empty case
	            $query->set( 'orderby',  'menu_order' );
	            $query->set( 'order',  'ASC' );
	            break;
    	}
	}
	
	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'wc_custom_statuses' :
				$text = __( 'Status name', 'woocommerce_status_actions' );
			break;
		}

		return $text;
	}

	/**
	 * Change label for insert buttons.
	 * @param  array $strings
	 * @return array
	 */
	public function change_insert_into_post( $strings ) {
		global $post_type;

		if ( $post_type == 'wc_custom_statuses' ) {
			$obj = get_post_type_object( $post_type );

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'woocommerce_status_actions' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'woocommerce_status_actions' ), $obj->labels->singular_name );
		}

		return $strings;
	}

	/**
	 * Hidden default Meta-Boxes.
	 * @param  array  $hidden
	 * @param  object $screen
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( 'wc_custom_statuses' === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}

		return $hidden;
	}

	/**
	 * Add extra bulk action options to mark orders as complete or processing.
	 *
	 */
	public function bulk_admin_footer() {
		global $post_type;

		if ( 'wc_custom_statuses' == $post_type ) {
			?>
			<script type="text/javascript" id="sa-status-bulk-actions">
			jQuery(function() {
				jQuery('select[name="action"] option[value="trash"], select[name="action2"] option[value="trash').remove();
				jQuery('select[name="action"] option[value="edit"], select[name="action2"] option[value="edit').remove();
				jQuery('<option>').val('wc_sa_delete_status').text('<?php _e('Delete permanently', 'woocommerce_status_actions'); ?>').appendTo('select[name="action"], select[name="action2"]');
			});
			</script>
			<?php
		}
	}

	public function custom_bulk_action()
	{
		global $typenow;
		$post_type = $typenow;
		
		if($post_type == 'wc_custom_statuses') {
			
			// get the action
			$wp_list_table = _get_list_table('WP_Posts_List_Table');  // depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc
			$action = $wp_list_table->current_action();
			
			// allow only defined actions
			$allowed_actions = array('wc_sa_delete_status');
			if(!in_array($action, $allowed_actions)) return;
			
			// security check
			check_admin_referer('bulk-posts');
			
			// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'ids'
			if(isset($_REQUEST['post'])) {
				$post_ids = array_map('intval', $_REQUEST['post']);
			}
			
			if(empty($post_ids)) return;
			
			$sendback = admin_url('admin.php?page=wc_sa_delete_status');
			foreach ($post_ids as $status_id) {
				$sendback .= '&status_id[]=' . $status_id;
			}
			
			wp_redirect($sendback);
			exit();
		}
	}

}

endif;

new WC_SA_Admin_Post_Types();