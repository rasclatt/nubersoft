<?php
class ImageFactory
    {
		public      $destination,
					$error;
		
        protected   $original,
					$searchfor_filename,
					$searchfor_location,
					$filesize;
		
		protected	static	$singleton;
        
		const	SMALL_INPUT		=	1000000;
		const	MEDIUM_INPUT	=	10000000;
		const	LARGE_INPUT		=	100000000;
		const	MAX_INPUT		=	10000000000000000;
		
		public	function __construct()
			{
				if(!isset(self::$singleton))
					self::$singleton	=	$this;
				
				return self::$singleton;
			}
			
		public	function SetFileSize()
			{
				$args_count	=	func_num_args();
				
				if($args_count > 0)
					$args	=	func_get_args();
				
				$this->max_filesize	=	(isset($args))? $args[0]: self::SMALL_INPUT;
				
				return $this;	
			}
		
		public  function FetchOriginal($file)
            {
                $size                       =   getimagesize($file);
                $this->original['width']    =   $size[0];
                $this->original['height']   =   $size[1];
                $this->original['type']     =   $size['mime'];
                return $this;
            }
		
		public	function ScrapThumbnails()
			{
				if(!empty($this->searchfor_filename)) {
					if(!empty($this->searchfor_location)) {
						foreach($this->searchfor_filename as $filename) {
							if(in_array($filename, $this->searchfor_location)) {
								unlink($filename);
								if(is_file($filename))
									$this->error[]	=	'Deleted: '.$filename;
							}
						}
					}
				}
				
				return $this;
			}
		
        public  function Thumbnailer($thumb_target = '', $width = 60,$height = 60,$SetFileName = false, $quality = 80)
            {	
				$this->ScrapThumbnails();
				
				$this->max_filesize	=	(isset($this->max_filesize))? $this->max_filesize : self::SMALL_INPUT;
				if($this->max_filesize < filesize($thumb_target)) {
					die(printpre($this->max_filesize));
					return false;
				}
                // Set original file settings
                $this->FetchOriginal($thumb_target);
                // Determine kind to extract from
                if($this->original['type'] == 'image/gif')
                    $thumb_img  =   imagecreatefromgif($thumb_target);
                elseif($this->original['type'] == 'image/png') {
                        $thumb_img  =   imagecreatefrompng($thumb_target);
                        $quality    =   7;
                    }
                elseif($this->original['type'] == 'image/jpeg')
                        $thumb_img  =   imagecreatefromjpeg($thumb_target);
                else
                    return false;
				
                // Assign variables for calculations
                $w  =   $this->original['width'];
                $h  =   $this->original['height'];
                // Calculate proportional height/width
                if($w > $h) {
                        $new_height =   $height;
                        $new_width  =   floor($w * ($new_height / $h));
                        $crop_x     =   ceil(($w - $h) / 2);
                        $crop_y     =   0;
                    }
                else {
                        $new_width  =   $width;
                        $new_height =   floor( $h * ( $new_width / $w ));
                        $crop_x     =   0;
                        $crop_y     =   ceil(($h - $w) / 2);
                    }
                // New image
                $tmp_img = imagecreatetruecolor($width,$height);
                // Copy/crop action
                imagecopyresampled($tmp_img, $thumb_img, 0, 0, $crop_x, $crop_y, $new_width, $new_height, $w, $h);
                // If false, send browser header for output to browser window
                if(!$SetFileName)
                    header('Content-Type: '.$this->original['type']);
                // Output proper image type
                if($this->original['type'] == 'image/gif')
                   ($SetFileName)? imagegif($tmp_img, $SetFileName) : imagegif($tmp_img);
                elseif($this->original['type'] == 'image/png')
                    ($SetFileName)? imagepng($tmp_img,$SetFileName,$quality) : imagepng($tmp_img);
                elseif($this->original['type'] == 'image/jpeg')
                    ($SetFileName)? imagejpeg($tmp_img, $SetFileName, $quality) : imagejpeg($tmp_img);
                // Destroy set images
                if(isset($thumb_img))
                    imagedestroy($thumb_img); 
                // Destroy image
                if(isset($tmp_img))
                    imagedestroy($tmp_img);
            }
		
		public	function SearchFor($filename = false)
			{
				if(empty($filename))
					return $this;
				
				$this->searchfor_filename[]	=	$filename;
				
				return $this;
			}
		
		public	function SearchLocation($dir = false)
			{
				if(empty($dir))
					return $this;
					
				if(!function_exists("get_directory_list"))
					AutoloadFunction('get_directory_list');
				
				$directory					=	get_directory_list(array("dir"=>$dir));
				
				if(isset($directory['host'])) {
					if(isset($this->searchfor_location) && is_array($this->searchfor_location))
						$this->searchfor_location	=	array_merge($this->searchfor_location,$directory['host']);
					else
						$this->searchfor_location	=	$directory['host'];
				}
					
				return $this;
			}
    }