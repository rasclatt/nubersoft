<?php
namespace Nubersoft\Flags;

class	Observer extends \Nubersoft\Flags\Controller implements \Nubersoft\nObserver
	{	
		public	function listen($flag = 'timestamp')
			{
				# Start the listener
				(new \nPlugins\Nubersoft\TimeStamp())->setFlagName($flag)->initialize();
				# Don't do anything
				if($this->getRequest('action') != 'nbr_timestamp_toggle')
					return;
				# Create or delete
				if(self::hasFlag($flag))
					self::delete($flag);
				else
					self::create($flag);
				# Check if flag there
				$hasFlag	=	self::hasFlag($flag);
				# Save action
				$this->toAlert('Members connected is '.(($hasFlag)? 'off' : 'on'));
				$this->getHelper('nRouter')->addRedirect($this->adminUrl('?requestTable='.$this->getRequest('requestTable').'&viewing=members_connected'));
			}
	}