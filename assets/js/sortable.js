jQuery(document).ready(function($) {
	function stopSortStatuses(event, ui) {
  var tbody = ui.item.closest('tbody');

  tbody.find('tr').each( function(index, el) {
    $(el).find('.column-sort input').val(index);
  });
  
  var fields = tbody.find('.column-sort');
  var data = $( '.column_sort_hidden', fields ).serializeJSON();
      data['action'] = 'wc_sa_sort';
      ui.item.find('.column-sort').append('<span class="spinner" style="display: block; visibility: visible;"></span>');
      ui.item.find('.column-sort').addClass('saving');
  $.ajax({
    url: wc_sa_sortable_opt.ajax_url,
    data: data,
    type: 'POST',
    success: function( response ) {
      ui.item.find('.column-sort .spinner').remove();
      ui.item.find('.column-sort').removeClass('saving');
    }
  });
}
console.log($('table.widefat'));
  if($('table.widefat').length > 0 ){
    $( "table.widefat tbody" ).sortable({
          placeholder: "ui-state-highlight",
          axis: "y",
          handle: ".column-sort",
          stop: stopSortStatuses
        });
  }

});