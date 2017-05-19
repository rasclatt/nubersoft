<?php
namespace nPlugins\Nubersoft;

class ComponentWidget extends \Nubersoft\nRender
	{
		protected	$table,
					$action_type,
					$set_comp_type;
		
		protected	function getCompData($key=false)
			{
				if(!empty($key))
					return (isset($this->data[$key]))? $this->data[$key] : false;
				
				return $this->data;
			}
		/*
		**	@description	Sets what the component should send for an action.
		**					The default is to prorcess from the "components" table
		*/
		public	function setActionType($action_type)
			{
				$this->action_type	=	$action_type;
				return $this;
			}
		/*
		**	@description	Gets the assigned action
		*/
		public	function getActionType()
			{
				return (!empty($this->action_type))? $this->action_type : 'nbr_save_edits';
			}
		/*
		**	@description	Gets the table name
		*/
		public	function getTable()
			{
				return (!empty($this->table))? $this->table : 'components';
			}
		/*
		**	@description	Sets the table name to use in the component
		*/
		public	function useTable($table)
			{
				$this->table	=	$table;
				return $this;
			}
		/*
		**	@description	Sets which table to draw the input layouts from. Default is "components"
		**					"main_menus" will set the options for that menuset
		*/
		public	function useComponentMap($type)
			{
				$this->set_comp_type	=	$type;
				return $this;
			}
			
		public	function getFormatType()
			{
				return (!empty($this->set_comp_type))? $this->set_comp_type : "components";
			}
		
		public	function getComponentData($settings = false)
			{
				$this->table	=	(!empty($settings['table']))? $settings['table'] : $this->getTable();
				$formatting		=	(!empty($settings['format']))? $settings['format'] : $this->getFormatType();
				# Fetch the values from the query
				$query		=	$this->nQuery();
				# Fetch the formatting for the table columns
				$result['format']	=	$this->getTableFormatting(array("table"=>$this->table));
				if(empty($result['format']))
					return false;
				$columns			=	array_keys($result['format']);
				# Fetch the drop downs pertaining to the table columns
				$options			=	$this->organizeByKey($query
											->select(array("assoc_column","menuName","menuVal"))
											->from("dropdown_menus")
											->wherein("assoc_column",$columns)
											->orderBy(array("page_order"=>"ASC"))
											->fetch(),"assoc_column",array('unset'=>false,'multi'=>true));
														
				$layout				=	$this->organizeByKey($query
											->select(array("component_value",'variable_type'))
											->from("component_builder")
											->where(array("component_name"=>$formatting,"page_live"=>"on"))
											->orderBy(array("page_order"=>"ASC","component_value"=>"ASC"))
											->fetch(),'variable_type',array('unset'=>false,'multi'=>true));
				
				if(is_array($layout))
					ksort($layout);
				
				$result['layout']	=	$layout;
				$result['options']	=	$options;
		
				return $result;
			}
		
		public	function getTableFormatting($settings = false)
			{
				$column		=	(!empty($settings['column']))? $settings['column'] : false;
				$table		=	(!empty($settings['table']))? $settings['table'] : $this->getTable();
				# Get columns from the table
				$columns	=	$this->getTableColumns($table);
				# Check if there are any special form options
				$formatted	=	$this->organizeByKey($this->nQuery()
									->select(array("column_name","column_type","size"))
									->from("form_builder")
									->wherein("column_name",$columns)
									->fetch(),"column_name",array('unset'=>false));
				# If there are no columns in table return false
				if(empty($columns))
					return false;
				# If there are no special columns, return columns
				if($formatted == 0)
					return $column;
				# Get the difference between total cols vs formatted cols
				$diff		=	array_diff($columns,array_keys($formatted));
				# Create a blank array to return same-result with blank as filled array
				$blank		=	array_fill(0,count($diff),array("column_name"=>"","column_type"=>"text","size"=>"100%"));
				# Merge the blank with the filled
				$arr		=	array_merge($formatted,array_combine($diff,$blank));
				# Sort the array
				ksort($arr);
				# Rerturn final array
				return $arr;	
			}
		
		public	function getTableColumns($alldata = false)
			{
				$table		=	$this->getTable();
				$newCols	=	array();
				# Fetch columns in table
				$columns	=	$this->nQuery()->query("describe ".$table)->getResults();
				
				if(!$alldata && is_array($columns)) {
					# Loop results, store column name
					foreach($columns as $cols) {
						$newCols[]	=	$cols['Field'];
					}
				}
				else {
					$newCols	=	$this->organizeByKey($columns, 'Field');				
				}
					
				return array_keys($newCols);
			}
	}