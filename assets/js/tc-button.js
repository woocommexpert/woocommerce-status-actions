(function() {  
    function get_menu (editor, url) {
      var menu = [
                  {
                      text: 'Standard',
                      menu: [
                          {
                              text: 'customer_first_name',
                              value: '{customer_first_name}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'customer_last_name',
                              value: '{customer_last_name}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'order_date',
                              value: '{order_date}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'order_number',
                              value: '{order_number}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'order_value',
                              value: '{order_value}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'payment_method',
                              value: '{payment_method}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_method',
                              value: '{shipping_method}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_address',
                              value: '{billing_address}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_address',
                              value: '{shipping_address}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          }
                      ]
                  },
                  {
                      text: 'Billing',
                      menu: [
                          {
                              text: 'billing_country',
                              value: '{billing_country}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_first_name',
                              value: '{billing_first_name}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_last_name',
                              value: '{billing_last_name}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_company',
                              value: '{billing_company}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_address_1',
                              value: '{billing_address_1}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_address_2',
                              value: '{billing_address_2}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_city',
                              value: '{billing_city}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_state',
                              value: '{billing_state}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_postcode',
                              value: '{billing_postcode}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_email',
                              value: '{billing_email}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'billing_phone',
                              value: '{billing_phone}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                      ]
                  },
                  {
                      text: 'Shipping',
                      menu: [
                          {
                              text: 'shipping_country',
                              value: '{shipping_country}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_first_name',
                              value: '{shipping_first_name}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_last_name',
                              value: '{shipping_last_name}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_company',
                              value: '{shipping_company}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_address_1',
                              value: '{shipping_address_1}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_address_2',
                              value: '{shipping_address_2}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_city',
                              value: '{shipping_city}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_state',
                              value: '{shipping_state}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'shipping_postcode',
                              value: '{shipping_postcode}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          }
                      ]
                  },
                  {
                      text: 'Tracking',
                      menu: [
                          {
                              text: 'tracking_provider',
                              value: '{tracking_provider}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'custom_tracking_provider',
                              value: '{custom_tracking_provider}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'tracking_number',
                              value: '{tracking_number}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'custom_tracking_link',
                              value: '{custom_tracking_link}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
                              text: 'date_shipped',
                              value: '{date_shipped}',
                              onclick: function(e) {
                                  e.stopPropagation();
                                  editor.insertContent(this.value());
                              }      
                          },
                          {
						      text: 'aftership_tracking_provider_name',
						      value: '{aftership_tracking_provider_name}',
						      onclick: function(e) {
						          e.stopPropagation();
						          editor.insertContent(this.value());
						      }      
						  },
						  {
						      text: 'aftership_tracking_number',
						      value: '{aftership_tracking_number}',
						      onclick: function(e) {
						          e.stopPropagation();
						          editor.insertContent(this.value());
						      }      
						  },
                      ]
                  },
             ];

      if( wc_sa_editor_btns != '' && Object.keys(wc_sa_editor_btns).length > 0 ){
        var additional = {text: 'Additional', menu : []};
        var i = 0;
        jQuery.each(wc_sa_editor_btns, function(index, val) {
          additional.menu.push({
                                  text: val.label,
                                  value: '{'+index+'}',
                                  onclick: function(e) {
                                      e.stopPropagation();
                                      editor.insertContent(this.value());
                                  }      
                              });         
        });
        menu.push(additional);
      }

      if( wc_sa_acf_editor_btns != '' && Object.keys(wc_sa_acf_editor_btns).length > 0 ){
        var advanced = {text: 'Advanced Custom Fields', menu : []};
        var i = 0;
        jQuery.each(wc_sa_acf_editor_btns, function(index, val) {
          advanced.menu.push({
                                  text: val.label,
                                  value: '{'+index+'}',
                                  onclick: function(e) {
                                      e.stopPropagation();
                                      editor.insertContent(this.value());
                                  }      
                              });         
        });
        menu.push(advanced);
      }
      return menu;
    }
    tinymce.PluginManager.add('wc_sa_tc_button', function( editor, url ) {
        editor.addButton( 'wc_sa_tc_button', {
            title: 'Shortcodes',
            icon: 'icon dashicons-cart',
            type: 'menubutton',
            menu: get_menu(editor, url)
        });
    });
})();