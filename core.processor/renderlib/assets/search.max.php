<?php if(!isset($access)) exit;
	AutoloadFunction('nQuery');
	$nubquery	=	nQuery();	
?>

<style>
#admin-tools-max a:link,
#admin-tools-max a:visited {
	text-decoration: none;
	padding: 5px 10px;
}
td.max-limit-select a:link,
td.max-limit-select a:visited,
td.max-limit a:link,
td.max-limit a:visited	{
	background-color: #06F;
	color: #FFF;
	text-shadow: 1px 1px 2px #000;
	border-radius: 4px;
}
td.max-limit-select	{
	padding: 5px;
}
td.max-limit a:link,
td.max-limit a:visited	{
	background-color: transparent;
	color: #888;
	text-shadow: none;
	text-decoration: none;
	padding: 5px 10px;
}
</style>
<div>
	<?php 
	if(isset($this->data)) { ?>
	<table cellpadding="0" cellspacing="0" border="0" id="admin-tools-max">
		<tr>
			<td>
				<span style="font-size: 12px; float: left;">PER PAGE:&nbsp;</span>
			</td>
			<?php
			$and		=	(!empty($this->data['query']))? "&":"";
			$num		=	$this->data['current'];
			
			foreach($maxlimits as $maxcounts) {
					$maxSelect	=	(isset($_REQUEST['max']) && $_REQUEST['max'] == $maxcounts)? 'max-limit-select':'max-limit'; ?>
			<td class="<?php echo $maxSelect; ?>"><?php echo '<a href="?currentpage='.$num.$and.'requestTable='.NubeData::$settings->engine->table_name.'&max='.$maxcounts.'">'.$maxcounts.'</a>'; ?></td>
			<?php } ?>
		</tr>
	</table>
<?php	} ?>
</div>