# nUberSoft-Framework
nUberSoft is a small in-the-works framework Library for PHP. It is a mix of old and new due to it's long development timeframe. Documentation is not as robust as it should be. The majority of this framework will work for non-Linux-based systems, but it is not tested and some security (.htaccess) are not read by Win servers.

The functions, classes, and namespaces are all fairly self-explanitory and usually are easy to follow along. The workings of the application can be automated using an xml file. This is the default for this application.

Because this library does not have any other requirements, you could add this class manually to your project. To resolve the class, you would need to use only the `src` directory and rename it to `Nubersoft` in order for your autoloader to work properly.

##### Autoload Example:

Provided you have a folder that contains all your classes like so:

```
index.php
|–- vendor
    |-- Nubersoft
        |-- All files and folders in src folder
```

*/index.php*

```
<?php
spl_autoload_register(function($class){
	# Set the vendor-based namespace-to-path name
	# If $class was \Nubersoft\Conversion\Data it would translate to something like:
	# /var/www/domains/mydomainroot/vendor/Nubersoft/Conversion/Data.php
	$file = str_repace('//', '/', __DIR__.'/vendor/'.str_replace('\\', '/', $class).'.php');
	# Include the file
	if(is_file($file))
		include_once($file);
});


```
