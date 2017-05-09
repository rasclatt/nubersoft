<?php
use Nubersoft\nApp as nApp;

function build_table_rows($table,$values = array(),$columns = array(),$settings = array(),$dropdowns = array(),$headerrow = false)
		{
			$nApp		=	nApp::call();
			$nubquery	=	$nApp->nQuery();	
			$regBuild	=	table_prefs();
			
			if(!empty($columns)) {
				foreach($columns as $column) {
					$name	=	$column;
					$type	=	(!empty($settings[$column]['column_type']))? $settings[$column]['column_type']: "text";
					$type	=	(preg_match('/^nul/i',$type) && strlen($type) <= 4)? "text": $type;
					
					if(isset($regBuild[$table])) {
						$type	=	(in_array($name,$regBuild[$table]))? $type : 'fullhide';
					}
					
					$size	=	(!empty($settings[$column]['size']))? $settings[$column]['size']:""; ?>
				<td style=" <?php if($type == 'fullhide') echo 'display: none;'; if($values !== 'head') { ?> padding: 5px;<?php } else { ?>background-color: #333; color: #FFF;<?php } ?> text-align: center;">
<?php					if($headerrow) {
?>						<div style="padding: 5px 10px; font-size: 12px; white-space: nowrap; <?php if($type == 'fullhide') echo 'display: none;'; ?>">
						<?php echo str_replace("_"," ",strtoupper($name)); ?>
					</div>
<?php					}
				
					if($headerrow) {
?>						<div style="padding: 5px; background-color: #333;<?php if($type == 'fullhide') echo 'display: none;'; ?>">
<?php 					}
					include(NBR_RENDER_LIB.DS.'assets'.DS.'form.inputs'.DS.$type.".php");
					if($headerrow) {
?>						</div>
<?php 					}
?>					</td>
<?php				}
			}
		}