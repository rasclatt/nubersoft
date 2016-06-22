<?php
	
	function printpre($val = false, $line = false, $file = false, $dump = false)
		{
			ob_start();
?>			<div class="nbsprintpre">
				<div>
					<h3><?php echo $line." | ".$file; ?></h3>
					<pre style="padding: 20px;">
					<?php
					if($dump == false)
						print_r($val);
					else
						var_dump($val); ?>
					</pre>
				</div>
			</div>
<?php		$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}