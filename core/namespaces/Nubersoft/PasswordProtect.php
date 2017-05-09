<?php
namespace Nubersoft;

interface PasswordProtect
	{
		public function __construct($rounds);
		
		public function hash($input);
		
		public function verify($input, $existingHash);
		
		public function verifyPassword($input, $existingHash);
		
		public function hashPassword($input);
		
		public function getHash();
		
		public function setUser($username);
		
		public function write();
		
		public function isValid();
	}