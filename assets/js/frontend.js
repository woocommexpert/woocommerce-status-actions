jQuery( function ( $ ) {
  	$('.order-actions a.cancel').click(function(event) {
       return confirm(wc_sa_opt.i18_prompt_cancel);
    });

    $(".order-actions a[class^='prompt_mark_custom_status_'], .order-actions a[class*=' prompt_mark_custom_status_']").click(function(event) {
       return confirm(wc_sa_opt.i18_prompt_change);
    });
});
