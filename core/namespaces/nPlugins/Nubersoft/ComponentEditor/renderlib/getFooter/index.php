
		<div style="padding: 30px; background: linear-gradient(#111,#444); text-align: center; font-size: 14px; color: #FFF;">
			Copyright &reg;<?php echo date("Y"); ?> nUbersoft.
		</div>
<?php $this->get3rdPartyHelper('\nPlugins\Nubersoft\JsLibrary')->nScroller(); ?>
<script>
njQuery(document).ready(function($) {
	
	function sizeFont(thisVal,minRange,maxRange)
		{
			var rInc		=	(maxRange-minRange)/100;
			var calcSize	=	Math.ceil(((thisVal*rInc)+minRange));
			var getClass	=	document.getElementsByClassName('textarea');
			var fontSize	=	calcSize;
			var lineHeight	=	(calcSize*1.3);
			getClass[0].style.setProperty('font-size',fontSize+"px",'important');
			getClass[0].style.setProperty('line-height',lineHeight+"px",'important');
			
			var sliderVals	=	$("#nbr_font_size");
			sliderVals.html(fontSize.toPrecision(3)+'px');
		}
	var fontStartSize	=	30;
	var fontSizeRng		=	$("input[type=range]");
	fontSizeRng.val(fontStartSize);
	sizeFont(fontStartSize,10,28);
	fontSizeRng.on('change',function(){
		sizeFont($(this).val(),10,28);
	});
	
	var AjaxEngine	=	new nAjax($);
	$('#nbr_save_component').click(function() {
		AjaxEngine
			.setUrl('<?php echo $this->siteUrl(); ?>')
			.ajax($("#nbr_component_editor_content").serialize(),function(response) {
				try{
					var getJson	=	JSON.parse(response);
					if(getJson.saved === true)
						alert('Component has been saved.');
					else
						alert('Save failed.');
				}
				catch(Exception) {
					console.log(response);
					console.log(Exception.message);
				}
			});
	});
});
</script>