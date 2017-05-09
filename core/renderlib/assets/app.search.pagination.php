<?php
if(!function_exists("AutoloadFunction"))
	exit;
?>
<ul class="nbr_pagination">
	<?php
	$searchApp['data']	=	Safe::to_array($SearchEngine->data);
	
	if(isset($searchApp['data'])) {
			
			$and		=	(!empty($searchApp['data']['query']))? "&":"";
?>	
<?php		if(isset($searchApp['data']['range']) && is_array($searchApp['data']['range'])) { 
					if(!in_array(1,$searchApp['data']['range'])) { ?>
		<li><?php echo '<a href="?currentpage=1'.$searchApp['data']['last_link'].'"><</a>'; ?></li>
<?php					}
					// Loop through pagination
					foreach($searchApp['data']['range'] as $num) {
							$and	=	(!empty($searchApp['data']['query']))? "&":""; ?>
		<li><?php echo ($searchApp['data']['current'] == $num)? '<span class="pagination-curr">'.$num.'</span>': '<a href="?currentpage='.$num.$and.$searchApp['data']['query'].'">'.$num.'</a>'; ?></li>
<?php					}

					if(!in_array($searchApp['data']['last'],$searchApp['data']['range'])) {
?>		<li><?php echo '<a href="?currentpage='.$searchApp['data']['last'].$searchApp['data']['last_link'].'">></a>'; ?></li>
<?php 					}
				}
		}
?>
	</ul>