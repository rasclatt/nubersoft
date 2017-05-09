<?php
if(!isset($this->inputArray))
	return; 

$rand	=	FetchUniqueId();
?>
        <div class="component_buttons_wrap">
            <div class="dup_button">
            	<button type="submit" name="duplicate" onClick="ScreenPop(); onthefly('ID=<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>&unique_id=<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>&duplicate=true','/ajax/confirm.php', 'POST')">dup</button>
            </div>
		</div>
		
<?php
	if(!isset($get_dup_list)) {
			AutoloadFunction('organize');
			$get_dup_list	=	organize($this->nubquery->select()->from("components")->where(array("ref_spot"=>"lib"))->orderBy(array("component_type"=>"DESC"))->fetch(), 'unique_id');
			$dup_list		=	(is_array($get_dup_list))? new ArrayObject($get_dup_list):(object) array();
?>
				<div class="dup_wrap">
					<div class="dup_span"></div>
                    <div class="dup_pop">
                   		<div style="margin-bottom: 5px; color: inherit;">Saved Components</div>
                        <div class="dup_scrolled">
						<?php
						if(!empty($dup_list)) {
								foreach($dup_list as $key => $value) {
										$this->driveDownArr($value);
									}
							}
						?>
                        </div>
                    </div>
                </div>
<?php } ?>