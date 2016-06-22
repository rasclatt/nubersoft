<?php
/*Title: read_from_file()*/
/*Description: This file will read a file if exists, then return the contents.*/
/*Settings: $filname(str) - File name*/
/*Settings: $lines(int) - Optional to read just the first X amount of lines in the file.*/

	function read_from_file($filename = false,$lines = false)
		{
			register_use(__FUNCTION__);
			$fw	=	fopen($filename,"r");
			
			if($lines == false) {
					$size	=	filesize($filename);
					$data	=	($size > 0)? trim(fread($fw,$size)):"";
				}
			else {
					$i = 0;
					ob_start();
					
					while(!feof($fw)) {
							echo fgets($fw);
							
							if($lines == $i)
								break;
							
							$i++;
						}
						
					$data	=	ob_get_contents();
					ob_end_clean();
					
				}
			
			fclose($fw);
			return	$data;
		}
?>