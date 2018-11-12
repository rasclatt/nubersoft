<?php
$data	=	$this->getDataNode('data')['data'];
$action	=	$this->getDataNode('data')['action'];
$Form	=	$this->getHelper('nForm');
$Markup	=	$this->getHelper('nMarkUp');
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Nubersoft Installer</title>
<head profile="http://www.w3.org/2005/10/profile">
<meta name="Author" content="Nubersoft">
<meta name="charset" content="utf-8">
<meta name="Description" content="Install the Nubersoft framework/cms.">
<meta name="viewport" content="width=device-width">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<script type="text/javascript" src="/core/template/default/media/js/nFunctions.js"></script>
<script type="text/javascript" src="/core/template/default/media/js/nScripts.js"></script>
<script type="text/javascript" src="/core/template/default/media/js/helpers.js"></script>
<script type="text/javascript" src="/core/template/default/media/js/nConfirm.js"></script>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Titillium+Web:300,400,600" />
<link type="text/css" rel="stylesheet" href="/core/template/default/media/css/form.css" />
<link type="text/css" rel="stylesheet" href="/core/template/default/media/css/grid.css" />
<link type="text/css" rel="stylesheet" href="/core/template/default/media/css/main.css" />
<link type="text/css" rel="stylesheet" href="/core/template/default/media/css/styles.css" />
<style>
	*,h1,h2,h3,h4,h5,h6,h7,p {
		font-family: 'Titillium Web', sans-serif;
	}
	h1,h2,h3,h4,h5,h6,h7 {
		color: #A49B9B;
	}
	p {
		font-size: 1.25em;
		color: #666;
	}
	table td {
		padding: 0.25em;
	}
	.pad-top {
		padding-top: 2em;
	}
	.pad-bottom {
		padding-bottom: 2em;
	}
	.align-right {
		text-align: right;
	}
	input.required::before {
		content: '* ';
		color: red;
	}
</style>
</head>
<body>
	<div class="col-count-3 offset pad-bottom">
		<div class="col-2 pad-top pad-bottom">
			<img src="/core/template/default/media/images/logo/nubersoft.png" style="max-width: 300px;" />
			<?php include(__DIR__.DS.$action.'.php') ?>
		</div>
	</div>	
</body>
</html>