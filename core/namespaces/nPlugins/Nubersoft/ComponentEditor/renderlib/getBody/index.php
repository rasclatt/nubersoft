<?php
use \Nubersoft\UserEngine as User;
$User	=	new User();
$Safe	=	$this->safe();
$this->addHeadPrefAttr('title','Component Editor');
echo $this->getHeader('frontend',array('link'=>realpath(__DIR__.DS.'..').DS.'getHeader'.DS.'index.php'));
$usergroup	=	(!empty($this->useData['ID'][0]['login_permission']))? $this->useData['ID'][0]['login_permission'] : false;
if(empty($usergroup) || !$User->isAllowed($usergroup)): ?>
<div class="col-count-3 offset">
	<div class="col-2" style="background-color: #FFF; padding: 30px;">
		<h1>Error 403</h1>
		<p>You are not allowed to edit this content.</p>
	</div>
</div>
<?php 
return false;
endif ?>

<body class="nbr nbr_ux_element">
	<div id="nbr_maincontent">
		<div id="editor_component">
			<div id="editor_tool_bar">
				<ul id="editor_tool_buttons">
					<li id="nbr_save_component">SAVE</li>
					<li id="nbr_close_component" onClick="window.close()">CLOSE</li>
					<li id="nbr_close_component">A<div style="display: inline;font-size: 10px;">A</div><input id="slider1" type="range" min="1" max="100" step="1" /><div style=" float: right; font-size: 13px; display: inline-block; position: absolute; top: 10px; right: 0;font-weight: bold; padding: 5px; background-color: #FFF;" id="nbr_font_size"></div></li>
				</ul>
			</div>
			<div id="editor_body">
				<form id="nbr_component_editor_content">
					<input type="hidden" name="ID" value="<?php echo $this->useData['ID'][0]['ID']; ?>" />
					<input type="hidden" name="action" value="nbr_save_single_editor" />
					<input type="hidden" name="token[nProcessor]" value="" />
					<textarea name="content" class="textarea nbr_text_editor"><?php echo $this->useData['ID'][0]['content']; ?></textarea>
				</form>
			</div>
		</div>
		<?php echo $this->getFooter('frontend',array('link'=>realpath(__DIR__.DS.'..').DS.'getFooter'.DS.'index.php')); ?>
	</div>
</body>
</html>