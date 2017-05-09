		<div class="nbr_debug_backtracer nParent">
			<table style="margin: 0 auto;"  cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>&#9818; <?php echo $cCount; ?></td>
					<td>&#9822; <?php echo $mCount; ?></td>
					<td>&#9823: <?php echo $fCount; ?></td>
					<td><div class="nbr_tiny_stats red">user:&nbsp;<?php echo $setFuncs['user']; ?></div></td>
					<td><div class="nbr_tiny_stats orange">internal:&nbsp;<?php echo (($fCount-$setFuncs['user'])-$setFuncs['anon']); ?></div></td>
					<td><div class="nbr_tiny_stats blue">anonymous:&nbsp;<?php echo $setFuncs['anon']; ?></div></td>
					<td><div class="nbr_debub_clicker nTrigger" data-instructions='{"data":{"fx":["slideToggle"],"acton":["find::.nbr_debug_pop::slideToggle"]}}'><span style="font-size: 18px;">&#128027;</span>DEBUGGER</div>
			</td>
				</tr>
			</table>
			<div class="nbr_debug_pop">
				<pre>
					<?php print_r($disp); ?>
				</pre>
			</div>
		</div>