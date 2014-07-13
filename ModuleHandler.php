<?php

class ModuleHandler
{
	private static $loadedModules = null;
	private static $files = null;

	public static function init()
	{
		self::$loadedModules = array();
		self::$files = array();
	}

	public static function loadModule($path)
	{
		$path = __DIR__.$path;

		require($path.'/lib/lib.php');

		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) 
		{
			if(endsWith($file, '.')) continue;

			$file = $file->getPathname();

			$logicalFile = substr($file, strlen($path));
			if(!isset(self::$files[$logicalFile]))
				self::$files[$logicalFile] = array();

			self::$files[$logicalFile][] = $file;
		}

	}

	public static function getFiles($file)
	{
		return self::$files[$file];
	}
	
	public static function getFile($file)
	{
		return self::$files[$file][count(self::$files[$file]) - 1];
	}

	public static function getFilesMatching($pattern)
	{
		$pattern = preg_quote($pattern);
		$pattern = str_replace('\*\*', '[^/]*', $pattern);
		$pattern = str_replace('\*', '.*', $pattern);
		$pattern = '#'.$pattern.'#';

		$res = array();
		foreach(self::$files as $file => $files)
			if(preg_match($pattern, $file))
				foreach($files as $entry)
					$res[] = $entry;

		return $res;
	}
}