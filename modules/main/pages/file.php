<?php 
//page /file/:id
//page /file/:id/$

//ABXD LEGACY
//page /get.php

function request($id)
{
	$file = Sql::querySingle('SELECT * FROM {files} WHERE id=?', $id);
	if(!$file)
		fail('No such file');

	Url::setCanonicalUrl('/file/:/$', $id, $file['name']);

	//Count downloads!
	Sql::query('UPDATE {files} SET downloads = downloads+1 WHERE id=?', $id);

	$dir = ModuleHandler::getRoot()."/uploads/".substr($file['hash'], 0, 2);
	$path = $dir.'/'.$file['hash'];

	if(!file_exists($path))
		fail('File found in DB but not on disk... :(');
	
	$fsize = filesize($path);
	$parts = explode(".", $file['name']);
	$ext = end($parts);
	$ext = strtolower($ext);
	$download = true;
	
	switch ($ext)
	{
		case 'gif': $ctype='image/gif'; $download = false; break;
		case 'apng':
		case 'png': $ctype='image/png'; $download = false; break;
		case 'jpeg':
		case 'jpg': $ctype='image/jpg'; $download = false; break;
		case 'css': $ctype='text/css'; $download = false; break;
		case 'txt': $ctype='text/plain'; $download = false; break;
		case 'swf': $ctype='application/x-shockwave-flash'; $download = false; break;
		case 'pdf': $ctype='application/pdf'; $download = false; break;
		default: $ctype='application/force-download'; break;
	} 

	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);
	header('Content-Type: '.$ctype);
	if($download)
		header('Content-Disposition: attachment; filename=\''.$file['name'].'\';');
	else
		header('Content-Disposition: filename=\''.$file['name'].'\'');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.$fsize);

	readfile($path);
}