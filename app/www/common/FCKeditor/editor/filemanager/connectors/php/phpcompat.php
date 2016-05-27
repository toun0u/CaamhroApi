<?php

if ( !isset( $_SERVER ) ) {
	$_SERVER = $_SERVER ;
}
if ( !isset( $_GET ) ) {
	$_GET = $_GET ;
}
if ( !isset( $_FILES ) ) {
	$_FILES = $_FILES ;
}

if ( !defined( 'DIRECTORY_SEPARATOR' ) ) {
	define( 'DIRECTORY_SEPARATOR',
		strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/'
	) ;
}
