<?php
/*Title: ChangePassword()*/
/*Description: This function just includes the change password html for use when user requests to change their password. It must be used in conjunction with `AutoloadFunction()`*/

	function ChangePassword()
		{
			printpre(NubeData::$settings);
			
			register_use(__FUNCTION__);
			$reset	=	NubeData::$settings->engine->reset->reset;
			$data	=	NubeData::$settings->engine->reset->data;
			if($reset != false)
				include(RENDER_LIB.'/assets/login/change.password.php');
		}
?>