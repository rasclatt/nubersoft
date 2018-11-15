<div class="sidebar">
	Tables
	<div class="sidebar submenu">
		<?php foreach(array_map(function($v){ return $v['Tables_in_'.base64_decode(DB_NAME)]; }, @$this->nQuery()->query("show tables")->getResults()) as $button): ?>
		<a href="?table=<?php echo $button ?>" class="sidebar"><?php echo $this->colToTitle($button) ?></a>
		<?php endforeach ?>
	</div>
</div>