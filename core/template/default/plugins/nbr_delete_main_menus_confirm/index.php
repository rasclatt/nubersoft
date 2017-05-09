<?php
if(!$this->isAdmin())
	return;
$nProcessor	=	$this->getHelper('nToken')->setMultiToken('nProcessor','delete_menu');
$nForm		=	$this->getHelper('nForm');
$POST		=	$this->toArray($this->getPost()->deliver);
$ID			=	$POST['ID'];
$getComp	=	$this->get3rdPartyHelper('\nPlugins\Nubersoft\CoreRouter')->setTable('main_menus')->getCompById($ID);
$comp		=	$getComp['component'];
$type		=	(!empty($comp['component_type']))? $comp['component_type'] : 'undefined';
$count		=	$getComp['orphans']['count'];
?>
<div style="max-height: 400px; overflow: auto; background-color: #FFF; border: 1px solid #ccc;">
	<h2>Are  you sure you want to delete this menu?</h2>
<?php
	if($count > 0) {
?>
	<p>You will orphan <?php echo $count ?> other menu<?php echo ($count > 1)? 's':'' ?>.</p>
<?php }
?>
	<?php echo $nForm->open(array('action'=>$this->siteUrl(),'method'=>'post')) ?>
		<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_delete_menu')) ?>
		<?php echo $nForm->fullhide(array('name'=>'ID','value'=>$ID)) ?>
		<?php echo $nForm->fullhide(array('name'=>'token[nProcessor]','value'=>$nProcessor)) ?>
		<?php echo $nForm->fullhide(array('name'=>'command','value'=>'delete')) ?>
		<div class="nbr_button">
			<?php echo $nForm->submit(array('name'=>'submit','value'=>'DELETE','style'=>"margin: 15px auto; background-color: #333; padding: 10px;")) ?>
		</div>
	<?php echo $nForm->close() ?>
</div>