<?php
$sms	=	$this->toArray($this->getDataNode('plugin_data_settings_admindeny')->data);
?>
<body class="nbr">
<style>
ul,li { list-style: none; }
table.nbr_code_retrieve {
	border: none;
	font-family: Arial, Helvetica, sans-serif;
}
table.nbr_code_retrieve tr td {
	padding: 5px;
}
table.nbr_code_retrieve tr td:first-child {
	color: #FFF;
}
h1.nbr_ux_element {
	color: #FFF;
	margin: 0;
}
ul.nbr_error_temp_top,
ul.nbr_error_temp_top li {
	list-style: none;
	padding: 0;
	margin: 0;
}
ul.nbr_error_temp_top li {
	display: table-cell;
	vertical-align: middle;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 35px;
	color: #FFF;
	text-shadow: 0 0 8px #000;
}
ul.nbr_error_temp_top li:last-child {
	padding: 10px;
	text-align: center;
}
</style>
	<div class="allblocks" style="border: 1px solid #888; padding: 30px; max-width: 500px; background-color: #222; border-radius: 5px;">
		<div style=" background-color: red; padding: 20px; border-radius: 3px;">
			<ul class="nbr_error_temp_top">
				<li><?php echo $this->getMedia('image','logo'.DS.'u.png',array('style'=>'max-width: 40px; margin: 0 auto;')) ?></li>
				<li><?php echo $this->getDataNode('plugin_data_settings_admindeny')->msg ?></li>
			</ul>
		</div>
		<?php
		$alerts	=	$this->getAlert('nbr_login_with_code');
		if(!empty($alerts)) { ?>
		<div class="nbr_errors"><?php echo implode('</div><div class="nbr_errors">',$alerts) ?></div>
		<?php } ?>
		<div class="nbr_general_form">
			<?php 
			if(!empty($this->getDataNode('plugin_data_settings_admindeny')->codearr)) {
				echo '<p class="nbr_ux_element" style="font-size: 22px;color: #FFF;">You have submitted a request already. Add code here or send another below.</p>'.PHP_EOL.$this->render($this->getBackEnd('code.form.php')).'<hr />';
			}
			?>
			<form action="" method="post" id="nbr_check_code" class="nbr_ajax_form" data-instructions='{"action":"nbr_check_admin_code","data":{"ajax_disp":"<?php echo str_replace($this->localeUrl(),'',$this->getServer('SCRIPT_URI')) ?>"}}'>
				<input type="hidden" name="action" value="nbr_request_admin_access" />
				<p class="nbr_ux_element" style="font-size: 22px;color: #FFF;">Select a carrier to send a text to yourself to gain accesss.</p>
				<table class="nbr_code_retrieve" id="codespot">
					<tr>
						<td colspan="2">
						<select name="carriers">
							<option value="">Select Carrier</option>
							<?php foreach($sms['sms_carrier'] as $carrier): ?>
							<option value="<?php echo $carrier['sms_domain'] ?>"><?php echo $carrier['sms_name'] ?></option>
							<?php endforeach ?>
						</select>
						<input type="hidden" name="token[nProcessor]" />
						</td>
					</tr>
					<tr>
						<td>Mobile #</td>
						<td style="max-width: 10em;">
							<input type="text" name="mobile" placeholder="<?php echo $carrier['sms_digits'] ?> Digit Phone" size="<?php echo $carrier['sms_digits'] ?>" maxlength="<?php echo $carrier['sms_digits'] ?>" />
						</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>
							<input type="text" name="email" placeholder="Email on file" autocomplete="off" />
						</td>
					</tr>
					<tr>
						<td>Password</td>
						<td>
							<input type="password" name="password" placeholder="Your password on file" autocomplete="off" />
						</td>
					</tr>
				</table>
				<div style="text-align: right;">
					<span id="spinner"></span>
					<div class="nbr_button">
						<input type="submit" id="submit_code" value="SEND CODE" disabled />
					</div>
				</div>
			</form>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		$('#submit_code').on('click',function() {
			$('#spinner').html('<?php echo $this->getMedia('image','ui'.DS.'loader.gif',array('local'=>true,'version'=>true)) ?>');
		});
		
		$("#nbr_check_code").on('change submit keyup',function(e) {
			var	nProc	=	$('input[name="token\\[nProcessor\\]"]').val();
			if(e.type == 'submit') {
				if(empty(nProc)) {
					e.preventDefault();
					alert('Form token invalid. Reload page.');	
					return;
				}
			}
			var	mobile		=	$('input[name=mobile]');
			var	hasCarrier	=	(!empty($('select[name=carriers]').val()));
			var hasToken	=	(!empty(nProc));
			var	hasEmail	=	(($('input[name=email]').val()).match(/([0-9a-zA-Z\.\-\_]{1,})@([0-9a-zA-Z\.\-\_]{1,}).([0-9a-zA-Z\.\-\_]{1,})/) != null);
			var	hasPassword	=	(!empty($('input[name=password]').val()));
			var	hasMobile	=	(!empty(mobile.val()) && (mobile.attr('maxlength') == (mobile.val()).length));
			
			if(hasMobile)
				hasMobile	=	(is_numeric(mobile.val()));
			
			var	isDisabled	=	(!empty(hasCarrier) && !empty(hasToken) && !empty(hasEmail) && !empty(hasPassword) && !empty(hasMobile))? false : true;
			$('#submit_code').prop('disabled',isDisabled);
		});
	});
	</script>
		<?php
		echo $this->setDefaultRenderTemplate(false,'foot','admintools',true);
		?>
</body>
</html>