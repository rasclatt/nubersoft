<?php if(!isset($this->inputArray)) return; ?>

	<div class="helpdesk_wrapper" id="helpdesk_button<?php echo $this->inputArray[0]['unique_id']; ?>"<?php if(isset($results['difficulty']) && $results['difficulty'] == 'on'): ?> style="background-image: url(/images/core/advanced.png);<?php endif; ?>" onClick="MM_changeProp('helpdesk_panel<?php echo $rand = rand(200000,900000); ?>','','display','inline-block');">
	</div>
	<div class="help_desk_popup" id="helpdesk_panel<?php echo $rand; ?>" onClick="MM_changeProp('helpdesk_panel<?php echo $rand; ?>','','display','none');" style="position: absolute; left: 0; margin-left: 30px; color: #FFFFFF; font-size: 12px; line-height: 16px; cursor: pointer;">
		<?php echo Safe::decode($results['content']); ?>
	</div>