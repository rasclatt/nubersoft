<ul id="nbr_prefpane_hdbar" class="nbr_ux_element">
	<li><?php echo $this->getHelper('nImage')->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'gear.png',array('style'=>'max-height: 40px;'),false); ?></li>
	<li>Site Preferences for: <?php echo $_SERVER['HTTP_HOST']; ?></li>
	<li>
		<ul class="nbr_prefpane_btn">
			<li class="nbr_reveal nTrigger" data-instructions='{"FX":{"acton":[".nbr_prefpanel_content","#nbr_site_pane"],"fx":["hide","slideDown"]}}'>
				<div>Site Prefs</div>
			</li>
			<li class="nbr_reveal nTrigger" data-instructions='{"FX":{"acton":[".nbr_prefpanel_content","#nbr_head_pane"],"fx":["hide","slideDown"]}}'>
				<div>Header Prefs</div>
			</li>
			<li class="nbr_reveal nTrigger" data-instructions='{"FX":{"acton":[".nbr_prefpanel_content","#nbr_foot_pane"],"fx":["hide","slideDown"]}}'>
				<div>Footer Prefs</div>
			</li>
			<li class="nbr_closer_small nTrigger" data-instructions='{"FX":{"fx":["slideUp","removeClass"],"acton":["#loadspot_modal","#loadspot_modal"]}}'></li>
		</ul>
	</li>
</ul>