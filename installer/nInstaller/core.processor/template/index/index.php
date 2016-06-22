<?php
// MasterMySQL
include("dbconnect.root.php");
NuberEngine::getRegistry("onload");
NuberEngine::getRegList()->getInstr();
// Create base core engine
NuberEngine::Init()->Core();