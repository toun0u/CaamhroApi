<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// on recherche les infos existantes
$user = new user();
$user->open($_SESSION['dims']['userid']);
$lst=$user->getFavorites(1,null,dims_const::_SYSTEM_OBJECT_FAVORITES);

// int