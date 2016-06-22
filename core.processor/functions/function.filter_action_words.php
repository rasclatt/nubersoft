<?php
/*Title: filter_action_words()*/
/*Description: This function creates `associative` or `value` `array` to match against/filter out.*/

	function filter_action_words($type = 'vals')
		{
			
			if($type == 'vals') {
					$filter[]	=	'requestTable';
					$filter[]	=	'action';
					$filter[]	=	'command';
					$filter[]	=	'add';
					$filter[]	=	'update';
					$filter[]	=	'delete';
					$filter[]	=	'thumbnail';
					$filter[]	=	'keep_name';
					$filter[]	=	'override';
				}
			else {
					$filter['requestTable']	=	"";
					$filter['action']		=	"";
					$filter['command']		=	"";
					$filter['add']			=	"";
					$filter['update']		=	"";
					$filter['delete']		=	"";
					$filter['thumbnail']	=	"";
					$filter['keep_name']	=	"";
					$filter['override']		=	"";
					$filter['token']		=	"";
				}
			
			return $filter;
		}