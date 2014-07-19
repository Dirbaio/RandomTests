<?php 
//page /

//ABXD LEGACY
//page /board
//page /board.php

function request()
{
	Url::setCanonicalUrl('/');

	renderPage('board.html', array('forum' => $forum));
}

