<?php

$desktop_collab=dims_load_securvalue('desktop_collab',dims_const::_DIMS_NUM_INPUT,true,true,false);

if ($desktop_collab>0) $_SESSION['dims']['desktop_collab']=$desktop_collab;

// on initialise la variable de desktop collab
if (!isset($_SESSION['dims']['desktop_collab'])) $_SESSION['dims']['desktop_collab']=dims_const::_DIMS_CSTE_TONEWS;

// calcul des infos a actualiser
$recentact=(isset($_SESSION['dims']['activities'][$_SESSION['dims']['workspaceid']])) ? $_SESSION['dims']['activities'][$_SESSION['dims']['workspaceid']] : 0;

$nbwait=0;
$nbfavorite=0;

// on compte le nombre de tickets a voir
require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
$usr=new user();
$usr->open($_SESSION['dims']['userid']);
// liste des users visibles par le user courant
//$lstusers=$usr->getusersgroup();
// liste des espaces de travail rattach
