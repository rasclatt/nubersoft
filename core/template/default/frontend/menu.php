<?php
echo printpre()
?>

    <nav id="menuWrap">
        <ul id="menuContainer">
<?php
$menuSet	=	$this->getDataNode('gmenu');
$mGSub		=	$this->getDataNode('gsub');
$editSatus	=	$this->getPlugin('\nPlugins\Nubersoft\core')->getEditStatus();
if(!empty($_menu_on)) {
	$page_id	=	$this->getData('pageURI')->unique_id;
	$useid		=	false;
	foreach($menuSet as $key => $array) {
		$submenu	=	array();
		if(isset($mGSub->{$key}))
			$submenu	=	$mGSub->{$key};
			
		if($array->in_menubar == 'on') {
			if(!empty($mGSub->{$key}))
				$useid	=	$mGSub->{$key};
				
			$_button['sub_set']		=	($useid && $useid->parent_id == $key);
			$_button['sub_live']	=	(!empty($useid->page_live) && $useid->page_live == 'on');
			# Assign refpage
			if($editSatus) {
				$this->component->ref_page	=	$key;
				$this->component->page_id	=	$page_id;
			}
?>
			<li class="nbr_button_cont <?php echo $key; ?>" id="btnctn<?php echo $key; ?>">
				<a class="mainMenu<?php echo ($key == $page_id)? "_on":''; ?>" href="<?php echo $this->siteUrl().$array->full_path; ?>" id="btn<?php echo $key; ?>"><?php echo ucwords($this->safe()->decode($array->menu_name)); ?></a>
				<?php		
				if($editSatus || !empty($submenu)) {
				?>
				<div class="nbr_menu_subpop"<?php if($editSatus) { ?> id="stick_it_<?php echo $key ?>_panel" data-subfx='{"removeClass":{"data":["displayBlock"]},"addClass":{"data":["displayBlock"]}}'<?php } ?>>
<?php			
					
				if($editSatus)
					$this->component($submenu,$key);
				else {
					
					if(isset($submenu->content) && $submenu->page_live == 'on') {
						$this->autoload('use_markup');
						echo $this->safe()->decode(use_markup($submenu->content,true));
					}
				} ?>
				</div>
<?php		} ?>
			</li>
<?php	}
	} 
}
						
if(($allowBypass == 0 && !empty($_bypass)) && (is_admin())) {
	throw new \Exception('Menu: File not found.');
}
?>
		</ul>
	</nav>