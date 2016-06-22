<?php
if(!function_exists("is_admin"))
	return;

if(!is_admin())
	return;
?>
        <div class="component_buttons_wrap">
            <div class="delete_button ajaxtrigger" data-gopage="confirm" data-gopagekind="g" data-gopagesend="requestTable=<?php echo Safe::encOpenSSL($this->table); ?>&ID=<?php if(isset($this->data['ID'])) echo $this->data['ID']; ?>&delete=on">
            </div>
		</div>