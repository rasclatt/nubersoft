<?php
	function render_admintools($settings = false)
		{
			register_use(__FUNCTION__);
			ob_start();
			include_once(NBR_RENDER_LIB."/assets/html/admin.php");
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>