jQuery(function($) {
	var sm_id;
	var fn_btn_sm_cancel = function() {
		var tr = $(this).parents('tr').prev('tr');
		var tr_quick_edit = $(this).parents('tr');

		tr_quick_edit.fadeOut('fast', function() {
			tr.fadeIn('fast', function() {
				tr_quick_edit.remove();
			});
		});
	};

	var fn_btn_sm_update = function() {
		var tr = $(this).parents('tr').prev('tr');
		var tr_quick_edit = $(this).parents('tr');

		// Show Spinner
		$(this).next('.spinner').show();

		// Set Data
		var params = {};
		params.action = 'nova_sm_quick_edit_update';
		params.id = sm_id;
		params.name = $('#sm_name').val();
		params.link_address = $('#sm_link').val();
		params.visibility = $('#sm_visible_y').is(':checked') ? 'Y' : 'N';
		$.post(nova_ajax.ajaxurl, params, function(response){
			var decode = JSON.parse(response);
			
			// Update row
			var row_actions = tr.find('td.title .row-actions');
			tr.find('td.title').empty().html(decode.name + '<div class="row-actions">' + row_actions.html() + '</div>');
			tr.find('td.title .nova_sm_qe_social_media').bind('click', fn_nova_sm_qe_snippet);
			tr.find('td.link_address').empty().html(decode.link_address_modified);
			tr.find('td.visibility').text(decode.visibility);
			tr.find('td.date').empty().html(decode.date);

			tr_quick_edit.fadeOut('fast', function() {
				tr.fadeIn('fast', function() {
					tr_quick_edit.remove();
				});
			});
		});
	};

	var fn_nova_sm_qe_snippet = function() {
		sm_id = $(this).data('id');

		// Check if there is an existing Quick Edit Row
		var tr_quick_edit = $('.quick-edit-row');
		if( tr_quick_edit.length > 0 ) {
			tr_quick_edit.hide();
			tr_quick_edit.prev('tr').show();
			tr_quick_edit.remove();
		}

		var tr = $(this).parents('tr');
		var tr_class = tr.prop('class') + ' inline-edit-row inline-edit-row-page quick-edit-row quick-edit-row-page';
		var colspan = tr.find('> td, > th').length;
		var tr_quick_edit = '\
			<tr class="' + tr_class + '">\
				<td colspan="' + colspan + '">\
					<h4>Quick Edit</h4>\
					<span class="spinner" style="float: left; display: block;"></span>\
					<p><br class="clear"></p>\
				</td>\
			</tr>\
		';
		tr.fadeOut('fast');
		tr.after(tr_quick_edit);

		// Set Form Data
		var nova_sm_snippet_categories = [];
		var nova_sm_snippet_details = [];

		// Get Social Media Details
		var params = {};
		params.action = 'nova_sm_get_social_media_details';
		params.id = sm_id;
		$.post(nova_ajax.ajaxurl, params, function(response){
			var decode = JSON.parse(response);
			nova_sm_details = decode;

			// Initialize Quick Edit Content
			var quick_edit_content = '\
				<form id="sb_snippet_form" action="">\
					<fieldset class="inline-edit-col-left" style="width: 50%">\
						<div class="inline-edit-col">\
							<h4>Quick Edit</h4>\
							<label>\
								<span class="title">Name</span>\
								<span class="input-text-wrap">\
									<input type="text" id="sm_name" class="ptitle" value="' + nova_sm_details.name + '" size="50">\
								</span>\
							</label>\
							<label style="margin-bottom: 20px;">\
								<span class="title">Link Address</span>\
								<span class="input-text-wrap">\
									<input type="text" id="sm_link" class="ptitle" value="' + nova_sm_details.link_address + '">\
								</span>\
							</label>\
							<label>\
								<span class="title">Visiblity</span>\
								<span class="input-text-wrap">\
									<input type="radio" name="sm_visibity" value="Y" id="sm_visible_y" ' + (nova_sm_details.visibility == 'Y' ? 'checked' : '') + '>Yes\
									&emsp;\
									<input type="radio" name="sm_visibity" value="N" id="sm_visible_n" ' + (nova_sm_details.visibility == 'N' ? 'checked' : '') + '>No\
								</span>\
							</label>\
						</div>\
					</fieldset>\
					<p class="submit inline-edit-save">\
						<a accesskey="c" id="btn_sm_cancel" class="button-secondary cancel alignleft">Cancel</a>\
						<a accesskey="s" type="submit" id="btn_sm_update" class="button-primary save alignright">Update</a>\
						<span class="spinner"></span>\
						<br class="clear">\
					</p>\
				</form>\
			';

			// Display Quick Edit
			$('tr.quick-edit-row').find('td').html(quick_edit_content);

			// Bind Buttons
			$('#btn_sm_cancel').bind('click', fn_btn_sm_cancel);
			$('#btn_sm_update').bind('click', fn_btn_sm_update);
		});
	};

	// Quick Edit Social Media
	$('.nova_sm_qe_social_media').click(fn_nova_sm_qe_snippet);
});