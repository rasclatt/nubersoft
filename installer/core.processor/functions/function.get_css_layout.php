<?php
	function get_css_layout($data = array())
		{	
			register_use(__FUNCTION__);
			AutoloadFunction('get_css_fields,organize,nQuery');
			$nubquery	=	nQuery();
			$css		=	get_css_fields();
			$form		=	organize($nubquery	->select(array("column_name","column_type","size"))
												->from("form_builder")
												->wherein("column_name",$css['raw'])
												->fetch(),"column_name");
			
			$idname		=	"comp".rand(1000000,9999999);
			$css_arr	=	array();
			
			try {
					AutoloadFunction("serialbox");
					$css_arr	=	serialbox($data,'c_options');
				} 
			catch (Exception $e) {
				}

			// Unserialize the css
			if(isset($data['c_options'])) {
					unset($data['c_options']);
				}
				
			$data		=	array_merge($css_arr,$data);
			
			ob_start(); ?>
			
		<div class="fancycompbutton" id="<?php echo $idname; ?>" onClick="PowerButton('<?php echo $idname; ?>','toggle','.fancycomppanel')">STYLE</div>
		<div id="<?php echo $idname; ?>_panel" class="fancycomppanel">
		<?php
				foreach($css['raw'] as $_inputname) {
						$_col_name	=	(isset($form[$_inputname]['column_name']))? $form[$_inputname]['column_name']: $_inputname;
						$_col_type	=	(isset($form[$_inputname]['column_type']))? $form[$_inputname]['column_type']:'text';
						$_col_size	=	(isset($form[$_inputname]['size']))? $form[$_inputname]['size']: '98%';
						
						// Determine which class to display
						if($_col_type == 'select')
							$class		=	"formElementSelectLeft";
						elseif($_col_type == 'textarea')
							$class		=	"formElementTextarea";
						else
							$class		=	"fieldsGradLeft"; ?>
				
				<div style="display: inline-block; background-color: inherit; width: 100%;"><?php
				if($_col_type == 'textarea' && isset($data['ID'])) { ?>
	
					<div onClick="ScreenPop(); AjaxFlex('#servResponse','/core.processor/renderlib/component.window.php?ID=<?php if(isset($data['ID'])) echo $data['ID']; ?>&unique_id=<?php if(isset($data['unique_id'])) echo $data['unique_id']; ?>&table=components&isolate=true')">
						<table class="base_rollover">
							<tr>
								<td><div class="WYSIWYG_button"></div></td>
								<td style="color: #FFFFFF; vertical-align: middle;">WYSIWYG</td>
							</tr>
					   </table>
					</div>
				<?php } ?>
					<div class="variableType"><?php echo ucwords(str_replace("_", " ", $_col_name)); ?></div>
					<div class="<?php echo $class ?>" style="float: left; text-align: left; clear: left;"><?php
					$_info		=	(isset($data[$_col_name]))? $data[$_col_name]: '';
					$_default	=	(isset($data))? $data: false;
					if($_col_type == 'select') {
							$name		=	$_col_name;
							include(NBR_RENDER_LIB.'/assets/form.inputs/select.php');
	
							if(isset($options))
								unset($options);
						}
					else {
							$formsettings["type"]		=	$_col_type;
							$formsettings["value"]		=	$_info;
							$formsettings["name"]		=	"c_options[".$_col_name."]";
							$formsettings["payload"]	=	$_default;
							$formsettings["size"]		=	$_col_size;
							
							FormElementSelector::Build($formsettings);
						}  ?>
					</div>
				</div>
				
			<?php	} ?>
		</div><?php
			
			$contents	=	ob_get_contents();
			ob_end_clean();
			
			return $contents;
		}
?>