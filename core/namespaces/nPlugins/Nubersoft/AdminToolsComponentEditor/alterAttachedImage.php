<?php
if(!$this->isAdmin())
	return;

$SERVER	=	$this->getDataNode('_SERVER');
?>
		<div class="component_buttons_wrap">
			<a href="#" class="nTrigger nbr_click_opacity" data-instructions='{"action":"nbr_open_modal","data":{"deliver":{"ID":"<?php echo $this->data['ID'] ?>","action":"nbr_component_edit_image","close_button":"CANCEL","jumppage":"<?php echo $SERVER->HTTP_REFERER ?>"}}}'><img src="<?php echo $this->siteUrl() ?>/media/images/core/icn_image.png" style="max-height: 40px; margin: 5px;" /></a>
		</div>