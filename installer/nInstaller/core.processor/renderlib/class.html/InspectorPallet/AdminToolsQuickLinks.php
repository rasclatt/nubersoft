<?php
if(!function_exists("AutoloadFunction"))
	return;
	
foreach($results as $object) {
		if(!preg_match('/^nb/',$object)) { ?>

			<li>
				<a href="<?php echo $admin_link['full_path']; ?>?requestTable=<?php echo $object; ?>"<?php echo (isset($_GET['requestTable']) && $object == $_GET['requestTable'])? ' style="background-color: #666; text-shadow: 1px 1px 3px #000; box-shadow: 0 0 8px #000;"':''; ?>><?php echo str_replace("_","&nbsp;",strtoupper($object)); ?></a>
			</li>

<?php 			} 
		}
?>