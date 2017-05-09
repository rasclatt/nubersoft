<?php
namespace Nubersoft;

interface Communicator
	{
		public	function __construct();
		public	function send();
		public	function addMessage($content);
		public	function addSubject($content);
		public	function addTo($content);
	}