<?php
if(!$this->isAdmin())
	return;
	
if(empty($this->data['ID'])) 
	return;

$rand	=	$this->tempid;
$query	=	$this->nQuery();

$settings	=	array(
	"action"=>"nbr_duplicate_component",
	"data"=>array(
		"deliver"=>array(
			"ID"=>((isset($this->data['ID']))? $this->data['ID'] : '')
		)
	)
);
?>
        <div class="component_buttons_wrap">
            <div class="duplicate_button nTrigger" data-instructions='<?php echo json_encode($settings) ?>'>
            </div>
		</div>
		<div class="component_buttons_wrap duplicates">
<?php
if(!isset($get_dup_list)) {
	$list	=	$query
		->select()
		->from("components")
		->where(array("ref_spot"=>"duplicate"))
		->orderBy(array("component_type"=>"DESC"))
		->fetch();
	
	if(!is_array($list))
		return;
?>
			<div class="dup_span nTrigger" data-instructions='{"FX":{"fx":["slideToggle"],"acton":["next::slideToggle"],"fxspeed":["fast"]}}'></div>
			<div class="dup_pop nbr_dup_slider">
				<div class="dup_scrolled">
					<div>
						<div>Saved Components</div>
						<?php
						foreach($list as $value) {
							$this->duplicateComponentsList($value);
						}
						?>
					</div>
				</div>
			</div>
<?php
}
?>
		</div>