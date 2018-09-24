<div class="nbsprintpre">
	<div style="text-align: left;">
		<pre style="padding: 20px;">
		<?php
		if(empty($values['dump']))
			print_r($print);
		else
			var_dump($print); ?>
		</pre>
		<?php if($backtrace) { ?>
		<div style="padding: 10px; border-radius: 3px; border: 1px solid; margin: 5px 0; background-color: #EBEBEB; font-family: Arial; box-shadow: inset 0 0 5px rgba(0,0,0,0.5);">
			<p style="margin: 0 0 5px 0;"><?php echo $debugBlock; ?>
		</div>
		<?php } ?>
		<?php if(!empty($values['debugger'])) echo $values['debugger']; ?>
	</div>
</div>