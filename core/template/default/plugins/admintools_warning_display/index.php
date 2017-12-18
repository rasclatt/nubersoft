<?php
	$success	=	
	$errors		=	[];
	$msg		=	$this->getSystemMessages();

	if(!empty($msg['success'])) {
		$this->extractAll($msg['success'],$success);
		echo '
<div class="nbr_warning_wrap" style="display: none;">
	<div class="col-1 span3 col-count-3 offset">
		<div class="col-2">
			<div class="nbr_success nTrigger pointer" data-instructions=\'{"FX":{"fx":["fadeOut"],"acton":[".nbr_success"]}}\'>'.implode('
			</div>
			<div class="nbr_success col-2">	
				',$success).'
			</div>
		</div>
	</div>
</div>';
	}

	if(!empty($msg['alert'])) {
		$this->extractAll($msg['alert'],$errors);
		echo '
<div class="nbr_warning_wrap" style="display: none;">
	<div class="col-1 span3 col-count-3 offset">
		<div class="col-2">
			<div class="nbr_warning">'.implode('
			</div>
			<div class="nbr_warning col-2">	
				',$errors).'
			</div>
		</div>
	</div>
</div>';
	}
	?>
<script>
$('.nbr_warning_wrap').delay(1000).fadeIn('fast');
</script>