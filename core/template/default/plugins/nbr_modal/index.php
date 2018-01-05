<?php
$REQUEST	=	$this->toArray($this->getPost());
$data		=	$REQUEST['deliver'];
$action		=	$data['action'];
$modalOpts	=	(isset($data['modal_options']))? $data['modal_options'] : false;
$max		=	(isset($modalOpts['max']))? 'max-width: '.$modalOpts['max'] : '';
$class		=	(isset($modalOpts['class']))? ' '.$modalOpts['class'] : '';

$closeArr	=	[
	# Removes the content of the model on cancel
	'DOM' => [
		'html'=>[
			' '
		],
		'sendto'=>[
			'#loadspot_modal'
		],
		'event' => [
			'click'
		]
	],
	# Clear out the visibilty
	'FX'=>[
		'fx'=>[
			'removeClass'
		],
		'acton'=>[
			'#loadspot_modal'
		],
		'events'=>['click']
	]
];
?>
<div class="col-count-3 offset">
	<div class="nbr_modal_container col-2 push-col-1 medium">
		<div class="nbr_login_window<?php echo $class ?>" style="margin-top: 30px;<?php echo $max ?>">
			<?php echo $this->useTemplatePlugin($action) ?>
			<?php if(!empty($data['close_button'])): ?>
			<a href="#" class="nbr_button cancel nTrigger" data-instructions='<?php echo json_encode($closeArr) ?>'><?php echo $data['close_button'] ?></a>
			<?php endif ?>
		</div>
	</div>
</div>