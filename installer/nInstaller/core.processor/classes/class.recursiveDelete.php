<?php

	class	recursiveDelete
		{
			private	$targets;
			
			public function delete($path)
				{
					if(!is_file($path) && !is_dir($path))
						return $this;
					
					$it	=	new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
					
					foreach ($it as $file) {
							try {
									if(in_array($file->getBasename(), array('.', '..')))
										continue;
									elseif($file->isDir())
										rmdir($file->getPathname());
									elseif($file->isFile() || $file->isLink())
										unlink($file->getPathname());
								}
							catch (Exception $e) {
								}
						}
						
					rmdir($path);
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
							$this->delete($this->targets[$i]);
						}
					
					return true;
				}
		}