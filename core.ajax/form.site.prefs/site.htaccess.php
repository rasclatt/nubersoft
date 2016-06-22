
						<label>	Htaccess <div class="nbr_trigger" data-action="get_htaccess" data-sendto="#htaccessDrop"><span id="htHider">GET CURRENT</span></div>
							<div class="form-input">
								<textarea id="htaccessDrop" name="content[htaccess]" placeholder="Create your own HTACCESS" class="textarea" ><?php echo (isset($site->htaccess))? $site->htaccess:""; ?></textarea>
							</div>
						</label>