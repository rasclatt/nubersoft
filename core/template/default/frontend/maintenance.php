<!DOCTYPE html>
<head>
<meta charset="utf-8" />
<?php if(!$this->isAdminPage()) { ?>
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<?php } ?>
<meta name="viewport" content="width=device-width">
<title>Site Offline</title>
<link rel="shortcut icon" href="<?php echo $this->siteUrl() ?>/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $this->siteUrl() ?>/favicon.png" />
<?php echo $this->getHelper('nHtml')->styleSheet('https://fonts.googleapis.com/css?family=Carrois+Gothic',false,false); ?>
<?php echo $this->getHelper('nHtml')->styleSheet('https://fonts.googleapis.com/css?family=Zeyada',false,false); ?>
<?php echo $this->getHelper('nHtml')->styleSheet(NBR_MEDIA.DS.'css'.DS.'styles.css'); ?>
<?php echo $this->getHelper('nHtml')->styleSheet(NBR_DEFAULT_TEMPLATE.DS.'css'.DS.'sitelive.css'); ?>
<?php echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\JsLibrary')->defaultJQuery(array("force_ssl"=>true))->getResults(); ?>
<?php echo $this->getHelper('nHtml')->javaScript('media'.DS.'js'.DS.'nFunctions.js'); ?>
<?php echo $this->getHelper('nHtml')->javaScript('media'.DS.'js'.DS.'helpers.js'); ?>
<?php echo $this->getHelper('nHtml')->javaScript('media'.DS.'js'.DS.'nScripts.js'); ?>
<style>
div.postit {
	background-image: url('<?php echo $this->siteUrl('/media/images/core/offline.png') ?>');
}
</style>
</head>
<body>
	<div>
		<center>
			<h2><?php echo $this->siteUrl() ?></h2>
			<div class="postit dragonit">
				<div class="rotate cursive">
					<h1>Stand by.</h1>
					<p><?php echo $this->getSiteOfflineMsg() ?></p>
				</div>
			</div>
		</center>
	</div>
</body>
</html>