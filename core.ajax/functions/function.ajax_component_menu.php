<?php
function ajax_component_menu()
	{
		ob_start();
		$component	=	fetch_component_options(array("table"=>"menu_display","parent_id"=>$_REQUEST['parent_id']));
?>
 <div class="nbr_component_wrap nbr_general_form">
	<form class="compform" action="<?php if(!empty($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>" enctype="application/x-www-form-urlencoded" method="post">
		<input type="hidden" name="requestTable" value="menu_display" />
		<input type="hidden" name="ID" value="<?php echo (!empty($component['values']['ID']))? $component['values']['ID']:""; ?>" />
		<input type="hidden" name="unique_id" value="<?php echo (!empty($component['values']['unique_id']))? $component['values']['unique_id']:""; ?>" />
		<input type="hidden" name="parent_id" value="<?php echo (!empty($component['values']['parent_id']))? $component['values']['parent_id']:$_REQUEST['parent_id']; ?>" />
<?php
		foreach($component['layout'] as $compname => $compvals)
				echo render_component_settings(array("title"=>$compname,"options"=>$compvals,"settings"=>$component['settings'],"values"=>$component['values'],"dropdowns"=>$component['options']));
			
		if(!empty($component['values']['ID'])) {
?>
		<label>Delete?
			<input type="checkbox" name="delete" />
		</label>
<?php 		}
?>
		<div class="nbr_button">
			<input disabled="disabled" type="submit" name="<?php echo $component['function']; ?>" value="<?php echo strtoupper($component['function']); ?>" style="margin: 15px auto 0 auto;" /></div>
	</form>
 </div>
 <script>
 	$(".nbr_button").find("input[type=submit]").prop("disabled",false);
 </script>
 <?php		$data	=	ob_get_contents();
 			ob_end_clean();
			
			return $data;
 		}