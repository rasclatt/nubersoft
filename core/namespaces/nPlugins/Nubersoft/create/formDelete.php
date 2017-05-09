<?php
use Nubersoft\nApp as nApp;
use Nubersoft\Safe as Safe;

if(!function_exists("is_admin"))
	return;
	
if(!is_admin())
	return;
?>
		<div class="component_buttons_wrap">
            <div class="delete_button ajaxtrigger" data-gopage="confirm" data-gopagekind="g" data-gopagesend="requestTable=<?php echo Safe::encOpenSSL($this->table); ?>&ID=<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>&delete=on">
            </div>
		</div>