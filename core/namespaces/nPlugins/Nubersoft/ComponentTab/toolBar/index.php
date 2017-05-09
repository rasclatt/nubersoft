<?php
# Not admin, stop
if(!$this->nApp->isAdmin())
	return;
# Check that the editor is set in sesstion/data node
$edit_status	=	(!empty($this->nApp->getSession('admintools')->editor));
# Get the toggle editor status (if set)
$request_set	=	$this->nApp->getRequest('toggle_editor');
# If there is a GET request, add the session variable
if($request_set)
	$this->nApp->setSession(array('admintools','editor'),$request_set,true);
# Check if the status of the view is on or off
$isEditOn		=	$this->getEditorStatus();
# Adds the opposite link for toggling
$toggle			=	($isEditOn)? 'toggle_editor=off' : 'toggle_editor=on';
# If the toggler is on, add in the toolbar
if(!empty($isEditOn))
	echo $this->nApp->render($this->nApp->fetchTemplate()->getBackEnd('toolbar.php'));
# Get the current components for this page. Checks if there is a component that is able to single-edit
$comps		=	$this->nApp->nQuery()
					->select(array("ID","component_type","content","page_live"))
					->from("components")
					->where(array("ref_page"=>$this->data['unique_id']))
					->fetch();
# Sets the image icon
$img_icn	=	($isEditOn)? 'view' : 'edit';
# Creates the link
$aLink		=	$this->nApp->siteUrl($this->nApp->getDataNode('_SERVER')->SCRIPT_URL."?".$toggle);
# Creates the image to use as content for the link
$aImg		=	$this->nImage->image(realpath(__DIR__.DS.'..').DS.'images'.DS."icn_{$img_icn}.png",array('style'=>'max-width: 25px;'),false);
?>
<div id="nbr_component_quick_links" class="nbr_ux_element">
	<?php echo $this->nHtml->a($aLink,$aImg,array('class'=>'nbr_plugin_component_tab'),false,false) ?>

<?php
	if($comps != 0) {
		$i 			= 0;
		$count		=	count($comps);
		$renderI	=	false;
		foreach($comps as $link) {
			if($link['component_type'] != 'code')
				continue;
			elseif($link['page_live'] != 'on')
				continue;

			if($i == 0) {
				$renderI	=	true;
?>
	<ul>
<?php		}

			$cont	=	(!empty($link['content']))? $link['content'] : 'Empty Content';
			$href	=	$this->nApp->siteUrl('/?action=nbr_load_single_editor&cId='.$link['ID']);
?>
		<li class="nbr_code_tool_btn" onClick="window.open('<?php echo $href; ?>','_blank')" data-cidshow="<?php echo $link['ID']; ?>">
			<?php echo $this->nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_code.png',false,false,true) ?>
			<div class="nbr_mini_preview_pop">
				<div class="nbr_mini_preview"><?php echo substr($cont,0,100); ?></div>
			</div>
		</li>
<?php		if(($i == $count) && ($renderI)) {
?>	</ul>
<?php		}
		$i++;
		}
	}
?>
</div>