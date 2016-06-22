<?php if(!isset($access)) exit; ?>

<style>
#admin-tools-pagination a:link,
#admin-tools-pagination a:visited,
#admin-tools-pagination span.pagination-curr	{
	padding: 6px 5px;
	width: 20px;
	font-size: 14px;
	text-decoration: none;
	display: inline-block;
	text-align: center;
	margin: 5px 2px;
	border-radius: 4px;
	border: 1px solid;
}
#admin-tools-pagination a:link,
#admin-tools-pagination a:visited	{
	color: #FFF;
	background-color: #333;
	border-color: #333;
}
span.pagination-curr	{
	background-color: #EBEBEB;
	color: #888;
	border-color: #888;
	cursor: default;
}
#admin-tools-pagination a:hover	{
	background-color: #888;
}

</style>
<div>
	<?php
	if(isset($this->data)) {
			AutoloadFunction('create_query_string'); ?>
	<table cellpadding="0" cellspacing="0" border="0" id="admin-tools-pagination">
		<?php
			if(is_array($this->data['range'])) { ?>
		<tr>
			<td>
				<span style="font-size: 12px; float: left;">PAGES:&nbsp;</span>
			</td>
		<?php
		foreach($this->data['range'] as $num) {
			$and	=	(!empty($this->data['query']))? "&":""; ?>
			<td><?php echo ($this->data['current'] == $num)? '<span class="pagination-curr">'.$num.'</span>': '<a href="?currentpage='.$num.$and.$this->data['query'].'">'.$num.'</a>' ?></td>
			<?php	} ?>
			<td>
				<?php echo (isset($this->data['last']) && $this->data['last'] != $this->data['current'])? '<a href="?currentpage='.$this->data['last'].$this->data['last_link'].'">></a>':""; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
<?php	} ?>
</div>