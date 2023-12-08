<?php
$dev = array(
		'app_id'	=>	'624768600897248',
		'app_secret'	=>	'98a1148fa0fe8b76bf78c9123e479627'
	);

$prod = array(
		'app_id'	=>	'614121232056746',
		'app_secret'	=>	'b019a0264057fb4281a8b433c39268c6'
	);

if( isset( $_SERVER['HTTP_HOST'] ) && preg_match('/^localhost/',$_SERVER['HTTP_HOST']) )
	return $dev;
else
	return $prod;