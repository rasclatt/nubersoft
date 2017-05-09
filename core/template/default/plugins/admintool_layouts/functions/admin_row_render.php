<?php
function admin_row_render(\Nubersoft\nRender $nRender, \Nubersoft\nForm $nForm,$table,$hType,$dispCols,$select,$dropdowns,$values = false)
	{
		ob_start();
?>
		<?php echo $nForm->open(array('action'=>$nRender->siteUrl().$nRender->getDataNode('_SERVER')->REQUEST_URI,'enctype'=>'multipart/form-data')) ?>
		<?php
		$dispCols	=	$nRender->toArray($dispCols);
		
		if(is_array($dispCols)) {
			foreach($dispCols as $column) {
				$isSel	=	(isset($dropdowns[$column]));
				$arr	=	array();
				$value	=	(!empty($values[$column]))? $values[$column] : false;
				$type	=	(isset($select[$column]['column_type']))? $select[$column]['column_type'] : 'text';
				$arr['value']	=	($type != 'password')? $value : '';
				
				$arr['value']	=	(strpos($arr['value'],'"') !== false)? $nRender->safe()->encodeSingle($arr['value']) : $arr['value'];
				
				$arr	=	(!empty($select[$column]['size']))? array_merge($arr,array('style'=>$select[$column]['size'])) : $arr;
				$sOpts	=	($isSel)? array_merge(array(array('name'=>'Select','value'=>'')),$dropdowns[$column]) : false;
				
				$arr	=	($isSel)? array_merge($arr,array('options'=>$nForm->setIsSelected($sOpts,$arr['value'],'value'))) : $arr;
				$arr	=	array_merge($arr,array('class'=>'nbr_admin_forms_'.$column));
				$arr['name']	=	$column;
			
		?>
			<<?php echo $hType; ?> class="hide_column_<?php echo $column ?> nbr_reset_all_cols">
				<?php if($hType == 'th') { ?>
				<div class="nbr_click_hide_col nbr_small_close_arrow">&#139;</div>
				<?php }
				else {
					if($type == 'file') {
						include(NBR_NAMESPACE_CORE.DS.'Nubersoft'.DS.'nForm'.DS.'imagepreview'.DS.'index.php');
					}
				}?>
				<?php echo $nForm->labelPos(false)->{$type}(array_merge(array('label'=>ucwords(str_replace('_','&nbsp;',$column))),$arr)) ?>
			</<?php echo $hType; ?>>
		<?php
			}
		?>
			<<?php echo $hType; ?>>
				<?php if($hType == 'td') { ?>
				Check&nbsp;to&nbsp;delete&nbsp;<?php echo $nForm->labelPos(false)->checkbox(array('name'=>'delete','value'=>'on')) ?>
				<?php } ?>
			</<?php echo $hType; ?>>
			<<?php echo $hType; ?>>
				<?php if(!in_array('action',$dispCols)) { ?>
				<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_edit_table_row')) ?>
				<?php } ?>
				<?php echo $nForm->fullhide(array('name'=>'requestTable','value'=>$table)) ?>
			</<?php echo $hType; ?>>
			<<?php echo $hType; ?>>
				<div class="nbr_button small">
					<?php echo $nForm->submit(array('name'=>'ADD','value'=>(($hType == 'td')? 'UPDATE': 'SAVE'))) ?>
				</div>
			</<?php echo $hType; ?>>
		<?php echo $nForm->close() ?>
<?php	
		}
		$data	=	ob_get_contents();
	}