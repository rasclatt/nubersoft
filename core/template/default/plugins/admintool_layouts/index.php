<?php
$this->autoload('admin_row_render',__DIR__.DS.'functions'.DS);
$data		=	$this->fetchData();
$nForm		=	$this->getHelper('nForm');
$nubquery	=	$this->nQuery();
$nProcToken	=	$this->getHelper('nToken')->setMultiToken('nProcessor','pagination');
$table		=	(!empty($this->getGet('requestTable')))? $this->getGet('requestTable') : $this->getTableName();
$dropdowns	=	$this->toArray($this->getDropDowns($table));
// Select items form builder options
$get_data	=	$nubquery	->select("`column_name`,`column_type`,`size`,`restriction`")
							->from("form_builder")
							->wherein("column_name",$this->toArray($data->columns))
							->fetch();
$select		=	$this->organizeByKey($get_data,'column_name',array('unset'=>false));
$pageData	=	$this->toArray($data->data);
$countCols	=	count($this->toArray($nubquery->query('describe '.$this->getTableName())->getResults()));
$results	=	$pageData['results'];
$dispCols	=	$this->toArray($data->columns);
$getCount	=	$this->nQuery()->query("select COUNT(*) as count from `{$table}`")->getResults(true);
$count		=	($getCount !== 0)? $getCount['count'] : 0;
if($count > 1000)
	$count	=	number_format(($count/1000),2).'K';

ksort($select);

# Filter usergroups menus
if(!empty($dropdowns['usergroup'])) {
	
	$myUGroup	=	$this->getCurrentGroup(false);
	
	foreach($dropdowns['usergroup'] as $dKey => $arr) {
		
		$uGroup	=	$this->convertUserGroup($arr['value']);
		
		if($myUGroup > $uGroup)
			$dropdowns['usergroup'][$dKey]['disabled']	=	true;
	}
}
?>
<?php echo $this->getHelper('nImage')->image(NBR_MEDIA_IMAGES.DS.'ui'.DS.'loader.gif',array('style'=>'max-height: 60px;','class'=>'nbr_load_image')) ?>
<?php if($this->tableExists($table)) { ?>
<span id="nbr_table_hide_on_load" style="display: none;">
	<table cellpadding="0" cellspacing="0" border="0" class="nbr_general_admin_table">
		<tr>
			<td colspan="<?php echo ($countCols+2) ?>" style="background: linear-gradient(#EBEBEB,#888); border-bottom: 2px solid #666;">
				<div style="text-align: left;">
					<h1 style="margin: 10px; text-shadow: 1px 1px 3px #FFF;">Editing Table: <?php echo ucwords(str_replace('_',' ',$table)) ?> (<?php echo $count ?>)</h1>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="<?php echo ($countCols+2) ?>" style="background-color: #333;">
				<div id="nbr_reset_prefs" style="display: inline-block;">
					<a href="#" class="nbr_button small coolset">Reset Table View</a>
				</div>
				<div id="nbr_reset_table_data" style="display: inline-block;">
					<form action="<?php echo $this->adminUrl() ?>/?requestTable=<?php echo $table ?>" method="post">
						<input type="hidden" name="requestTable" value="<?php echo $table ?>" />
						<input type="hidden" name="action" value="nbr_reset_table_data" />
						<input type="checkbox" class="nChange" data-instructions='{"FX":{"fx":["disableToggle"],"acton":["#nbr_reset_table_data_input"],"cancel":["click"]}}' />
						<input type="submit" value="RESET" id="nbr_reset_table_data_input" disabled />
					</form>
				</div>
				<div id="nbr_table_search_bar">
					<div class="nbr_table_search_pagination">
						<ul>
							<li>
								<form action="" method="get">
									<input type="search" name="search" placeholder="Search" value="<?php echo $this->getGet('search') ?>" />
									<input type="hidden" name="requestTable" value="<?php echo $table ?>" />
									<input type="submit" value="SEARCH" />
								</form>
							</li>
							<li style="padding: 5px 5px 0 0;">
								<?php echo $this->useTemplatePlugin('admin_pagination','pagination.php') ?>
							</li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
		<tr class="nbr_general_admin_row">
			<?php echo admin_row_render($this, $nForm,$table,'th',$dispCols,$select,$dropdowns) ?>
		</tr>
		<?php
		if(empty($results)) {
		?>
			<tr class="nbr_general_admin_row">
				<td colspan="<?php echo ($countCols+2) ?>">
					No results
				</td>
			</tr>
		<?php
		}
		else {
			$i = 0;
			foreach($results as $rows) {
				$classModulus	=	($i%2 == 0)? 'even' : 'odd';
		?>
		<tr class="nbr_general_admin_row <?php echo $classModulus ?>">
			<?php echo admin_row_render($this, $nForm,$table,'td',$dispCols,$select,$dropdowns,$rows) ?>
		</tr>
		<?php
				$i++;
			}
		}
		?>
	</table>
	<?php
	
	}
	?>
