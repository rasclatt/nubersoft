<?php
namespace Nubersoft;

use \Nubersoft\Dto\SFtpSsh2\ConstructRequest;

class SFtpSsh2
{
	protected $sftp, $host, $user, $pass, $nApp, $con;
	public $root;
	
	public function __construct(ConstructRequest $options)
	{
        $this->nApp = new \Nubersoft\nApp;
		$this->host = $options->host;
		$this->user = $options->user;
		$this->pass = $options->pass;
		
		if(!function_exists('ssh2_connect'))
			die('Missing the "ssh2_sftp" plugin. This extension must be installed before you can use this plugin.');
		$this->con = ssh2_connect($this->host, $options->port);
		if (!$this->con) {
			throw new \Exception('Connection to FTP failed.', 404);
		}
		$valid = ssh2_auth_password($this->con, $this->user, $this->pass);
		if(!$valid) {
			throw new \Exception('Login failed.', 403);
		}
		$this->sftp = ssh2_sftp($this->con);
        $this->root = $options->root;

		return $this;
	}
	
	public function sendFile($from, $to, $is_path = true,$method = 'w')
	{
        $sftp = intval($this->sftp);
        $destination = fopen("ssh2.sftp://{$sftp}{$to}", $method);
		
        if (!$destination)
            throw new \Exception("Connection to host failed.");
		
        $content = ($is_path && is_file($from))? file_get_contents($from) : $from;
        fwrite($destination, $content);
        fclose($destination);
	}
    /**
     *	@description	
     *	@param	
     */
    public function removeFile(string $path)
    {
        ssh2_sftp_unlink($this->sftp, $path);
        return $this;
    }
	
	public function transmit($content, $to, $is_path = true, $method = 'w')
	{
		$endpoint = "ssh2.sftp://{$this->user}:{$this->pass}@{$this->host}:22{$to}";
		file_put_contents($endpoint, $content);
	}

	public function sendContents($from, $to, $is_path = true, $method = 'w')
	{
		if(is_file($from))
			$from = file_get_contents($from);
		$sftp_fd = intval($this->sftp);
		$endpoint = "ssh2.sftp://{$sftp_fd}:22{$to}";
		file_put_contents($endpoint, $from);
	}
	
	public function getFile($file)
	{
		return file_get_contents("ssh2.sftp://{$this->user}:{$this->pass}@{$this->host}:22{$file}");
	}
	/**
	 *	@description	
	 */
	public function createDir($dirname, $chmod = 0777, $recursive = true)
	{
        return ssh2_sftp_mkdir($this->sftp, $dirname, $chmod, $recursive);
	}
	/**
	 *	@description	
	 */
	public function removeDir($dirname)
	{
        return ssh2_sftp_rmdir($this->sftp, $dirname);
	}
	/**
	 *	@description	
	 */
	public function listContents($path)
	{
        try {
            $sftp_fd = intval($this->sftp);
            $handle = opendir("ssh2.sftp://{$sftp_fd}{$path}");
            $new    =   [];
            while (false != ($entry = readdir($handle))){
                $new[]  =   $entry;
            }
            return $new;
        }
        catch(\Exception $e) {
            die($e);
        }
	}
}