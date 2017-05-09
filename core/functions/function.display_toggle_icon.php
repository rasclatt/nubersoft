<?php
	function display_toggle_icon($value = false)
		{
			
			$value	=	strtolower($value);
			
			$live	=	($value == 'off' || empty($value))? "red":"green";
			ob_start(); ?>
			<img src="/images/core/led_<?php echo $live; ?>.png" width="15" height="15" />
			<?php
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}
?>