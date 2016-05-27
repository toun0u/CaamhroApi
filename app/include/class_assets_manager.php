<?php
/**
*	AssetsManager
*	Permet de gérer un ensemble d'assets
*	@author Baptiste Wallerich @ Netlor
*	@package Includes
*/
class AssetsManager {

	/**
	*	Adresse du dossier assets
	*/
	public static $assetsPath;

	/**
	*	Initialisation de la classe
	*/
	public function __construct(){

		// Initialisation de la racine des assets
		self::$assetsPath = DIMS_WEB_PATH.'assets/';
	}

	/**
	*	Vérifie si on est ou non en développement
	*/
	public function isProduction(){

		return (! _DIMS_DEBUGMODE);

	}
	/**
	*	Permet de debuger rapidement un manager
	*/
	public function debug(){
		var_dump($this);
		die();
	}
}
?>