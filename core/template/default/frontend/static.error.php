<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $title ?></title>
<style>
	* {
		font-family: Arial;
		font-size: 1.05em;
	}
	body,html {
		margin: 0;
		padding: 0;
		background-color: #CCC;
	}
	div.container {
		display: flex;
		background-color: transparent;
		justify-content: center;
	}
	div.main-content {
		max-width: 1200px;
		flex-basis: 25% 50% 25%;
		background-color: #FFF;
		flex-grow: 3;
		padding: 1.5em;
	}
	h1 {
		font-size: 2em;
		font-weight: normal;
		color: #666;
	}
	p,h1 {
		display: block;
	}
</style>
</head>
<body>
	<div class="container">
		<div class="main-content">
			<h1>Application Error:</h1>
			<p><?php echo $useData ?></p>
		</div>
	</div>
</body>
</html>