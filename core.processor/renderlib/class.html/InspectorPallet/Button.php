<?php
if(!function_exists("AutoloadFunction"))
	return;
AutoloadFunction('get_edit_status');
?>
        <div class="nbr_menu_toggler_wrap">
			<form enctype="application/x-www-form-urlencoded" method="post">
				<input type="hidden" name="command" value="toggle_set" />
				<input type="hidden" name="toggle" value="<?php echo (get_edit_status())? '':'1'; ?>">
				<table border="0" cellpadding="0" cellspacing="0" id="toggle_table">
					<tr>
<?php if(!get_edit_status()) {
?>						<td class="fullsite"><span class="toggle_text">EDIT <input type="hidden" name="type" value="track" /></span></td>
<?php }
?>						<td id="nbr_toggler_edit<?php echo (get_edit_status())? '_on':''; ?>">
							<input disabled="disabled" type="submit" name="edit" value="&nbsp;" />
						</td>
<?php if(get_edit_status()) {
?>					</tr>
				</table>
			</form>
        </div>
        <div style="display: inline-block; float: left;">
            <form action="" enctype="application/x-www-form-urlencoded" method="post">
				<input type="hidden" name="command" value="toggle_set" />
                <input type="hidden" name="toggle" value="1">
                <input type="hidden" name="edit" value="1">
<?php }
?>                    </tr>
                </table>
            </form>
        </div>