<?php
//page /login

function request($username, $password)
{
	$salt = "blarg blarg blarg";

	$user = Sql::querySingle("SELECT * FROM users WHERE name=?", $username);

	if(!$user || $user["password"] != Util::hash($password.$salt.$user['pss']))
		fail("Wrong username or password");

	Session::start($user["id"]);
	return Session::get();
}