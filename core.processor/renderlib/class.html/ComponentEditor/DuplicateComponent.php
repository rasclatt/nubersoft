<?php
	if(!function_exists("is_admin"))
		return;
	
	if(!is_admin())
		return;
		
	if(empty($this->data['ID'])) 
		return;
	
	$rand	=	$this->tempid;
	$query	=	nQuery();
?>
		
        <div class="component_buttons_wrap">
            <div class="duplicate_button ajaxtrigger" data-gopage="confirm" data-gopagekind="g" data-gopagesend="ID=<?php if(isset($this->data['ID'])) echo $this->data['ID']; ?>&duplicate=true">
            </div>
		</div>
<?php
	if(!isset($get_dup_list)) {
			$list	=	$query	->select()
								->from("components")
								->where(array("ref_spot"=>"lib"))
								->orderBy(array("component_type"=>"DESC"))
								->fetch();
			
			if(!is_array($list))
				return;
?>
		<div class="component_buttons_wrap">
			<div class="dup_span nbrAccordion"></div>
			<div class="dup_pop" style="display: inline-block; position: absolute; left: 0; right: 0; overflow: visible; display: none; background-color: #222; padding-top: 5px; box-shadow: 0 0 20px #000;">
				<div class="dup_scrolled" style="display: inline-block; width: 100%; background-color: #888; position: relative; left: 0; padding: 0;">
				<div style="padding: 10px 0 3px 0; display: inline-block; width: 100%;">
					<div class="nbr_closer_passive ajax_active_click" data-closewhat="up>.dup_pop"></div>
				</div>
					<div style="padding: 10px; background-color: #CCC;">
						<div style="margin-bottom: 5px; color: inherit;">Saved Components</div>
						<?php
						foreach($list as $value) {
								$this->DuplicateComponentsList($value);
							}
						?>
						</div>
				</div>
			</div>
		</div>
<?php	}
?>