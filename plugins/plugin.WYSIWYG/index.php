<?php
if(!function_exists("is_admin"))
	return;
	
if(!is_admin())
	return;
?>
<a href="?<?php echo (isset(NubeData::$settings->table_name))? "requestTable=".NubeData::$settings->table_name:''; echo (isset($_SESSION['wysiwyg']))? '&wysiwyg=off':'&wysiwyg=on'; ?>" style="font-size: 10px; width: auto;text-decoration: none; text-align: center;"><img src="/images/buttons/wyswyg_<?php echo (isset($_SESSION['wysiwyg']))? 'on':'off'; ?>_on.png" style="width: 60px; border: none; margin: 0 5px;" /><br />WYSIWYG <?php echo (isset($_SESSION['wysiwyg']))? 'OFF':'ON';
?></a>