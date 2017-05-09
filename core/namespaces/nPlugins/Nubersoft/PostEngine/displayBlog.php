<div style=" padding: 5px; border: 1px solid rgb(<?php echo rand(0,255); ?>,<?php echo rand(0,255); ?>,<?php echo rand(0,255); ?>);">
	<?php echo $this->posts[$key]['content']; ?>
	<?php
	if(is_admin())
		echo $this->mTools($this->posts[$key]);
	
	if($this->allow_subs)
		echo $this->replyForm(array("parent_id"=>$this->posts[$key]['ID']));
	
	if(is_array($value))
		echo $this->displayBlog($value,$key);
?>
</div>