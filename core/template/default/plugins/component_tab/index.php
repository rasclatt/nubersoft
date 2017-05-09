<?php
$ComponentTab	=	new \nPlugins\Nubersoft\ComponentTab(
						$this,
						new \Nubersoft\nImage(
							new \Nubersoft\nHtml
						)
					);

echo $ComponentTab->toolBar();