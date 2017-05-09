<form enctype="multipart/form-data" action="#" method="POST">
            <?php
				
				if(!isset($this->check_perms))
					$this->check_perms	=	new	permissionCheck($this->requestTable);
				// Generate Form Blank Fields for NEW insertion
				foreach($_row as $keys => $value) {
						$_allow		=	$this->verify($keys);
						// Generate a form set unless the column is ID
						if($_allow	==	true) {
								global $con; ?>   
				<td class="adtoolsTblAdd">
                	<div class="fieldsGrad" style="margin: 0 8px;">
									<?php
								// Acceptions to insert REQUEST values if set
								if(isset($unique_id) && $result == 'unique_id')
									$request_values = $unique_id;
								elseif(isset($ID) && $result == 'ID')
									$request_values = $ID;
								elseif(isset($col) && $result == $col)
									$request_values = $ref_col_val;
									
								// Set up array to handle form fomatting
								$keys	=	Safe::encode($keys);
								if(!isset($this->_fieldType[$keys])) {
										$setInputQuery	=	$this->nubsql->fetch("select * from form_builder where column_name = '".$keys."'");
										$setInputRes	=	$setInputQuery[0];
										$size			=	(isset($setInputRes['size']) && !empty($setInputRes['size']))? $setInputRes['size']: '98%';
										$inputType		=	(isset($setInputRes['column_type']) && !empty($setInputRes))? $setInputRes['column_type']: 'text';
										$this->_fieldType[$keys]['size']	=	$size;
										$this->_fieldType[$keys]['type']	=	$inputType;
									}

								// Render all the form inputs
								formInputs::Compile($this->_fieldType[$keys]['type'],false,$keys,false,$this->_fieldType[$keys]['size'],''); ?>
        		    </div>
				</td>
									  <?php
                                       }
									// Generate the ID field with no form type
									elseif($result == 'ID')
										{ ?>
                	<td class="adtoolsTblAdd">
						<div class="fieldsGrad" style="margin-right: 0;">
                        <?php echo $result; ?>
            			</div>
                	</td>
			<?php						} 
							} ?>
            <td class="adtoolsTblAdd" colspan="2">
            <input type="hidden" name="requestTable" value="<?php echo fetch_table_id($_GET['requestTable'],$this->nuber); ?>" />
			<div class="formButtonAdd"><input <?php $disabled = ($this->check_perms->allow_write == true )? false: true; echo ($disabled == false)? 'type="submit"': 'disabled'; ?> name="<?php echo ($disabled == false)? 'add': 'disabled'; ?>" value="<?php echo ($disabled == false)? 'ADD ENTRY': 'DISABLED'; ?>" style="display: inline-block; float: right;" /></div>
        </form>
        	</td>