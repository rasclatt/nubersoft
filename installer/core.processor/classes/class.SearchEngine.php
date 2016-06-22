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
			
			public	function __construct($table = false, $admintoggle = false)
				{
					register_use(__METHOD__);
					AutoloadFunction('nQuery,is_admin');
					$this->nubquery		=	nQuery();
					$this->table		=	($table == false || empty($table))? NubeData::$settings->table : $table;
					$this->admintoggle	=	($admintoggle && is_admin());
				}
			
			public	function FilterColumns($compare = array(), $addlist = false)
				{
					register_use(__METHOD__);
					
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
								$filter	=	array_push($filter,$addlist);
							
							$filter		=	array_unique($filter);
							return array_diff($compare,$filter);
						}
					else
						return array();
				}
			
			public	function Fetch($settings = array())
				{
					register_use(__METHOD__);
						
					$method		=	(!isset($settings['method']))? 'get':strtolower($settings['method']);
					$def_limit	=	(!isset($settings['limit']))? 10 : $settings['limit'];
					$spread		=	(!isset($settings['spread']))? 4 : $settings['spread'];
					$sort		=	(!isset($settings['sort']))? 'ASC': $settings['sort'];
					$orderby	=	(!isset($settings['order']))? 'ID' : $settings['order'];
					
					if(isset($settings['columns']) && !empty($settings['columns']))
						$this->columns_allowed	=	(!is_array($settings['columns']))? explode(",",$settings['columns']) : $settings['columns'];
					
					$this->columns_allowed	=	(!isset($this->columns_allowed))? "*":$this->columns_allowed;
					// If admin allowed, and user is admin
					$constraints			=	($this->admintoggle)? false:array('page_live'=>'on');
					$this->stats['table']	=	$this->table;
					
					// Reset all counts
					$this->numrows	=	0;
					$this->results	=	0;
					$this->columns	=	0;
					$this->s_count	=	0;
					$this->spread	=	$spread;
					
					// If the query engine is available, go at it.
					if($this->nubquery != false) {
							// Method to do search by
							if($method == 'post')
								$req	=	$_POST;
							elseif($method == 'request')
								$req	=	$_REQUEST;
							else
								$req	=	$_GET;
							
							// Limit count
							$this->limit			=	(isset($req['max']) && is_numeric($req['max']))? $req['max']: $def_limit;
							// Save search word
							$this->searchword		=	(isset($req['search']))? $req['search']:false;
							
							// Retrieve columns to search from
							$this->columns	=	(!is_array($this->columns_allowed))? $this->nubquery->tableExists($this->table)->columns_in_table:$this->columns_allowed;
							
							if($constraints != false) {
									foreach($constraints as $col => $val) {
											$const[]	=	$col." = '".$val."'";
										}
								}
							
							$this->constr_string	=	(isset($const))? 'and '.implode(" and ",$const):"";
							$this->constr_array		=	$constraints;
							
							// Count total rows with search result
							if(isset($req['search'])) {
									$c_query	=	$this->nubquery;
									$c_query	->select("COUNT(*) as count")
												->from($this->table)
												->like(array('columns'=>$this->FilterColumns($this->columns),'like'=>$req['search']));
												
									if($constraints != false)				
										$c_query->addCustom($this->constr_string);
									
									$count	=	$c_query->fetch();
								}
							else {
									// Count total rows in table
									$c_query	=	$this->nubquery;
									$c_query	->select("COUNT(*) as count")
												->from($this->table);
												
									if($constraints != false)
										$c_query->where($constraints);
										
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
					register_use(__METHOD__);
					
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
							$searcher	=	$this->nubquery	->select($this->columns_allowed)
															->from($this->table);
															
							$_isAdmin	=	($this->admintoggle)? true:false;
							
							// Set the search for the table
							if(isset($req['search'])) {
									// Add common like addition
									$searcher->like(array('columns'=>$this->FilterColumns($this->columns),'like'=>$req['search']));
									// If not admin, add the page_live restriction
									if(!$_isAdmin)
										$searcher->addCustom($this->constr_string);
								}
							else {
									// If not admin, add restriction
									if(!$_isAdmin)
										$searcher->where($this->constr_array);
								}
							
							// Add limit and order by
							$searcher->limit($this->limit,$page)->orderBy(array($orderB=>$orderH));
							// Assign results
							$searched	=	$searcher->fetch(ConstructMySQL::FETCH_OBJECT);
							// After search, results array
							$this->results		=	$searched->results;
							// After search, row count
							$this->s_count		=	$searched->rowCount;
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