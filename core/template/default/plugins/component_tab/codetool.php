<?php
namespace Nubersoft;

# Not admin, stop
if(!$this->isAdmin())
	return;
/*
# Check that the editor is set in sesstion/data node
$edit_status	=	(!empty($this->getSession('admintools')->editor));
# Get the toggle editor status (if set)
$request_set	=	$this->getRequest('toggle_editor');
# If there is a GET request, add the session variable
if($request_set)
	$this->setSession(array('admintools','editor'),$request_set,true);
# Check if the status of the view is on or off
# If the toggler is on, add in the toolbar
//if(!empty($isEditOn))
//	echo $this->render($this->fetchTemplate()->getBackEnd('toolbar.php'));
*/
# Fetch page helpers
$nImage		=	new nImage();
$nHtml		=	new nHtml();
$Component	=	new \nPlugins\Nubersoft\ComponentTab($this);
$isEditOn	=	$Component->getEditorStatus();
# Sets the image icon
$img_icn	=	($isEditOn)? 'view' : 'edit';
# Adds the opposite link for toggling
$toggle		=	($isEditOn)? 'off' : 'on';
# Creates the link
$aLink		=	$this->siteUrl($this->getSession('SCRIPT_URL')."?toggle_editor=".$toggle);
# Get the current components for this page. Checks if there is a component that is able to single-edit
$comps		=	$this->nQuery()
					->select(array("ID","component_type","content","page_live"))
					->from("components")
					->where(array("ref_page"=>$Component->getData()->getComponentTabData('unique_id')))
					->fetch();
# Creates the image to use as content for the link
$aImg		=	$nImage->image(__DIR__.DS.'images'.DS."icn_{$img_icn}.png",array('style'=>'max-width: 25px;'),false);
?>
<div id="nbr_sizer"></div>
<div id="nbr_component_quick_links" class="nbr_ux_element">
	<?php echo $nHtml->a($aLink,$aImg,array('class'=>'nbr_plugin_component_tab'),false,false) ?>

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
			$href	=	$this->siteUrl('/?action=nbr_load_single_editor&cId='.$link['ID']);
?>
		<li class="nbr_code_tool_btn" onClick="window.open('<?php echo $href; ?>','_blank')" data-cidshow="<?php echo $link['ID']; ?>">
			<?php echo $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_code.png',false,false,true) ?>
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
<script>
$(document).ready(function(docevent){
	var	setFader	=	function()
		{
			var	self		=	this;
			this.elem		=	document.getElementById('nbr_sizer');
			
			this.show		=	function()
				{
					this.elem.style.display	=	'block';
					var	test				=	this.elem;
					test.innerHTML			=	window.innerWidth+'px';
					this.elem				=	test;
					console.log(this.elem);
					return this;
				}
			
			this.countDown	=	function(timeout)
				{
					return setTimeout(function(){
						if(isset(self,'elem'))
							self.elem.style.display	=	'none';
					},timeout);
				}
				
			this.cancel	=	function()
				{
					clearTimeout(self.countDown);
					self.elem	=	
					self		=	null;
				}
				
		}
	
	var	runDown	=	new setFader();
	
	$(window).on('resize',function(e) {
		runDown.cancel();
		runDown	=	new setFader();
		runDown.show();
		runDown.countDown(4000);
	});
});
</script>