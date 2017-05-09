<?php
$data	=	$this->toArray($this->fetchData());
$class	=	'nbr_standard';
if(!empty($data['pagination']['search_class']))
	$class	=	$data['pagination']['search_class'];
 ?>
<div class="<?php echo $class ?>">
	<form action="" method="get">
		<input type="text" name="search" placeholder="Search" value="<?php echo $this->getGet('search') ?>" />
		<input type="hidden" name="requestTable" value="<?php echo $data['data']['table'] ?>" />
		<input type="submit" value="SEARCH" />
	</form>
	<?php if(!empty($this->getGet('search'))) { ?>
	<a class="nbr_reset" href="<?php echo $this->adminUrl('?requestTable='.$data['data']['table']) ?>">Reset</a>
	<?php } ?>
</div>