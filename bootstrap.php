<?php
set_include_path(get_include_path() . PATH_SEPARATOR . getcwd());
set_include_path(get_include_path() . PATH_SEPARATOR . getcwd() . '/classes');
set_include_path(get_include_path() . PATH_SEPARATOR . getcwd() . '/utilities');
set_include_path(get_include_path() . PATH_SEPARATOR . getcwd() . '/api');
set_include_path(get_include_path() . PATH_SEPARATOR . getcwd() . '/api/external');
set_include_path(get_include_path() . PATH_SEPARATOR . getcwd() . '/api/external/admin');
//var_dump(get_include_path());

spl_autoload_register(function ($class)
{
	//composer autoloader somehow being called during execution of standalone version of phpunit 
	//that was not installed with composer and shouldn't even know that it's installed on the system
	//much less be trying to look for components of it - but it is
	if ($class != 'Composer\Autoload\ClassLoader') {
	    include getcwd() . '/' . strtolower(str_replace("\\","/",$class)) . '.php';
	}
});

?>
