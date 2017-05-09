<?php $View	=	$this->get3rdPartyHelper('\nPlugins\Nubersoft\View'); ?>
<div class="nbr_error_msg_block">
	<div class="nbr_error_block_child">
		<div>
			<?php echo $View->renderIncidental('login') ?>
			<?php echo $View->renderIncidental('mismatch','is_error') ?>
			<?php echo $View->renderIncidental('token_mismatch','is_error') ?>
		</div>
	</div>
</div>