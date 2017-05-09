<ul>
<?php
$getData	=	$this->toArray($this->fetchData());
$data		=	$getData['data'];
$range		=	$data['range'];
$curr		=	$data['current'];
if(is_array($range) && !empty($range)) {
	if($curr != 1 && $data['count'] > 0) {
?>
	<li><a href="?<?php echo $data['query'] ?>&current=1" class="nbr_table_pagination_num">&lt;&lt;</a></li>
<?php
	}
	
	if($curr != 1) { ?>
	<li><a href="?<?php echo $data['query'] ?>&current=<?php echo $data['previous'] ?>" class="nbr_table_pagination_num">&lt;</a></li>
	<?php
	}
	
	foreach($range as $number) {
		if($curr == $number) {
?>
		<li><div class="nbr_table_pagination_num"><?php echo $number ?></div></li>
<?php
		}
		else {
?>	
		<li><a href="?<?php echo $data['query'] ?>&current=<?php echo $number ?>" class="nbr_table_pagination_num"><?php echo $number ?></a></li>
<?php
		}
	}
	
	if(!empty($data['next'])) { ?>
	<li><a href="?<?php echo $data['query'] ?>&current=<?php echo $data['next'] ?>" class="nbr_table_pagination_num">&gt;</a></li>
	<?php
	}
	
	if(!empty($data['last'])) {
		if($data['last'] != $curr) {
?>
	<li><a href="?<?php echo $data['query'] ?>&current=<?php echo $data['last'] ?>" class="nbr_table_pagination_num">&gt;&gt;</a></li>
<?php
		}
	}
}

foreach($data['max_range'] as $val) {
?>
	<li><?php if($val != $data['limit']) { ?><a href="?requestTable=<?php echo $data['table'] ?>&max=<?php echo $val ?>" class="nbr_table_pagination_max"><?php echo $val ?></a><?php } else { ?><div class="nbr_table_pagination_max" style="background-color: #000; cursor: default;"><?php echo $val ?></div><?php } ?></li>
<?php
}
?>
</ul>
<?php //echo printpre($this->fetchData()->data);