<?php
use Nubersoft\nApp as nApp;
use Nubersoft\Safe as Safe;
?>
                <form enctype="multipart/form-data" action="#" method="POST">
                <tr>
            <?php
					// Generate the forms and the filler text
					foreach($result as $keys => $values) {
							$echoFormEl	=	$this->verify($keys);
							

							if($echoFormEl == true) {
									$table			=	$this->requestTable;
									
									if(isset($restrictions[$table]['assoc_column']) && $restrictions[$table]['assoc_column'] == $keys && $restrictions[$table]['assoc_value'] == $values)
										$echo_it	=	($restrictions[$table]['user_access'] == 1)? true: false;
									else
										$echo_it	=	true;
?>
                	<td class="adtoolsTblguts">
<?php								//if( $odd = $i%2 ) echo 'FFFFFF'; else  echo 'EBEBEB';
									if($echo_it) {
?>
                    	<div class="fieldsGrad" style="margin: 0 8px;">
						<?php	// Grab formatting from the form_builder DB
								$keys	=	Safe::encode($keys);
								if(!isset($this->_fieldType[$keys])) {
										$setInputQuery	=	$this->nubsql->getResults("select * from form_builder where column_name = '" . $keys . "'");
										$setInputRes	=	$setInputQuery[0];
										$size			=	(isset($setInputRes['size']) && !empty($setInputRes['size']))? $setInputRes['size']: '98%';
										$inputType		=	(isset($setInputRes['column_type']) && !empty($setInputRes))? $setInputRes['column_type']: 'text';
										
										$this->_fieldType[$keys]['size']	=	$size;
										$this->_fieldType[$keys]['type']	=	$inputType;
									}
									
								// Render all the form inputs
								formInputs::Compile($this->_fieldType[$keys]['type'],$result,$keys,$values,$this->_fieldType[$keys]['size'],$this->requestTable); ?>
                            </div><?php		} ?>
                    </td>
					<?php		}
						} // END WHILE ?>
                <td class="adtoolsTblguts">
				<?php if(isset($this->check_perms) && ($this->check_perms->allow_update == true && $this->check_perms->allow_delete == true)) :
						$input_kind	=	'';
						$value_kind	=	'Delete?';
					else :
						$input_kind	=	'disabled';
						$value_kind	=	'Delete?<br /><span style="font-size:10px;color: orange; font-style:italic;">DISABLED</span>';
					endif; ?>
                	<div class="fieldsWht"><span style="font-size:13px;">&nbsp;&nbsp;&nbsp;<?php echo $value_kind; ?></span><input <?php echo $input_kind; ?> type="checkbox" name="delete" />&nbsp;</div>
                </td>
                <td class="adtoolsTblguts">
                <input type="hidden" name="requestTable" value="<?php echo fetch_table_id($this->requestTable,$this->nuber); ?>" />
            
                <div class="formButton">
                <?php if(isset($this->check_perms) && ($this->check_perms->allow_update == true)) :
						$input_kind	=	'type="submit"';
						$value_kind	=	'UPDATE ENTRY';
					else :
						$input_kind	=	'disabled';
						$value_kind	=	'DISABLED';
					endif; ?>
                <input <?php echo $input_kind; ?> name="update" value="<?php echo $value_kind; ?>" style="display: inline-block; float: right;" />
                
                </div>
                	</td>
                </tr>
			</form>