<?php

class AEBaseApi
{

	public $products = array();
	public $pluginFiles = array();

	/**
	 * Class constructor. Sets error messages if any. Registers the 'pre_set_site_transient_update_plugins' filter.
	 *
	 * @param string $user_name The buyer's Username
	 * @param string $api_key   The buyer's API Key can be accessed on the marketplaces via My Account -> My Settings -> API Key
	 */
	public function __construct()
	{		
	}

	/**
	 * Enqueue CSS and Scripts
	 */

	public function enqueue_scripts( $hook_suffix ) {

		global $pagenow;

		if ( 'plugins.php' == $pagenow || 'plugin-install.php' == $pagenow ) {
			wp_enqueue_style(
				'ae-plugin-update-css',
				plugins_url( 'css/updates.css', __FILE__ ),
				array()
			);

			

			wp_enqueue_script(
				'ae-plugin-update-js',
				plugins_url( 'js/updates.js', __FILE__ ),
				array( 'jquery' )
			);

		}

	}

	/**
	 * Set up the filter for plugins in order to include Envato plugins
	 *
	 * @private
	 */
	public function onInit()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		$products = $this->get_products();
		if(empty($products)){
			return $plugins; // No plugins from Envato Marketplace found
		}
		$purchase_codes = get_option(AEBaseApi::PURCHASE_CODES_OPTION_KEY, array());

		// Setup parent class with the correct credentials, if we have them
		foreach ($products as $file) {
			$plugin_slug = basename($file, '.php');
			$code = isset($purchase_codes[$plugin_slug]) ? $purchase_codes[$plugin_slug] : '';
			$url  = 'http://actualityextensions.com/updates/server/?action=get_metadata&slug=' . $plugin_slug . '&purchase_code=' . $code;
			PucFactory::buildUpdateChecker( $url, $file , $plugin_slug );
		}
	}

	/**
	 * Holds the name of the user meta key that will store the Envato api key
	 *
	 * @type string
	 */
	const PURCHASE_CODES_OPTION_KEY = 'ae_purchase_codes';

	/**
	 * Creates the sidebar menu
	 */
	public function addPluginPages()
	{
		add_dashboard_page('Actuality Extensions', 'Actuality Extensions', 'manage_options', 'ae_license', array($this,'pageDashboard'));
	}

	public function add_product($file = '')
	{
		if( !empty($file) && !in_array($file, $this->products)){
			$this->products[] = $file;
			$this->pluginFiles[] = plugin_basename($file);
		}
	}

	public function get_products()
	{
		 return $this->products;
	}

	public function get_products_plugin_files()
	{
		 return $this->pluginFiles;
	}

	public function pageDashboard(){ include(EUP_PLUGIN_DIR.'pages/index.php'); }
}