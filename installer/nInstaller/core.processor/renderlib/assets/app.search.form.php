<?php if(!function_exists("AutoloadFunction")) return; ?>
	<div class="search_container">
		<form action="#" method="GET" enctype="multipart/form-data">
			<div class="nbr_general_form">
				<ul class="nbr_search_bar_container">
					<li>
						<input type="text" name="search" placeholder="Type search phrase or word" />
					</li>
					<li>
						<input type="hidden" name="requestTable"  value="<?php echo $table_name; ?>" />
						<div class="nbr_button"><input disabled="disabled" type="submit" value="<?php echo $submit; ?>" /></div>
					</li> 
				</ul>
			</div>
		</form>
<?php if(isset(NubeData::$settings->pagination->data->count) && NubeData::$settings->pagination->data->count == 0) {
?>		<a class="nbr_pagination_reset" href="?requestTable=<?php echo NubeData::$settings->pagination->data->table; ?>">Reset</a>

<?php	}
?>
	</div>