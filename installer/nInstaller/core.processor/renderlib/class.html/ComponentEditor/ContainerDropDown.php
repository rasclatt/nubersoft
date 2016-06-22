<?php
if(!function_exists("is_admin"))
	return;
	
if(!is_admin())
	return;
?>
		<select name="parent_id">
				<option value="">None</option>
			<?php
			if(is_array($containers)) {
				
					if($_parent == true) {
							// This is the parent row
							$_checkArray	=	$_parents[$this->data['unique_id']];
						}
						
					foreach($containers as $parents) {
								if($_parent == true || in_array($parents['unique_id'],$_checkArray))
									$_disabled	=	true;
								elseif($this->data['unique_id'] == $parents['unique_id'])
									$_disabled	=	true;
								else
									$_disabled	=	false;
							?>
				<option value="<?php echo $parents['unique_id']; ?>"<?php if($_disabled == true) { ?> disabled<?php } if($parents['unique_id'] == $this->data['parent_id']) { ?> selected<?php } ?>><?php echo (!empty($parents['content']))? Safe::encode(substr(Safe::decode($parents['content']),0,20)):substr($parents['unique_id'],0,20); ?></option>
				<?php
						}
				} ?>
		</select>
