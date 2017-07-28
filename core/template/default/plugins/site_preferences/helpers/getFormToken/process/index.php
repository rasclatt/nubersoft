<?php
\Nubersoft\nApp::call()->autoload(array('nbr_getFormToken'));
echo json_encode(nbr_getFormToken());
exit;