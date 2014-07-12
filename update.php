<?php

error_reporting(E_ALL ^ E_NOTICE | E_STRICT);

require(__DIR__."/config.php");
require(__DIR__."/Schema.php");
require(__DIR__."/platform/Sql.php");
require(__DIR__."/platform/SchemaUpdater.php");

function fail($why) {
	die($why);
}

Sql::connect($config["mysql"]);
SchemaUpdater::run();
