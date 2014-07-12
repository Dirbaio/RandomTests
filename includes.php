<?php


error_reporting(E_ALL ^ E_NOTICE | E_STRICT);

// Error handling
//==============================

function fail($why) {
	throw new Exception($why);
}

function my_error_handler()
{
	$last_error = error_get_last();
	if ($last_error && ($last_error['type']==E_ERROR || $last_error['type']==E_USER_ERROR))
		header("HTTP/1.1 500 Internal Server Error");
}
register_shutdown_function('my_error_handler');


// Classes
//============================

require(__DIR__."/config.php");
require(__DIR__."/Schema.php");
require(__DIR__."/platform/includes.php");


// Set up stuff
//============================

Sql::connect($config["mysql"]);

session_start(); //For Csrf class
Session::load();

