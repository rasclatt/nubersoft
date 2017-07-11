<?php
if(!$this->isAdmin())
	return;

$this->autoload('admintools_toggler_status',__DIR__.DS.'functions'.DS);
# If the viewer toggle isn't set, don't start the menubar
if(admintools_toggler_status($this) || $this->isAdminPage()) {
		
	$object	=	array(
					'action'=>'admintools_load_tools',
					'data'=>array(
						'deliver'=>array(
							'ID'=>$this->getPage('ID')
						)
					),
					'nEvents'=>array(
						'ajax_response_before'=>array(
							'load_tools'=>false
						)
					)
				);
?>
<script>
eEngine.addEvent({
	'name':'ajax_response_before',
	'use':'load_tools'
	},function() {
		var response	=	eEngine.getData('ajax_response_before');
		//console.log(eEngine.getAll());
		if(!is_object(response) && !preg_match('\^{|\}$',response))
			$('#tool_inspector_container').html(response);
		//else
		//	console.log(response);
});
</script>
<div id="tool_inspector_container" class="nListener" data-instructions='<?php echo json_encode($object); ?>'>
	<!-- LOAD SPOT FOR THE ADMIN CONTROLS -->
</div>
<?php
}
if(!$this->isAdminPage()) { ?>
<?php echo $this->useTemplatePlugin('component_tab') ?>
<?php }