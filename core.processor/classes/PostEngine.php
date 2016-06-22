<?php
	class PostEngine
		{
			public		$posts;
			public		$linkage;
			public		$display;
			
			protected	$permissions;
			protected	$active;
			
			private		$settings;
			private		$bEngine;
			private		$ref_page;
			private		$ref_spot;
			private		$allow_subs;
			
			const	DEFAULT_TABLE	= 'components';
			
			public	function __construct($ref_page,$ref_spot)
				{
					AutoloadFunction('PaginationInitialize,PaginationResults,tree_structure_by_id');
					$this->ref_spot		=	$ref_spot;
					$this->ref_page		=	(!empty($ref_page))? $ref_page : nApp::getPage('ID');
					$this->allow_subs	=	false;
					$this->bEngine		=	false;
					$bSettings			=	(is_admin())? array("ref_spot"=>$this->ref_spot,"ref_page"=>$this->ref_page) : array("page_live"=>"on","ref_spot"=>$this->ref_spot,"ref_page"=>$this->ref_page);//,"parent_id"=>""
					$bCol				=	array("content","ID","unique_id","parent_id");
					$this->settings		=	array("columns"=>$bCol,"constraints"=>$bSettings);
				}
			
			public	function useTable($table)
				{
					$this->settings['table']	=	$table;
					return $this;
				}
			
			public	function setAttr($array)
				{
					if(!is_array($array))
						return $this;
						
					$this->settings	=	array_merge($this->settings,$array);
					return $this;
				}
			
			public	function init($allow_subs = false)
				{
					if(!empty($allow_subs))
						$this->allow_subs	=	true;

					PaginationInitialize($this->settings);
					return $this;
				}
			
			public	function view($aPerms = 2,$pPerms = 3)
				{
					$this->posts	=	organize(Safe::to_array(NubeData::$settings->pagination->data->results),'ID');
					$this->active	=	false;
					
					if(!empty($this->posts) && is_array($this->posts)) {
						$ids			=	array_keys($this->posts);
						$this->active	=	organize(nQuery()->query("select `page_live`,`ID` from components where `ID` in (".implode(",",$ids).")")->getResults(),'ID');
					}
					
					$this->linkage	=	tree_structure_by_id($this->posts);
					if(empty($this->linkage)) {
						if(!$pPerms || ($pPerms && is_loggedin(array("usergroup"=>$pPerms)))) {
							echo $this->ReplyForm();
						}
					}
					
					$this->permissions['aPerms']	=	$aPerms;
					$this->permissions['pPerms']	=	$pPerms;
					
					$this->displayBlog($this->linkage);
					
					if(!$pPerms || ($pPerms && is_loggedin(array("usergroup"=>$pPerms)))) {
						echo $this->ReplyForm();
					}
				}
			
			public	function fetchPosts($ids = false)
				{
					$vals['ref_spot']	=	$this->ref_spot;
					$query				=	nQuery();
					$get_posts			=	$query	->select(array("ID","unique_id","ref_page","parent_id","content"))
													->from("components");

					if(is_array($ids) && !empty($ids))
						$get_posts->wherein("ID",$ids)->addCustom("and ref_spot = '".$vals['ref_spot']."'");
					else
						$get_posts->where($vals);
					
					$this->posts		=	$get_posts->fetch();
					
					return $this;
				}
			
			public	function FetchPostsByParent($parent = false)
				{
					AutoloadFunction('tree_structure,organize');
					
					$this->linkage	= false;
					
					if(!is_numeric($parent)) {
						$this->posts	=	0;
						return $this;
					}
					
					$vals['ref_spot']	=	"blog";
					$vals['ref_anchor']	=	$parent;
					
					$query			=	nQuery();
					$data			=	$query	->select(array("ID","unique_id","ref_page","parent_id","content"))
												->from("components")
												->where($vals)
												->fetch();
					
					$this->posts	=	organize($data,'unique_id');
					$this->linkage	= tree_structure($this->posts);
					
					return $this;
				}
			
			public	function displayBlog($array = array(), $skey = false)
				{
					foreach($array as $key => $value) {
?>					<div style=" padding: 5px; border: 1px solid rgb(<?php echo rand(0,255); ?>,<?php echo rand(0,255); ?>,<?php echo rand(0,255); ?>);">
<?php						echo $this->posts[$key]['content'];

							if(is_admin()) {
								echo $this->mTools($this->posts[$key]);
							}

							if($this->allow_subs)
								echo $this->ReplyForm(array("parent_id"=>$this->posts[$key]['ID']));
							
						if(is_array($value)) {
							$this->DisplayBlog($value,$key);
						}
?>					</div>
<?php				}
				}

			public	function prepare($settings = false)
				{
					$recurse	=	(!empty($settings['subread']))? $settings['subread']:true;
					$array		=	(isset($this->linkage) && is_array($this->linkage))? $this->linkage:array();
					ob_start();
					if($recurse)
						$this->DisplayBlog($array);
					$data	=	ob_get_contents();
					ob_end_clean();
					$this->display	=	$data;
					return $this;
				}
			
			public	function mTools($payload)
				{
					echo printpre($payload);

					ob_start();
?>				<form method="post">
					<fieldset class="">
					<input type="hidden" name="requestTable" value="components" />
					<input type="hidden" name="ID" value="<?php echo $payload['ID']; ?>" />
					<input type="hidden" name="unique_id" value="<?php echo $$payload['unique_id']; ?>" />
					<select name="page_live">
						<option value="off">DISABLE</option>
						<option value="on" selected="selected">POST</option>
					</select>
					<textarea name="content"><?php echo $payload['content']; ?></textarea>
					<input type="submit" name="update" value="EDIT" />
					</fieldset>
				</form>
<?php				$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
			
			public	function replyForm($data = false)
				{
					ob_start();
?>				<form method="post">
					<fieldset class="">
					<input type="hidden" name="requestTable" value="components" />
					<?php echo (!empty($data['ID']))? $id['ID'] : ""; ?><br />
					<?php echo (!empty($data['unique_id']))? $id['unique_id'] : ""; ?><br />
					<input type="hidden" name="ID" value="<?php echo (!empty($data['ID']))? $id['ID'] : ""; ?>" />
					<input type="hidden" name="unique_id" value="<?php echo (!empty($data['unique_id']))? $id['unique_id'] : ""; ?>" />
					<input type="hidden" name="ref_page" value="<?php echo $this->ref_page; ?>" /><br />
					<input type="hidden" name="ref_spot" value="<?php echo $this->ref_spot; ?>" />
<?php				if(is_admin()) {
?>					<select name="page_live">
						<option value="off">DISABLE</option>
						<option value="on" selected="selected">POST</option>
					</select>
<?php				}
					else {
?>					<input type="hidden" name="page_live" value="on" />
<?php				}
?>
<?php 				if(!empty($data['parent_id'])) {
?>					<label>Parent</label>
					<input type="text" name="parent_id" value="<?php echo $data['parent_id']; ?>" />
<?php				}
?>					<label>Reply</label>
					<textarea name="content"></textarea>
					<input type="submit" name="<?php echo (!empty($data['ID']))? "update" : "add"; ?>" value="POST" />
					</fieldset>
				</form>
<?php				$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
		}