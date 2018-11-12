<?php
include(__DIR__.DS.'index.'.(($this->getSession('editor'))? 'editor' : 'view').'.php');