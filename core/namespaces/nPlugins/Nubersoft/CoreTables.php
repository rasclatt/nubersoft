<?php
/*
**	Copyright (c) 2017 Nubersoft.com
**	Permission is hereby granted, free of charge *(see acception below in reference to
**	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
**	and associated documentation files (the "Software"), to deal in the Software without
**	restriction, including without limitation the rights to use, copy, modify, merge, publish,
**	or distribute copies of the Software, and to permit persons to whom the Software is
**	furnished to do so, subject to the following conditions:
**	
**	The base CMS software* is not used for commercial sales except with expressed permission.
**	A licensing fee or waiver is required to run software in a commercial setting using
**	the base CMS software.
**	
**	*Base CMS software is defined as running the default software package as found in this
**	repository in the index.php page. This includes use of any of the nAutomator with the
**	default/modified/exended xml versions workflow/blockflows/actions.
**	
**	The above copyright notice and this permission notice shall be included in all
**	copies or substantial portions of the Software.
**
**	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
**	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
**	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
**	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
**	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
**	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
**	SOFTWARE.

**SNIPPETS:**
**	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
**	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
**	NOT BEEN LOCATED AND DELETED.
*/
namespace nPlugins\Nubersoft;

class CoreTables extends \nPlugins\Nubersoft\CoreDatabase
	{
		public	function saveComponent($POST = false, $skip = false)
			{
				# Get post values
				$POST	=	(empty($POST))? $this->toArray($this->getPost()) : $POST;
				# Get the table
				$rTable	=	(!empty($POST['requestTable']))? $POST['requestTable'] : 'components';
				# Sets the update table
				$this->setTable($rTable);
				# See if there is an option to remove empty
				$filter	=	(!empty($POST['action_options']['filter']));
				# See if a thumbnail is required to be created
				$thumb	=	(!empty($POST['action_options']['thumb']));
				# See if there is a token
				$match	=	$this->getHelper('nToken')->getSetToken();
				# If not admin stop
				if(!$this->isAdmin()) {
					$this->saveError($POST['action'],'You must be an admin user.',true);
					return;
				}
				# Allows for skipping form authentication
				if(!$skip) {
					# If token doesn't match, stop
					if($match != $POST['token']['nProcessor']) {
						$this->saveError($POST['action'],'Invalid token',true);
						return;
					}
				}
				# Filter columns/values
				$filtered	=	$this->filterAvailableColumns($POST);
				# If empty set, remove empty fields
				if($filter) {
					$filtered	=	array_filter($filtered);
				}
				
				$files	=	$this->uploadFile();
				$count	=	(is_array($files))? count($files) : 1;
				
				for($i = 0; $i < $count; $i++) {
					$base	=	array();
					$base	=	(is_array($files))? array_merge($filtered, $files[$i]) : $filtered;
					# If there is an id, the update
					if(!empty($POST['ID']))
						$this->updateComponent($base,$POST['ID']);
					else {
						$this->addNewRow($base);
					}
				}
			}
		
		public	function addNewRow($POST)
			{
				# add a timestamp
				if(isset($POST['date_created'])) {
					# By default set timestamp on system save
					if(empty($POST['date_created'])) {
						# Make sure the timezone is already set so it doesn't do a false timezone
						$this->getHelper('GetSitePrefs')->setAppTimeZone();
						# Populate the field
						$POST['date_created']	=	date('Y-m-d H:i:s');
					}
				}
				
				$table				=	$this->getTable();
				$POST				=	array_filter($POST);
				$POST['unique_id']	=	$this->fetchUniqueId();
				$columns			=	array_keys($POST);
				$cols				=	'`'.implode('`, `',$columns).'`';
				$vals				=	':'.implode(', :',$columns);
				try {
					$query	=	$this->getConnection()->prepare("INSERT INTO `{$table}` ({$cols}) VALUES({$vals})");
					$query->execute($this->standardBind($POST));
				}
				catch(\PDOException $e) {
					# If the user is a superuser show full sql
					if($this->isGroupMember('SUPERUSER')){
						$this->toError($e->getMessage());
					}
					# If not a super user
					else {
						# If still admin
						if($this->isAdmin()) {
							# make more readable if duplicate
							if(stripos($e->getMessage(),'Duplicate entry') !== false) {
								$getKey	=	preg_match("/for key '([^']{1,})'/",$e->getMessage(),$matched);
								$this->toError('You can not have a duplicate '.$matched[1],'other_db');
							}
							else
								# Do raw error
								$this->toError($e->getMessage());
						}
						else {
							# Make general error for user benefit
							$this->toError('An error occurred. It is likely this error will not be resolved. Please contact support.');
						}
					}
				}
			}
		
		public	function saveComponentById($id = false,$content = false)
			{
				$content	=	($content !== false)? $content : $this->getPost('data')->html;
				$id			=	(!empty($id) && is_numeric($id))? $id : $this->getPost('data')->deliver->ID;
				
				if(!is_numeric($id))
					return false;
				
				$this->nQuery()->query("UPDATE `components` SET `content` = :0 WHERE `ID` = :1",array($content,$id));
				
				if(!$this->isAjaxRequest())
					die(json_encode(array("alert"=>"compnent")));
				else
					return true;
			}
	}