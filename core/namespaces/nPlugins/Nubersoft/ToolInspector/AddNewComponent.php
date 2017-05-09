<?php
if(!$this->isAdmin())
	return;
$SERVER			=	$this->getDataNode('_SERVER');
$nForm			=	$this->getHelper('nForm');
$nProccessor	=	$this->getHelper('nToken')->setMultiToken('nProcessor','component');
$action			=	((isset($SERVER->HTTP_REFERER))? $SERVER->HTTP_REFERER : '#');
?>
		<div class="component_buttons_wrap">
			<?php echo $nForm->open(array('action'=>$action, 'enctype'=>"multipart/form-data")) ?>
				<?php echo $nForm->fullhide(array('name'=>'action','value'=>'nbr_create_menu')) ?>
				<?php echo $nForm->fullhide(array('name'=>"token[nProcessor]",'value'=>$nProccessor)) ?>
				<input type="hidden" name="ID" />
				<input type="hidden" name="unique_id" />
				<?php
				$parAllow	=	array('div','row');
				$pageid		=	(isset($this->data['ref_page']))? $this->data['ref_page'] : $this->data['parent_id'];
				$parent		=	(isset($this->data['component_type']) && in_array($this->data['component_type'],$parAllow));
				?>
				<input type="hidden" name="parent_id" value="<?php if(!empty($parent)) echo $this->data['unique_id'] ?>" />
				<input type="hidden" name="ref_page" value="<?php echo $pageid; ?>" />
				<div class="nbr_component_add">
					<label>
						<input disabled="disabled" style="display: none;" type="submit" name="add" value="ADD" />
					</label>
				</div>
			</form>
		</div>