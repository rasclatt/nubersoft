
						<?php
						$dirs	=	get_directory_list(array("dir"=>NBR_CLIENT_DIR.'/template/'));
						if(!empty($dirs['dirs'])) {
						?>
						<label><div>Primary Template</div>
							<select name="content[template_folder]">
								<option value="<?php echo $basedir = Safe::encode(str_replace(NBR_ROOT_DIR,"",NBR_TEMPLATE_DIR."/default")); ?>" <?php if(!empty($site->template_folder) && $site->template_folder == $basedir) echo " selected"; ?>><?php echo basename($basedir); ?></option>
							<?php
									foreach($dirs['dirs'] as $folder) {
											$trimmed	=	rtrim($folder,"/");
							?>
								<option value="<?php echo $thisDir = Safe::encode(str_replace(NBR_ROOT_DIR,"",$trimmed)); ?>"<?php if(!empty($site->template_folder) && $site->template_folder == $thisDir) echo " selected"; ?>><?php echo basename($thisDir); ?></option>
							<?php
										}
								}
							?>
							</select>
						</label>