<?php
	class PostEngine
		{
			public		$posts;
			public		$linkage;
			public		$display;
			
			protected	$nubquery;
			
			public	function __construct()
				{
					AutoloadFunction('nQuery');
					$this->nubquery	=	nQuery();
				}
			
			public	function FetchPosts($ids = false)
				{
					$vals['ref_spot']	=	"blog";
					$query				=	$this->nubquery;
					$get_posts			=	$query	->select(array("ID","unique_id","ref_page","parent_id","content"))
													->from("components");
					
					AutoloadFunction('check_empty');
					if(is_array($ids) && !empty($ids))
						$get_posts->wherein("ID",$ids)->addCustom("and ref_spot = 'blog'");
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
			
			public	function DisplayBlog($array = array(), $skey = false)
				{   
					foreach($array as $key => $value) { ?>
					<div style=" padding: 5px; border: 1px solid rgb(<?php echo rand(0,255); ?>,<?php echo rand(0,255); ?>,<?php echo rand(0,255); ?>);">
						<?php
							echo $this->posts[$key]['content'];
							if(is_array($value)) {
									$this->DisplayBlog($value,$key);
								} ?>
					</div>
					<?php
						}
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
			
			public	function ReplyForm()
				{ ?>
				<form method="post">
					<input type="requestTable" name="components" />
					<input type="text" name="parent_id" />
					
				</form>
					<?php
				}
		}
?>