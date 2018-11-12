<div id="main-menu" class="col-2">
	<?php
	foreach($this->getHelper("Settings\Model")->getMenu('on', 'in_menubar') as $menu):
		if($menu['is_admin'] == 3) {
			if($this->isLoggedIn())
				continue;
		}
	?>
	<div><a href="<?php echo $menu['full_path'] ?>"><?php echo $menu['menu_name'] ?></a></div>
	<?php endforeach ?>
	<?php if($this->isLoggedIn()): ?>
	<div><a href="?action=logout">Welcome <?php echo $this->user('first_name') ?>. Sign out?</a></div>
	<?php endif ?>
</div>