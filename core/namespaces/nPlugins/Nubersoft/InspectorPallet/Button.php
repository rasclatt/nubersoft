<?php $nForm	=	$this->getHelper('nForm');
?>
<div class="nbr_menu_toggler_wrap">
	<?php echo $formOpen = $nForm->open(array('enctype'=>"application/x-www-form-urlencoded")) ?>
		<?php echo $nForm->fullhide(array('name'=>"action",'value'=>"nbr_admin_toggle")) ?>
		<?php echo $nForm->fullhide(array('name'=>"toggle",'value'=>(($useData->getEditStatus())? '':'1'))) ?>
		<div class="<?php if(!$useData->getEditStatus()) echo 'col-count-2' ?>" id="toggle_table">
			<?php if(!$useData->getEditStatus()) { ?>

			<div class="fullsite align-middle">
				<span class="toggle_text">EDIT <?php echo $nForm->fullhide(array('name'=>"type",'value'=>"track")) ?></span>
			</div>
			
			<?php } ?>

			<div class="align-middle" id="nbr_toggler_edit<?php echo ($useData->getEditStatus())? '_on':''; ?>">
				<?php echo $nForm->submit(array('name'=>"edit",'value'=>"&nbsp;",'disabled'=>"disabled",'class'=>"disabled-submit")) ?>
			</div>
			<?php
	if($useData->getEditStatus()) { ?>
		</div>
	<?php echo $nForm->close() ?>
</div>
<div style="display: inline-block; float: left;">
	<?php echo $formOpen ?>
		<?php echo $nForm->fullhide(array('name'=>"command",'value'=>"toggle_set")) ?>
		<?php echo $nForm->fullhide(array('name'=>"toggle",'value'=>"1")) ?>
		<?php echo $nForm->fullhide(array('name'=>"edit",'value'=>"1")) ?>
	<?php echo $nForm->close() ?>
</div>
<?php }
	else { ?>
			</div>
	<?php echo $nForm->close() ?>
</div>
<?php
	}
				
	if($this->isAjaxRequest()) { ?>
<script>$('.disabled-submit').attr("disabled",false);</script>
<?php }