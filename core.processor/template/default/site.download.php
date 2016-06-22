<?php
if(!function_exists('AutoloadFunction'))
	die('Direct linking not allowed.');
// Autoload some needed functions
AutoloadFunction("default_jQuery,FetchPublicPage,get_header,render_footer,render_masthead");
$referrer	=	FetchPublicPage();
$useRefer	=	(isset($_REQUEST['referrer']))? Safe::decOpenSSL($_REQUEST['referrer']) : $referrer['url'];
// Load the head without the <head> tag
echo get_header(array("head"=>false)); ?>
<head>
<?php echo default_jQuery(); ?>
<script src="/js/admintools.js" type="text/javascript"></script>
<link rel="stylesheet" href="/css/default.css" />
<link rel="stylesheet" href="/css/admintools.css" />
<script type="text/javascript">
$(document).ready(function() {
	$("a.base_button").click(function(e) {
		e.preventDefault;
	});
	
	$(".hidetoggle").click(function() {
		$(".hidetoggle_panel").fadeToggle();
	});
});
</script>
<style>
a.base_button:link,
a.base_button:visited	{
	font-family: Arial, Helvetica, sans-serif; font-size: 18px; padding: 15px 20px; cursor: pointer; background-color: #EBEBEB; border: 1px solid #888;
}
</style>
</head>
<body class="nbr">
<?php echo render_masthead(); ?>
<div style="width: 100%; display: block; margin: 0; padding: 60px 0 60px 0; background-color: #FFF; text-align: center;">
	<div style="display: inline-block; margin: 0 auto; max-width: 1000px;">
		<?php
		if(!empty($response)) { ?>
			<h2><?php echo $response; ?></h2>
			<a class="nbr_button" href="<?php echo $useRefer; ?>">Close</a>
		<?php
			}
		elseif(!empty($error)) { ?>

			<h2><?php echo (!empty($error->multiple))? "There are multiple files with this name.":"Unknown Error Occurred"; ?></h2>
			<p><?php echo (!empty($error->multiple))? "Your method of download must be more specific where to download this file from.":"Sorry, you can not download this file. It is missing or renamed."; ?></p>
			<?php
			}
		elseif(!empty($this->terms)) {
			 ?>
				<div class="nbr_terms_wrap">
					<div class="hidetoggle_panel" style="display: none;">
						<h2>Thank you.</h2>
							<p>Your download will now commence.</p>
							<div class="base_nbr_button" onClick="window.close();">Close</div>
					</div>
					<div class="hidetoggle_panel">
					<?php
					if(!$this->allow && empty(NubeData::$errors->download->{0}->usergroup)) {
						// Unfinished
						$nubquery	=	nQuery();
						$terms		=	$nubquery	->select()
													->from("components")
													->where(array("ref_spot"=>"terms","ref_anchor"=>$this->dlInfo->terms_id))
													->fetch();
						if($terms != 0) { ?>
						<?php echo Safe::decode($terms[0]['content']); ?>
						<form action="download.php" enctype="multipart/form-data" method="post">
							<input type="hidden" name="referrer" value="<?php echo Safe::encOpenSSL(strip_tags($_SERVER['HTTP_REFERER'])); ?>" />
							<input type="hidden" name="file" value="<?php echo strip_tags($_REQUEST['file']); ?>" />
							<input type="hidden" name="timestamp" value="<?php echo date('Y-m-d H:i:s'); ?>" />
							<input type="hidden" name="terms_id" value="<?php echo $this->dlInfo->terms_id; ?>" />
							<input type="hidden" name="item" value="<?php echo strip_tags($_REQUEST['file']); ?>" />
							<input type="hidden" name="username" value="<?php echo strip_tags($_SESSION['username']); ?>" />
							<input type="hidden" name="accepted" value="1" />
							<div class="nbr_button"><input type="submit" name="submit" value="I Agree" class="hidetoggle" /></div>
						</form>
<?php						}
						else { ?>
						<h1>An error occurred!</h1>
						<p>This download has no Terms or Agreements associated with it. It is unavailable for download.</p>
						<?php }
					}
				else { ?>
					<div class="hidetoggle_panel">
						<h2>Permission Error.</h2>
						<p>You can not download this file due to permission limitations on your account.</p>
						<a class="base_button" href="<?php echo $referrer['url']; ?>" >Close</a>
					</div>
				<?php } ?>
					</div>
			</div>
				<?php
			}
		else { ?>
				<h2>Whoops!</h2>
				<p>No one by that name lives here.</p><?php
			}  ?>
		</div>
	</div>
</div>
<?php echo render_footer(); ?>
</body>
</html>