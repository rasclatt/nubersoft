// JavaScript Document
$(document).ready(function() {
	var codeTool		=	$(".nbr_code_tool_btn");
	var codeToolBlock	=	$("#nbr_component_quick_links ul");
	var	allDataShow		=	$('*[data-cid]');
	var thisDataShow	=	false;
	
	if(codeToolBlock.length > 0) {
		codeToolBlock.hover(
			function(){
				$(this).css({'opacity':'1.0','-moz-opacity':'1.0','-webkit-opacity':'1.0'});
			},
			function() {
				$(this).css({'opacity':'0.4','-moz-opacity':'0.4','-webkit-opacity':'0.4'});
			});
	}
	
	codeToolBlock.css({"right":"0px"});
	
	//console.log(allDataShow);
				
	if(codeTool.length > 0) {
		var thisCodeTool;
		var thisHighlight	=	false;
		
		$(".nbr_code_tool_btn").hover(
			function() {
				thisHighlight		=	false;
				thisCodeTool		=	$(this);
				thisDataShow		=	thisCodeTool.data('cidshow');
				
			//	console.log(thisCodeTool);
			//	console.log(thisDataShow);
			//	console.log(allDataShow);
				
				$.each(allDataShow,function(k,v) {
					var thisHElem	=	$(v).data('cid');
					if(thisHElem == thisDataShow) {
						thisHighlight	=	$('*[data-cid="'+thisHElem+'"]');
						thisHighlight.addClass("nbr_tabtool_wrap");
						return false;
					}
				});
				
				if(!is_object(thisHighlight))
					return;
					
				var thisOffset	=	thisHighlight.offset();
				
				$("html, body").animate({ scrollTop: (thisOffset.top-100) }, 'slow');
				
				thisCodeTool.animate({right: '-10'},200);
			},
			function() {
				
				if(thisHighlight)
					thisHighlight.removeClass("nbr_tabtool_wrap");
					
				thisCodeTool.animate({right: '-30'},50);
			}
		);
	}
});