<?php

require(__DIR__.'/Browsers.php');
require(__DIR__.'/Config.php');
require(__DIR__.'/Csrf.php');
require(__DIR__.'/Fetch.php');
require(__DIR__.'/Permissions.php');
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


$isBot = Browsers::isBot();

//Check the amount of users right now for the records
$misc = Sql::querySingle('SELECT * FROM {misc}');

$onlineUsers = Sql::queryAll(
		'SELECT id FROM {users} WHERE lastactivity > ? or lastposttime > ? ORDER BY name',
		time()-300, time()-300);

// Max users record.
if(count($onlineUsers) > $misc['maxusers'])
{
	$onlineUsersList = '';
	foreach($onlineUsers as $onlineUser)
		$onlineUsersList .= ':'.$onlineUser['id'];

	Sql::query(
		'UPDATE misc SET maxusers = ?, maxusersdate = ?, maxuserstext = ?',
		count($onlineUsers), time(), $onlineUsersList);
}

// Max posts record, in 1 hour and 1 day.
$new = Sql::querySingle(
	'SELECT 
		(SELECT count(*) FROM {posts} WHERE date > ?) AS hour,
		(SELECT count(*) FROM {posts} WHERE date > ?) AS day',
	time() - 3600, time() - 86400);

if($records['hour'] > $misc['maxpostsday'])
	Sql::query(
		'UPDATE misc SET maxpostshour = ?, maxpostshourdate = ?',
		$records['hour'], time());

if($records['day'] > $misc['maxpostsday'])
	Sql::query(
		'UPDATE misc SET maxpostsday = ?, maxpostsdaydate = ?',
		$records['day'], time());


//DELETE oldies visitor FROM the guest list. We may re-add him/her later.
Sql::query('DELETE FROM {guests} WHERE date < ?', time()-300);

//DELETE expired sessions
Sql::query('DELETE FROM {sessions} WHERE expiration != 0 and expiration < ?',
	time());

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
