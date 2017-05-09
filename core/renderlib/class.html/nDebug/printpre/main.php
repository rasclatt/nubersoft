<div class="nbsprintpre">
	<div style="text-align: left;">
		<h3><?php echo implode(' | ',$assemble); ?></h3>
		<pre style="padding: 20px;">
		<?php
		if(empty($values['dump']))
			print_r($print);
		else
			var_dump($print); ?>
		</pre>
		<?php if(!empty($values['debugger'])) echo $values['debugger']; ?>
	</div>
</div>