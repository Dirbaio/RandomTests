<?php

require(__DIR__."/includes.php");

function getPages()
{
	$prefix = '//page ';
	
	$pages = array();
	foreach(glob(__DIR__.'/pages/*.php') as $file) 
	{
		$handle = @fopen($file, "r");
		if (!$handle)  continue;

	    while (($line = fgets($handle, 4096)) !== false)
			if(startsWith($line, $prefix))
			{
				$page = substr($line, strlen($prefix));
				$page = trim($page);
				$pages[$page] = $file;
			}
		
		fclose($handle);
	}
	return $pages;	
}


try
{
	$pages = getPages();
	$path = Url::getPath();

	//Kill trailing and extra slashes.
	$origpath = $path;
	$path = preg_replace("#/+$#", "", $path);
	$path = preg_replace("#//+#", "/", $path);
	if($path == '') $path = '/';
	if($path != $origpath)
		Url::redirect($path);

	$input = array();
	foreach($_GET as $key => $value)
		$input[$key] = $value;
	foreach($_POST as $key => $value)
		$input[$key] = $value;

	$foundPagefile = null;
	foreach($pages as $page=>$pagefile) 
	{
		//match $path against $page
		$names = array();
		$pattern = preg_replace_callback('/(:|#)([a-zA-Z][a-zA-Z0-9]*|)/', function($matches) use (&$names) {
			if($matches[1] == '#')
				$regex = '[0-9]+';
			else
				$regex = '[a-zA-Z0-9-_]+';
			if($matches[2])
			{
				$names[] = $matches[2];
				return "($regex)";
			}
			else
				return $regex;
		}, $page);

        if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
        	foreach($names as $idx => $name)
        		$input[$name] = $matches[$idx+1];

        	$foundPagefile = $pagefile;
            break;
        }
	}

	if(!$foundPagefile)
		fail("404 Not Found");

	$input["input"] = $input;

	require($foundPagefile);

	//Calculate parameters
	$params = array();
	$refFunc = new ReflectionFunction("request");
	foreach( $refFunc->getParameters() as $param ) {
		if(isset($input[$param->name]))
			$params[] = $input[$param->name];
		else if($param->isDefaultValueAvailable())
			$params[] = $param->getDefaultValue();
		else
			fail("Missing parameter: ".$param->name);
	}

	//Call the thing
	call_user_func_array("request", $params);
}
catch(Exception $e)
{
	header('X-PHP-Response-Code: 403', true, 403);
	echo $e->getMessage();
}


