<?php
# Activate tinyMCE
if(isset($_SESSION['wysiwyg'])) {
	$this->autoload('tiny_mce');
	echo tiny_mce(true);
}