<?php
namespace Nubersoft;

class nCache extends \Nubersoft\nApp
{
    protected   $destination, $has_layout, $layout, $basepath;
    private $refresh = false;

    public function delete()
    {
        $this->refresh = true;
        return $this;
    }

    public function start($path_to_file)
    {
        $this->destination = $path_to_file;
        $this->has_layout = false;
        $exists = is_file($this->destination);

        if ($this->refresh && $exists) {
            unlink($this->destination);
            $exists = is_file($this->destination);
        }

        if ($exists) {
            $this->has_layout = true;
        }

        ob_start();

        return $this;
    }

    public function isCached()
    {
        return $this->has_layout;
    }

    public function render()
    {
        if ($this->isCached()) {
            include($this->destination);
        }
        $this->layout = ob_get_contents();
        ob_end_clean();

        if (!$this->isCached()) {
            $path   =   pathinfo($this->destination, PATHINFO_DIRNAME);
            if ($this->isDir($path, 1))
                file_put_contents($this->destination, $this->layout);
        }

        return $this->layout;
    }
    /**
     *	@description	
     */
    public function setBasePath($path)
    {
        $this->basepath =   $path;
        return $this;
    }
    /**
     *	@description	
     */
    public function __($func, $def = 'index.html')
    {
        if (empty($this->basepath))
            $path =   NBR_CLIENT_CACHE . DS . 'pages' . DS . $this->getDataNode('routing')['ID'] . DS . $def;
        else
            $path   =   $this->basepath . $def;

        $this->start($path);
        if (!$this->isCached()) {
            echo (is_callable($func)) ? $this->getHelper('nReflect')->reflectFunction($func) : $func;
        }

        return $this->render();
    }
}
