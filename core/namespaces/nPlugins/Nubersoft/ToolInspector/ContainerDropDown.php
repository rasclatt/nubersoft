<?php
if(!$this->isAdmin())
	return;
?>
<select name="parent_id">
	<option value="">None</option>
	<?php
	if(is_array($containers)) {
		// This is the parent row
		$_checkArray	=	(!empty($_parent) && isset($this->data['unique_id']))? $_parents[$this->data['unique_id']] : array();
		$_disabled	=	false;
		
		foreach($containers as $parents) {
			if(!empty($_parent) || in_array($parents['unique_id'],$_checkArray))
				$_disabled	=	true;
			elseif(isset($this->data['unique_id']) && ($this->data['unique_id'] == $parents['unique_id']))
				$_disabled	=	true;
			
			if(isset($parents['content']) && !empty(trim($parents['content'])))
				$name	=	trim($parents['content']);
			elseif(!empty(trim($parents['menu_name'])))
				$name	=	ucfirst(trim($parents['menu_name']));
			elseif(!empty(trim($parents['full_path'])))
				$name	=	ucfirst(trim($parents['full_path'],'/'));
			elseif(!empty($parents['unique_id']))
				$name	=	substr($parents['unique_id'],0,20);
			else
				$name	=	'Untitled';
				
			$selected	=	(isset($this->data['parent_id']) && ($parents['unique_id'] == $this->data['parent_id']));
	?>

	<option value="<?php echo $parents['unique_id']; ?>"<?php if($_disabled) { ?> disabled<?php } if($selected) { ?> selected<?php } ?>><?php echo $this->safe()->encode($name); ?></option>
		<?php
		}
	} ?>
</select>
