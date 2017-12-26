<?php
echo $this->useTemplatePlugin((!empty($this->getGet('admintool_plugin'))? 'plugin_'.$this->getGet('admintool_plugin') : 'admintool'));