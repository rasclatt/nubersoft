<form method="post">
	<fieldset class="">
	<input type="hidden" name="requestTable" value="components" />
	<?php echo (!empty($useData['ID']))? $useData['ID'] : ""; ?><br />
	<?php echo (!empty($useData['unique_id']))? $useData['unique_id'] : ""; ?><br />
	<input type="hidden" name="ID" value="<?php echo (!empty($useData['ID']))? $useData['ID'] : ""; ?>" />
	<input type="hidden" name="unique_id" value="<?php echo (!empty($useData['unique_id']))? $useData['unique_id'] : ""; ?>" />
	<input type="hidden" name="ref_page" value="<?php echo $this->ref_page; ?>" /><br />
	<input type="hidden" name="ref_spot" value="<?php echo $this->ref_spot; ?>" />
<?php
	if(is_admin()) {
?>	<select name="page_live">
		<option value="off">DISABLE</option>
		<option value="on" selected="selected">POST</option>
	</select>
<?php
	}
	else {
?>	<input type="hidden" name="page_live" value="on" />
<?php	
	}
	
	if(!empty($useData['parent_id'])) {
?>	<label>Parent</label>
	<input type="text" name="parent_id" value="<?php echo $useData['parent_id']; ?>" />
<?php
	}
?>	
	<label>Reply</label>
	<textarea name="content"></textarea>
	<input type="submit" name="<?php echo (!empty($useData['ID']))? "update" : "add"; ?>" value="POST" />
	</fieldset>
</form>