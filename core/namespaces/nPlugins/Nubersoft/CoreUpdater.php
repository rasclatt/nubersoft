<?php
namespace nPlugins\Nubersoft;

class CoreUpdater extends \Nubersoft\nRouter
	{
		public	function getGitFile()
			{
				$remote		=	'https://github.com/rasclatt/nUberSoft-Framework/archive/master.zip';
				$endpoint	=	$this->getSettingsDir(DS.'deploy'.DS.'nUberSoft-Framework-master.zip');
				if(!$this->isDir(pathinfo($endpoint,PATHINFO_DIRNAME))) {
					$this->toAlert('Save folder could not be created to move update to.');
					return;
				}
				$contents	=	file_get_contents($remote);
				file_put_contents($endpoint,$contents);

				if(is_file($endpoint))
					$this->addRedirect($this->adminUrl('/?action=nbr_deploy_changes'));
				else
					$this->toAlert('Update file was not found. Error probably occurred saving or retieving file.');
			}
	}