</span>
<script>

function createInput(newForm,name,value,type)
	{
		if(empty(type))
			type	=	'text';
		var	input	=	document.createElement('INPUT');
		input.setAttribute('type',type);
		input.setAttribute('name',name);
		input.setAttribute('value',value);
		newForm.appendChild(input);
		
		return newForm;
	}

$(document).ready(function() {
	$('input[name=ADD]').on('click',function(e) {
		var newForm		=	document.createElement('FORM');
		var getDeleted	=	$('input[name=delete]');
		var cnt			=	0;
		
		newForm.method	=	'post';
		newForm.action	=	'<?php echo $this->adminUrl('/?requestTable='.$this->getGet('requestTable')) ?>';
		newForm			=	createInput(newForm,'action','nbr_edit_table_row');
		newForm			=	createInput(newForm,'requestTable','<?php echo $this->getGet('requestTable') ?>');
		$.each(getDeleted,function(k,v) {
			if($(v).is(":checked")) {
				newForm		=	createInput(newForm,'ID[]',$($(v).parents('tr').find('input[name=ID]')).val());
				cnt+=1;
			}
		});
	
		newForm	=	createInput(newForm,'delete','on');
		newForm	=	createInput(newForm,'ADD','UPDATE','submit');
		
		if(cnt > 1) {
			e.preventDefault();
			document.body.appendChild(newForm);
			newForm.submit();
		}
	});
	
	
	var	colViewName		=	'column_views_<?php echo $table ?>';
	var tableRestBtn	=	$('#nbr_reset_prefs');
	$(this).on('click','#nbr_reset_prefs',function() {
		tableRestBtn	=	$(this);
		AjaxEngine.ajax({
			'action': 'nbr_save_interface_pref',
			'data':{
				'name': colViewName,
				'action': 'remove'
			}
		},
		function(response) {
			try{
				var getJson	=	JSON.parse(response);
				if(isset(getJson,'remove')) {
					if(getJson.remove == "true" || getJson.remove == true)
						$('.nbr_reset_all_cols').fadeIn('fast').css({"display":"table-cell",'overflow':'normal'});
				}
				tableRestBtn.css({"background-color":"transparent"});
			}
			catch(Exception) {
				console.log(response);
			}
		});
	});
	
	AjaxEngine.ajax({
		'action': 'nbr_get_interface_pref',
		'data':{
			'name': colViewName,
			'action': 'get'
		}
	},
	function(response) {
		console.log(response);
		try {
			var parseJson	=	JSON.parse(response);
			if(isset(parseJson,'class')) {
				$.each(parseJson.class,function(k,v) {
					$(v).hide();
				});
				tableRestBtn.css({"background-color":"red"});
			}
				
			$('.nbr_load_image').css({"display":"none"});
			$('#nbr_table_hide_on_load').slideDown('fast');
		}
		catch(Exception) {
			console.log(response);
		}
	});
	
	$(this).on('click','.nbr_click_hide_col',function(e) {
		var getCol	=	'.'+$(this).parents('th').attr('class');
		var	mainCol	=	getCol.split(' ');
		
		$(mainCol[0]).css({"display":"block",'overflow':'hidden'}).animate({ width: '0' }, 500,function() {
			$(this).hide();
		});
		//$(mainCol[0]).hide();
		AjaxEngine.ajax({
			'action':'nbr_save_interface_pref',
			'data':{
				'name': colViewName,
				'action': 'edit',
				'push': 'class',
				'store': {
					'class': mainCol[0],
					'table': '<?php echo $table ?>'
				}
			}
		},function(response){
			var	getReset	=	$('#nbr_reset_prefs');
			if(!getReset.is(":visible"))
				tableRestBtn.css({"background-color":"red"});
		});
	});
});
</script>