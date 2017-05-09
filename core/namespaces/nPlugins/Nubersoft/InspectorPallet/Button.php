<?php $nForm	=	$this->getHelper('nForm'); ?>
<div class="nbr_menu_toggler_wrap">
	<?php echo $formOpen = $nForm->open(array('enctype'=>"application/x-www-form-urlencoded")) ?>
		<?php echo $nForm->fullhide(array('name'=>"action",'value'=>"nbr_admin_toggle")) ?>
		<?php echo $nForm->fullhide(array('name'=>"toggle",'value'=>(($useData->getEditStatus())? '':'1'))) ?>
		<table border="0" cellpadding="0" cellspacing="0" id="toggle_table">
			<tr>
<?php if(!$useData->getEditStatus()) {
?>
				<td class="fullsite">
					<span class="toggle_text">EDIT <?php echo $nForm->fullhide(array('name'=>"type",'value'=>"track")) ?></span>
				</td>
<?php }
?>
				<td id="nbr_toggler_edit<?php echo ($useData->getEditStatus())? '_on':''; ?>">
					<?php echo $nForm->submit(array('name'=>"edit",'value'=>"&nbsp;",'disabled'=>"disabled",'class'=>"disabled-submit")) ?>
				</td>
<?php if($useData->getEditStatus()) {
?>
			</tr>
		</table>
	<?php echo $nForm->close() ?>
</div>
<div style="display: inline-block; float: left;">
	<?php echo $formOpen ?>
		<?php echo $nForm->fullhide(array('name'=>"command",'value'=>"toggle_set")) ?>
		<?php echo $nForm->fullhide(array('name'=>"toggle",'value'=>"1")) ?>
		<?php echo $nForm->fullhide(array('name'=>"edit",'value'=>"1")) ?>
<?php }
?>
			</tr>
		</table>
	<?php echo $nForm->close() ?>
</div>
<script>
$('.disabled-submit').attr("disabled",false);
</script>