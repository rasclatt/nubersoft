<?php include(__DIR__.DS.'header.php'); ?>
	<div class="login_container">
		<div class="login_bkg" style="margin:30px auto; max-width: 500px; background-color: #EBEBEB;border: 5px solid #FFF; box-shadow: 0 0 10px #000; padding: 30px 30px 10px 30px;">
			<h3 class="nFont">You have no <code>dbcreds.php</code> file which is responsible for connecting to your database. You need to run the installer again, OR create a dbcreds.php file.</h3>
			<div style="text-align: left;">
				<h3>Creating a dbcreds.php file:</h3>
				<p><strong>1)</strong> In the <code>/client/settings/"</code> directory <i>(create one if not already created)</i> make a <code>php</code> file named <code>dbcreds.php</code></p>
				<p><strong>2)</strong> Paste the code below, substituting your database credentials.</p>
				<code class="fullwidth">
					<pre>
&lt;?php
if(!function_exists("autoload_function"))
	return;

$this->_creds['user']	=	base64_encode("myusername");
$this->_creds['pass']	=	base64_encode("mypassword");
$this->_creds['host']	=	base64_encode("myhost");
$this->_creds['data']	=	base64_encode("mydatabase");
					</pre>
				</code>
			</div>
			
			<div>
				<a class="nbr_button" href="/">CONTINUE</a>
			</div>
		</div>
	</div>
<?php include(__DIR__.DS.'footer.php'); ?>