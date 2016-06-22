<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<form method="get" action="">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="padding: 10px 10px 10px 0;">
							<div class="login_fields">
							<input type="text" name="search" placeholder="Type Search" value="<?php echo (isset($search) && !empty($search))? Safe::encodeSingle($search):""; ?>" style="font-size: 15px; padding: 5px 10px; margin-right: 10px;" />
							</div>
						</td>
						<td>
							<div class="login_button">
							<input disabled="disabled" type="submit" value="SEARCH" />
							</div>
						</td>
					</tr>
				</table>
			</form>
		</td>
		<?php if(isset($search) && !empty($search)) { ?>
		<td>
			<form method="get" action="">
				<div class="login_button"><input disabled="disabled" type="submit" value="RESET" /></div>
			</form>
		</td>
		<?php } ?>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<?php
		if(isset($range) && !empty($range)) {
				
				if(isset($search) && !empty($search))
					$settings['search']	=	"search=".$search;
					
				if(isset($limit) && !empty($limit))
					$settings['limit']	=	"max=".$limit;
				
				$q_string			=	(isset($settings))? "&".implode("&",$settings):"";
				
				if($range[0] != 1) { ?>
		<td>
			<a class="paginate-numbers" href="?currentpage=1<?php echo $q_string; ?>"><<</a>
		</td>
		<?php	}
		
				foreach($range as $page_num) { ?>
		<td>
			<?php		if($page_num == $current) { ?>
			<div class="paginate-current">
				<?php echo $page_num; ?>
			</div>
			<?php			}
						else { ?>
			<a class="paginate-numbers" href="?currentpage=<?php echo $page_num; echo $q_string; ?>"><?php echo $page_num; ?></a>
			<?php 			} ?>
		</td>
<?php				}	?>
		<td>
			<?php if($last != $current) { ?>
			<a class="paginate-numbers" href="?currentpage=<?php echo $last; echo $q_string; ?>">>></a>
			<?php } ?>
		</td>
		<?php
			} ?>
		<td>
		<div style="color: #888; margin-left: 20px;">PER PAGE:</div>
		</td>
		<?php
			$max_array[]	=	5;
			$max_array[]	=	10;
			$max_array[]	=	20;
			$max_array[]	=	50;
			
			foreach($max_array as $max) { ?>
		<td>
					<?php
					if($max == $limit) { ?>
			<div class="paginate-max-current">
				<?php echo $max; ?>
			</div>
			<?php
						}
					else {
							$page_num	=	(isset($page_num))? $page_num: 1; ?>
			<a class="paginate-max" href="?currentpage=<?php echo $page_num; echo "&max=$max"; echo (isset($settings['search']))? "&".$settings['search']:""; ?>"><?php echo $max; ?></a>
				<?php 
						} ?>
		</td><?php
				} ?>
	</tr>
</table>