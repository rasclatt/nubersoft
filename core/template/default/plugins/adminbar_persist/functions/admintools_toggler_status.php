<?php
function admintools_toggler_status(\Nubersoft\nApp $nApp)
	{
		if($nApp->getGet('toggle_editor') == 'on')
			return true;
		elseif($nApp->getGet('toggle_editor') == 'off')
			return false;
		else
			return (isset($nApp->getSession('admintools')->editor) && $nApp->getSession('admintools')->editor == 'on');
	}