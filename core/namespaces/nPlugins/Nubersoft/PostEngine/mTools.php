<form method="post">
	<fieldset class="">
		<input type="hidden" name="requestTable" value="components" />
		<input type="hidden" name="ID" value="<?php echo $useData['ID']; ?>" />
		<input type="hidden" name="unique_id" value="<?php echo $useData['unique_id']; ?>" />
		<select name="page_live">
			<option value="off">DISABLE</option>
			<option value="on" selected="selected">POST</option>
		</select>
		<textarea name="content"><?php echo $useData['content']; ?></textarea>
		<input type="submit" name="update" value="EDIT" />
	</fieldset>
</form>