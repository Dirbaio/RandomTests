<?php
//page /logout

function request()
{
	Session::checkLoggedIn();

	Session::end();
	return true;
}

