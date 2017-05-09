<?php
namespace nPlugins\Nubersoft;

class PostEngine extends \Nubersoft\nRender
	{
		public		$posts,
					$linkage,
					$display;
		
		protected	$permissions,
					$active;
		
		private		$settings,
					$bEngine,
					$ref_page,
					$ref_spot,
					$allow_subs;
		
		const	DEFAULT_TABLE	= 'components';
		
		public	function __construct($ref_page,$ref_spot)
			{
				$this->ref_spot		=	$ref_spot;
				$this->ref_page		=	(!empty($ref_page))? $ref_page : $this->getPage('ID');
				$this->allow_subs	=	false;
				$this->bEngine		=	false;
				$bSettings			=	($this->isAdmin())? array("ref_spot"=>$this->ref_spot,"ref_page"=>$this->ref_page) : array("page_live"=>"on","ref_spot"=>$this->ref_spot,"ref_page"=>$this->ref_page);//,"parent_id"=>""
				$bCol				=	array("content","ID","unique_id","parent_id");
				$this->settings		=	array("columns"=>$bCol,"constraints"=>$bSettings);
				
				return parent::__construct();
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
				
				$this->autoloadContents($this->toSingleDs(NBR_ROOT_DIR.DS.$this->getDefaultTemplate().DS.'plugins'.DS.'admintool'.DS.'functions'.DS));
				
				pagination_initialize($this->settings);
				
				return $this;
			}
		
		public	function view($aPerms = 2,$pPerms = 3)
			{
				$pagination		=	(!empty($this->getDataNode('pagination')->data->results))? $this->getDataNode('pagination')->data->results : array();
				$this->posts	=	$this->organizeByKey($this->toArray($pagination),'ID');
				$this->active	=	false;
				
				if(!empty($this->posts) && is_array($this->posts)) {
					$ids			=	array_keys($this->posts);
					$this->active	=	$this->organizeByKey($this->nQuery()->query("select `page_live`,`ID` from components where `ID` in (".implode(",",$ids).")")->getResults(),'ID');
				}
				
				$this->linkage	=	$this->getTreeStructure($this->posts);
				if(empty($this->linkage)) {
					if(!$pPerms || ($pPerms && $this->isLoggedIn(array("usergroup"=>$pPerms)))) {
						echo $this->replyForm();
					}
				}
				
				$this->permissions['aPerms']	=	$aPerms;
				$this->permissions['pPerms']	=	$pPerms;
				
				$this->displayBlog($this->linkage);
				
				if(!$pPerms || ($pPerms && $this->isLoggedIn(array("usergroup"=>$pPerms)))) {
					echo $this->replyForm();
				}
			}
		
		public	function fetchPosts($ids = false)
			{
				$vals['ref_spot']	=	$this->ref_spot;
				$query				=	$this->nQuery();
				$get_posts			=	$query	->select(array("ID","unique_id","ref_page","parent_id","content"))
												->from("components");

				if(is_array($ids) && !empty($ids))
					$get_posts->wherein("ID",$ids)->addCustom("and ref_spot = '".$vals['ref_spot']."'");
				else
					$get_posts->where($vals);
				
				$this->posts		=	$get_posts->fetch();
				
				return $this;
			}
		
		public	function fetchPostsByParent($parent = false)
			{
				$this->linkage	= false;
				
				if(!is_numeric($parent)) {
					$this->posts	=	0;
					return $this;
				}
				
				$vals['ref_spot']	=	"blog";
				$vals['ref_anchor']	=	$parent;
				
				$query			=	$this->nQuery();
				$data			=	$query	->select(array("ID","unique_id","ref_page","parent_id","content"))
											->from("components")
											->where($vals)
											->fetch();
				
				$this->posts	=	$this->organizeByKey($data,'unique_id');
				$this->linkage	=	$this->getTreeStructure($this->posts);
				
				return $this;
			}
		
		public	function displayBlog($array = array(), $skey = false)
			{
				if(!is_array($array))
					return;
				
				foreach($array as $key => $value) {
					include(__DIR__.DS.'PostEngine'.DS.__FUNCTION__.'.php');
				}
			}

		public	function prepare($settings = false)
			{
				$recurse	=	(!empty($settings['subread']))? $settings['subread']:true;
				$array		=	(isset($this->linkage) && is_array($this->linkage))? $this->linkage:array();
				ob_start();
				if($recurse)
					$this->displayBlog($array);
				$data	=	ob_get_contents();
				ob_end_clean();
				$this->display	=	$data;
				return $this;
			}
		
		public	function mTools($payload)
			{
				return $this->render(__DIR__.DS.'PostEngine'.DS.__FUNCTION__.'.php',$payload);
			}
		
		public	function replyForm($data = false)
			{
				return $this->render(__DIR__.DS.'PostEngine'.DS.__FUNCTION__.'.php',$payload);
			}
	}