// JavaScript Document

function nbrSideSlide(ObjElem,Width)
	{
		ObjElem.animate({width:'toggle'},Width);
	}

$(document).ready(function() {
	// @description: Tool Toggler Revealer
	// @required: This gives the page a chance to load before revealing the tool editor
	var ToolToggler	=	$("#nbr_tooltoggle");
	// Shows the toggle menu on load of page
	ToolToggler.delay(1000).animate({ width: "50px" },800);
	/*ToolToggler.click(function() { 
		nbrSideSlide($('#InspectorPalletWrap'),400);
	});*/
	// Borrowed from StackOverflow:
	// http://stackoverflow.com/questions/6637341/use-tab-to-indent-in-textarea/6637396#6637396
	$(this).on('keydown','.textarea', function(e) {
		var keyCode = e.keyCode || e.which;
	
		if (keyCode == 9) {
			e.preventDefault();
			var start = $(this).get(0).selectionStart;
			var end = $(this).get(0).selectionEnd;
	
			// set textarea value to: text before caret + tab + text after caret
			$(this).val($(this).val().substring(0, start)
					+ "\t"
					+ $(this).val().substring(end));
	
			// put caret at right position again
			$(this).get(0).selectionStart =
			$(this).get(0).selectionEnd = start + 1;
		}
	});
});
