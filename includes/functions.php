<?php
/**
 * Returns array of statuses.
 *
 * @return array $statuses
 */
function wc_sa_get_statuses() {
  global $wpdb;
  $result   = $wpdb->get_results("SELECT ID, post_name, post_title FROM {$wpdb->posts} WHERE post_type = 'wc_custom_statuses' AND post_status = 'publish' ORDER BY menu_order ASC");
  $statuses = array();
  if( $result ) {
    foreach ($result as $key => $value) {
      $statuses[$value->ID] = (object)array('title' => $value->post_title, 'label' => $value->post_name);
    }
    return $statuses;
  }
  return array();
}
function wc_sa_get_statuses_by_meta($meta_key = '', $meta_value = '', $ids = false)
{
  global $wpdb;
  $query    = "SELECT status.ID, status.post_name FROM {$wpdb->posts} as status 
              LEFT JOIN {$wpdb->postmeta} meta ON (meta.post_id = status.ID AND meta.meta_key = '{$meta_key}')
              WHERE status.post_type = 'wc_custom_statuses' AND status.post_status = 'publish' AND meta.meta_value = '{$meta_value}'
              ORDER BY status.menu_order ASC";
  $result   = $wpdb->get_results($query);
  $statuses = array();
  if( $result ) {
    foreach ($result as $key => $value) {
      if( $ids === true ){
        $statuses[$value->ID] = $value->post_name;
      }else{
        $statuses[] = $value->post_name;
      }
    }
    return $statuses;
  }
  return array();
}

function wc_sa_get_display_in_reports_statuses()
{
  return wc_sa_get_statuses_by_meta('_display_in_reports', 'yes');
}
function wc_sa_get_can_cancel_statuses()
{
  return wc_sa_get_statuses_by_meta('_customer_cancel_orders', 'yes');
}
function wc_sa_get_pay_button_statuses()
{
  return wc_sa_get_statuses_by_meta('_customer_pay_button', 'yes');
}

/**
 * Returns array of statuses.
 *
 * @return array $statuses
 */
function wc_sa_get_statusesList() {
  global $wpdb;
  $result   = $wpdb->get_results("SELECT ID, post_name, post_title FROM {$wpdb->posts} WHERE post_type = 'wc_custom_statuses' AND post_status = 'publish' ORDER BY menu_order ASC");
  $statuses = array();
  if( $result ) {
    foreach ($result as $key => $value) {
      $statuses['wc-'.$value->post_name] = $value->post_title;
    }
    return $statuses;
  }
  return array();
}
/**
 * @return WC_SA_Status
 */
function wc_sa_get_status($the_order_status = false)
{
  if ( ! did_action( 'wc_sa_init' ) ) {
    _doing_it_wrong( __FUNCTION__, __( 'wc_sa_get_status should not be called before the wc_sa_init action.', 'dl_calc' ), '1.0.0' );
    return false;
  }
  return new WC_SA_Status( $the_order_status );
}
/**
 * Returns the status.
 *
 * @param string $name
 * @return mixed WC_SA_Status|boolean
 */
function wc_sa_get_status_by_name( $name ) {
  global $wpdb;
  $status_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = '{$name}' AND post_type = 'wc_custom_statuses' ");
  if( $status_id ) {
    return wc_sa_get_status( $status_id );
  }
  return false;
}
function wc_sa_get_acf_editor_btns()
{
  global $wpdb;
  $acf_fields = $wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'acf' ");
  $btn        = '';
  if ( $acf_fields){
    foreach ($acf_fields as $field) {
      $id   = $field->ID;
      $rule = get_post_meta($id, 'rule', true);
      if($rule && $rule['param'] == 'post_type' ){
        $is_shop = false;
        switch ($rule['operator']) {
          case '==':
            if( ($rule['value'] == 'shop_order' || $rule['value'] == 'all') ){
              $is_shop = true;
            }
            break;
          case '!=':
            if( $rule['value'] != 'shop_order'){
              $is_shop = true;
            }
            break;
        }
        if($is_shop){
          if($btn == '')
            $btn = array();

          $post_meta = get_post_meta($id);

          foreach ($post_meta as $key => $value) {
            if( strrpos($key, 'field_') === 0){
              $meta = maybe_unserialize($value[0]);
              $btn[$meta['name']] = array('label'=>$meta['label']);

            }
          }
        }
        
      }
    }
  }
  return $btn;
}

function wc_sa_get_allowed_screens()
{
  $def = array(
    'dashboard',
    'edit-wc_custom_statuses',
    'wc_custom_statuses',
    'edit-shop_order',
    'shop_order',
    'toplevel_page_wc_crm',
  );
  $allowed_screens = apply_filters('wc_sa_allowed_screens', array());
  if( !is_array($allowed_screens)){
      $allowed_screens = array();
  }
  return array_merge($def, $allowed_screens);
}
function wc_sa_get_default_order_statuses()
{
  $custom                 = wc_sa_get_statusesList();
  $order_statuses         = wc_get_order_statuses();
  $default_order_statuses = array_diff_key($order_statuses, $custom);
  return $default_order_statuses;
}

function wc_sa_get_hide_statuse_for_bulk()
{
  global $wpdb;
  $status_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = '{$name}' AND post_type = 'wc_custom_statuses' ");
  if( $status_id ) {
    return wc_sa_get_status( $status_id );
  }
  return false;
}

