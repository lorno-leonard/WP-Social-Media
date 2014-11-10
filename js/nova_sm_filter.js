jQuery(function($) {
	$('.btn_sm_visible_filter').click(function() {
		var visibility = $(this).prev('select').val();
		var visibility_var = 'visible=' + visibility;

		// Check if category is not in URl
		if( location.href.indexOf('visible') == -1 ) {
			location.href += '&' + visibility_var;
		}
		else {
			var visibility_url = location.href.substr(location.href.indexOf('visible'));
			if( visibility_url.indexOf('&') > -1 ) {
				visibility_url = visibility_url.substr(0, visibility_url.indexOf('&'));
			}
			location.href = location.href.replace(visibility_url, visibility_var);
		}
	});
});