<?php

// Error handling
//==============================

error_reporting(E_ALL ^ E_NOTICE | E_STRICT);

function fail($why) {
	throw new Exception($why);
}

function my_error_handler()
{
	$last_error = error_get_last();
	if ($last_error && ($last_error['type']==E_ERROR || $last_error['type']==E_USER_ERROR))
		header('HTTP/1.1 500 Internal Server Error');
}
register_shutdown_function('my_error_handler');


// Load main module
//============================

require(__DIR__.'/ModuleHandler.php');
require(__DIR__.'/vendor/autoload.php');
ModuleHandler::init();
ModuleHandler::loadModule('/modules/main');
ModuleHandler::loadModule('/themes/cheese');

if(isset($_COOKIE['mobileversion']) && $_COOKIE['mobileversion'] && $_COOKIE['mobileversion'] != 'false')
	ModuleHandler::loadModule('/modules/mobile');
else
	ModuleHandler::loadModule('/modules/nsmbhd');


// Set up stuff
//============================

Config::load(__DIR__.'/config.php');

Sql::connect(Config::get('mysql'));

session_start(); //For Csrf class
Session::load();


// Run the page
//============================


function getPages()
{
	$prefix = '//page ';
	
	$pages = array();
	foreach(ModuleHandler::getFilesMatching('/pages/**.php') as $file) 
	{
		$handle = @fopen($file, 'r');
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

function renderPage($template, $vars)
{
	$navigation = array(
		array('url' => '/', 'title' => __('Main')),
		array('url' => '/members', 'title' => __('Members')),
		array('url' => '/online', 'title' => __('Online users')),
		array('url' => '/search', 'title' => __('Search')),
		array('url' => '/lastposts', 'title' => __('Last posts')),
		array('url' => '/faq', 'title' => __('FAQ/Rules')),
	);

	$user = Session::get();

	if($user)
		$userpanel = array(
			array('user' => $user),
			array('url' => Url::format('/members/#-#/edit', $user['id'], $user['name']), 'title' => __('Edit profile')),
			array('url' => '/private', 'title' => __('Private messages')),
			array('url' => '/logout', 'title' => __('Log out')),
		);
	else
		$userpanel = array(
			array('url' => '/register', 'title' => __('Register')),
			array('url' => '/login', 'title' => __('Log in')),
		);
 
	$layout = array(
		'template' => $template,
		'css' => ModuleHandler::toWebPath(ModuleHandler::getFilesMatching('/css/**.css')),
		'js' => ModuleHandler::toWebPath(ModuleHandler::getFilesMatching('/js/**.js')),
		'title' => 'RandomTests',
		'pora' => true,
		'poratext' => 'Hello World',
		'poratitle' => 'ASDF',
		'views' => 12345678,
		'user' => $user,
		'navigation' => $navigation,
		'userpanel' => $userpanel,
	);
	$vars['layout'] = $layout;
	$vars['loguser'] = Session::get();

	if(!isset($vars['breadcrumbs']) || !is_array($vars['breadcrumbs']))
		throw new Exception('breadcrumbs not found in vars, must be there and be an array');
	if(!isset($vars['actionlinks']) || !is_array($vars['actionlinks']))
		throw new Exception('actionlinks not found in vars, must be there and be an array');

	array_unshift($vars['breadcrumbs'], 
		array('url' => Url::format('/'), 'title' => __('Main')));

	Template::render('layout/main.html', $vars);

}

function runPage()
{
	$pages = getPages();
	$path = Url::getPath();

	//Kill trailing and extra slashes.
	$origpath = $path;
	$path = preg_replace('#/+$#', '', $path);
	$path = preg_replace('#//+#', '/', $path);
	if($path == '') $path = '/';
	if($path != $origpath)
		Url::redirect($path);


	if ($_SERVER['REQUEST_METHOD'] === 'POST') 
	{
		$input = json_decode(file_get_contents('php://input'), true);
		if(!is_array($input) || json_last_error() !== JSON_ERROR_NONE)
			$input = $_POST;

		foreach($_GET as $key => $value)
			$input[$key] = $value;
	}
	else
		$input = $_GET;

	$foundPagefile = null;
	foreach($pages as $page=>$pagefile)
	{
		//match $path against $page
		$names = array();
		$pattern = preg_replace_callback('/(:|#|\$)([a-zA-Z][a-zA-Z0-9]*|)/', 
			function($matches) use (&$names) 
			{
				if($matches[1] == '#')
					$regex = '[0-9]+';
				else if($matches[1] == '$')
					$regex = '[^/]+';
				else
					$regex = '[a-zA-Z0-9-_]+';
				if($matches[2])
				{
					$names[] = $matches[2];
					return '('.$regex.')';
				}
				else
					return $regex;
			}, $page);

		if (preg_match('#^' . $pattern . '$#', $path, $matches)) 
		{
			foreach($names as $idx => $name)
				$input[$name] = $matches[$idx+1];

			$foundPagefile = $pagefile;
			break;
		}
	}

	if(!$foundPagefile)
		$foundPagefile = __DIR__.'/modules/main/pages/404.php';

	$input['input'] = $input;

	require($foundPagefile);

	//Calculate parameters
	$params = array();
	$refFunc = new ReflectionFunction('request');
	foreach($refFunc->getParameters() as $param) 
	{
		if(isset($input[$param->name]))
			$params[] = $input[$param->name];
		else if($param->isDefaultValueAvailable())
			$params[] = $param->getDefaultValue();
		else
			fail('Missing parameter: '.$param->name);
	}

	//Call the thing
	call_user_func_array('request', $params);
}


try
{
	runPage();
}
catch(Exception $e)
{
	header('X-PHP-Response-Code: 403', true, 403);
	echo $e->getMessage();
}


