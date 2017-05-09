<?php
	class SearchEngine
		{
			public		$numrows;
			public		$totalpages;
			public		$currentpage;
			public		$results;
			public		$s_count;
			public		$stats;
			public		$table_permission;
			public		$columns;
			
		//	protected	$permissions;
			protected	$table;
			protected	$sql;
			protected	$sql_mod;
			protected	$maxcount;
			protected	$limit;
			protected	$nuber;
			protected	$nubsql;
			protected	$nubquery;
			protected	$spread;
			protected	$searchword;
			protected	$admintoggle;
			protected	$constr_string;
			protected	$constr_array;
			protected	$columns_allowed;
			
			private		$constraints;
			
			public	function __construct($table = false, $admintoggle = false)
				{
					AutoloadFunction('nQuery,is_admin');
					$this->nubquery		=	nQuery();
					$this->table		=	(empty($table))? nApp::getDefaultTable() : $table;
					$this->admintoggle	=	($admintoggle && is_admin());
				}
			
			public	function FilterColumns($compare = array(), $addlist = false)
				{
					if(is_array($compare)) {
						// List all columns skip searching in
						$filter[]	=	'core_setting';
						$filter[]	=	'ID';
						$filter[]	=	'page_live';
						$filter[]	=	'page_order';
						$filter[]	=	'password';
						$filter[]	=	'timestamp';
						$filter[]	=	'usergroup';
						$filter[]	=	'user_status';
						
						// If an extra array is added, push to end.
						if(is_array($addlist))
							$filter	=	array_merge($filter,$addlist);
						
						
						
						$filter		=	array_unique($filter);
						return array_diff($compare,$filter);
					}
					else
						return array();
				}
			
			public	function addConstraints($array)
				{
					if(!is_array($array))
						return $this;
					
					$this->constraints	=	$array;
					
					return $this;
				}
			
			public	function fetch($settings = array())
				{
					$method		=	(!isset($settings['method']))? 'get':strtolower($settings['method']);
					$def_limit	=	(!isset($settings['limit']))? 10 : $settings['limit'];
					$spread		=	(!isset($settings['spread']))? 4 : $settings['spread'];
					$sort		=	(!isset($settings['sort']))? 'ASC': $settings['sort'];
					$orderby	=	(!isset($settings['order']))? 'ID' : $settings['order'];
					
					if(!empty($settings['columns']))
						$this->columns_allowed	=	(!is_array($settings['columns']))? explode(",",$settings['columns']) : $settings['columns'];

					$this->columns_allowed	=	(!isset($this->columns_allowed))? "*":$this->columns_allowed;
					
					if(empty($this->constraints))
						// If admin allowed, and user is admin
						$this->constraints	=	($this->admintoggle)? false : array('page_live'=>'on');
		
					$this->stats['table']	=	$this->table;
					
					// Reset all counts
					$this->numrows	=	0;
					$this->results	=	0;
					$this->columns	=	0;
					$this->s_count	=	0;
					$this->spread	=	$spread;
					
					// If the query engine is available, go at it.
					if(nApp::siteValid()) {
						// Method to do search by
						if($method == 'post')
							$req	=	Safe::to_array(nApp::getPost());
						elseif($method == 'request')
							$req	=	Safe::to_array(nApp::getRequest());
						else
							$req	=	Safe::to_array(nApp::getGet());
						
						// Limit count
						$this->limit			=	(isset($req['max']) && is_numeric($req['max']))? $req['max']: $def_limit;
						// Save search word
						$this->searchword		=	(isset($req['search']))? $req['search']:false;
						// Retrieve columns to search from
						$this->columns	=	(!is_array($this->columns_allowed))? nApp::getColumns($this->stats['table']) : $this->columns_allowed;
						
						if(!empty($this->constraints)) {
							foreach($this->constraints as $col => $val) {
								$const[]	=	$col." = '".$val."'";
							}
						}
						// This converts possible entries to an array instead of an object
						$this->columns			=	Safe::to_array($this->columns);
						$this->constr_string	=	(!empty($const))? 'and '.implode(" and ",$const):"";
						$this->constr_array		=	$this->constraints;
						$c_query				=	nQuery();
						// Count total rows with search result
						if(!empty($this->searchword)) {
							$c_query	->select("COUNT(*) as count")
										->from($this->table)
										->like(array('columns'=>$this->FilterColumns($this->columns),'like'=>$this->searchword));
							
							if(!empty($const))				
								$c_query->addCustom($this->constr_string);
							
							$count	=	$c_query->fetch();
						}
						else {
							$c_query	->select("COUNT(*) as count")
										->from($this->table);
										
							if(!empty($const))
								$c_query->where($this->constraints);
								
							$count	=	$c_query->fetch();
						}
						// Assign total rows found
						$this->numrows	=	$count[0]['count'];
						// Retrieve arrays
						$this->CalculateVals($req,$orderby,$sort);
					}
					
					$this->stats	=	(!isset($this->stats))? false:$this->stats;
					
					return $this;
				}
			
			protected	function CalculateVals($req,$orderB = 'ID',$orderH = 'ASC')
				{
					AutoloadFunction('create_query_string');
					
					if($this->numrows > 0) {
							$formula			=	$this->numrows / $this->limit;
							$this->totalpages	=	ceil($formula);
							// get the current page or set a default
							$this->currentpage	=	(isset($req['currentpage']) && is_numeric($req['currentpage']))? (int) $req['currentpage']: 1;
							
							// if current page is greater than total pages...
							if ($this->currentpage > $this->totalpages) {
								// set current page to last page
								$this->currentpage = $this->totalpages;
							}
							// if current page is less than first page...
							if ($this->currentpage < 1) {
							   // set current page to first page
							   $this->currentpage = 1;
							}
								
							// the offset of the list, based on current page 
							$page 		=	($this->currentpage - 1) * $this->limit;
							// Create base query
							$searcher	=	nQuery()	->select($this->columns_allowed)
													->from($this->table);
							$_isAdmin	=	($this->admintoggle);
							
							// Set the search for the table
							if(isset($req['search'])) {
									// Add common like addition
									$searcher->like(array('columns'=>$this->FilterColumns($this->columns),'like'=>$req['search']));
									// If not admin, add the page_live restriction
									if(!$_isAdmin)
										$searcher->addCustom($this->constr_string);
									
									//echo (printpre($this->FilterColumns($this->columns),'FILTER').printpre($this->constr_string,'CONSTR').printpre($searcher,'SEARCHER'));
								}
							else {
									// If not admin, add restriction
									if(!$_isAdmin)
										$searcher->where($this->constr_array);
								}
							
							// Add limit and order by
							$searcher->limit($this->limit,$page)->orderBy(array($orderB=>$orderH));
							// Assign results
							$searched			=	$searcher->fetch('obj');
							
							// After search, results array
							$this->results		=	(isset($searched->results))? $searched->results : array();
							// After search, row count
							$this->s_count		=	(isset($searched->rowCount))? $searched->rowCount : 0;
						}
					
					$this->stats['query'][]			=	(isset($req['search']))? "search=".$req['search']:"";
					$this->stats['query'][]			=	"requestTable=".urlencode($this->stats['table']);
					$this->stats['query'][]			=	(isset($req['max']))? "max=".urlencode($req['max']):"";
					$this->stats['query']			=	trim(implode("&",$this->stats['query']),"&");
					// Total pages containing results list
					$this->stats['total']			=	(isset($this->totalpages))? $this->totalpages:0;
					// Max amount per page
					$this->stats['limit']			=	(isset($this->limit))? $this->limit:0;
					// Current Page
					$this->stats['current']			=	(isset($this->currentpage))? $this->currentpage:0;
					// Total row count
					$this->stats['count']			=	(isset($this->s_count))? $this->s_count:0;
					// Results
					$this->stats['results']			=	(isset($this->results))? $this->results:0;
					// Next page + $previous page
					$next 							=	($this->stats['current'] + 1);
					$previous						=	($this->stats['current'] - 1);
					// Assign Next
					$this->stats['next']			=	($next > $this->stats['total'])? false:$next;
					// Assign Previous
					$this->stats['previous']		=	($previous <= 0)? 1:$previous;
					// Last							
					$this->stats['last']			=	$this->stats['total'];
					$this->stats['last_link']		=	create_query_string(array("currentpage"),$_GET);
					// Make range for pagination
					$low							=	(int) ($this->stats['current'] - $this->spread);
					$high							=	($this->stats['current'] + $this->spread);
					
					$difference['low']				=	($low <= (int) 0)? str_replace("-","",$low): 0;
					$difference['high']				=	($high >= $this->totalpages)? ($high - $this->totalpages): 0;
					$range['low']					=	($low <= 0)? 1 : ((int) $low - (int) $difference['high']);
					$addhigh						=	((int) $high + (int) $difference['low']);
					$range['high']					=	($addhigh >= $this->totalpages)? $this->totalpages : $addhigh;
					
					if($range['low'] <= 0)
						$range['low']				=	1;
					
					$this->stats['range']			=	($this->totalpages != 0)? range($range['low'],$range['high']):false;
					$this->stats['search']			=	$this->searchword;
				}
		}