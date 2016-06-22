<?php
	class	FileMaster
		{
			protected	static	$singleton;
			protected	$filename;
			protected	$resource;
			public		$fileinfo;
			
			public	function __construct()
				{
					if(!self::$singleton)
						self::$singleton	=	$this;
					
					return self::$singleton;
				}
			
			public	function Initialize()
				{
					$this->resource	=	finfo_open(FILEINFO_MIME_TYPE);
					
					return $this;
				}
			
			public	function GetInfo($filename = false)
				{
					if(isset($this->resource)) {
						$this->filename	=	($filename != false)? $filename : false;
						AutoloadFunction('get_file_type');
						
						$this->fileinfo	=	get_file_type($filename,$this->resource);
					}
						
					return $this;
				}
			
			public	function ThumbnailPreview()
				{
					if(isset($this->resource)) {
						if(!empty($this->filename)) {
							AutoloadFunction('render_thumb_previewer');
							return	(object) render_thumb_previewer($this->filename,$this->resource);
						}
					}
						
					return false;
				}
		}