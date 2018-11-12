$(function() {
	$(this).on('click', '.nbr_confirm',function(e) {
		// Stop click
		e.preventDefault();
		// Get instructions
		var getInstr	=	$(this).data('instructions');
		var	getMsg		=	getInstr.msg;
		var	getActs		=	(getInstr.ok).match(/(this::)(.*)/i);
		// Stop if there is no message
		if(empty(getMsg))
			return false;
		// Get the confirm value
		var	isConfirmed	=	confirm(getMsg);
		// Check if the actions are an array (found this::)
		if(is_array(getActs)) {
			// Check if there is a link in the array
			if(in_array(getActs,'href')) {
				// Extract link from current element
				var getLink	=	$(this).attr('href');
				// If confirmed, go to link
				if(isConfirmed) {
					window.location	=	getLink;
				}
			}
		}
	});
});