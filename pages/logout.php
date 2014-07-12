<?php

function request()
{
	Session::checkLoggedIn();

	Session::end();
	return true;
}

