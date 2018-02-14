<?php
# Decode content block
$this->payload['content']	=	(!empty($this->payload['content']))? $this->safe()->decode($this->payload['content']):"";

# Set rules for TEXT INPUT
switch($this->payload['component_type']) {
	case('text'): ?>

<span <?php echo $inline; ?>><?php echo $this->payload['content']; ?></span>
		
		<?php break;
	# Set rules for CODE INPUT
	case('code'):
		$this->autoload(array("use_markup"));
		if($inline): ?>

<div <?php echo $inline; ?>><?php endif ?>
	<?php echo PHP_EOL."\t\t\t\t\t\t\t\t".use_markup($this->payload['content']) ?>
	<?php if($inline): ?>
</div>

		<?php
		endif;
		break;
	# Set rules for IMAGE INPUT
	case('image'):
		if(!empty($this->payload['file_path']))
			$filePath	=	$this->payload['file_path'].$this->payload['file_name'];
		else {
			$file_check_res	=	$query->select(array("file","file_path"))
									->from("media")
									->where (array("ref_page"=>\Nubersoft\Singleton::$settings->page_prefs->unique_id,"ID"=>$this->payload['ID']))
									->fetch();
			$file_check_dir	=	($file_check_res != 0)? str_replace(NBR_ROOT_DIR, "", $file_check_res[0]['file_path']): '/client/images/default/';
			$filePath		=	$file_check_dir.$file_check_res[0]['file'];
		}

		if(isset($filePath)): ?>

<img src="<?php echo $filePath; ?>" <?php echo $inline; ?> />

		<?php
		endif;
		break;
	# Set rules for BUTTON INPUT
	case('button'): ?>

<a href="<?php echo $this->safe()->decode($this->payload['a_href']); ?>" <?php echo $inline; ?>><?php echo $this->payload['content']; ?></a>
		
		<?php
		break;
	# Set rules for EMAIL INPUT
	case('form_email'):
		echo $this->getPlugin('\nPlugins\Nubersoft\Emailer')
			->create(array("attributes"=>$inline,"info"=>$this->payload));
		break;
	default:
		$inc = NBR_CLIENT_DIR.DS.'Components'.DS.$this->payload['component_type'].DS.'view.php';
		if(is_file($inc))
			include($inc);
}