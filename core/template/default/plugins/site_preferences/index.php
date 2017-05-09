<div class="nbr_general_form nbr_ux_element" id="nbr_system_settings">
	<div class="nbr_general_cont">
		<div class="nbr_prefpane_content">
			<div class="nbr_hidewrap">
				<div id="nbr_prefpage_toolbar">
				<?php include(__DIR__.DS.'toolbar.php'); ?>	
				</div>
				<div class="nbr_prefpanel_content" id="nbr_site_pane">
					<p style="font-size: 22px; color: #FFF; margin: 20px 0 0 0;">Site Preferences</p>
					<div class="nbr_general_form">
						<?php include(__DIR__.DS.'favicon.php'); ?>
						<?php include(__DIR__.DS.'logo.php'); ?>
					</div>
					<?php include(__DIR__.DS.'site.php'); ?>
				</div>
				<div class="nbr_prefpanel_content" id="nbr_head_pane" style="display: none;">
					<?php include(__DIR__.DS.'head.php'); ?>
				</div>
				<div class="nbr_prefpanel_content" id="nbr_foot_pane" style="display: none;">
					<?php include(__DIR__.DS.'foot.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include(__DIR__.DS.'javascript.php'); ?>