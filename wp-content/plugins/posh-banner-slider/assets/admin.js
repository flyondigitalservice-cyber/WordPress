jQuery(function ($) {
	$(document).on('click', '.posh-bs-pick', function (e) {
		e.preventDefault();
		var $input = $(this).siblings('.posh-bs-image');
		var frame = wp.media({ title: 'Select banner image', library: { type: 'image' }, multiple: false });
		frame.on('select', function () {
			$input.val(frame.state().get('selection').first().toJSON().url);
		});
		frame.open();
	});
	$(document).on('click', '.posh-bs-clear', function (e) {
		e.preventDefault();
		$(this).siblings('.posh-bs-image').val('');
	});
});
