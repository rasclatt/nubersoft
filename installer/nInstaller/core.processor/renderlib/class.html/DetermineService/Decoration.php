<script src="http://www.nubersoft.com/js/jquery.js"></script>
<script src="http://www.nubersoft.com/js/onthefly.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<style>
div.formButton input	{ padding: 5px; border-radius: 5px; border: 1px solid #FFF: }
div.instWrap			{ display: inline-block; padding: 30px; border: 10px solid #FFF; background-color: #EBEBEB; box-shadow: 1px 1px 8px rgba(0,0,0,0.5); margin: 60px 0 20px 0; }
#fader					{ text-align: center; display: none; }
label					{ font-size: 12px; color: #666; text-align: center; }
#installitwindow		{ display: none; padding: 30px; background-color: #EBEBEB; max-width: 600px; width: 100%; }
div.installButton		{ display: block; margin: 20px auto; background-color: #888; border-radius: 4px; box-shadow: 1px 1px 5px rgba(0,0,0,0.4); font-size: 18px; font-family: Arial, Helvetica, sans-serif; padding: 10px 20px; color: #FFF; text-shadow: 1px 1px 3px #000; cursor: default; border: 2px solid #FFF; text-align: center; }
div.installButton:hover	{ box-shadow: none; cursor: pointer; background-color: #CCC; color: #333; text-shadow: 1px 1px 2px #FFF; }
#DBandFiles				{ display: none; }
</style>

    <div id="fader">
        <div class="instWrap">
            <img src="http://www.nubersoft.com/client_assets/images/logo/default.png" style="max-width: 250px;" />
			<p><?php echo $_service; ?></p>
            
				<div style="text-align: center;">
					<label for="installdb">Include Database Installer?</label>
					<input type="checkbox" id="installdb" name="installdb" />
				</div>
				<div id="justFiles" class="installButton" onClick="AjaxSimpleCall('installitwindow','/core.processor/renderlib/admintools/ajax/loader.php?installit=true')">INSTALL FILES</div>
				<div id="DBandFiles" class="installButton" onClick="AjaxSimpleCall('installitwindow','/core.processor/renderlib/admintools/ajax/loader.php?installit=true&installdb=true')">INSTALL ALL</div>
        </div>
		<div style="width: 100%; text-align: center;">
			<div id="installitwindow">
			
			</div>
		</div>
			
    </div>
			
    </div>
<script>
	$('#fader').delay(400).fadeIn('slow');
	$('#welcome').delay(1000).fadeIn('slow');
	
	$('#installdb').click(function() {
			$('#justFiles').toggle();
			$('#DBandFiles').toggle();
		});
	
	$('.installButton').click(function() {
			$('#installitwindow').fadeIn('slow');
		});
	
	function AjaxSimpleCall(targetId,url)
		{
			// Get element by id
			var GetId		=	$(this).attr('id');
			var StingSet	=	'<div class="loading"><h3>Loading...</h3></div>';
			$("#"+targetId).html(StingSet);
			
			$.ajax({
					url: url,
					cache: false,
					success: function() {
							$('#'+targetId).load(url);
						}
				});
		}
</script>