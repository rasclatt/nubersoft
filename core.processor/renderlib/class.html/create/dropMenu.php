<?php
if(!isset($this->inputArray[0]))
	return; ?>
					<select name="parent_id">
							<option value="">None</option>
						<?php
						if(is_array($containers)) {
								// This is the parent row
								if($_parent)
									$_checkArray	=	$_parents[$payload['unique_id']];
									
								foreach($containers as $parents) {
											if($_parent || in_array($parents['unique_id'],$_checkArray))
												$_disabled	=	true;
											elseif($payload['unique_id'] == $parents['unique_id'])
												$_disabled	=	true;
											else
												$_disabled	=	false;
										?>
							<option value="<?php echo $parents['unique_id']; ?>"<?php if($_disabled) { ?> disabled<?php } if($parents['unique_id'] == $payload['parent_id']) { ?> selected<?php } ?>><?php echo (!empty($parents['content']))? htmlentities(substr(Safe::decode($parents['content']),0,20),ENT_QUOTES):substr($parents['unique_id'],0,20); ?></option>
							<?php
									}
							} ?>
					</select>