<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_SA {

    /**
     * The single instance of WC_SA.
     * @var     object
     * @access  private
     * @since   1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin uploads directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $uploads_dir;

    /**
     * The plugin uploads URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $uploads_url;

    /**
     * The plugin templates directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $templates_dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * Default order statuses
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $default_statuses;

    /**
     * Default order statuses
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $color_statuses;

    /**
     * Default order statuses
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $default_editing;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct ( $file = '', $version = '1.0.0' ) {

        #Disable the compression of pages.
        ini_set('zlib.output_compression','off');

        $this->default_statuses = array(
            'wc-pending'    => _x( 'Pending Payment', 'Order status', 'woocommerce' ),
            'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
            'wc-on-hold'    => _x( 'On Hold', 'Order status', 'woocommerce' ),
            'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
            'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
            'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
            'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
        );
        $this->color_statuses = array(
            'wc-pending'    => '#ffba00',
            'wc-processing' => '#73a724',
            'wc-on-hold'    => '#999999',
            'wc-completed'  => '#2ea2cc',
            'wc-cancelled'  => '#a00a00',
            'wc-refunded'   => '#999999',
            'wc-failed'     => '#d0c21f',
            );

        $this->default_editing = array(
            'wc-pending'    => 'yes',
            'wc-on-hold'    => 'yes'
            );

        $this->_version = $version;
        $this->_token = 'wc_sa';

        // Load plugin environment variables
        $upload_dir = wp_upload_dir();        
        $this->uploads_dir   = trailingslashit($upload_dir['basedir']) . 'wc_sa_uploads';
        $this->uploads_url   = esc_url( trailingslashit($upload_dir['baseurl']) . 'wc_sa_uploads' );

        $this->file = $file;
        $this->dir = dirname( $this->file );
        $this->templates_dir = trailingslashit( $this->dir ) . 'templates';
        $this->assets_dir    = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url    = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

        $this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook( $this->file, array( $this, 'install' ) );

        // Load frontend JS & CSS
        #add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

        // Load admin JS & CSS
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );
        
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        add_action( 'init', array($this, 'init') );        

        do_action('wc_sa_init');

        // Handle localisation
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );
    } // End __construct ()

    public function define_constants()
    {
        define( 'WC_CUSTOM_STATUS_PLUGIN_PATH', plugin_dir_url($this->file) );
        define( 'WC_SA_PLUGIN_BASENAME', plugin_basename($this->file) );
        define( 'WC_SA_FILE', $this->file );
        define( 'WC_SA_DIR', $this->dir );
        define( 'WC_SA_VERSION', $this->_version );
        define( 'WC_SA_TOKEN', $this->_token );
    }

    private function includes()
    {
        include_once( 'functions.php');
        include_once( 'class-wc-sa-ajax.php' );
        include_once( 'class-wc-sa-post-types.php' );
        include_once( 'class-wc-sa-status.php' );
        include_once( 'class-wc-sa-emails.php' );
        include_once( 'class-wc-sa-order.php' );

        include_once( 'class-wc-sa-install.php' );

        // Load API for generic admin functions
        if ( is_admin() ) {
            include_once( 'admin/class-wc-sa-admin-post-types.php' );
            include_once( 'admin/meta-boxes/class-wc-sa-meta-box-status-data.php' );
            include_once( 'admin/class-wc-sa-delete.php' );
        }else{
            include_once( 'class-wc-sa-frontend.php' );            
        }
    }

    public function init()
    {
        $this->register_post_status();
    }

    public function init_hooks()
    {
        add_action('admin_head', array($this, 'message_tc_button'));
        add_filter('wc_order_statuses', array($this, 'add_order_statuses'), 10, 1);
        add_filter('woocommerce_get_settings_pages', array($this, 'add_settings_pages'), 10, 1);
        add_filter('wc_sa_run_automatic_trigger', array($this, 'automatic_trigger'), 10, 3);
        add_filter('woocommerce_reports_order_statuses', array($this, 'reports_order_statuses'), 10, 1);
        add_filter('woocommerce_after_dashboard_status_widget', array($this, 'dashboard_status_widget'), 10, 1);
    }

     public function message_tc_button()
    {
        global $typenow;
        // check user permissions
        if ( !current_user_can('manage_woocommerce') ) {
            return;
        }
        $screen = get_current_screen();

        if( $screen->id != 'wc_custom_statuses' )
            return;
        // check if WYSIWYG is enabled
        if ( get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", array( $this, "message_add_tinymce_plugin") );
            add_filter('mce_buttons', array( $this, 'message_register_tc_button') );
        }
    }

    public function message_add_tinymce_plugin($plugin_array) {
        $plugin_array['wc_sa_tc_button'] = esc_url( $this->assets_url ) . 'js/tc-button.js';
        return $plugin_array;
    }
    public function message_register_tc_button($buttons) {        
        array_push($buttons, "wc_sa_tc_button");
        return $buttons;
    }

    public function register_post_status()
    {
        $statuses = wc_sa_get_statuses();

        foreach ($statuses as $status) {
            
            register_post_status( 'wc-'.$status->label, array(
                'label'                     => $status->title,
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( $status->title.' <span class="count">(%s)</span>', $status->title.' <span class="count">(%s)</span>', 'woocommerce_status_actions' )
            ) );
        }

    }

    public function add_settings_pages($settings)
    {
        $settings[] = include( 'admin/class-wc-sa-admin-settings.php' );
        return $settings;
    }

    public function add_order_statuses($statuses)
    {
        if( !is_array($statuses)) $statuses = array();

        $changed_statuses   = get_option( 'wc_custom_status_edit_existing_status', '' );
        
        foreach ($statuses as $key => $value) {
            if(isset($changed_statuses[$key]) && !empty($changed_statuses[$key]['name']))
                $statuses[$key] = $changed_statuses[$key]['name'];
        }        

        $_st = wc_sa_get_statuses();
        foreach ($_st as $status) {            
            $statuses['wc-'.$status->label] = $status->title;
        }

        return $statuses;
    }

    public function automatic_trigger($order_id, $new_status, $old_status)
    {
        $order  = wc_get_order($order_id);
        $all_st = wc_get_order_statuses();
        
        if ( isset( $all_st[$new_status]) && $order->has_status($old_status) ) {
            $new_status = substr($new_status, 3);
            $note = apply_filters('bulk_handler_custom_action_note', '', $new_status, $order );
            $note = apply_filters('automatic_trigger_handler_custom_action_note', $note, $new_status, $order );
            $order->update_status( $new_status, $note );
        }
    }

    public function reports_order_statuses($order_status)
    {
        if(!is_array($order_status))
            return $order_status;
        
        if(in_array('refunded', $order_status) && sizeof($order_status) == 1)
            return $order_status;

        $new_st = wc_sa_get_display_in_reports_statuses();
                
        $order_status = array_merge($order_status, $new_st);

        return $order_status;
    }

    public function dashboard_status_widget($reports)
    {
        $counts = array();
        foreach ( wc_get_order_types( 'order-count' ) as $type ) {
            $_counts           = (array) wp_count_posts( $type );
            if( empty($counts) ){
                $counts = $_counts;
            }else{
                foreach ($_counts as $key => $value) {
                    if( isset( $counts[$key] ) ){
                        $counts[$key] += (int)$value;
                    }else{
                        $counts[$key] = (int)$value;
                    }
                }
            }
        }
        $statuses = wc_sa_get_statuses();
        foreach ($statuses as $st_id => $st) {
            $show = get_post_meta($st_id, '_dashboard_widget', true);
            if ( isset($counts['wc-' . $st->label]) && $show === 'yes' ) {
                $count = $counts['wc-' . $st->label];
                ?>
                <li class="<?php echo $st->label; ?>-orders dashboard_status">
                    <a href="<?php echo admin_url( 'edit.php?post_status=wc-'.$st->label.'&post_type=shop_order' ); ?>">
                        <?php printf( _n( "<strong>%s order</strong> %s", "<strong>%s orders</strong> %s", $count, 'woocommerce_status_actions' ), $count, strtolower($st->title) ); ?>
                    </a>
                </li>
                <?php
            }
        }
    }

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles () {        
        wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
        wp_enqueue_style( $this->_token . '-frontend' );
    } // End enqueue_styles ()

    /**
     * Load frontend Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_scripts () {
        wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
        wp_enqueue_script( $this->_token . '-frontend' );

        $options = array(
            'i18_prompt_cancel' => __('Are you sure you want to cancel this order?', 'woocommerce_status_actions'),
            'i18_prompt_change' => __('Are you sure you want to change the status of this order?', 'woocommerce_status_actions'),
            );
        wp_localize_script( $this->_token . '-frontend', 'wc_sa_opt', $options );
    } // End enqueue_scripts ()

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles ( $hook = '' ) {
        $screen = get_current_screen();
        $allowed_screens = wc_sa_get_allowed_screens();

        if( in_array($screen->id, array('edit-shop_order')) ){
            wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
            wp_enqueue_style( $this->_token . '-admin' );
        }


        if( in_array($screen->id, array('edit-wc_custom_statuses', 'wc_custom_statuses')) ){
            wp_enqueue_style( 'fonticonpicker_styles', esc_url( $this->assets_url ) . 'css/fontpicker/jquery.fonticonpicker.min.css' );
            wp_enqueue_style( $this->_token . '-admin-metabox-statuses', esc_url( $this->assets_url ) . 'css/metabox-statuses.css', array(), $this->_version );
        }
        
        //register_post_type
        if(in_array($screen->id, $allowed_screens )){
            $d_version = $this->_version;
            $file_path = $this->uploads_dir . '/dynamic-font-icons.css';
            if (file_exists($file_path)) {
                $d_version = filemtime($file_path);
            }
            $f_version = $this->_version;
            $file_path = $this->assets_dir . '/css/font-icons.css';
            if (file_exists($file_path)) {
                $f_version = filemtime($file_path);
            }
            wp_register_style( $this->_token . '-font-icons', esc_url( $this->assets_url ) . 'css/font-icons.css', array(), $f_version );
            wp_enqueue_style( $this->_token . '-dynamic-font-icons', esc_url( $this->uploads_url ) . '/dynamic-font-icons.css', array($this->_token . '-font-icons'), $d_version  );
        }


    } // End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts ( $hook = '' ) {

        wp_register_script( 'jquery-iconpicker', esc_url( $this->assets_url ) .  'js/jquery.fonticonpicker.js');
        wp_register_script( $this->_token . '_font_icons', esc_url( $this->assets_url ) .  'js/font-icons.js' );

        $screen = get_current_screen();
        if( in_array($screen->id, array( 'wc_custom_statuses', 'edit-wc_custom_statuses' )) ){
            $depth = array(
                'jquery', 'jquery-ui-sortable', 'serializejson',
                $this->_token . '_font_icons',
                'jquery-iconpicker',
                'jquery-tiptip',
                'wp-color-picker',
                'wc-admin-meta-boxes',
                );
            wp_enqueue_script( $this->_token . '-admin-meta-boxes', esc_url( $this->assets_url ) . 'js/meta-boxes' . $this->script_suffix . '.js', $depth, $this->_version );


            $editor_btns = get_option('wc_fields_additional');
            $acf_btns    = wc_sa_get_acf_editor_btns();

            wp_localize_script( $this->_token . '-admin-meta-boxes', 'wc_sa_editor_btns', $editor_btns );
            wp_localize_script( $this->_token . '-admin-meta-boxes', 'wc_sa_acf_editor_btns', $acf_btns );

            if( $screen->id == 'edit-wc_custom_statuses'){
                wp_enqueue_script( $this->_token . '-sortable', esc_url( $this->assets_url ) .  'js/sortable.js', array('jquery') );
                wp_localize_script( $this->_token . '-sortable', 'wc_sa_sortable_opt', array('ajax_url' => WC()->ajax_url() ) );
            }
        }

        if( $screen->id == 'woocommerce_page_wc-settings' && isset($_GET['tab']) && $_GET['tab'] == 'wc_sa_settings'){

            $defaults = array(
                'labels'  => $this->default_statuses,
                'colors'  => $this->color_statuses,
                'editing' => $this->default_editing
            );

            wp_enqueue_script( $this->_token . '-admin-settings', esc_url( $this->assets_url ) .  'js/admin-settings' . $this->script_suffix . '.js', array('wp-color-picker'));
            wp_localize_script( $this->_token . '-admin-settings', 'wc_sa_defaults', $defaults );
        }

        if( $screen->id == 'edit-shop_order'){
            wp_enqueue_media();
            wp_enqueue_script( $this->_token . '-note_promt', esc_url( $this->assets_url ) .  'js/note_promt.js', array('jquery') );            
        }

    } // End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation () {
        load_plugin_textdomain( 'woocommerce_status_actions', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    } // End load_localisation ()

    /**
     * Load plugin textdomain
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain () {
        $domain = 'woocommerce_status_actions';

        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
    } // End load_plugin_textdomain ()

    /**
     * Main WC_SA Instance
     *
     * Ensures only one instance of WC_SA is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WC_SA()
     * @return Main WC_SA instance
     */
    public static function instance ( $file = '', $version = '1.0.0' ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $file, $version );
        }
        return self::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
    } // End __wakeup ()

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since    2.0.0
     * @return  void
     */
    public function install ($networkwide) {        
        global $wpdb;
                     
        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    WC_SA_Install::install();
                }
                switch_to_blog($old_blog);
                return;
            }   
        } 
        else{
            WC_SA_Install::install();
        }
    } // End install ()

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number () {
        update_option( $this->_token . '_version', $this->_version );
    } // End _log_version_number ()

}