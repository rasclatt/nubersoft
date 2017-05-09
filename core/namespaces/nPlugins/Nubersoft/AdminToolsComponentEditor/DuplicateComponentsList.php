<?php
if(!$this->isAdmin())
	return;

$name	=	strip_tags($this->safe()->decode($values['ref_anchor']));
$key	=	$values['component_type'];
if(empty($key))
	$key	=	"unknown";
?>
	<form action="<?php echo $this->getDataNode('_SERVER')->HTTP_REFERER; ?>" method="post" class="nForm" data-instructions='{"action":"nbr_change_component_name"}'>
		<input type="hidden" name="table" value="<?php echo $this->getTable(); ?>" />
		<input type="hidden" name="ref_page" value="<?php echo $this->getDefPageIdVal('ref_page',$this->getPost()) ?>" />
		<?php
		if($values['component_type'] == 'div' || $values['component_type'] == 'container') { ?>
		<input type="hidden" name="parent_id" value="<?php echo $values['unique_id'] ?>" />
		<?php } ?>
		<input type="hidden" name="action" value="nbr_insert_dup_component" />
		<input type="hidden" name="ID" value="<?php echo (isset($values['ID']))? $values['ID']:""; ?>" />
		<?php
		foreach($values as $cols => $vals) {
			if(!empty($vals) && $cols != 'ID' && $cols != 'unique_id' && $cols != 'parent_id') {
				$settings[]	=	'
				<tr>
					<td style="padding: 5px; background: linear-gradient(#CCC,#888); color: #FFF;">'.ucwords(str_replace("_"," ",$cols)).'</td>
					<td style="padding: 5px; background: linear-gradient(#888,#555); color: #FFF; max-width: 240px; overflow: hidden;">'.$vals.'</td>
				</tr>';
			}
		} ?>             
		<div class="dup_guts_container" id="dup-container-ajax_<?php echo $values['ID']; ?>">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="width: 30px;">
						<img src="/media/images/core/<?php echo $icon_arr[$key]; ?>" style="width: 20px;" id="prev_<?php echo $values['ID']; ?>" />
					</td>
					<td class="nbr_dup_comp_title">
						<input type="text" name="dup_comp_name" class="nKeyUp" value="<?php echo $this->safe()->encodeSingle($name); ?>" />
					</td>
					<td style="width: 40px;">
						<div class="formButton nbr_dup_btn add">
							<input id="controller<?php echo $values['ID']; ?>" type="submit" name="add" value="+" />
						</div>
					</td>
					<td>
						<div class="formButton nbr_dup_btn remove">
							<input type="submit" name="delete" value="&ndash;" />
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>