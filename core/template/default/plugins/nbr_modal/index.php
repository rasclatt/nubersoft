<?php
$REQUEST	=	$this->toArray($this->getPost());
$data		=	$REQUEST['deliver'];
$action		=	$data['action'];
$modalOpts	=	(isset($data['modal_options']))? $data['modal_options'] : false;
$max		=	(isset($modalOpts['max']))? 'max-width: '.$modalOpts['max'] : '';
$class		=	(isset($modalOpts['class']))? ' '.$modalOpts['class'] : '';
?>
<div class="nbr_modal_container">
	<div class="nbr_login_window<?php echo $class ?>" style="margin-top: 30px;<?php echo $max ?>">
		<?php
		echo $this->useTemplatePlugin($action);
		if(!empty($data['close_button'])) {
		?>
		<a href="#" class="nbr_button cancel nTrigger" data-instructions='<?php echo json_encode(array('html'=>array(' '),'sendto'=>array('#loadspot_modal'),'events'=>array('click'))) ?>'><?php echo $data['close_button'] ?></a>
		<?php } ?>
	</div>
</div>