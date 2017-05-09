<?php
	function code_markup($string = false,$ignoretick = false)
		{
			if(!function_exists("markup_temp")) {
					function markup_temp($matches)
						{
							$matches[0]	=	Safe::decode($matches[0]);
							
							return '<code class="te-markup">'.str_replace(array("if","else","{","}"),array('<span class="te-ie">if</span>','<span class="te-ie">else</span>','<span class="te-brackets">{</span>','<span class="te-brackets">}</span>'),preg_replace_callback('/[a-zA-Z0-9\_]{1,}\(|\"([^\"\<]{1,})\"|\'[^\'\<]{1,}\'|\$[a-zA-Z0-9\_]{1,}|function\s|\<[^\<\s]{1,}|[^\=][\>\?\/]{1,2}/',"markup_formatter", $matches[0])).'</code>';
						}
				}
				
			if(!function_exists("markup_formatter")) {
					
					function markup_formatter($matches)
						{
							if(strpos($matches[0],"<?php") !== false)
								$format	=	preg_replace('/\<\?php/','<span class="te-phptag"><</span><span class="te-phptag">?php</span>',$matches[0]);
							elseif(strpos($matches[0],"<") !== false)
								$format	=	'<span class="te-tag"><</span><span class="te-tag">'.str_replace("<","",$matches[0])."</span>";
							elseif(strpos($matches[0],'?>') !== false)
								$format	=	preg_replace('/\?>/','<span class="te-phptag">?</span><span class="te-phptag">></span>',$matches[0]);
							elseif(strpos($matches[0],'>') !== false)
								$format	=	'<span class="te-tag">'.$matches[0]."</span>";
							elseif(strpos($matches[0],"(") !== false && strpos($matches[0],'"') === false && strpos($matches[0],"'") === false) 
								$format	=	'<span class="te-arr-wrp">'.str_replace(array("array","("),array('<span class="te-arr">array</span>','<span class="te-par">(</span>'),$matches[0])."</span>";
							elseif(strpos($matches[0],'$') !== false)
								$format	=	'<span class="te-ie">'.$matches[0].'</span>';
							elseif(strpos($matches[0],"(") !== false && (strpos($matches[0],'"') !== false || strpos($matches[0],"'") !== false))
								$format	=	'<span class="te-quote">'.preg_replace_callback('/\"[^\"]{1,}\"|\'[^\']{1,}\'/',"markup_quotes", $matches[0])."</span>";
							elseif(strpos($matches[0],"'") !== false || strpos($matches[0],'"') !== false)
								$format	=	'<span class="te-str">'.$matches[0].'</span>';
							elseif(strpos($matches[0],"function") !== false)
								$format	=	'<span class="te-str">'.$matches[0].'</span>';
							else
								$format	=	$matches[0];
							
							return (isset($format))? $format:"";
						}
				}
			
			if(!function_exists("markup_quotes")) {
					function markup_quotes($matches)
						{
							$format	=	'<span style="color: #990000;">'.preg_replace('/(\'[^\']{1,}\')|(\'[^\']{1,}\')/','<span style="color:#888;">$1</span>',$matches[0])."</span>";
							return $format;
						}
				}
			
			$preg	=	($ignoretick)? '/.*/':'/\`([^\`]{1,})\`/';	
			
			ob_start();
			echo $value	=	preg_replace('/(global)|(define)|(echo)|(print_r)|(\-\>)|(\[)|(\])|(__[A-Z]{1,}__)/i','<span class="te-const">$1$2$3$4$5$6$7$8$9</span>',Safe::decodeForm(str_replace("`","",preg_replace_callback($preg,'markup_temp',$string))));
			
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>