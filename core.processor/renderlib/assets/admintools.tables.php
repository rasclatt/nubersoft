<?php if(!isset($access)) exit;
	AutoloadFunction('nQuery,organize');
	$nubquery	=	nQuery();	 ?>
<div>
	<?php
	if($dropdowns == false)
		$dropdowns	=	array();
	
	function BuildTableRows($values = array(),$columns = array(),$settings = array(),$dropdowns = array())
		{
			$nubquery	=	nQuery();	
			if(!empty($columns)) {
					foreach($columns as $column) {
							$name	=	$column;
							$type	=	(!empty($settings[$column]['column_type']))? $settings[$column]['column_type']: "text";
							$type	=	(preg_match('/^nul/i',$type) && strlen($type) <= 4)? "text": $type;
							$size	=	(!empty($settings[$column]['size']))? $settings[$column]['size']:""; ?>
			<td style=" <?php if($values !== 'head') { ?> padding: 5px;<?php } else { ?>background-color: #333; color: #FFF;<?php } ?> text-align: center;">
				<?php	if($values == 'head') { ?>
				<div style="padding: 5px 10px; font-size: 12px;white-space:nowrap;"><?php echo str_replace("_"," ",strtoupper($name)); ?></div>
					<?php	}
				if($values == 'head') { ?>
				<div style="padding: 5px; background-color: #333;"><?php }
					include(NBR_RENDER_LIB._DS_.'assets'._DS_.'form.inputs'._DS_.$type.".php");
					if($values == 'head') { ?>
				</div><?php } ?>
			</td><?php	}
				}
		}
	// Select items form builder options
	$select	=	organize($nubquery	->select(array('column_name','column_type','size','restriction'),true)
									->from("form_builder")
									->wherein("column_name",$this->columns)
									->fetch(),'column_name');
									
	// Check if this table has a custom table layout
	AutoLoadFunction('CustomTableLayout');
	$temp	=	CustomTableLayout(nApp::getGlobalArr('engine','table_name'),$this->data['results'],$this->columns,$select,$dropdowns);
	
	if(!$temp) { ?>
	<style>
	.form-builder	{
		border: 1px solid #666;
	}
	.form-builder td	{
		cursor: default;
		vertical-align: top;
	}
	tr.form-builder-head td	{
		background-color: #666;
		color: #FFF;
	}
	tr.odd-table-rows td {
		background-color: #CCC;
		background: linear-gradient(#EBEBEB,#CCC);
		border-bottom: 1px solid #888;
	}
	tr.even-table-rows td {
		background-color: #EBEBEB;
		background: linear-gradient(#EBEBEB,#CCC);
		border-bottom: 1px solid #666;
	}
	
	tr.odd-table-rows:hover td,
	tr.even-table-rows:hover td {
		background: linear-gradient(rgba(0,0,0,0),rgba(0,0,0,0));
		background-color: #FFF;
	}
	</style>
	<h2 style="padding: 10px 20px; background-color: #666; border-top-left-radius: 6px; border-top-right-radius: 6px; color: #FFF; text-shadow: 1px 1px 3px #000; display: inline-block; cursor: default;">Viewing <?php echo ucwords(str_replace("_"," ",NubeData::$settings->engine->table_name)); ?></h2>
	<table cellpadding="0" cellspacing="0" border="0" class="form-builder">
<?php	if(is_array($this->columns)) {
?>		<form method="POST" enctype="multipart/form-data">
			<input type="hidden" name="requestTable" value="<?php echo NubeData::$settings->engine->table_name; ?>" />
		<tr class="form-builder-head">
			<?php BuildTableRows('head',$this->columns,$select,$dropdowns); ?>
			<td style="background-color: #333; color: #FFF;">
			</td>
			<td style="background-color: #333; color: #FFF;">
				<div style="background-color: #333; color: #FFF; padding: 5px 10px; font-size: 12px;">FUNCTION</div>
				<div style="background-color: #333;" class="padding-5">
					<input type="hidden" name="add" value="add" />
					<div class="formButton"><input disabled="disabled" type="submit" value="ADD" /></div>
				</div>
			</td>
		</tr>
		</form>
<?php		if(isset($this->data['results']) && $this->data['results'] !== 0) {
				$dResCnt	=	count($this->data['results']);
				for($i = 0; $i < $dResCnt; $i++) {
?>
		
		<form method="POST" enctype="multipart/form-data">
			<input type="hidden" name="requestTable" value="<?php echo NubeData::$settings->engine->table_name; ?>" />
		<tr class="<?php if(($i % 2) == 0) echo "even"; else echo "odd"; ?>-table-rows">
			<?php BuildTableRows($this->data['results'][$i],$this->columns,$select,$dropdowns); ?>
			<td>
				<div style=" font-size: 10px;">DELETE?</div>
				<input type="checkbox" name="delete" />
			</td>
			<td class="padding-5">
				<input type="hidden" name="update" value="update" />
				<div class="formButton"><input disabled="disabled" type="submit" value="UPDATE" /></div>
			</td>
		</tr>
		</form>
<?php 			}
			}
		}
?>
	</table>
<?php	if($this->data['total'] == 0 && isset(nApp::getGet('search'))) { ?>0 results found for "<?php echo $this->data['search']; ?>"<?php }
	}
?>
</div>