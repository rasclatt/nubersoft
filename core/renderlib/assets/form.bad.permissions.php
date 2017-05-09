
	        <div style="display: block; margin: 0 auto 100px auto; height: 200px; width: 100%; padding: 30px 0 30px 0;">
                <div style="max-width: 400px; border: 10px solid #FFF; background-color: #EBEBEB; min-height: 200px; margin: 60px auto; position: relative; padding: 30px; box-shadow: 2px 2px 8px #000000;">
	            	<?php if(isset($error)) echo $error;?>
                    <p> You do not have enough permissions to view this page. Log in as different user go back to <a href="http://<?php echo $_SERVER['HTTP_HOST'];?>" />home page.</a></p>
                    <a class="base_button" style="margin-top: 20px;" href="http://<?php echo $_SERVER['HTTP_HOST'];?>" />Home Page</a>
				</div>
			</div>