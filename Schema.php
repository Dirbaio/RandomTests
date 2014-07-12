<?php

class Schema
{
	private static function varchar($len)
	{
		return array(
			"type" => "varchar($len)",
			"notNull" => true,
			"default" => ""
		);
	}

	public static function get()
	{
		$int = array(
			"type" => "int(11)",
			"notNull" => true,
			"default" => "0"
		);
		$bool = array(
			"type" => "tinyint(1)",
			"notNull" => true,
			"default" => "0"
		);
		$text = array(
			"type" => "text",
			"notNull" => false,  //NOT NULL breaks in certain versions/settings.
			"default" => ""
		);
		$textLong = array(
			"type" => "mediumtext",
			"notNull" => false,  //NOT NULL breaks in certain versions/settings.
			"default" => ""
		);
		$AI = array(
			"type" => "int(11)",
			"notNull" => true,
			"autoIncrement" => true,
		);
		$ip = self::varchar(50);

		$keyID = array
		(
			"fields" => array("id"),
			"type" => "primary",
		);



		return array
		(

			"users" => array
			(
				"fields" => array
				(
					"id" => $AI,
					"name" => self::varchar(32),
					"password" => self::varchar(256),
					"pss" => self::varchar(256),
					"powerlevel" => $int,
					"email" => self::varchar(128),
					"lostkey" => self::varchar(128),
					"lostkeytimer" => $int,
					"lastip" => $ip,
				),
				"keys" => array
				(
					$keyID,
					array(
						"fields" => array("name"),
						"type" => "unique",
					),
				),
			),
			
			"files" => array
			(
				"fields" => array
				(
					"id" => self::varchar(32),
					"date" => $int,
				),
				"keys" => array($keyID),
			),
			
			"sessions" => array
			(
				"fields" => array
				(
					"id" => self::varchar(256),
					"user" => $int,
					"expiration" => $int,
					"autoexpire" => $bool,
					"iplock" => $bool,
					"iplockaddr" => self::varchar(128),
					"lastip" => self::varchar(128),
					"lasturl" => self::varchar(128),
					"lasttime" => $int,
				),
				"keys" => array
				(
					$keyID,
					array(
						"fields" => array("expiration"),
					),
				),
			),
		);
	}
}
