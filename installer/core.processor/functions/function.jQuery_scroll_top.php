<?php
/*Title: jQuery_scroll_top()*/
/*Description: This function applies the scroll-to-top jQuery jump button*/
/*Example: `jQuery_scroll_top(array("img"=>"file.jpg"));`*/

	function jQuery_scroll_top($settings = false)
		{
			AutoloadFunction('site_url');
			$img	=	(!empty($settings['img']))? $settings['img']: site_url()."/images/ui/arrowup.png";
			$class	=	(!empty($settings['class']))? $settings['class']:"scroll-top";
			$id		=	(!empty($settings['id']))? ' id="'.$settings['id'].'"':"";
				 ?>
	<div class="<?php echo $class; ?>"<?php echo $id; ?>><img src="<?php echo $img; ?>" /></div>
		<?php
		}