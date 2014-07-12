<?php

require(__DIR__."/includes.php");

function getPages() 
{
	$pages = array();
	foreach(glob(__DIR__.'/pages/*.php') as $file) 
	{
		$handle = @fopen($file, "r");
		if (!$handle)  continue;

		$line = fgets($handle, 4096);
		fclose($handle);
		$prefix = '<?php //page ';
		if(!startsWith($line, $prefix)) continue;

		$page = substr($line, strlen($prefix));
		$page = trim($page);
		$pages[$page] = $file;
	}
	return $pages;	
}

function getPath() 
{
	if (!empty($_SERVER['PATH_INFO']))
		return $_SERVER['PATH_INFO'];

	if (!empty($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] !== '/index.php')
		return $_SERVER['ORIG_PATH_INFO'];

	if (!empty($_SERVER['REQUEST_URI']))
		return (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];

	return '/';
}

try
{
	$pages = getPages();
	$path = getPath();

	if(!isset($pages[$path]))
		fail("404 Not Found");

	$pagefile = $pages[$path];
	require($pagefile);

	//Calculate input
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		$input = file_get_contents("php://input");
		$input = json_decode($input, true);
	}
	else
	{
		$input = $_GET;
		unset($input["req"]);
	}

	$input["input"] = $input;

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
	$res = call_user_func_array("request", $params);

	if($res !== "THIS_IS_HTML")
	{
		header('Content-type: application/json');
		echo json_encode($res);
	}
}
catch(Exception $e)
{
	header('X-PHP-Response-Code: 403', true, 403);
	echo $e->getMessage();
}


