<?php
$message	=	$this->toArray($this->getDataNode('error404'));
$path		=	$this->toSingleDs($this->getDefaultTemplate().DS.'frontend'.DS.'error404.php');
$display	=	$this->toSingleDs(NBR_ROOT_DIR.DS.$path);
if(!is_file($display))
	throw new \Nubersoft\nException('Error template is invalid (not found ironically): '.$display);

echo $this->getTemplateDoc('error.head.php');
?>
<body class="nbr">
<?php
echo $this->error404($display,$message);
echo $this->getTemplateDoc('error.foot.php');
?>
</body>
</html>