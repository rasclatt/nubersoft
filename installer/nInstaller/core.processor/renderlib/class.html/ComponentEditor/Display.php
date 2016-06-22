<?php
	if(!function_exists("is_admin"))
		return;
	
	if(!is_admin())
		return;
?>

	<!-- Component buttons -->
	<div style="width: 98%; padding: 1%; display: inline-block;">
	<?php $this->AddNewComponent($CompSet); if($CompSet) $this->DeleteComponent($CompSet); /* $this->TinyMCE($CompSet); $this->HelpDesk(); */ $this->DuplicateComponent(); ?>
	</div>

<?php
	// Determine if the component is new or old
	$function			=	($CompSet)? 'update': 'add';
	
	// Determine if it's been admin locked
	$echoField	=	(!empty($this->data['admin_lock']))? is_admin() : true;
?>
			<div class="nbr_component_wrap nbr_general_form"><?php if($echoField) { ?>
				<form action="<?php if(isset($_SERVER['HTTP_REFERER'])) echo $_SERVER['HTTP_REFERER']; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="requestTable" value="<?php echo $this->table; ?>" />
					<input type="hidden" name="ID" value="<?php if(isset($this->data['ID'])) echo $this->data['ID']; ?>" />
					<input type="hidden" name="unique_id" value="<?php if(isset($this->data['unique_id'])) echo $this->data['unique_id']; ?>" />
					<input type="hidden" name="command" value="component" />
					<input type="hidden" name="override" value="1" />
					<input type="hidden" name="thumbnail" value="1" />
					<div class="form-input">
						<div style="display: inline-block; width: 100%;">
							<?php $this->ContainerDropDown();// Nesting menu ?>
						</div>
<?php					// Create all the form elements
						echo $this->createFormElements();
						?>
					</div>
					<?php
					if(isset($this->ref_page) && $this->ref_page != false) { ?>
					<input type="hidden" name="ref_page" value="<?php if(isset($this->ref_page)) echo $this->ref_page; ?>" /><?php } ?>
					<div class="nbr_button">
						<input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="margin: 15px auto 0 auto;" /></div>
				</form>
<script>
$("input[type=submit]").removeAttr('disabled');
</script>			
			<?php }
				else { ?>
						Component Locked: <br />You must be a Superuser to Unlock.
			<?php } ?>
            </div>