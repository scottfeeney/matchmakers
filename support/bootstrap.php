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

	if ($class != 'Composer\Autoload\ClassLoader') {
		try {
			$includePath = getcwd() . '/' . strtolower(str_replace("\\","/",$class)) . '.php';
			//var_dump(PHP_EOL.$includePath.PHP_EOL);
			include $includePath;
		} catch (Exception $e) {
			//in case of case sensitivity issues
			include getcwd() . '/' . str_replace("\\","/",$class) . '.php';
		}
	}
});

//$test = new \Classes\Job(0);

?>
