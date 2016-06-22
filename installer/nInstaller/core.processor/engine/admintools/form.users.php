<?php
	if(!function_exists("AutoloadFunction"))
		return; ?>
	<div class="fullscreen">
		<div class="misc-window">			
				<div style="width: 100%; display: inline-block;">
					<div class="close-button" onClick="ShowHide('.fullscreen','','fade')">x</div>
				</div>
			<div class="misc-window-cont nbr_general_form">
				<h2>Add User</h2>
				<form enctype="multipart/form-data" method="post" action="">
					<input type="hidden" name="requestTable" value="users" />
					<?php Input(false,'ID',false,'fullhide',$dropdowns); ?>
					<?php Input(false,'unique_id',false,'fullhide',$dropdowns); ?>
					<table style="display: inline-block;">
						<tr>
							<td class="padding-bottom-15">
								<label for="username">Username</label>
								<div class="login_fields"><?php Input(false,'username',false,'text',$dropdowns); ?></div>
							</td>
							<td class="padding-bottom-15">
								<label for="password">Password</label>
								<div class="login_fields"><?php Input(false,'password',false,'password',$dropdowns); ?></div>
							</td>
						</tr>
						<tr>
							<td class="padding-bottom-15">
								<label for="firstname">First Name</label>
								<div class="login_fields"><?php Input(false,'first_name',false,'text',$dropdowns); ?></div>
							</td>
							<td class="padding-bottom-15">
								<label for="lastname">Last Name</label>
								<div class="login_fields"><?php Input(false,'last_name',false,'text',$dropdowns); ?></div>
							</td>
						</tr>
						<tr>
							<td class="padding-bottom-15">
								<label for="email">E-mail</label>
								<div class="login_fields"><?php Input(false,'email',false,'text',$dropdowns); ?></div>
							</td>
							<td class="padding-bottom-15">
								<label for="page_live">Active</label>
								<div class="login_fields"><?php Input(false,'page_live',false,'select',$dropdowns); ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="2">Usergroup<div class="login_fields"><?php Input(false,'usergroup',false,'select',$dropdowns); ?></div></td>
						</tr>
						<tr>
							<td style="vertical-align: bottom; text-align: center;" colspan="2">
								<div style="float: none; display: inline-block; margin-top: 20px;">
									<div class="nbr_button"><input disabled="disabled" type="submit" name="add" value="ADD USER" /></div>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<h2><div class="close-button" onClick="ShowHide('.fullscreen','','fade')">+</div>Viewing: Users</h2>
	<table class="data_tables" cellpadding="0" cellspacing="0" border="0">
		<tr class="header_row"><!--
			<td>ID</td>
			<td>UNIQUE ID</td>-->
			<td>USERNAME</td>
			<td>FIRST NAME</td>
			<td>LAST NAME</td>
			<td>EMAIL ADDRESS</td>
			<td>ACTIVE</td>
		</tr>
<?php if($values != 0) { ?>
		<?php foreach($values as $row) { ?>
		<tr class="data_row" onClick="PowerButton('u_<?php echo $row['ID']; ?>','default','.hidethis')"><!--
			<td><?php echo $row['ID']; ?></td>
			<td><?php echo $row['unique_id']; ?></td>-->
			<td><?php echo $row['username']; ?></td>
			<td><?php echo $row['first_name']; ?></td>
			<td><?php echo $row['last_name']; ?></td>
			<td><?php echo $row['email']; ?></td>
			<td><?php echo $row['page_live']; ?></td>
		</tr>
		<tr>
			<td colspan="6" style="vertical-align: bottom;">
				<div id="u_<?php echo $row['ID']; ?>_panel" class="hidethis inset-box">
					<h3 style="background-color: #666; color: #FFF; padding: 15px; border-radius: 4px; margin: 0 0 20px 0;">User Profile: <?php echo (!empty($row['email']))? $row['email']:$row['username']; ?></h3>
					<form enctype="multipart/form-data" method="post">
						<input type="hidden" name="requestTable" value="users" />
						<?php Input($row,'ID',false,'fullhide',$dropdowns); ?>
						<?php Input($row,'unique_id',false,'fullhide',$dropdowns); ?>
						<input type="hidden" name="thumbnail" value="1" />
						<table style="display: inline-block;">
							<tr>
								<td>Username</td><td><div class="login_fields"><?php Input($row,'username',false,'text',$dropdowns); ?></div></td>
								<td rowspan="4" style="padding: 20px;vertical-align: top;">
									<div style="border: 1px solid #666; height: 100px; width: 100px; background-color: #888;<?php if(!empty($row['file_name'])) { ?>background-image: url(<?php echo '/client_assets/thumbs/users/'.$row['file_name']; ?>); background-repeat: no-repeat; background-size: cover;<?php } ?>">
									</div>
									<?php Input($row,'file',false,'file',$dropdowns,$nuber); ?>
								</td>
							</tr>
							<tr>
								<td>Password</td><td><div class="login_fields"><?php Input($row,'password',false,'password',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td>First Name</td><td><div class="login_fields"><?php Input($row,'first_name',false,'text',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td>Last Name</td><td><div class="login_fields"><?php Input($row,'last_name',false,'text',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td>E-mail</td><td><div class="login_fields"><?php Input($row,'email',false,'text',$dropdowns); ?></div></td>
								<td rowspan="2" style="vertical-align: bottom;">
									<div style="float: right; display: inline-block;">
										<label>Delete User?</label>
										<input type="checkbox" name="delete" />
										<div class="login_button"><input disabled="disabled" type="submit" name="update" value="SAVE" /></div>
									</div>
								</td>
							</tr>
							<tr>
								<td>Usergroup</td><td><div class="login_fields"><?php Input($row,'usergroup',false,'select',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td>Page</td><td><div class="login_fields"><?php Input($row,'page_live',false,'select',$dropdowns); ?></div></td>
							</tr>
						</table>
					</form>
				</div>
			</td>
		</tr>
			<?php }
		} ?>
	</table>
	