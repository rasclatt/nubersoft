<?php if(!isset($access)) exit; ?>
	<div class="search_container">
		<form action="#" method="GET" enctype="multipart/form-data">
			<div class="serachbttnblock">
				<div id="searchbutton">Search<?php if(isset($_GET['search'])) echo ' again'; ?></div>
				<table class="searchcomponent">
					<tr>
						<td>
							<div class="search_bar_set">
								<input type="text" name="search" placeholder="Type search phrase or word" />
							</div>
							<input type="hidden" name="requestTable"  value="<?php echo $table_name; ?>" />
						</td>
						<td>
							<div class="search_bar_search"><input disabled="disabled" type="submit" value="&nbsp;" /></div>
						</td>
					</tr>
				</table>
		</div>
		</form>
	</div>
	<script>
	$('#searchbutton').click(function() {
			$('.searchcomponent').fadeToggle('fast');
		});
	</script>