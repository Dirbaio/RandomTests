<?php

class Url
{

	public static function getPath()
	{
		if (!empty($_SERVER['PATH_INFO']))
			return $_SERVER['PATH_INFO'];

		if (!empty($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] !== '/index.php')
			return $_SERVER['ORIG_PATH_INFO'];

		if (!empty($_SERVER['REQUEST_URI']))
			return (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];

		return '/';
	}

	public static function setCanonicalUrl($url)
	{
		if(self::getPath() !== $url)
			self::redirect($url);
	}


	public static function slugify($urlname)
	{
		$urlname = strtolower($urlname);
		$urlname = str_replace("&", "and", $urlname);
		$urlname = preg_replace("/[^a-zA-Z0-9]/", "-", $urlname);
		$urlname = preg_replace("/-+/", "-", $urlname);
		$urlname = preg_replace("/^-/", "", $urlname);
		$urlname = preg_replace("/-$/", "", $urlname);
		return $urlname;
	}

	public static function redirect($url)
	{
		header("Location: ".$url);
		die();
	}

	public static function isHttps()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) || $_SERVER["SERVER_PORT"] == 443;
	}

	public static function getServerUrl()
	{
		global $boardroot;
		$https = self::isHttps();
		$stdport = $https?443:80;
		$port = "";
		if($stdport != $_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"])
			$port = ":".$_SERVER["SERVER_PORT"];
		return ($https?"https":"http") . "://" . $_SERVER['HTTP_HOST'] . $port ;
	}

	public static function getRequestUrl()
	{
		return self::getServerURL().$_SERVER['REQUEST_URI'];
	}

}