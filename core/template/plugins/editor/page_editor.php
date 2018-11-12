<?php
if($this->getSession('editor') != 1)
	return false;
elseif(!$this->isAdmin())
	return false;

$page		=	$this->getPage();
$Form		=	$this->getHelper('nForm');
$Page		=	$this->getHelper('Settings\Page\Controller');
$Settings	=	$this->getHelper('Settings\Controller');
?>
<div class="col-2 page-settings-editor">
	<h3 class="page-edit-title"><img src="/core/template/default/media/images/core/led_<?php echo $this->getPage('page_live') ?>.png" style="max-width: 20px; width: auto; display: inline;" />&nbsp;<?php echo $page['menu_name'] ?>&nbsp;&nbsp;<span style="color: red">|</span>&nbsp;&nbsp;<span class="white" id="path-domain"><?php echo $this->getPage('full_path') ?></span><a class="mini-btn green no-bx-shadow no-margin nTrigger" href="#" data-instructions='{"FX":{"event":["click"],"acton":["#page-settings"],"fxspeed":["fast","fast"],"fx":["slideToggle"]}}'>EDIT</a></h3>
	<div class="" style="background: linear-gradient(#666, #999); padding: 0.5em 1em 1em 1em; border-radius: 5px; box-shadow: 2px 2px 8px #000; <?php if($this->getPost('action') != 'update_page'): ?>display: none;<?php endif ?>" id="page-settings">
		<?php echo $Form->open() ?>
			<?php echo $Form->fullhide(["name" => "action", "value" => "update_page", "class" => "nbr"]) ?>
			<?php echo $Form->fullhide(["name" => "token[nProcessor]", "value" => "", "class" => "nbr"]) ?>
			<?php echo $Form->fullhide(["name" => "ID", "value" => $page['ID'], "class" => "nbr"]) ?>
			<?php echo $Form->fullhide(["name" => "unique_id", "value" => $page['unique_id'], "class" => "nbr"]) ?>
			<?php echo $Form->fullhide(["name" => "parent_id", "value" => $page['parent_id'], "class" => "nbr"]) ?>
			<?php echo $Form->fullhide(["name" => "link", "value" => $page['link'], "class" => "nbr"]) ?>
			<div class="col-count-4 gapped med-2 gapped sml-1 gapped page-editor-container">
				
				<div class="span-4 push-col-2 medium push-col-1 small nTrigger pointer page-editor-header" data-instructions='{"FX":{"fx":["hide","accordian"],"acton":[".hide","next::slideDown"],"event":["click","click"],"fxspeed":["fast","fast"]}}'>
					<h3 class="no-margin no-padding">Template Setup</h3>
				</div>
				<div class="span-4 push-col-2 medium push-col-1 small hide" style="display: none;">
					<div class="col-count-4 gapped med-2 gapped sml-1 gapped">
						<div class="span-2 push-col-2 medium push-col-1 small">
							<?php echo $Form->text(['label' => 'Page Title', "name" => "menu_name", "value" => $page['menu_name'], "class" => "nbr", 'other' =>['required="required"']]) ?>
						</div>
						<div class="span-2 push-col-2 medium push-col-1 small">
							<?php echo $Form->text(['label' => 'Slug (URL Path)', "name" => "full_path", "value" => $page['full_path'], "class" => "nbr", 'other' =>['required="required"']]) ?>
						</div>
						<!--
						<?php echo $Form->text(['label' => '', "name" => "group_id", "value" => $page['group_id'], "class" => "nbr"]) ?>
						-->
						<?php echo $Form->select(['label' => 'Page Type', "name" => "is_admin", "options" => [
							['name' => 'Common Page', 'value' => '', 'selected' => ($page['is_admin'] == '')],
							['name' => 'Admin Page', 'value' => 1, 'selected' => ($page['is_admin'] == 1)],
							['name' => 'Home Page', 'value' => 2, 'selected' => ($page['is_admin'] == 2)],
							['name' => 'Login Page', 'value' => 3, 'selected' => ($page['is_admin'] == 3)]
						], "class" => "nbr"]) ?>
						<?php echo $Form->select(['label' => 'Template', "name" => "template", "options" => array_map(function($v) use ($page) {
							return [
								'selected' => ($page['template'] == $v['value']),
								'name' => $v['name'],
								'value' => $v['value']
							];
						}, $Page->getTemplateList()), "class" => "nbr"]) ?>
						<?php echo $Form->text(['label' => 'Use Template Sub-page', "name" => "use_page", "value" => $page['use_page'], "class" => "nbr"]) ?>
					</div>
				</div>
				
				
				<div class="col-1 span-4 push-col-2 medium push-col-1 small nTrigger pointer page-editor-header" data-instructions='{"FX":{"fx":["hide","accordian"],"acton":[".hide","next::slideDown"],"event":["click","click"],"fxspeed":["fast","fast"]}}'>
					<h3 class="no-margin no-padding">Page Caching</h3>
				</div>
				<div class="span-4 push-col-2 medium push-col-1 small hide" style="display: none;">
					<div class="col-count-4 gapped med-2 gapped sml-1 gapped">
						<div class="col-1">
						<?php echo $Form->select(['label' => 'Cache Page?', "name" => "auto_cache", "options" => [
							['name' => 'Off', 'value' => 'off', 'selected' => ($page['auto_cache'] == 'off')],
							['name' => 'On', 'value' => 'on', 'selected' => ($page['auto_cache'] == 'on')]
						], "class" => "nbr"]) ?>
						</div>
					</div>
				</div>
				
				<div class="col-1 span-4 push-col-2 medium push-col-1 small nTrigger pointer page-editor-header" data-instructions='{"FX":{"fx":["hide","accordian"],"acton":[".hide","next::slideDown"],"event":["click","click"],"fxspeed":["fast","fast"]}}'>
					<h3 class="no-margin no-padding">Activation Settings</h3>
				</div>
				<div class="span-4 push-col-2 medium push-col-1 small hide" style="display: none;">
					<div class="col-count-4 gapped med-2 gapped sml-1 gapped">
						<?php echo $Form->select(['label' => 'Page Live?', "name" => "page_live", "options" => [
							['name' => 'Off', 'value' => 'off', 'selected' => ($page['page_live'] == 'off')],
							['name' => 'On', 'value' => 'on', 'selected' => ($page['page_live'] == 'on')]
						], "class" => "nbr"]) ?>
						<?php echo $Form->select(['label' => 'Activate In Navigation?', "name" => "in_menubar", "options" => [
							['name' => 'Off', 'value' => 'off', 'selected' => ($page['in_menubar'] == 'off')],
							['name' => 'On', 'value' => 'on', 'selected' => ($page['in_menubar'] == 'on')]
						], "class" => "nbr"]) ?>
						<?php echo $Form->text(['label' => 'Navigation Bar Order', "name" => "page_order", "value" => $page['page_order'], "class" => "nbr"]) ?>
					</div>
				</div>
				
				<div class="col-1 span-4 push-col-2 medium push-col-1 small nTrigger pointer page-editor-header" data-instructions='{"FX":{"fx":["hide","accordian"],"acton":[".hide","next::slideDown"],"event":["click","click"],"fxspeed":["fast","fast"]}}'>
					<h3 class="no-margin no-padding">Forwarding Settings</h3>
				</div>
				<div class="span-4 push-col-2 medium push-col-1 small hide" style="display: none;">
					<div class="col-count-4 gapped med-2 gapped sml-1 gapped">
						<?php echo $Form->text(['label' => 'Forward to', "name" => "auto_fwd", "value" => $page['auto_fwd'], "class" => "nbr"]) ?>
						<?php echo $Form->select(['label' => 'Forward After Login?', "name" => "auto_fwd_post", "options" => [
							['name' => 'Off', 'value' => 'off', 'selected' => ($page['auto_fwd_post'] == 'off')],
							['name' => 'On', 'value' => 'on', 'selected' => ($page['auto_fwd_post'] == 'on')]
						], "class" => "nbr"]) ?>
					</div>
				</div>
				
				<div class="col-1 span-4 push-col-2 medium push-col-1 small nTrigger pointer page-editor-header" data-instructions='{"FX":{"fx":["hide","accordian"],"acton":[".hide","next::slideDown"],"event":["click","click"],"fxspeed":["fast","fast"]}}'>
					<h3 class="no-margin no-padding">Permission Settings</h3>
				</div>
				<div class="span-4 push-col-2 medium push-col-1 small hide" style="display: none;">
					<div class="col-count-4 gapped med-2 gapped sml-1 gapped">
						<?php echo $Form->select(['label' => 'Require Login?', "name" => "session_status", "options" => [
							['name' => 'Off', 'value' => 'off', 'selected' => ($page['session_status'] == 'off')],
							['name' => 'On', 'value' => 'on', 'selected' => ($page['session_status'] == 'on')]
						], "class" => "nbr"]) ?>

						<?php echo $Form->select(['label' => 'Usergroup Allowed', "name" => "usergroup", "options" => array_map(function($v) use ($page) {
							if($page['usergroup'] == $v['value'])
								$v['selected']	=	true;

							return $v;

						}, $Settings->getFormAttr('users')['usergroup']['options']), "class" => "nbr"]) ?>
					</div>
				</div>
				
				<div class="col-1 col-count-3 lrg-2 med-1"><?php echo $Form->checkbox(['label' => 'Delete Page?', 'name'=>'delete', 'value' => 'on']) ?></div>
				
				<div class="col-1 span-3 push-col-2 medium push-col-1 small col-count-6 lrg-4 med-2 sml-1">
				<?php echo $Form->submit(["name" => "", "value" => "SAVE", "class" => "nbr medi-btn dark"]) ?>
				</div>
			</div>
		<?php echo $Form->close() ?>
	</div>
	<h3 class="page-edit-title">Page Components</h3>
</div>
<script>
	$(function(){
		$('input[name="full_path"]').on('keyup change',function(){
			$('#path-domain').text($(this).val());
		});
	});
</script>