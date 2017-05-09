$(document).ready(function() {
	$("#dbdl-click").click(function() {
			$(".dbdl-drop").slideToggle(200);
		});
	$("#save-reload").click(function() {
			window.setTimeout(function(){location.reload()},5000);
		});
});