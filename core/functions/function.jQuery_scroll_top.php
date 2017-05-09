<?php
/*Title: jQuery_scroll_top()*/
/*Description: This function applies the scroll-to-top jQuery jump button*/
/*Example: `jQuery_scroll_top(array("img"=>"file.jpg"));`*/

	function jQuery_scroll_top($settings = false)
		{
			return nApp::jsEngine()->nScroller($settings);
		}