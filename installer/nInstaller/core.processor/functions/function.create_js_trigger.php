<?php
	function create_js_trigger($dname = false,$info = false)
		{
			if($dname == false)
				return;
				
			ob_start(); ?>
			<span class="js_trigger" data-instruct="<?php echo $dname; ?>" style="display: none;"><?php echo $info; ?></span>
			<?php
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>