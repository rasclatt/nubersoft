
                    <div style=" padding: 0 15px; text-align: center;">
                        <table cellpadding="0" cellspacing="0" border="0">
                             <tr>
                                 <td><?php
                                    $search->_activate_max	= true;
                                  //  $search->execute(1);
                                   $search->ProcessSearch();
                                   if(isset($search->_post_out['search'])) { ?>
                                   <p onClick="window.location='<?php echo $_SERVER['SCRIPT_URL']; ?>'" class="Pagination EmptyButton fontSize12 cursorPointer underlined" style="display: width: auto; clear: left; float: left;">FINISHED SEARCH</p><?php } ?>
                                 </td>
                             </tr>
                             <tr>
                                <td>
                                     <div style="display: inline-block; padding: 0; margin-top: 10px; width: 100%;">
                                         <div style="display: inline-block; padding: 10px 0 0 0; float: left; height: 50px;">
                                            <?php $search->SearchBar(); ?>
                                         </div>
                                         <div style="display: inline-block; height: 50px; float: left;">
                                         <?php
                                            $search->PageTracker();
                                            $query	=	$search->fetch(); ?>
                                         
                                         </div>
                                         <?php if(isset($search->_post_out['search'])) { ?>
                                               <script>
                                               var QueryCount	=	'<?php echo count($query); ?>';
                                               document.getElementById('searchResNum').innerHTML = ' ('+QueryCount+' Results)';
                                               </script><?php } ?>
                                    </div>
                                </td>
                            </tr>
                         </table>
                    </div>
                    <div style=" padding: 15px; text-align: center;">
						<div id="tester"></div>
                    	<table cellpadding="0" cellspacing="0" border="0" class="adtools">
                    <?php
						// Set a table
						$_reqTable		=	$this->requestTable;
						$_colsInTble	=	$this->getColumnsFromTable($this->nuber->table_name);
				
						foreach($_colsInTble as $colkey) {
								$array[$colkey]	=	'';
							}
						
                    	// There is a prepopulated query, run it
						if(!empty($query))
							$query		=	$query;
						// If not, assign an empty array with the column names for the foreach output
						// This way, the foreach doesn't have to be too complicated.
						else {
								$query	=	array($array);
								$_noAdd	=	true;
							}
							
						$o = 0;
						if(!empty($query)) {
								if($templates->formatter !== false) { ?>
							<tr>
								<td colspan="<?php echo (count($results) + 2); ?>"><?php }
								foreach($query as $results) {
										
										if( $templates->formatter == 'img') { ?>
									<?php 
												if($o == 0)
													$templates->ImageStorer($array);
												$templates->ImageStorer($results);
											 }
										else {
												if($o == 0) {
														$this->tableHead($results);
														$this->tableAdd($results);
													}
												if(!isset($_noAdd))
													$this->tableGuts($results);
											}
										$o++;
									}
							} 
							 
								if($templates->formatter !== false) { ?>
								</td>
							</tr>
										<?php } ?>
                            <tr>
                            	<td colspan="<?php echo (count($results)+2); ?>">
                                    <div class="SearchBkgWrap">
                                        <div style="display: inline-block; margin: 0 auto;"><?php $search->PageTracker(); ?></div>
                                    </div>
                                </td>
                            </tr>
						</table>
                    </div>