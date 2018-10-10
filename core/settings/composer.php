<?php
if(!is_file(NBR_ROOT_DIR.DS.'composer.json') && !is_file(NBR_ROOT_DIR.DS.'composer.lock')) {
	return false;
}

$cmd	=	[
	'cd '.NBR_ROOT_DIR,
	'/opt/plesk/php/5.6/bin/php /usr/lib64/plesk-9.0/composer.phar install'
];

foreach($cmd as $command) {
	exec($command);
}