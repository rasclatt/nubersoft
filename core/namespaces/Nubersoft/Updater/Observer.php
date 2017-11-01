<?php
namespace Nubersoft\Updater;

class Observer extends \Nubersoft\nObserver
{
	public	function listen()
	{
		if(!defined('NBR_TRIGGER_UPDATER'))
			return false;
		elseif(!NBR_TRIGGER_UPDATER)
			return false;
		# Fetch the next run date
		$config	=	$this->getFlagContents();
		# If not yet set
		if(empty($config)) {
			# Create file                      # Re-fetch
			$config	=	$this->setNextRunDate()->getFlagContents();
		}
		# If the config is empty at this point, it's not going to work
		if(!$config)
			return false;
		# If past time
		if(strtotime('now') > $config) {
			# Res save the next run date
			$this->setNextRunDate();
			# Fetch the installer and save to deploy folder
			$content	=	$this->fetchDeployable();
			# Check if the file is there, then redirect to deploy
			if(is_file($this->localEndpoint))
				$this->addRedirect($this->adminUrl('/?action=nbr_deploy_changes'));
			# Don't do anything if it's not there
			else
				$this->toAlert('Update file was not found. Error probably occurred saving or retieving file.');
		}
	}
	
	public	function setNextRunDate($date='4 days')
	{
		# First remove if set
		if(is_file($this->getFlagPath()))
			unset($this->getFlagPath());
		# Put new file
		file_put_contents($this->getFlagPath(),strtotime('now + '.$date));
		# Return object
		return $this;
	}
	
	public	function getFlagContents()
	{
		# Get flag path
		$config	=	$this->getFlagPath();
		# Check file directory is set
		if(!$this->isDir(pathinfo($config,PATHINFO_DIRNAME)))
			return false;
		# Send back date if contains something
		return (!is_file($config))? false : file_get_contents($config);
	}
	
	public	function getFlagPath()
	{
		return NBR_CLIENT_DIR.DS.'settings'.DS.'update.flag';
	}
}