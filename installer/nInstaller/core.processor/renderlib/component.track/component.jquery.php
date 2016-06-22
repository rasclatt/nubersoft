<?php	
	if(!isset($sectionBreak))
		return;
		
		$filterd_var	=	array("/", "!", "@","#","$","%","^","&","*","(",")","{","}","[","]","|","\\",',',".");
		$final_filtered	=	$randomizer . str_replace($filterd_var, "",$sectionBreak[$fieldKeys]);
		 ?>
    <div class="toolsheaders" id="tools<?php echo $final_filtered; ?>_button_on" onClick="PowerButton('tools<?php echo $final_filtered; ?>','toggle','.toolsToggleBox')">
		<?php echo ucwords(str_replace($filterd_var, " / ", $sectionBreak[$fieldKeys])); ?>
	</div>
    <div class="toolsToggleBox" id="tools<?php echo $final_filtered; ?>_panel">