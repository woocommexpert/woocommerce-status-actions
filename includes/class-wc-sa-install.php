<?php
/**
 * Installation related functions and actions.
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
 * WC_SA_Install Class
 */
class WC_SA_Install {

	/** @var array DB updates that need to be run */
	private static $db_updates = array(
		'1.7.8' => 'updates/wc_sa-update-1.7.8.php',
		'2.0.2' => 'updates/wc_sa-update-2.0.2.php',
		'2.0.6' => 'updates/wc_sa-update-2.0.6.php',
	);

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'plugin_action_links_' . WC_SA_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ), 80, 1 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		
		// Run this on activation.
		add_action( 'wpmu_new_blog', array( __CLASS__, 'new_blog'), 10, 6);
	}

	/**
	 * check_version function.
	 */
	public static function check_version() {
		
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( WC_SA_TOKEN . '_version' ) != WC_SA_VERSION ) ) {
			self::install();
			do_action( 'wc_sa_updated' );
		}
	}


	/**
	 * Install WC
	 */
	public static function install() {
		global $wpdb;
		if( defined( 'WC_SA_INSTALLING' ) ){
			return;
		}
		if ( ! defined( 'WC_SA_INSTALLING' ) ) {
			define( 'WC_SA_INSTALLING', true );
		}

		try {
			if ( ! file_exists( WC_SA()->uploads_dir ) ) {
			    wp_mkdir_p( WC_SA()->uploads_dir );
			}
			$file_path = WC_SA()->uploads_dir . '/dynamic-font-icons.css';
			if ( ! file_exists( $file_path ) ) {
			    $fp = fopen($file_path, "w+");
				fwrite($fp, '');
				fclose($fp);
			}
		} catch (Exception $e) {}

		// Queue upgrades/setup wizard
		#$current_wc_sa_version  = get_option( WC_SA_TOKEN . '_version', null );
		#$current_db_version     = get_option( WC_SA_TOKEN . '_db_version', null );

		$old_wc_sa_version      = get_option( "woocommerce_status_actions_db_version", null );

		#$major_wc_version       = substr( WC_SA_VERSION, 0, strrpos( WC_SA_VERSION, '.' ) );
		#$major_cur_version      = substr( $current_wc_sa_version, 0, strrpos( $current_wc_sa_version, '.' ) );

		if( !is_null($old_wc_sa_version)){
			self::update('1.7.8');
			delete_option( "woocommerce_status_actions_db_version" );
		}
		

		/*if ( ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
			WC_SA_Admin_Notices::add_notice( 'wcsa_update' );
		} else {
			self::update_db_version();
		}*/

		self::update();
		self::update_db_version();
		self::update_wcsa_version();

		/*
		 * Deletes all expired transients. The multi-table delete syntax is used
		 * to delete the transient record from table a, and the corresponding
		 * transient_timeout record from table b.
		 *
		 * Based on code inside core's upgrade_network() function.
		 */
		$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d";
		$wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) );
		
		// Trigger action
		do_action( 'wc_sa_installed' );
	}

	/**
	 * Handle updates
	 */
	private static function update($v = '') {
		if( !empty($v) && isset(self::$db_updates[$v])){
			include( self::$db_updates[$v] );
		}else{
			$current_db_version = get_option( WC_SA_TOKEN.'_db_version');

			foreach ( self::$db_updates as $version => $updater ) {
				if ( version_compare( $current_db_version, $version, '<' ) ) {
					include( $updater );
					self::update_db_version( $version );
				}
			}			
		}

		self::update_db_version();
	}

	/**
	 * Update WC version to current
	 */
	private static function update_wcsa_version() {
		delete_option( WC_SA_TOKEN . '_version' );
		update_option( WC_SA_TOKEN . '_version', WC_SA_VERSION );
	}

	/**
	 * Update DB version to current
	 */
	private static function update_db_version( $version = null ) {
		delete_option( WC_SA_TOKEN.'_db_version' );
		add_option( WC_SA_TOKEN.'_db_version', is_null( $version ) ? WC_SA_VERSION : $version );
	}

	

	/**
     * Show action links on the plugin screen.
     *
     * @param   mixed $links Plugin Action links
     * @return  array
     */
    public static function plugin_action_links( $links ) {
        $plugin_links = array();
    
        $plugin_links['settings'] = sprintf( '<a href="%s" title="%s">%s</a>', '' . admin_url( 'admin.php?page=wc-settings&tab=wc_sa_settings' ) . '', __( 'View Settings', 'woocommerce_status_actions' ), __( 'Settings', 'woocommerce_status_actions' ) );

        return array_merge( $plugin_links, $links );
    }
    
    /**
    * Show row meta on the plugin screen.
    *
    * @param mixed $links Plugin Row Meta
    * @param mixed $file  Plugin Base file
    * @return  array
    */
    public static function plugin_row_meta( $links, $file ) {
        if ( $file == WC_SA_PLUGIN_BASENAME ) {
          $row_meta = array(
            'docs'    => '<a href="' . esc_url( 'http://actualityextensions.com/documentation/woocommerce-order-status-actions-manager/' ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce_status_actions' ) ) . '">' . __( 'Docs', 'woocommerce_status_actions' ) . '</a>',
            'support' => '<a href="' . esc_url( 'http://actualityextensions.com/contact/' ) . '" title="' . esc_attr( __( 'Visit Support', 'wc_sa' ) ) . '">' . __( 'Support', 'woocommerce_status_actions' ) . '</a>',
          );
        
          return array_merge( $links, $row_meta );
        }
        
        return (array) $links;
    }


	public function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        global $wpdb;
        if (is_plugin_active_for_network(WC_SA_PLUGIN_BASENAME)) {
            $old_blog = $wpdb->blogid;
            switch_to_blog($blog_id);
            self::install();
            switch_to_blog($old_blog);
        }
    }
	
}

WC_SA_Install::init();