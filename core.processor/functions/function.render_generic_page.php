<?php
	function render_generic_page($settings)
		{
			
			$content	=	(!empty($settings['content']))? $settings['content']:"";
			$back		=	(check_empty($settings,'back'))? '<a class="nbr_button" href="'.strip_tags($settings['back']).'">BACK</a>':false;
			$exit		=	(check_empty($settings,'exit',true))? true:false;
			
			ob_start();
			echo get_header().PHP_EOL; ?>
<body>
	<?php echo render_masthead().PHP_EOL; ?>
	<div id="maincontent">
		<div class="allblocks">
			<?php echo $content; ?>
			<?php if($back) echo $back; ?>
		</div>
	</div>
	<?php echo render_footer(); ?>
</body>
</html><?php
			$data	=	ob_get_contents();
			ob_end_clean();
			
			if($exit) {
					echo $data;
					exit;
				}
				
			return $data;
		}
?>