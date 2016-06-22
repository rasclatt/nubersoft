<?php
	interface PasswordProtect
		{
			public function __construct($rounds);
			
			public function hash($input);
			
			public function verify($input, $existingHash);
			
			public function verify_password($input, $existingHash);
			
			public function encrypt_password($input);
			
			public function get_hash();
			
			public function set_user($username);
			
			public function write();
		}