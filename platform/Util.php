<?php

class Util
{

	public static function randomString($length = 32) {
		$cstrong = false;
	    $bytes = openssl_random_pseudo_bytes($length, $cstrong);
	    
	    if(!$cstrong)
	    	fail("Crypto fail OMG WHY?!?");
	    
	    return bin2hex($bytes);
	}

	public static function hash($lol)
	{
		return hash('sha256', $lol);
	}

	public static function isHttps()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) || $_SERVER["SERVER_PORT"] == 443;
	}

	public static function getServerURL()
	{
		global $boardroot;
		$https = self::isHttps();
		$stdport = $https?443:80;
		$port = "";
		if($stdport != $_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"])
			$port = ":".$_SERVER["SERVER_PORT"];
		return ($https?"https":"http") . "://" . $_SERVER['HTTP_HOST'] . $port ;
	}

	public static function getRequestURL()
	{
		return self::getServerURL().$_SERVER['REQUEST_URI'];
	}

}


function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
