
				<!-- MENU START -->
				<div id="nbr-frontend-menu">
					<?php if(!$this->isHomePage()) { ?>

					<div><a href="<?php echo $this->localeUrl('/') ?>">HOME</a></div>

					<?php } ?>

					<div><a href="<?php echo $this->localeUrl('/contact/') ?>">Contact</a></div>
					<div><a href="<?php echo $this->localeUrl('/framework/') ?>">Framework</a></div>

					<?php if($this->isLoggedIn()) { ?>

					<div><a href="?action=logout">Log Out</a></div>

					<?php } ?>

				</div>
				<!-- END MENU -->
