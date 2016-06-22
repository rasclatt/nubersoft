$j	=	jQuery;
$j(document).ready(function() {
	$j(".button_trigger").click(function() {
		var ThisObj		=	$j(this);
		var	getInstr	=	ThisObj.data('instructions');
		
		if(getInstr.data.deliver.toggle == 'on') {
			var useData			=	getInstr.data.deliver;
			var	RequestTable	=	(useData.requestTable != '')? '&requestTable='+useData.requestTable:'';
			var	nProcessor		=	(useData.nProcessor != '')? '&token[nProcessor]='+useData.nProcessor:'';
			window.location='?action=nbr_cache_delete'+RequestTable+nProcessor;
		}
	});
});