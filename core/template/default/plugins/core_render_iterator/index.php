<?php
# Initialize the processing of array settings
$this->renderEngine->initialize($this->info[$key]);	
$_layout	=	$this->renderEngine	->setStyles()
									->setIdClass('_id')
									->setIdClass('class')
									->compile()
									->display_inline;

$_perm		=	$this->renderEngine->checkPermissions();		
$id			=	$this->getInfo($key,'ID');
$currType	=	$this->getInfo($key,'component_type','undefined');
$isCode		=	($currType == 'code');
$renderWrap	=	($currType == 'div' || $currType == 'row' || $isCode);
$locales	=	$this->getLocaleRestrictions($id);
# If there is an array of locales (possible restrictions), set to restrict
$restrict	=	(!empty($locales));
# If there is an array of ids
if(is_array($locales) && !empty($locales)) {
	# If the current locale is in the array of approved
	if(in_array($this->getLocale(),$locales)) {
		# Set no restrictions
		$restrict	=	false;
	}
}
else
	# Remove restriction if false positive
	$restrict	=	false;

if(empty($current)) {
	if($_perm) {
		if($currType == 'code' && $this->isAdmin() && !$restrict) { ?><article data-cid="<?php echo $id; ?>"><?php }

		if(!$restrict)
			echo $this->renderEngine->Display()->getDisplay();

		if($currType == 'code' && $this->isAdmin() && !$restrict) { ?></article><?php }
	}
}

if($_perm && !$restrict):
	if(is_array($current) || $isCode): ?>
		<?php if($renderWrap): ?>

<div <?php echo $_layout; ?>>
	
		<?php endif ?>
	<?php endif ?>
	<?php
	if(is_array($current)) {
		foreach($current as $childkey => $childvalue) {
			$this->renderIterator($childvalue,$childkey);
		}
	}

	if(is_array($current) || $isCode): ?>
		<?php if($renderWrap): ?>
</div>
		<?php endif ?>
	<?php endif ?>
<?php endif ?>