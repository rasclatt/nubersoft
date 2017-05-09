<?php
if(!$this->isAdmin())
	return;

$nImage	=	$this->getHelper('nImage');
?>
<div class="head-button nTrigger" data-instructions='{"FX":{"fx":["slideToggle"],"acton":["next::slideToggle"],"fxspeed":["fast"]}}'>
	<ul>
		<li><?php echo $nImage->imageBase64(__DIR__.DS.'images'.DS.'mysql.png',array('style'=>'max-height: 40px;')) ?></li>
		<li>SQL Direct</li>
	</ul>
</div>
<div class="hideall nbr_admintools_section_block fullwidth"<?php if(!empty($this->getPost('sql'))) echo ' style="display: block;"' ?>>
	<form method="post" action="<?php echo $this->getDataNode('_SERVER')->REQUEST_URI ?>">
		<input type="hidden" name="action" value="nbr_run_raw_query" />
		<textarea name="sql" class="nbr_code textarea"><?php echo $this->getPost('sql') ?></textarea>
		<ul>
			<li>
			<select name="type">
				<option value="write">Write</option>
				<option value="select"<?php if($this->getPost('type') == 'select') echo ' selected="selected"' ?> >Select</option>
			</select>
			</li>
			<li>
				<div class="nbr_button small">
					<input type="submit" value="RUN" />
				</div>
			</li>
		</ul>		
	</form>
	<?php
	$getResults	=	$this->getDataNode('core_database_raw_query');
	if(!empty($getResults))
		echo printpre($getResults,array('backtrace'=>false));
	?>
</div>
</div>
<script>
$('#sql_master_errors').delay(1000).fadeIn('fast');
</script>