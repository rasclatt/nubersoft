<?php
if(!$this->isAdmin())
	return;
if(!empty($this->getDataNode('_SERVER')->HTTP_REFERER))
	$ref	=	$this->getDataNode('_SERVER')->HTTP_REFERER;
elseif(!empty($this->getPageURI('full_path')))
	$ref	=	$this->localeUrl($this->getPageURI('full_path'));
else
	$ref	=	$this->localeUrl();
?>
<div class="component_buttons_wrap">
	<div class="delete_button nTrigger" data-instructions='<?php echo json_encode(array('action'=>'nbr_delete_'.$this->getTable().'_confirm','data'=>array('deliver'=>array('action'=>'nbr_delete_'.$this->getTable().'_confirm','ID'=>$this->data['ID'],'jumppage'=>$ref,'close_button'=>'CANCEL','modal_options'=>array('max'=>'400px'))))) ?>'>
	</div>
</div>