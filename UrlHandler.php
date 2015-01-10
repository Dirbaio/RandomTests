<?php

// A "path" is a page url independent of the UrlHandler in use. For example: "/members/1-dirbaio" 
// An "url" corresponding to a "path" is the actual URL the browser is visiting. Can vary depending
// on the configuration. For example:
// With URL rewriting: http://example.com/members/1-dirbaio
// Without URL rewriting: http://example.com/?members/1-dirbaio

interface UrlHandler
{
	// Returns the url to access the given path
    public function getUrlForPath($path);

    // Returns the path for this request
    public function getPath();
}


// Rewriting UrlHandler. Generates fancier URLs but requires additional server config.
class RewritingUrlHandler implements UrlHandler
{
	// Returns the url to access the given path
    public function getUrlForPath($path)
    {
    	return $path;
    }

    // Returns the path for this request
    public function getPath()
    {
		// Allow running from CLI
		global $argv;
		if(php_sapi_name() === 'cli')
		{
			if($argv[1])
				return $argv[1];
			else
				return '/';
		}

		// Legacy ABXD compat
		if(!empty($_GET['page']))
			return '/'.$_GET['page'].'.php';

		// Try to figure out the pathinfo
		if(!empty($_SERVER['PATH_INFO']))
			return $_SERVER['PATH_INFO'];

		if(!empty($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] !== '/index.php')
			return $_SERVER['ORIG_PATH_INFO'];

		if(!empty($_SERVER['REQUEST_URI']))
			return (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];

		return '/';
    }
}

// Simple URL handler, requires no extra configuration in the server.
class SimpleUrlHandler implements UrlHandler
{
	// Returns the url to access the given path
    public function getUrlForPath($path)
    {
    	if($path == '/')
    		return './';
    	return './?'.$path;
    }

    // Returns the path for this request
    public function getPath()
    {
		$res = $_SERVER['QUERY_STRING'];
		if($res[0] != '/')
			$res = '/' . $res;
		return $res;
    }
}
