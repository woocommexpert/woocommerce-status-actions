<?php
/**
 * Status Settings
 *
 * @author   Actuality Extensions
 * @category Admin
 * @package  WC_SA/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SA_Settings' ) ) :

/**
 * WC_SA_Settings.
 */
class WC_SA_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'wc_sa_settings';
		$this->label = __( 'Order Statuses', 'woocommerce_status_actions' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			''          	=> __( 'General', 'woocommerce_status_actions' ),
			'gateways'       => __( 'Gateways', 'woocommerce_status_actions' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		add_action( 'woocommerce_admin_field_edit_existing_status', array($this, 'edit_existing_status') );
		add_action( 'woocommerce_admin_field_edit_gateway_order_status', array($this, 'edit_gateway_order_status') );

		$settings = $this->get_settings( $current_section );

		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		unset($settings['edit_existing_status']);
		unset($settings['edit_gateway_order_status']);

        if( isset($_POST['wc_custom_status_edit_existing_status'])){
            $e_st = is_array($_POST['wc_custom_status_edit_existing_status']) ? $_POST['wc_custom_status_edit_existing_status'] : array();
            update_option('wc_custom_status_edit_existing_status', $e_st);

            $this->generate_styles();
        }
        if( isset($_POST['wc_sa_payment_gateway_status']) ){
            update_option('wc_sa_payment_gateway_statuses', $_POST['wc_sa_payment_gateway_status']);
        }

		WC_Admin_Settings::save_fields( $settings );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'gateways' == $current_section ) {

			$settings = apply_filters( 'woocommerce_product_settings', array(

				array(
					'title' => __( 'Gateway Order Status', 'woocommerce_status_actions' ),
					'type' 	=> 'title',
					'desc' 	=> __('You can define which status is set when order is complete depending on the payment gateway.', 'woocommerce_status_actions'),
					'id' 	=> 'gateway_order_status_options'
				),

				'edit_gateway_order_status' => array(
	                'id'       => 'wc_sa_payment_gateway_statuses',
	                'type'     => 'edit_gateway_order_status',
	            ),

			));
		
		} else {
			$status_list = wc_get_order_statuses();
			$settings = apply_filters( 'wc_sa_general_settings', array(
				array(
					'title' 	=> __( 'General Settings', 'woocommerce_status_actions' ),
					'type' 		=> 'title',
					'id' 		=> 'custom_status_general_settings'
				),

				array(
					'title'    => __( 'Status Style', 'woocommerce_status_actions' ),
	                'id'       => 'woocommerce_status_actions_default_style',
	                'default'  => '0',
	                'desc_tip'  =>  __( 'This will effect the default WooCommerce\'s statuses (e.g. Processing, Completed, etc.).', 'woocommerce' ),
	                'type'     => 'select',
	                'class'    => 'chosen_select',
	                'css'      => 'min-width: 350px;',
	                'options'  => array(
	                    '0'      => __( 'Icon', 'woocommerce_status_actions' ),
	                    '1' => __( 'Fill Text', 'woocommerce_status_actions' ),
	                    '2' => __( 'Line Text', 'woocommerce_status_actions' )
	                )
				),

				array(
					'title'    => __( 'Successful Payment', 'woocommerce_status_actions' ),
	                'id'       => 'wc_custom_status_payment_complete_status',
	                'default'  => 'wc-processing',
	                'desc_tip'  =>  __( 'Choose what default status to have upon successful payment. Default: Processing.', 'woocommerce' ),
	                'type'     => 'select',
	                'class'    => 'chosen_select',
	                'css'      => 'min-width: 350px;',
	                'options'  => $status_list
				),

			));

			$settings = array_merge($settings, array(
				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'edit_default_status_options'
				),
				array(
					'title' => __( 'Core Order Statuses', 'woocommerce_status_actions' ),
					'type'  => 'title',
					'id'    => 'edit_default_status'
					),
				'edit_existing_status' => array(
	                'id'       => 'wc_custom_status_edit_existing_status',
	                'type'     => 'edit_existing_status',
	            ),
				array(
					'type' 	=> 'sectionend',
					'id' 	=> 'edit_default_status_options'
				),
			) );
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}

	public function edit_gateway_order_status($val)
	{
		$saved_st = get_option( $val['id'] );
		$wc_order_statuses  = wc_sa_get_default_order_statuses();
		$all_order_statuses = wc_sa_get_statusesList();
		//$all_order_statuses = array_diff_key($all_order_statuses, $wc_order_statuses);

        if( !is_array($saved_st) ){
            $saved_st = array();
        }
		?>
		<table class="wp-list-table widefat striped posts default_status_payments_methods">
	        <thead>
	            <tr>
	                <th><?php _e('Gateway', 'woocommerce_status_actions'); ?></th>
	                <th><?php _e('Order Status', 'woocommerce_status_actions'); ?></th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php
	            foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
	                $st = isset($saved_st[$gateway->id]) ? $saved_st[$gateway->id] : '';
	            ?>
	            <tr>
	                <td class="payment-gateway">
	                    <?php echo $gateway->get_title(); ?>
	                </td>
	                <td>
	                    <select name="wc_sa_payment_gateway_status[<?php echo $gateway->id; ?>]" class="payment_gateways_st">
	                        <option value="" <?php selected('',$st, true); ?> ><?php _e('Default status', 'woocommerce_status_actions'); ?></option>
	                        <optgroup label="<?php _e('WooCommerce statuses', 'woocommerce_status_actions'); ?>">
	                            <?php foreach ($wc_order_statuses as $key => $name) { ?>
	                                <option value="<?php echo $key; ?>" <?php selected($key, $st, true); ?> ><?php echo $name; ?></option>
	                            <?php } ?>                                        
	                        </optgroup>
	                        <optgroup label="<?php _e('Custom statuses', 'woocommerce_status_actions'); ?>">
	                            <?php foreach ($all_order_statuses as $key => $name) { if( isset($wc_order_statuses[$key]) ) continue; ?>
	                                <option value="<?php echo $key; ?>" <?php selected($key, $st, true); ?> ><?php echo $name; ?></option>
	                            <?php } ?>                                        
	                        </optgroup>
	                    </select>
	                </td>
	            </tr>
	            <?php
	            }
	            ?>
	        </tbody>
	        <tfoot>
	          <tr>
	              <th colspan="7">
	                  <input type="button" value="<?php _e('Reset Defaults', 'woocommerce_status_actions'); ?>" class="button alignright" id="reset_payment_gateways_st">
	              </th>
	          </tr>
	      </tfoot>
	    </table>
		<?php



	}

	public function edit_existing_status($val)
    {
        $data = get_option( $val['id'] );
        $order_statuses  = wc_sa_get_default_order_statuses();
        $color_statuses  = WC_SA()->color_statuses;
        $default_editing = WC_SA()->default_editing;
        if( !is_array($data) ){
            $data = array();
            foreach ($order_statuses as $key => $value){
                if(isset($default_editing[$key]))
                    $data[$key]['item_editing'] = $default_editing[$key];
            }
        }
        ?>
        
        <table class="wp-list-table widefat fixed posts default_status_settings">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;"><?php _e( 'Icon', 'woocommerce_status_actions' ); ?></th>
                    <th><?php _e( 'Slug', 'woocommerce_status_actions' ); ?></th>
                    <th><?php _e( 'Name', 'woocommerce_status_actions' ); ?></th>
                    <th><?php _e( 'Label', 'woocommerce_status_actions' ); ?></th>
                    <th class="status_colour"><?php _e( 'Colour', 'woocommerce_status_actions' ); ?></th>
                    <th style="text-align: center;"><?php _e( 'Bulk Actions', 'woocommerce_status_actions' ); ?> <img width="16" height="16" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" class="help_tip" data-tip="<?php _e( 'Check this box to hide this status from the Bulk Actions menu.', 'woocommerce_status_actions' ); ?>"></th>
                    <th style="text-align: center;"><?php _e( 'Item Editing', 'woocommerce_status_actions' ); ?> <img width="16" height="16" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" class="help_tip" data-tip="<?php _e( 'Check this box to enable item editing for this status.', 'woocommerce_status_actions' ); ?>"></th>
                </tr>
            </thead>
          <tbody>
          
            <?php foreach ($order_statuses as $key => $value) {
                $label = 'wc-' === substr( $key, 0, 3 ) ? substr( $key, 3 ) : $key
                ?>
            <tr valign="top" data-statuskey="<?php echo $key; ?>" class="wc_order_statuses">
                <th class="order_status column-order_status" scope="row">
                    <mark class="<?php echo $label; ?> tips" data-tip="<?php echo $value; ?>"><?php echo $value; ?></mark>
                </th>
                <th class="titledesc" scope="row">
                  <label><?php echo $key; ?></label>
                </th>
                <td class="forminp status_name">
                   <input type="text" name="<?php echo $val['id']; ?>[<?php echo $key; ?>][name]" value="<?php echo isset($data[$key]) && !empty($data[$key]['name']) ? $data[$key]['name'] : $value; ?>" class="statusname">
                </td>
                <td class="forminp status_label">
                   <input type="text" name="<?php echo $val['id']; ?>[<?php echo $key; ?>][label]" value="<?php echo isset($data[$key]) && !empty($data[$key]['label']) ? $data[$key]['label'] : $label; ?>"  class="statuslabel" maxlength="17" >
                </td>
                <td class="forminp">
                    <?php
                    $color = '';
                    if(isset($color_statuses[$key])){
                        $color = $color_statuses[$key];                        
                    }
                    if(isset($data[$key]) && !empty( $data[$key]['color']) )
                        $color = $data[$key]['color'];
                    ?>
                   <input type="text"
                    autocomplete="off"
                    class="change_colour statuscolor" 
                    name="<?php echo $val['id']; ?>[<?php echo $key; ?>][color]" 
                    value="<?php echo $color; ?>">
                </td>
                <td style="text-align: center; ">
                    <input type="checkbox" name="<?php echo $val['id']; ?>[<?php echo $key; ?>][hide_bulk]" value="yes" <?php echo isset($data[$key]) && isset($data[$key]['hide_bulk']) ? 'checked="checked"' : ''; ?>>
                </td>
                <td style="text-align: center; ">
                    <input class="default_editing" type="checkbox" name="<?php echo $val['id']; ?>[<?php echo $key; ?>][item_editing]" value="yes" <?php echo isset($data[$key]) && isset($data[$key]['item_editing']) ? 'checked="checked"' : ''; ?>>
                </td>
            </tr>
            <?php }?>
          </tbody>
          <tfoot>
              <tr>
                  <th colspan="7">
                      <input type="button" value="Reset Defaults" class="button-primary alignright" id="wc_order_statuses_reset_defaults">
                  </th>
              </tr>
          </tfoot>
        </table>
        <?php
    }

    private function generate_styles( ){
    	$e_st = is_array($_POST['wc_custom_status_edit_existing_status']) ? $_POST['wc_custom_status_edit_existing_status'] : array();
    	$d_st = isset($_POST['woocommerce_status_actions_default_style']) ? $_POST['woocommerce_status_actions_default_style'] : 0;

		$file_path = WC_SA()->uploads_dir . '/dynamic-font-icons.css';
		$content   = '';
		if( file_exists( $file_path ) ) {
            $content = file_get_contents( $file_path );
        }

        $delimiter  = '#';
		$startTag   = '/*status-default_statuses-start*/';
		$endTag     = '/*status-default_statuses-end*/';
		$regex = $delimiter . preg_quote($startTag, $delimiter) 
		                    . '(.*?)' 
		                    . preg_quote($endTag, $delimiter) 
		                    . $delimiter 
		                    . 's';
		preg_match($regex,$content,$matches);

		$new_style = '';
		switch ($d_st) {
			case 1:
			case 2:
				$new_style .= '
				body .widefat tr .column-order_status{
					width: 80px;
				}
				';
				break;
		}
		if( $e_st ){
			foreach ($e_st as $st_key => $data) {
				$st = substr( $st_key, 3 );
				switch ($d_st) {
					case 0:
						$new_style .= '
						body .widefat .column-order_status mark.'.$st.':after{
							color: '.$data['color'].' !important;
						}
						';
						break;
					case 1:
						$new_style .= '
						body .widefat .column-order_status mark.'.$st.'{
							background-color: '.$data['color'].';
							border: 1px solid '.$data['color'].';
							color: #fff !important;
						    display: block;
						    border-radius: 16px;
						    font-size: 0px;
						    font-weight: normal;
						    line-height: 0px;
						    min-width: 80px;
						    padding: 0;
						    text-align: center;
						    width: auto;
						    height: auto;
						}
						body .widefat .column-order_status mark.'.$st.':after{
							content: "'.$data['label'].'";
							color: #fff !important;
						    display: block;
						    font-size: 9px;
						    line-height: 17px;
						    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
						    position: relative;
						    -webkit-font-smoothing: auto;
						    font-weight: bold;
						    text-transform: uppercase;
						}';
						break;
					case 2:
						$new_style .= '
						body .widefat .column-order_status mark.'.$st.'{
							border: 2px solid '.$data['color'].';
							color: '.$data['color'].' !important;
							display: block;
						    border-radius: 16px;
						    font-size: 0px;
						    font-weight: normal;
						    line-height: 0px;
						    min-width: 80px;
						    padding: 0;
						    text-align: center;
						    width: auto;
						    height: auto;
						}
						body .widefat .column-order_status mark.'.$st.':after{
							content: "'.$data['label'].'";
							color: '.$data['color'].' !important;
						    display: block;
						    font-size: 9px;
						    line-height: 15px;
						    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
						    position: relative;
						    -webkit-font-smoothing: auto;
						    font-weight: bold;
						    text-transform: uppercase;
						}
						';
						break;
				}
			}
		}

		$new_style = $startTag . $new_style . $endTag;
		
		if( $matches ){
			$content = str_replace($matches[0], $new_style, $content);
		}else{
			$content .= $new_style;
		}
		$fp = fopen($file_path, "w+");
		fwrite($fp, $content);
		fclose($fp);
	}
}

endif;

return new WC_SA_Settings();
