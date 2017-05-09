
<label>	Htaccess <div class="nbr_trigger nTrigger" data-instructions='{"action":"nbr_get_current_htaccess","data":{"deliver":{"sendback":"#nbr_htaccess_display_current"}}}'><span id="htHider">GET CURRENT</span></div>
	<div class="form-input">
		<textarea id="nbr_htaccess_display_current" name="content[htaccess]" placeholder="Create your own HTACCESS" class="textarea" ><?php echo (isset($settings['htaccess']))? $settings['htaccess']:""; ?></textarea>
	</div>
</label>