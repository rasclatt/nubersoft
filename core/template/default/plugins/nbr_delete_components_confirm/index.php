<?php
if(!$this->isAdmin())
	return;
$nForm		=	$this->getHelper('nForm');
$POST		=	$this->toArray($this->getPost());
$ID			=	$POST['deliver']['ID'];
$getComp	=	$this->get3rdPartyHelper('\nPlugins\Nubersoft\CoreDatabase')->getCompById($ID);
$comp		=	$getComp['component'];
$count		=	$getComp['orphans']['count'];
$page		=	$getComp['page'];

if(!empty($comp['component_type']))
	$type	=	$comp['component_type'];
elseif(!empty($comp['ref_spot']))
	$type	=	$this->colToTitle($comp['ref_spot']);
else
	$type	=	'undefined';
?>
<div style="max-height: 400px; overflow: auto; background-color: #FFF; border: 1px solid #ccc;">
	<h2>Are you sure you want to delete this <?php echo $this->safe()->encodeSingle('"'.$type.'"') ?> component?</h2>
<?php
	if($count > 0) {
?>
	<p>You will orphan <?php echo $count ?> other component<?php echo ($count > 1)? 's':'' ?>, which may have unintended results.</p>
<?php }
?>
	<?php echo $nForm->open(array('action'=>$this->siteUrl().$page,'method'=>'post')) ?>
		<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_delete_component')) ?>
		<?php echo $nForm->fullhide(array('name'=>'ID','value'=>$ID)) ?>
		<div class="nbr_button">
			<?php echo $nForm->submit(array('name'=>'submit','value'=>'DELETE','style'=>"margin: 15px auto; background-color: #333; padding: 10px;")) ?>
		</div>
	<?php echo $nForm->close() ?>
</div>