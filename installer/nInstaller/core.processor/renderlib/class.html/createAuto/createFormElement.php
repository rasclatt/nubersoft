
					<div class="fancycompbutton" id="comp<?php echo $_useID; ?>" onClick="ShowHide('#comp<?php echo $_useID; ?>_panel','','slide')">
						<?php echo strtoupper($_input['sort']); ?>
					</div>
					<div id="comp<?php echo $_useID; ?>_panel" class="fancycomppanel">
					<?php
							foreach($_input['vals'] as $_inputname) {
											$_col_name	=	(isset($_cols[$_inputname]['column_name']))? $_cols[$_inputname]['column_name']: $_inputname;
											$_col_type	=	(isset($_cols[$_inputname]['column_type']))? $_cols[$_inputname]['column_type']:'text';
											$_col_size	=	(isset($_cols[$_inputname]['size']))? $_cols[$_inputname]['size']: '98%';
											
											// Determine which class to display
											if($_col_type == 'select')
												$class		=	"formElementSelectLeft";
											elseif($_col_type == 'textarea')
												$class		=	"formElementTextarea";
											else
												$class		=	"fieldsGradLeft"; ?>
							
                            <div style="display: inline-block; background-color: inherit; width: 100%;"><?php
							if($_col_type == 'textarea' && isset($this->inputArray[0]['ID'])) { ?>

                                <div onClick="ScreenPop(); onthefly('ID=<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>&unique_id=<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>&table=<?php if(isset($this->table)) echo $this->table; ?>&isolate=true','/core.ajax/admintools.component.php', 'POST')">
                                	<table class="base_rollover">
                                    	<tr>
                                        	<td><div class="WYSIWYG_button"></div></td>
                                            <td style="color: #FFFFFF; vertical-align: middle;">WYSIWYG</td>
                                        </tr>
                                   </table>
                                </div>
                                <?php } ?>
                            	<div class="variableType"><?php echo ucwords(str_replace("_", " ", $_col_name)); $this->helpdesk($this->table, $_col_name); ?></div>
                                <div class="<?php echo $class ?>" style="float: left; text-align: left; clear: left;"><?php
								$_info		=	(isset($this->inputArray[0][$_col_name]))? $this->inputArray[0][$_col_name]: '';
								$_default	=	(isset($this->inputArray[0]))? $this->inputArray[0]: false;	
								
                                FormElementSelector::Build($_col_type,$_default,$_col_name,$_info,$_col_size);  ?>
                                </div>
                            </div>
							
						<?php	} ?>
					</div>