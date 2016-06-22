<?php
function ajax_confirm()
	{
		ob_start();
		AutoloadFunction("determine_action",__DIR__.'/');
		// Sort data
		$component	=	determine_action(Safe::to_array(nApp::getRequest()));
		$action		=	$component['action'];
		$table		=	$component['table'];
		$icon		=	($component['action'] == 'duplicate')? 'icn_dup_on.png':'icn_alert.png';
		// Simplify array name
		$setData	=	$component['query'];
		// Fetch query engine
		$nubquery	=	nQuery();
?>
<div class="nbr_general_form" id="nbr_system_settings">
	<div class="nbr_general_cont">
		<div class="nbr_prefpane_content">
			<div class="nbr_hidewrap">
				<div id="nbr_prefpage_toolbar">
					<ul id="nbr_prefpane_hdbar">
						<li><img src="/core_images/core/<?php echo $icon; ?>" style="max-height: 40px; margin: 0 10px 0 0;" /></li>
						<li><?php echo ($setData != 0)? "Are you sure you want to":"Action may fail."; ?> <?php if(empty($component['action'])) { ?>delete component<?php } else echo strtolower(str_replace("_", " ", $component['action']));  ?>?</li>
						<li>
							<ul class="nbr_prefpane_btn">
								<li class="nbr_closer_small" data-closewhat="nbr_system_setttings" data-filter="slide"></li>
							</ul>
						</li>
					</ul>
				</div>
				<div class="nbr_prefpanel_content" id="nbr_site_pane">
					<?php include(__DIR__."/../confirm/{$action}.php"); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$(".nbr_hidewrap").fadeIn("slow");
	$("#nbr_system_settings").on("click",".nbr_closer_small", function(){
		$("#nbr_system_settings").fadeOut();
	});
});
</script>
<?php	$data	=	ob_get_contents();
		ob_end_clean();

		return $data;
	}