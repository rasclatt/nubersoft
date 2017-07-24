<?php
$dirs	=	$this->getDirList(array('dir'=>NBR_CLIENT_DIR.DS.'template'.DS,'recursive'=>false));
$dirs['dirs']	=	array_filter($dirs['dirs']);
?>
<label><div>Primary Template</div>
	<select name="content[template_folder]">
		<option value="<?php echo $basedir = $this->safe()->encode(str_replace(NBR_ROOT_DIR,"",NBR_TEMPLATE_DIR."/default")); ?>" <?php if(!empty($settings['template_folder']) && $settings['template_folder'] == $basedir) echo " selected"; ?>><?php echo ucfirst(basename($basedir)); ?></option>
		<?php
		
		if(!empty($dirs['dirs'])) {
			foreach($dirs['dirs'] as $folder) {
				$trimmed	=	rtrim($folder,"/");
				$basename	=	basename($folder);
				
				if(strtolower($basename) == 'plugins')
					continue;
		?>
		<option value="<?php echo $thisDir = $this->safe()->encode(str_replace(NBR_ROOT_DIR,"",$trimmed)); ?>"<?php if(!empty($settings['template_folder']) && $settings['template_folder'] == $thisDir) echo " selected"; ?>><?php echo ucfirst(str_replace('_',' ',$basename)) ?></option>
		<?php
			}
		}
		?>
	</select>
</label>