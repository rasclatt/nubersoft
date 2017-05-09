<?php
namespace nPlugins\Nubersoft;

class AdminToolsMaster extends \Nubersoft\nApp
	{
		public	function menuBar()
			{
				$this->render(__DIR__.DS.'AdminToolsMaster'.DS.'MenuBar.php');
			}
			
		public	function plugins()
			{
				$this->render(__DIR__.DS.'AdminToolsMaster'.DS.'Plugins.php');
			}
			
			
		public	function mastHead()
			{
				$this->render(__DIR__.DS.'AdminToolsMaster'.DS.'MastHead.php');
			}
			
		public	function css()
			{
				$this->render(__DIR__.DS.'AdminToolsMaster'.DS.'CSS.php');
			}
	}