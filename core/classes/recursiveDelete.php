<?php

	class	recursiveDelete
		{
			private	$targets;
			
			public function delete($path)
				{
					if(!is_file($path) && !is_dir($path))
						return $this;
					
					$it	=	new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
					
					try {
						foreach ($it as $file) {
							if(in_array($file->getBasename(), array('.', '..')))
								continue;
							elseif($file->isDir()) {
								$pathname	=	$file->getPathname();
								if(rmdir($pathname))
									$removed[]	=	$pathname;
								else
									throw new Exception("Could not delete path: {$pathname}");
							}
							elseif($file->isFile() || $file->isLink()) {
								$pathname	=	$file->getPathname();
								if(unlink($pathname)){
									$removed[]	=	$pathname;
								}
								else
									throw new Exception("Could not delete file: {$pathname}");
							}	
						}
						
						\nApp::saveIncidental('delete_cache',array('success'=>true,'paths'=>$removed));
					}
					catch (Exception $e) {
						if(is_admin())
							die($e->getMessage());
						else
							\nApp::saveIncidental('delete_cache',array('success'=>false,'paths'=>false));
					}
					
					if(rmdir($path))
						$removed[]	=	$path;
					
					return (!empty($removed))? $removed : false;
				}
			
			public	function addTarget($path = false)
				{
					if(empty($path))
						return $this;
						
					$this->targets[]	=	$path;
					
					return $this;
				}
				
			public	function deleteAll($execTime = 3000)
				{
					if(empty($this->targets))
						return false;
					
					$count		=	count($this->targets);
					$maxTime	=	($count*$execTime);
					ini_set("max_execution_time",$maxTime);
					
					for($i = 0; $i < $count; $i++) {
						$remove[]	=	$this->delete($this->targets[$i]);
					}
					
					return (!empty($remove))? $remove : false;
				}
		}