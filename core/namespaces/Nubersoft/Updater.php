<?php
namespace Nubersoft;

class Updater extends \Nubersoft\nApp
{
	private		$gitEndpoint	=	'https://github.com/rasclatt/nUberSoft-Framework/archive/master.zip';
	private		$localEndpoint;
	protected	$endpointContent;
	
	public	function getLatestVersion($localEndpoint = false)
	{
		# Store endpoint where download will be saved
		$this->localEndpoint	=	(!empty($localEndpoint))? $localEndpoint : $this->getSettingsDir(DS.'deploy'.DS.'nUberSoft-Framework-master.zip');
		# Check if the deploy folder is already made
		if(!$this->isDir(pathinfo($this->localEndpoint,PATHINFO_DIRNAME))) {
			$this->toMsgCoreAdminAlert('Save folder could not be created to move update to.');
			return false;
		}
		# Get the git master file
		$this->endpointContent	=	file_get_contents($this->gitEndpoint);
		# Send back chainable
		return $this;
	}
	
	public	function getEndpointContent()
	{
		return (!empty($this->endpointContent))? $this->endpointContent : false;
	}
	
	public	function fetchDeployable()
	{
		# Fetch contents
		$contents	=	$this->getLatestVersion()->getEndpointContent();
		# If there are no contents or endpoint, stop
		if(!$contents || empty($this->localEndpoint))
			return false;
		# Save remote file to 
		file_put_contents($this->localEndpoint,$contents);
	}
}