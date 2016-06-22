<?php
define("_DS_",DIRECTORY_SEPARATOR);
define("NBR_ROOT_DIR", __DIR__);
define('NBR_CORE', NBR_ROOT_DIR._DS_.'core.processor');
define("NBR_CLASS_CORE", NBR_ROOT_DIR._DS_.'core.processor'._DS_.'classes');
define("NBR_PLUGINS", NBR_ROOT_DIR._DS_.'core.plugins');
define("NBR_CLIENT_DIR", NBR_ROOT_DIR._DS_.'client_assets');
define("NBR_RENDER_LIB", NBR_CORE._DS_.'renderlib');
define("NBR_TEMPLATE_DIR",NBR_CORE._DS_.'template');
define("NBR_FUNCTIONS",NBR_CORE._DS_.'functions');
define("NBR_NAMESPACE_CORE",NBR_CORE._DS_.'namespaces');
define("NBR_ENGINE_CORE",NBR_CORE._DS_.'engine');
define("NBR_ENGINE_CLIENT",NBR_CLIENT_DIR._DS_.'settings'._DS_.'engine');
define("NBR_THUMB_DIR",NBR_CLIENT_DIR._DS_.'thumbs');
define("NBR_AJAX_DIR",NBR_ROOT_DIR._DS_.'core.ajax');