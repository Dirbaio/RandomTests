<?php

require(__DIR__.'/Browsers.php');
require(__DIR__.'/Config.php');
require(__DIR__.'/Csrf.php');
require(__DIR__.'/Fetch.php');
require(__DIR__.'/OnlineUsers.php');
require(__DIR__.'/Permissions.php');
require(__DIR__.'/Records.php');
require(__DIR__.'/SchemaUpdater.php');
require(__DIR__.'/Session.php');
require(__DIR__.'/Sql.php');
require(__DIR__.'/Tag.php');
require(__DIR__.'/Template.php');
require(__DIR__.'/Url.php');
require(__DIR__.'/Util.php');
require(__DIR__.'/Validate.php');

require(__DIR__.'/postfilter/htmlfilter.php');

Config::load(ModuleHandler::getRoot().'/config.php');

Sql::connect(Config::get('mysql'));

session_start(); //For Csrf class
Session::load();
Records::update();
OnlineUsers::update();

// CHeck IP Bans
function isIPBanned($ip)
{
	$rIPBan = Sql::query('SELECT * FROM {ipbans} WHERE instr(?, ip)=1', $ip);
	
	$result = false;
	while($ipban = Sql::fetch($rIPBan))
	{
		if (IPMatches($ip, $ipban['ip']))
			if ($ipban['whitelisted'])
				return false;
			else
				$result = $ipban;
	}
	return $result;
}

function IPMatches($ip, $mask) {
	return $ip === $mask || $mask[strlen($mask) - 1] === '.';
}

$ipban = isIPBanned($_SERVER['REMOTE_ADDR']);

if($ipban)
	fail('You\'re banned.');
