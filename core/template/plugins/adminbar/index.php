<?php
if(!$this->isAdmin())
	return false;

$editActive	=	(!empty($this->getSession('editor')))? "off" : "on";
$adminPage	=	($this->getPage('is_admin') == 1)? '/' : $this->getAdminPage('full_path').'?action=set_edit_mode&active=off';
$title		=	($this->getPage('is_admin') == 1)? "icn_home" : "gear";
$livetitle	=	($editActive == 'off')? "View Mode" : "Edit Mode";
$icn		=	($editActive == 'off')? 'view' : 'edit';
$imgpath	=	'/core/template/default/media/images';
$iconlib	=	[
	'' => $docicon = $this->localeUrl($imgpath."/core/icn_doc.png"),
	'1' => $this->localeUrl($imgpath."/core/gear.png"),
	'2' => $this->localeUrl($imgpath."/core/icn_home.png"),
	'3' => $docicon
];
?>
<nav id="admin-menubar">
	<div>
		<a href="<?php echo $this->localeUrl($adminPage) ?>"><img src="<?php echo $this->localeUrl($imgpath."/core/{$title}.png") ?>" style="max-height: 25px; width: auto; margin: 0;" /></a>
		<?php if($this->getPage('is_admin') != 1):?>
		<a href="<?php echo $this->localeUrl($this->getPage('full_path')."?action=set_edit_mode&active=".$editActive) ?>"><img src="<?php echo $this->localeUrl($imgpath."/core/icn_{$icn}.png") ?>" style="max-height: 25px; width: auto;" /></a>
		<?php endif ?>
		<div class="admin-menu">
			<img src="<?php echo $docicon ?>" style="max-height: 25px; width: auto;" />
			<div class="admin-submenu">
				<?php
				foreach($this->getHelper('Settings\Model')->getMenu() as $menu): ?>
				
				<a href="<?php echo $this->localeUrl($menu['full_path']) ?>" class="col-count-7 left-align">
					<img src="<?php echo (!empty($iconlib[$menu['is_admin']]))? $iconlib[$menu['is_admin']] : $docicon ?>" style="max-height: 13px; width: auto;" class="col-1 span-1" />
					<span class="span-6 pointer">&nbsp;<?php echo str_replace(' ', '&nbsp;', $menu['menu_name']) ?></span>
				</a>
				
				<?php endforeach ?>
				<div style="padding: 0.5em;">
					<?php
					$Form	=	@$this->nForm();
					echo $Form->open() ?>
					<?php echo $Form->fullhide(['name'=>'token[nProcessor]', 'value'=>'']) ?>
					<?php echo $Form->fullhide(['name'=>'action', 'value'=>'create_new_page']) ?>
					<?php echo $Form->submit(['value'=>'+PAGE','class'=>'medi-btn green']) ?>
					<?php echo $Form->close() ?>
				</div>
			</div>
		</div>
		<?php if(is_dir(NBR_CLIENT_CACHE) && count(scandir(NBR_CLIENT_CACHE)) > 2): ?>
		<div class="divider vertical light"></div>
		<a href="?action=clear_cache"><img src="<?php echo $this->localeUrl($imgpath."/buttons/deleteCache.png") ?>" style="max-height: 25px; width: auto;" />&nbsp;Clear Cache</a>
		<?php endif ?>
		<div class="divider vertical light"></div>
		<a href="?action=logout">Sign Out</a>
	</div>
</nav>