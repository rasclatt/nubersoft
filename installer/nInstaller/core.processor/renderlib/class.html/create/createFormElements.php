<?php
if(!isset($this->inputArray))
	return;

AutoloadFunction('FetchUniqueId');
// Break down the field into keys and values to separate out the Field row
foreach($includeArray as $fieldKeys => $fieldVals) {
	$randomizer		=	(!empty($this->inputArray[0]['unique_id']))? $this->inputArray[0]['unique_id']: FetchUniqueId();
	$keyMinus		=	(int) 1;
	@$prevSection	=	$sectionBreak[$fieldKeys-$keyMinus];
	$matched		=	($sectionBreak[$fieldKeys] == $prevSection);


	if(!$matched) {
		$final_filtered	=	preg_replace('/[^0-9a-zA-Z]/',"",$sectionBreak[$fieldKeys]);
		$useid			=	$final_filtered.$randomizer;	

		if($fieldKeys != 0) {
?>	</div>
<?php 	} ?>
    <div class="toolsheaders nbrAccordion"><?php echo ucwords($final_filtered); ?></div>
    <div class="nbr_tools_headers_panels"><?php
	}

		$values				=	(!empty($this->inputArray[0][$fieldVals]))? $this->inputArray[0][$fieldVals]: '';
		$setInputRes		=	(isset($_cols[$fieldVals][0]))? $_cols[$fieldVals][0]:'text';
		$size				=	(isset($setInputRes['size']) && !empty($setInputRes['size']))? $setInputRes['size']: '96%';
		$inputType			=	(isset($setInputRes['column_type']) && !empty($setInputRes))? $setInputRes['column_type']: 'text';
		
		// Determine which class to display
		switch($inputType) {
			case ('select'):
				$class	=	"formElementSelectLeft";
				break;
			case ('textarea'):
				$class	=	"formElementTextarea";
				break;
			default:
				$class	=	"fieldsGradLeft";
		}
?>	<div style="display: inline-block; background-color: transparent; width: 100%;; padding: 0;">
		<div style="padding: 10px;">
			<div class="variableType"><?php  echo ucwords(str_replace("_", " ", Safe::decode($fieldVals))); $this->helpdesk($this->table, $fieldVals); ?></div>
			<?php
			// Allow for wysiwyg editor without having to reinitiate the tinyMCE interface
			// Disable Array
			$disabled	=	array('row','div','image','button');
			if($inputType == 'textarea' && !in_array($this->inputArray[0]['component_type'], $disabled)) { ?>
	
			<div onClick="ScreenPop(); onthefly('ID=<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>&unique_id=<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>&isolate=true','/core.ajax/admintools.component.php', 'POST')">
				<table class="base_rollover">
					<tr>
						<td><div class="WYSIWYG_button"></div></td>
						<td style="color: #FFFFFF; vertical-align: middle;">WYSIWYG</td>
					</tr>
			   </table>
			</div>
			<?php } ?>
			<div class="<?php echo $class ?>">
				<div class="comp-set-attr">
				<?php 
					$payload	=	(isset($this->inputArray[0]))? $this->inputArray[0]:array();
					// Add form inputs
					// Settings: payload array, field name, size, type of input, droptions, query
					Input($payload, $fieldVals, $size,$inputType, $this->dropdowns);
				 ?>
				 </div>
			</div>
		</div>
	</div>
<?php		}
?></div>