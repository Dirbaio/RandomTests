<?php


class Sql
{
	private static $db = null;
	private static $config = null;

	public static function connect($config)
	{
		self::$config = $config;
		self::$db = new PDO($config["db"], $config["user"], $config["pass"]);
	    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public static function getDbName()
	{
		return self::$config["name"];
	}
	public static function getUser()
	{
		return self::$config["name"];
	}
	public static function getPrefix()
	{
		return self::$config["prefix"];
	}

	public static function query()
	{
		//Get the query and the args
		$args = func_get_args();
		if (is_array($args[0])) $args = $args[0];

		$query = $args[0];
		array_shift($args); //Remove first element of args, so args only contains the actual arguments
		if ($args !== NULL && isset($args[0]) && is_array($args[0])) $args = $args[0];
		
		if($args !== NULL && count(array_filter(array_keys($args), 'is_string')) != 0) //Associative array!
		{
			$newArgs = array();
			foreach($args as $key => $val)
				$newArgs[":$key"] = $val;
			$args = $newArgs;
		}

		//Prepare statement
		$stmt = self::$db->prepare($query);
		$stmt->execute($args);

		return $stmt;
	}

	public static function queryValue()
	{
		$res = self::query(func_get_args());
		$res = $res->fetchAll(PDO::FETCH_NUM);
		return $res[0][0];
	}

	public static function querySingle()
	{
		$stmt = self::query(func_get_args());
		return self::fetch($stmt);
	}

	public static function queryAll()
	{
		$stmt = self::query(func_get_args());
		return self::fetchAll($stmt);
	}

	public static function fetch($result)
	{
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public static function fetchAll($result)
	{
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
}
