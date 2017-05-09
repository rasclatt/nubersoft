<?php
if(!$this->isAdmin())
	return;

$getDefVal	=	function($key,$obj)
	{
		if(isset($obj->data[$key]))
			return $obj->data[$key];
		elseif(isset($obj->getPost('deliver')->query_data->{$key}))
			return $obj->getPost('deliver')->query_data->{$key};
		else
			return false;
	};

$SERVER			=	$this->getDataNode('_SERVER');
$nForm			=	$this->getHelper('nForm');
$nProccessor	=	$this->getHelper('nToken')->setMultiToken('nProcessor','component');
$action			=	((isset($SERVER->HTTP_REFERER))? $SERVER->HTTP_REFERER : '#');
$ref_page		=	$getDefVal('ref_page',$this);

$parAllow		=	array('div','row');
$pageid			=	($ref_page)? $ref_page : $this->data['parent_id'];
$parent			=	(isset($this->data['component_type']) && in_array($this->data['component_type'],$parAllow));
?>
		<div class="component_buttons_wrap nodrag">
			<?php echo $nForm->open(array('action'=>$action, 'enctype'=>"multipart/form-data")) ?>
				<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_save_edits')) ?>
				<?php echo $nForm->fullhide(array('name'=>"token[nProcessor]",'value'=>$nProccessor)) ?>
				<?php echo $nForm->fullhide(array('name'=>'ID','value'=>'')) ?>
				<?php echo $nForm->fullhide(array('name'=>'unique_id','value'=>'')) ?>
				<?php echo $nForm->fullhide(array('name'=>'ref_spot','value'=>$getDefVal('ref_spot',$this))) ?>
				<?php echo $nForm->fullhide(array('name'=>'parent_id','value'=>((!empty($parent))? $this->data['unique_id'] : ''))) ?>
				<input type="hidden" name="ref_page" value="<?php echo $pageid; ?>" />
				<div class="nbr_component_add">
					<label>
						<input disabled="disabled" style="display: none;" type="submit" name="add" value="ADD" />
					</label>
				</div>
			</form>
		</div>