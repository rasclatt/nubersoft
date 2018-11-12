<?php
$hasFile	=	(!empty($compData['file_path']));
$hasIcon	=	(is_file($icon = NBR_CLIENT_DIR.DS.'media'.DS.'images'.DS.'components'.DS.'icons'.DS.$compData['component_type'].'.png'));
$activated	=	($compData['page_live'] == 'on')? 'on' : 'off';
$page_live	=	'/core/template/default/media/images/core/led_'.$activated.'.png';
?>
		<div class="component-editor-toggle">
			<?php if($hasFile || $hasIcon): ?>
			
			<div style="z-index: -1; <?php if(!empty($compData['file_path'])): ?>background-image: url('<?php echo $this->getThumbnail($compData['file_path'], $compData['file_name']) ?>'); <?php endif ?>" class="nTrigger pointer opacity-button" data-instructions='{"action":"nbr_load_component","data":{"deliver":{"ID":"<?php echo $ID ?>"}}}'>
				<div style="position: relative; top: 0; left: 0; right: 0; bottom: 0;">
					<img src="<?php echo $page_live ?>" class="active-status" style="position: absolute; top: 0; left: 0;" />
					<?php if($hasIcon): ?>
					<img src="<?php echo str_replace(NBR_ROOT_DIR,'',$icon) ?>" style="z-index: -1; height: auto; margin: 1.5em auto 0 auto; width: auto; max-width: 60px;<?php echo (!empty($compData['file_path']))? " opacity: 0.6;" : "" ?>" />
					<?php endif ?>
				</div>
			</div>
			<?php else: ?>
			<div class="img-empty">
				<div style="position: relative; top: 0; left: 0; right: 0; bottom: 0;">
					<img src="<?php echo $page_live ?>" class="active-status" style="position: absolute; top: 0;" />
				</div>
			</div>
			<?php endif ?>
			<div class="editor-title">
				<?php
				$icon_kind	=	NBR_ROOT_DIR.'/core/template/default/media/images/core/icn_'.$compData['component_type'].'.png';
				if(is_file($icon_kind))
					echo '<img src="'.$this->localeUrl(str_replace(NBR_ROOT_DIR, '', $icon_kind)).'" style="margin-bottom: 5px; max-height: 30px; width: auto;" />';
				?>
				<div style="position: relative; top: -15px; display: inline; color: #777;"><?php echo $this->colToTitle($title) ?></div>
			</div>
			<div class="col-count-3 offset">
				<a href="#" class="span-3 nbr button small nTrigger component-element no-radius" data-instructions='{"action":"nbr_load_component","data":{"deliver":{"ID":"<?php echo $ID ?>"}}}'>EDIT</a>
			</div>
			<div id="editorid-<?php echo $ID ?>" class="component-editor dragonit"></div>
		</div>
