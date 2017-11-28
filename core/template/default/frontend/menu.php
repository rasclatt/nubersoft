<?php
# Load up the menu layout and save the results of the menu to persisting
$this->getHelper('Methodize')->saveAttr('menu',$this->getComponent('menu_layout')->toArray());
# Check if the editor is toggled
$toggled	=	$this->getSessionNode()->setStrictMode(true)->getToggle()->getEdit()->getType();
?>

			<div id="menu-wrapper">

				<?php echo $this->useTemplatePlugin('nbr_menu') ?>

			</div>