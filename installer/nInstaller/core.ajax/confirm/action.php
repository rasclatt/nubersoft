<?php
	if(!function_exists("is_admin"))
		return false;
	
	if(!is_admin())
		exit;
?>

		<table style="width: 100%;">
			<tr>
				<td>
					<form action="<?php echo $_SERVER['HTTP_REFERER']; ?>" method="post" enctype="multipart/form-data"><?php
							foreach($useArray as $keys => $values) {
									if($keys !== 'update' && $keys !== 'submit' && $keys !== 'add')
										{ ?>
						<p>Setting:<?php echo $keys; ?> - Value: <?php echo $values; ?></p>
						<input type="hidden" name="<?php echo $keys; ?>" value="<?php echo $values; ?>" /><?php
										}
								} ?>
						<div class="nbr_button"><input type="submit" name="submit" value="<?php echo (isset($_REQUEST['action']))? ucwords(str_replace("_", " ", $_REQUEST['action'])): 'Continue'; ?>?" style="margin: 0;float: right;" /></div>
					</form>
				</td>
			</tr>
		</table>