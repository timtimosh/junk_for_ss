<?php
$dir = $_SERVER['DOCUMENT_ROOT'].'/ss/data/';
$csv = $dir.'redirects.csv';
$delimeter = ',';

// Make dir and csv
if(!file_exists($csv)){
	if(is_dir($dir) === false) {
		mkdir($dir, 0755);
	}		
	touch($csv);
}

// Read a csv
$handle = fopen($csv,'r');
$urls = array();
while ( ($data = fgetcsv($handle, 5000, $delimeter) ) !== false ) {
	$urls[trim($data[0])] = (isset($data[1]) && trim($data[1]))? $data[1] : '/';
}

// Check urls
$newUrl = false;
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

if(isset($urls[$_SERVER['REQUEST_URI']])){
	$newUrl = $urls[$_SERVER['REQUEST_URI']];
}elseif(isset($urls[$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']])){
	$newUrl = $urls[$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']];
}


// Do the redirect
if($newUrl && $_SERVER['REQUEST_URI'] != $newUrl){
	header('Location: '.$newUrl, true, 301);
	die('redirecting');
}

/*
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/ss/redirects.php')) {
	include_once $_SERVER['DOCUMENT_ROOT'] . '/ss/redirects.php';
}
*/
