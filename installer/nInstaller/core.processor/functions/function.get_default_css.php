<?php
	function get_default_css($type = false)
		{
			register_use(__FUNCTION__);
			ob_start();
?>
<link rel="stylesheet" href="/css/default.css" />
<link rel="stylesheet" href="/css/menu.css" />
<?php		if($type != false) {
?>
<link rel="stylesheet" href="/css/admintools.css" />
<?php			}
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}
?>