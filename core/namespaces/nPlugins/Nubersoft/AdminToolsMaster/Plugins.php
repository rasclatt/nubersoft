				<div style="background-color: transparent; min-height: 100px; padding: 20px;">
				<?php
				if(empty($plugins_array)) {
					echo '<h2>No plugins detected.</h2>';
					return false;
				} ?>
				<table style="float: left;" cellpadding="0" cellspacing="0" border="0">
					<tr>						
				<?php
				foreach($plugins_array as $file) {
					if(preg_match('/\index.php$/',$file)) { ?>
						<td style="border-right: 2px groove; padding:0 10px;" class="auto-add-plugins">
							<?php include_once($file); ?>
						</td><?php
						}
				} ?>
						</tr>
					</table>
				</div>