jQuery(document).ready(function($) {
	$('body').on('click', '.sa_note_prompt_modal .media-modal-close', function(event) {
		$('.sa_note_prompt_modal, .media-modal-backdrop').remove();
		return false;
	});
  $('.sa_note_prompt').click(function(event) {
  	var action = $(this).attr('href');
    var modal  = $('#tmpl-media-modal').html();
    var form   = $('#tmpl-wc_as_note_prompt-modal').html();
    var $modal = $(modal);
    console.log(action);
    $modal.addClass('sa_note_prompt_modal').find('.media-modal-content').html(form);
    $modal.find('form').attr('action', action);
    $modal.appendTo('body');
    return false;
  });

});