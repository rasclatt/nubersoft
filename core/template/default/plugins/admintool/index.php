<?php
$this->autoloadContents(__DIR__.DS.'functions');
$this->useTemplatePlugin('admin_pagination','initialize.php');

echo pagination_results($this);