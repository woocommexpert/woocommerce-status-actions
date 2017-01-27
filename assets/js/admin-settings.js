jQuery( function ( $ ) {
  	$('.change_colour').wpColorPicker();
  	$('#wc_order_statuses_reset_defaults').click(function(event) {
      
      $('table tr.wc_order_statuses').each(function(index, el) {
        var key   = $(el).data('statuskey');
        var label = key;
        if (key.substring(0, 3) == 'wc-')
          label = key.substring(3);

        $(el).find('input.statusname').val(wc_sa_defaults.labels[key]);
        $(el).find('input.statuslabel').val(label);
        $(el).find('input.statuscolor').val(wc_sa_defaults.colors[key]).trigger('change');
        
        if( typeof wc_sa_defaults.editing[key] == 'undefined')
          $(el).find('input.default_editing').attr('checked', false).trigger('change');
        else
          $(el).find('input.default_editing').attr('checked', 'checked').trigger('change');

      });
    });
    $('#reset_payment_gateways_st').click(function(event) {
      $('.payment_gateways_st').prop('value', '');
      return false;
    });
});
