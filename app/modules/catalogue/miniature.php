<?php
session_start();

chdir('../..');
include './include/config.php';
include './include/global.php';
include './include/global_phpini.php';
include './include/class_cache.php';

$dims_cache = new cache();

if (!empty($_GET['photo'])) {
	$imagefile = "./photos/orig/{$_GET['photo']}";
	if (file_exists($imagefile)) {
		if (!isset($_GET['size'])) $size = 50;
		else $size = $_GET['size'];

		if (!$dims_cache->start("schaller_{$_GET['photo']}_$size",8640000,false,realpath('./cache')._DIMS_SEP)) {
			dims_resizeimage($imagefile,0,$size,$size);
			$dims_cache->end();
		}
	}
}
