<?php 
if(!function_exists("AutoloadFunction")) return;
AutoloadFunction("check_empty");

if(isset($SearchEngine->data) && $SearchEngine->data->count > 0) {
?>
	<ul id="nbr_pagination_limit">
<?php
			$and		=	(!empty($SearchEngine->data->query))? "&":"";
			$num		=	(isset($SearchEngine->data->current))? $SearchEngine->data->current:0;
			$search		=	(!empty($SearchEngine->data->search))? "&search=".$SearchEngine->data->search: ""; 
			foreach($maxlimits as $maxcounts) {
					$is_selected	=	check_empty($_GET,'max',$maxcounts);
					$maxSelect		=	(isset($SearchEngine->data->limit) && $SearchEngine->data->limit == $maxcounts)? 'max-limit-select':'max-limit';
?>
			<li>
<?php				if(!$is_selected) { ?>
				<a href="?currentpage=<?php echo $num.$and.'requestTable='.$SearchEngine->data->table.$search.'&max='.$maxcounts; ?>"><?php }echo $maxcounts; if(!$is_selected) { ?></a><?php } ?>
				
			</li>
<?php		} ?>
	</ul>
<?php
	}
?>
</div>