<?php

class	CacheEngine
	{
		public	static function app()
			{
				return new BuildCache();
			}
	}