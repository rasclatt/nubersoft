<?php
$success	=	array_unique($this->getSystemMessages('success'));
$errors		=	array_unique($this->getSystemMessages('errors'));
$msg		=	(!empty($this->getRequest('msg')))? $this->getRequest('msg') : false;
?>

<?php if(!empty($errors)): ?>
	<div class="col-2">
		<div class="nbr_error pointer"><?php echo implode('</div><div class="nbr_error">', $errors) ?></div>
	</div>
<?php elseif(!empty($success)): ?>
	<div class="col-2">
		<div class="nbr_success pointer"><?php echo implode('</div><div class="nbr_success">', $success) ?></div>
	</div>
<?php elseif(!empty($msg)): ?>
	<div class="col-2">
		<div class="nbr_warning pointer"><?php echo $msg ?></div>
	</div>
<?php endif ?>
<script>
	$(function(){
		<?php if($this->isAdmin()): ?>
		$('.nbr_warning, .nbr_success, .nbr_error').delay(3000).slideUp('fast');
		<?php endif ?>
		$('.nbr_warning, .nbr_success, .nbr_error').on('click', function(){
			$(this).slideUp('fast');
		});
	});
</script>