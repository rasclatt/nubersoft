<?php
$View	=	$this->get3rdPartyHelper('\nPlugins\Nubersoft\View');
?>
<div style="height: 0px; overflow: visible;">
	<div style="display: inline-block; width: 100%; margin-bottom: 10px; max-width: 350px;">
		<?php echo $View->renderIncidental('login') ?>
		<?php echo $View->renderIncidental('mismatch') ?>
		<?php echo $View->renderIncidental('token_mismatch') ?>
	</div>
</div>