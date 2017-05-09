<?php
# Activate, deactivate tinymce
if($this->getRequest('wysiwyg')) {
	if(isset($_SESSION['wysiwyg']) && ($this->getRequest('wysiwyg') == 'off'))
		unset($_SESSION['wysiwyg']);
	elseif($this->getRequest('wysiwyg') == 'on')
		$_SESSION['wysiwyg']	=	true;
}