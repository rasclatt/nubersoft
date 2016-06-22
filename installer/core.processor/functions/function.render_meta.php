<?php
	function render_meta()
		{
			AutoloadFunction("get_page_options");
			$meta	=	get_page_options();
			if(empty($meta['meta']))
				return;

			ob_start();
			foreach($meta['meta'] as $key => $value) {
?>
<meta name="<?php echo Safe::decode($key); ?>" content="<?php echo Safe::decode($value); ?>" />
<?php		}
			
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}