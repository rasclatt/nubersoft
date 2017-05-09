<?php
if(!$this->isAdmin())
	return;
?>
<div class="component_buttons_wrap">
	<div class="delete_button nTrigger" data-instructions='<?php echo json_encode(array('action'=>'nbr_delete_'.$this->getTable().'_confirm','data'=>array('deliver'=>array('action'=>'nbr_delete_'.$this->getTable().'_confirm','ID'=>$this->data['ID'],'jumppage'=>$this->getDataNode('_SERVER')->HTTP_REFERER,'close_button'=>'CANCEL','modal_options'=>array('max'=>'400px'))))) ?>'>
	</div>
</div>