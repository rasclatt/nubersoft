<?php
/*Title: display_autoloaded()*/
/*Description: This function is an administrative function that will report how many times a function is included.*/

	function display_autoloaded()
		{
			if(defined('SERVER_MODE') && SERVER_MODE) {
				if(!is_admin())
					return;
			
				AutoloadFunction('printpre,create_query_string');
				global $_cLoaded;
				
				$functions	=	scandir(FUNCTIONS);
					
				if(isset($_cLoaded['used'])) {
						$_cLoaded['used']	=	array_count_values($_cLoaded['used']);
						foreach($functions as $files) {
							if($files != '.' && $files != '..') {
								$func		=	str_replace(".php","",preg_replace('/(function\.)([^\.]{1,})(\.php)/',"$2",$files));
								
								if(!isset($_cLoaded['used'][$func]))
									$_cLoaded['used'][$func]	=	0;
							}
						}
							
						if(isset($_GET['sorted']))
							ksort($_cLoaded['used']);
					}
?>
<style>
tr.autoloaded-hd td	{
	background-color: #CCC;
	background: linear-gradient(#EBEBEB,#888);
	color: #000;
	text-shadow: 1px 1px 3px #FFF;
	padding: 5px;
	font-size: 18px;
	font-family: Arial, Helvetica, sans-serif;
}
tr.autoloaded-row td	{
	padding: 5px;
	font-size: 14px;
	font-family: Arial, Helvetica, sans-serif;
	border-top: 1px solid #666;
	border-bottom: 1px solid #CCC;
	background: linear-gradient(#FFF,#EBEBEB);
}
tr.notused td	{
	background-color: #F90;
	background: linear-gradient( #FC0, #FFA700);
	color: #FFF;
	text-shadow: 1px 1px 3px #330900;
	border-top: 1px solid #AA6700;
	border-bottom: 1px solid #FC6;
}
.autoload-btn,
.autoload-btnmod	{
	padding: 5px 10px;
	background-color: #222;
	color: #FFF;
	text-shadow: 1px 1px 3px #000;
	border-radius: 3px;
	font-size: 10px;
	display: inline-block;
	margin: 5px;
	cursor: pointer;
	border: 1px solid #444;
}
.autoload-btn:hover,
.autoload-btnmod:hover	{
	background-color: #444;
}
tr.autoloaded-row:hover td	{
	opacity: 0.8;
}
.cursorDefault td	{
	cursor: default;
}
.ind	{
	color: #FFF;
	text-shadow: 1px 1px 2px #000;
}
.C	{
	background-color: green;
	border-radius: 10px;
	padding: 3px 5px 0 5px;
	width: 10px;
	height: 20px;
}
.F	{
	background-color: red;
	padding: 3px 5px 0 5px;
	width: 10px;
	height: 20px;
}
</style>
	<div class="allblocks">
		<div style="max-width: 1000px; max-height: 400px;margin: 0 auto;" class="cursorDefault">
			<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
				<tr class="autoloaded-hd">
					<td colspan="2">FUNCTIONS/CLASSES</td><td style="text-align: center;">USED</td>
				</tr>
				<tr class="autoloaded-btn">
					<td colspan="3" style="background-color: #000; background: linear-gradient(#000,#333);">
						<div id="autoload-all" class="autoload-btn" data-setclass="all">ALL</div>
						<div id="autoload-noused" class="autoload-btn" data-setclass="used">ON</div>
						<div id="autoload-notused" class="autoload-btn" data-setclass="notused">OFF</div>
						<div class="autoload-btnmod" onClick="window.location='?sorted=true<?php echo create_query_string(array("sorted"),$_GET); ?>'">SORT</div>
					</td>
				</tr>
			</table>
		</div>
		<div style="max-width: 1000px; max-height: 400px; overflow: auto; margin: 0 auto;" class="cursorDefault">
			
		<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
				<?php foreach($_cLoaded['used'] as $key => $values) {
					
					$used	=	($values == 0)? 'notused':'used'; ?>
			<tr class="autoloaded-row <?php echo $used; ?>">
				<td class="autoloadtd<?php echo $used; ?>"><?php echo (strpos($key,"::") !== false)? '<div class="ind C">C</div>':'<div class="ind F">F</div>'; ?></td>
				<td class="autoloadtd<?php echo $used; ?>"><?php echo $key; ?></td>
				<td class="autoloadtd<?php echo $used; ?>"><?php echo $values; ?></td>
			</tr>	
					<?php } ?>
		</table>
		</div>
	</div>
	<div id="sql-reporting" onClick="ShowHide('#sql-reporting_panel','','slide')">SQL/FILE EXECUTION</div>
		<div style="display: none;" id="sql-reporting_panel">
			<?php global $_dbrun;
				printpre($_dbrun);
			 ?>
		</div>
	</div>
	
<script>
$(document).ready(function() {
	$(".autoload-btn").click(function() {
		var ThisButton	=	$(this).data('setclass');
		if(ThisButton == 'all') {
				$(".autoloadtdused").slideDown('fast');
				$(".autoloadtdnotused").slideDown('fast');
			}
		else {
				var InActive = (ThisButton == 'notused')? ".autoloadtdused":".autoloadtdnotused";
				
				$(InActive).slideUp('fast');
				$(".autoloadtd"+ThisButton).delay(300).slideDown('fast');
			}
	});
});
</script>
<?php				
					unset($_cLoaded);
				}
		}
?>