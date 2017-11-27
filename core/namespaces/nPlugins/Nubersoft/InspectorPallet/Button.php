<div class="nbr_menu_toggler_wrap">
<?php $nForm	=	$this->getHelper('nForm'); ?>
	<?php echo $formOpen = $nForm->open(array('enctype'=>"application/x-www-form-urlencoded")) ?>
		<?php echo $nForm->fullhide(array('name'=>"action",'value'=>"nbr_admin_toggle")) ?>
		<?php echo $nForm->fullhide(array('name'=>"toggle",'value'=>(($useData->getEditStatus())? '':'1'))) ?>
		<div class="col-count-2" id="toggle_table">
<?php if(!$useData->getEditStatus()) {
?>
				<div class="fullsite col-1 span-1 align-middle">
					<span class="toggle_text">EDIT <?php echo $nForm->fullhide(array('name'=>"type",'value'=>"track")) ?></span>
				</div>
<?php }
?>
				<div class=" push-col-6 medium col-2 span-1 align-middle" id="nbr_toggler_edit<?php echo ($useData->getEditStatus())? '_on':''; ?>">
					<?php echo $nForm->submit(array('name'=>"edit",'value'=>"&nbsp;",'disabled'=>"disabled",'class'=>"disabled-submit")) ?>
				</div>
<?php if($useData->getEditStatus()) {
?>
		</div>
	<?php echo $nForm->close() ?>
</div>
<div style="display: inline-block; float: left;">
	<?php echo $formOpen ?>
		<?php echo $nForm->fullhide(array('name'=>"command",'value'=>"toggle_set")) ?>
		<?php echo $nForm->fullhide(array('name'=>"toggle",'value'=>"1")) ?>
		<?php echo $nForm->fullhide(array('name'=>"edit",'value'=>"1")) ?>
<?php }
?>
</div>
	<?php echo $nForm->close() ?>
</div>
<script>
$('.disabled-submit').attr("disabled",false);
</script>