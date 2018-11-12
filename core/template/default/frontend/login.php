<!doctype html>
<html>
<?php echo $this->getFrontEnd('head.php') ?>
<body class="<?php if($this->getSession('editor')) echo 'edit-mode' ?>">
<?php echo $this->getPlugin('adminbar') ?>
<div class="col-count-3 offset content">
	<?php echo $this->getPlugin('editor', DS.'page_editor.php') ?>
	<?php echo $this->getFrontEnd('menu.php') ?>
	<?php echo $this->getPlugin('notifications') ?>
	<div class="col-2">
		<div<?php if(!$this->siteLogoActive()): ?> class="col-count-3"<?php else: ?> style="padding: 0 2em 2em 2em;"<?php endif ?>>
			<a href="<?php echo $this->localeUrl() ?>" id="admin-logo" class="col-2"><?php echo $this->getSiteLogo(false, true) ?></a>
		</div>
		<?php echo $this->getPlugin('login') ?>
	</div>
</div>
<?php if(!$this->getSession('editor')) : ?>
<div class="content">
	<?php echo $this->getFooter() ?>
	<?php echo $this->getFrontEnd('foot.php') ?>
</div>
<?php endif ?>
</body>
</html>