<?php


function request()
{
	Session::checkLoggedIn();

	SchemaUpdater::run();

	return "THIS_IS_HTML";
}