<?php

	interface UpdateSys
		{
			public	function setDestination();
			
			public	function getFiles();
			
			public	function moveFiles();
			
			public	function deleteFiles();
		}