<?php
if(!$this->isAdmin())
	return;

$SERVER	=	$this->getDataNode('_SERVER');

$arr	=	[
	"action" => "nbr_open_modal",
	"DOM" => [
		"html" => [
			'<div class="load-modal-spinner"><img src="/media/images/ui/loader.gif"></div>'
		],
		"sendto" => [
			"#loadspot_modal"
		],
		"event" => [
			"click"
		],
		"events" => [
			"click"
		]
	],
	"data" => [
		"deliver" => [
			"ID" => $this->data['ID'],
			"action" => "nbr_component_edit_image",
			"close_button" => "CANCEL",
			"jumppage" => $SERVER->HTTP_REFERER
		]
	]
];

?>
		<div class="component_buttons_wrap">
			<a href="#" class="nTrigger nbr_click_opacity" data-instructions='<?php echo json_encode($arr) ?>'><img src="<?php echo $this->siteUrl() ?>/media/images/core/icn_image.png" style="max-height: 40px; margin: 5px;" /></a>
		</div>