$(document).ready(function() {
	$(".button_trigger").click(function() {
		var ThisObj	=	$(this);
		var	Toggle	=	ThisObj.data('toggle');
		var	Value	=	ThisObj.data('val');
		
		if(Toggle == 'on') {
			var	RequestTable	=	(Value != '')? '&requestTable='+Value:'';
			window.location='?cache=delete'+RequestTable;
		}
	});
});