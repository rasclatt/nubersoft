function getDragElem(idVal,$)
	{
		return $(idVal).html();
	}


$(document).ready(function() {
	
	var	sortEventHandler	=	function(event, ui)
		{
			var getTarget	=	$(event.target);
			var getSort		=	getTarget.prop('outerHTML');
			var	getInstr	=	getTarget.data('instructions');
			var	getAction	=	(isset(getInstr,'action'))? getInstr.action : false;
			var	repStrDq	=	getSort.replace(/\"/gi,"\'");
			var repStrSq	=	repStrDq.replace(/&quot;/gi,'\"');
			var	send		=	{
				"action": getAction,
				"data": {
						"html": repStrSq,
						"deliver":	getInstr.data.deliver
					}
				};
			
			console.log(event.target);
			console.log(getTarget);
			console.log(send);
			
			AjaxEngine.ajax(
				send,
				function(response) {
					console.log(response);
			});
			/**/
		};
	
	$(".sortable").sortable({
		"stop":sortEventHandler
	});
	
    $( ".sortable" ).disableSelection();
	/*
	var	doc		=	$(this);
	var	getId	=	'';
	doc.on('mousedown mouseup','.sortable',function(e) {
		
	});
	*/
});