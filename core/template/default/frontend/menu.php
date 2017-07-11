<?php
# Load up the menu layout and save the results of the menu to persisting
$this->getHelper('Methodize')->saveAttr('menu',$this->getComponent('menu_layout')->toArray());
# Check if the editor is toggled
$toggled	=	$this->getSessionNode()->getToggle()->getEdit()->hasValue();
?>
<nav id="menuWrap">
	<ul id="menuContainer" class="nbr_ux_element">
		<!-- Depending on status, show compiled or editor -->
		<?php echo ($toggled)? $this->useTemplatePlugin('nbr_menu','component.php') : $this->useTemplatePlugin('nbr_menu') ?>
	</ul>
</nav>