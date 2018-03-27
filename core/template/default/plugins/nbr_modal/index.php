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
		'events'=>[
			'click'
		]
	]
];
?>
<div class="col-count-3 nbr_modal_container">
	<div class="col-2 push-col-3 medium">
		<div class="col-count-2 nbr_login_window<?php echo $class ?>">
			<div class="span-2">
				<?php echo $this->useTemplatePlugin($action) ?>
				<?php if(!empty($data['close_button'])): ?>
				<a href="#" class="nbr button cancel nTrigger" data-instructions='<?php echo json_encode($closeArr) ?>'><?php echo $data['close_button'] ?></a>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>