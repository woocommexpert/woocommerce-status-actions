<?php
if(!defined('ABSPATH')) return;

if( !class_exists('AEBaseApi')) {

	/**
	 * Set the system path to the plugin's directory
	 */
	define('EUP_PLUGIN_DIR', realpath(dirname(__FILE__)).'/');

	//#!-- Load dependencies
	require('lib/plugin-update-checker.php');
	require('lib/AEBaseApi.php');
	$AEApi = new AEBaseApi();
	$GLOBALS['aebaseapi'] = $AEApi;


	add_action('admin_init', array($AEApi, 'onInit'));

	//#!-- Add sidebar menu
	if(function_exists('is_multisite') && is_multisite()){
		add_action('network_admin_menu', array($AEApi,'addPluginPages'));
	}
	else {
		add_action('admin_menu', array($AEApi,'addPluginPages'));
	}

	function ae_updater_validate_code($slug = '', $code = '')
	{
		$url = 'http://actualityextensions.com/updates/server/?action=validate_code&slug=' . $slug . '&purchase_code=' . $code;

		$transient = 'ae_code' . md5($url);
		$resultEnvato  = get_transient( $transient );
		
		if( !$resultEnvato ){

			$options = array(
				'timeout' => 10, //seconds
				'headers' => array(
					'Accept' => 'application/json'
				),
			);
			$options = apply_filters('puc_request_info_options-'.$slug, $options);
			
			$result = wp_remote_get(
				$url,
				$options
			);

			if ( is_wp_error($result) ) { /** @var WP_Error $result */
				return false;
			}

			if ( !isset($result['response']['code']) ) {
				return false;
			}

			if ( $result['response']['code'] !== 200 ) {
				return false;
			}

			if ( empty($result['body']) ) {
				return false;
			}
			
			$resultEnvato = json_decode($result['body']);
		}
		set_transient( $transient, $resultEnvato, 12 * HOUR_IN_SECONDS );

	    if( isset($resultEnvato->error) && !empty($resultEnvato->error)){
	    	return false;
	    }

	    return true;

	}

}