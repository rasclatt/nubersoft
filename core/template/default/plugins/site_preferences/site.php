<?php
$token		=	$this->fetchData('token');
$settings	=	$this->toArray($this->getSitePrefs());
?>
<form id="settings_site" method="post" action="<?php echo $this->getDataNode('_SERVER')->HTTP_REFERER ?>">
	<input type="hidden" name="token[nProcessor]" value="<?php echo $token ?>" />
	<input type="hidden" name="action" value="nbr_edit_system_settings" />
	<input type="hidden" name="page_element" value="settings_site" />
	<?php
	$dPrefs	=	$this->getDirList(__DIR__.DS.'form.site.prefs'.DS);
	foreach($dPrefs['host'] as $includes) {
		if(preg_match('/(\/site\.).*/',$includes))
			include($includes);
	}
	?>

	<div class="nbr_div_button nbr_formadd" data-formadd="settings_site" data-formgroup="additional" style="font-size: 14px; padding: 8px 16px; float: right;">ADD CUSTOM SETTING</div>
	<?php
		if(!empty($settings['custom'])) { ?>
	<div style="display: inline-block; width: 100%;">
		<div style="padding: 10px; background-color: #555; margin: 20px 0;">
			<h2 style="color: #CCC;">Custom Settings</h2>
			<?php
			foreach($settings['custom'] as $key => $vals) {
				$charLength	=	(strlen($this->safe()->decode($vals))+2);
			?>
			<div class="form_custElemWrap">
				<div class="form_removethis" style="color: #CCC; background-color: #000; padding: 15px 8px; border-radius: 3px; font-size: 13px; display: inline-block; float: right;">DELETE</div>
				<span>
					<label style="margin: 0 0 10px 0; padding: 0; width: auto; border: none; float: left; display: inline-block;"><?php echo $custName = ucwords(str_replace("_"," ", $key)); ?>
						<div class="form-input">
							<input type="text" name="content[custom][<?php echo $key; ?>]" value="<?php echo $vals; ?>" placeholder="Custom Setting for <?php echo $custName; ?>" size="<?php echo ($charLength < 10)? 10: $charLength; ?>" style="width: auto;" />
						</div>
					</label>
				</span>
				<hr style="border: 1px dashed #CCC; width: 99%;" />
			</div>
			<?php
			}
			?>
		</div>
	</div>
		<?php
		}
		?>
	<div class="nbr_button">
		<input type="submit" name="update" value="SAVE" />
	</div>
</form>