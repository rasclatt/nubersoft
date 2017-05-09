<?php
use Nubersoft\nApp as nApp;

if(!isset($this->inputArray[0]))
	return;
	
$nApp	=	nApp::call();
// Sort the drop downs by directory name
$checkRows	=	$nApp->organizeByKey($checkRows,"full_path",array('unset'=>false));
ksort($checkRows,SORT_NATURAL);
?>
				<select name="parent_id">
					<?php if(!preg_match('/select parent direc/i',$default_disp)) { ?>
					<option value="<?php echo $default_container; ?>"><?php echo substr($default_disp,0,30); ?></option>
					<?php } ?>
					<option value="">No Parent Directory</option>
					<?php
				foreach($checkRows as $options) {
						if(isset($options['unique_id']) || ((isset($options['unique_id']) && isset($payload['unique_id'])) && $options['unique_id'] !== $payload['unique_id']) || ((isset($options['parent_id']) && isset($payload['parent_id'])) && $options['parent_id'] !== $payload['parent_id'])) {
								// If no name is in content field, use unique id
								$content 	=	(!empty($options[$this->displayCol]))?	$options[$this->displayCol]: $options['unique_id']; ?>
					<option value="<?php echo $options['unique_id']; ?>"><?php echo substr($nApp->getHelper('Safe')->decode($content), 0, 30); ?></option>
					<?php	}
					} ?>
				</select>