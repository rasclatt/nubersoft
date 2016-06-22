<?php
// MasterMySQL
include("config.php");
NuberEngine::getRegistry("onload");
NuberEngine::getRegList()->getInstr();
// Create base core engine
NuberEngine::Init()->Core();