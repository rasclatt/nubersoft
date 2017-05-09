<?php
if(!$this->isAdmin()) {
	//http_response_code(403);
	throw new \Exception('You must be an administrator.');
	die();
}
?>
<div id="ajax_admindrop"></div>
<?php
# Tool pallet, provide the editor is turned on
$this->getHelper('InspectorPallet',$this->getHelper('nHtml'))->execute();
?>
<div id="ajax_loadwindow"></div>
