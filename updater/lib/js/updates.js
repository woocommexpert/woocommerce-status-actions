(function($) {
	$( document ).ready( function() {
		var $el = $( '.column-description .ae-plugin-update-row-meta-register-for-updates-inline' );
		$el.each( function(){
			$plugin_row = $(this).parents('tr[data-slug]');
			$(this).remove();
			$plugin_row.next().find( '.update-message em, .update-message .update-link' ).replaceWith( $(this) );
		});
	});

})( jQuery );