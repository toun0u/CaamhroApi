<?php
/**
*	ScriptsManager
*	Classe permettant de gérer avec plus de facilité un ensemble de scripts
*	@author Baptiste Wallerich @ Netlor
*	@package Includes
*/
class ScriptsManager extends AssetsManager {

	/**
	*	Tableau contenant les scripts à inclure en développement
	*/
	public $scripts = array();

	/**
	*	Adresse du dossier des scripts
	*/
	public static $scriptsPath;

		/**
		*	Adresse du dossier des librairies
		*/
		public static $libsPath;

		/**
		*	Adresse du dossier des modules
		*/
		public static $modulesPath;

	/**
	*	Fichier de configuration décodé
	*/
	public $outputs;

	/**
	*	Initialisation de la classe
	*/
	function __construct(){

		// Appel au constructeur parent
		parent::__construct();

		// Initialisation de la racine des scripts
		self::$scriptsPath = parent::$assetsPath.'javascripts/';

		// Initialisation de la racine des librairies
		self::$libsPath = self::$scriptsPath.'libs/';

		// Initialisation de la racine des modules
		self::$modulesPath = self::$scriptsPath.'modules/';

		// Inclusion des scripts à partir du fichier de configuration
		$this->loadConfig();

	}

	/**
	*	Charge le fichier de configuration
	*/
	private function loadConfig(){
		$this->outputs = json_decode(file_get_contents(parent::$assetsPath.'scripts_outputs.json'), true);
	}

	/**
	*	Charge les scripts d'une application
	*	@param String $name Nom de l'application à charger
	*	@param String $loadModules Charge aussi ou non les modules liés à l'application
	*	@param String $loadLibs    Charge aussi ou non les librairies liées à l'application
	*/
	public function loadRessource(
		$type,
		$nom,
		$loadLibs    = true
	){

		if(empty($nom)){

			echo "Aucune ressource à charger, veuillez spécifier $name dans votre appel à loadRessource()";
			return false;

		}

		if(empty($type)){

			echo "Aucune type de ressource à charger, veuillez spécifier $type dans votre appel à loadRessource()";
			return false;

		}

		// Inclusion des scripts par défaut
		$default_includes = $this->outputs['default']['includes'];
		foreach ($default_includes as $includeMe) {

			if($this->pathType($includeMe) == 'file'){
				$this->addScript(array(self::$scriptsPath.$includeMe));
			} elseif($this->pathType($includeMe) == 'folder'){
				$this->addFolder(self::$scriptsPath.$includeMe);
			}
		}
		//var_dump($this->scripts); die();

		// Exclusion des scripts par défaut
		$default_excludes = $this->outputs['default']['excludes'];
		foreach ($default_excludes as $excludeMe) {

			if($this->pathType($excludeMe) == 'file'){
				$this->removeScript(self::$scriptsPath.$excludeMe);
			} elseif($this->pathType($excludeMe) == 'folder'){
				$this->removeFolder(self::$scriptsPath.$excludeMe);
			}

		}
		//var_dump($this->scripts); die();
		if($type == 'backoffice'){

			$extra_includes = $this->outputs['default']['includes_backoffice'];

		} else if($type == 'frontoffice'){

			$extra_includes = $this->outputs['default']['includes_frontoffice'];
		}


		// Inclusion des scripts par défaut
		// d'un template backoffice / frontoffice
		if(isset($extra_includes)){
			foreach ($extra_includes as $includeMe) {
				if($this->pathType($includeMe) == 'file'){
					$this->addScript(array(self::$scriptsPath.$includeMe));
				} elseif($this->pathType($includeMe) == 'folder'){
					$this->addFolder(self::$scriptsPath.$includeMe);
				}
			}
		}
		//var_dump($this->scripts); die();


		/**
		*	Règles spécifiques
		*/
		$exception = $this->outputs['default']['exceptions'];
		if($type == 'backoffice'){
			if(array_key_exists('backoffice', $exception)){
				if(array_key_exists($nom, $exception['backoffice'])){
					$exception = $exception['backoffice'][$nom];
				}
			}

		} else if($type == 'frontoffice'){
			if(array_key_exists('frontoffice', $exception)){
				if(array_key_exists($nom, $exception['frontoffice'])){
					$exception = $exception['frontoffice'][$nom];
				}
			}
		}

		// Pas de règle spécifique
		// Inclusion du dossier du template
		if(is_null($exception)){

			$this->addFolder(self::$scriptsPath.$type.'/'.$nom);
			//var_dump($this->scripts); die();
			// Seems nice

		// Application des exception du template
		} else {

			// Inclusions
			if(array_key_exists('includes', $exception)){

				foreach ($exception['includes'] as $includeMe) {
					if($this->pathType($includeMe) == 'file'){
						$this->addScript(array(self::$scriptsPath.$includeMe));
					} elseif($this->pathType($includeMe) == 'folder'){
						$this->addFolder(self::$scriptsPath.$includeMe);
					}
				}
			}
			//var_dump($this->scripts);die();

			// Exclusions
			if(array_key_exists('excludes', $exception)){

				foreach ($exception['excludes'] as $excludeMe) {
					if($this->pathType($excludeMe) == 'file'){
						$this->removeScript(self::$scriptsPath.$excludeMe);
					} elseif($this->pathType($excludeMe) == 'folder'){
						$this->removeFolder(self::$scriptsPath.$excludeMe);
					}
				}
			}
			//var_dump($this->scripts);die();

			// Inclusions propre au template
			$this->addFolder(self::$scriptsPath.$type.'/'.$nom);
			//var_dump($this->scripts);die();
		}
	}

	/**
	*	Inclusion conditionnelle des styles du template en fonction de _DIMS_DEBUGMODE
	*/
	public function includeScripts(){

		if(count($this->scripts) == 0){

			echo "Aucun style à inclure 0_o";
			echo "Veuillez vérifier que vous avez bien fait appel à loadRessource()";
			return false;
		}


		// On veut donner une adresse de fichier sur le serveur
		// et non plus sur le système de fichiers
		foreach ($this->scripts as $key => $scripts) {

			$this->scripts[$key][0] = str_replace($_SERVER['DOCUMENT_ROOT'],'', $scripts[0]);

		}

		return $this->renderScriptList();

	}

	/**
	*	Ajoute un script à inclure
	*	@param Array $script Script à inclure
	*/
	public function addScript($script){
		$this->scripts[] = $script;
	}

	/**
	*	Ajoute un script à exclure
	*	@param String $script Script à exclure
	*/
	public function removeScript($script_name){

		foreach($this->scripts as $key => $script){
			if($script[0] == $script_name){
				unset($this->scripts[$key]);
				break;
			}
		}
	}

	/**
	*	Ajoute un dossier de scripts à exclure
	*	@param String $folder Dossier à exclure
	*/
	public function removeFolder($path){

		foreach ($this->scripts as $key => $script){
			if(strstr($script[0], $path)){
				unset($this->scripts[$key]);
			}
		}
	}

	/**
	*	Ajoute une liste de scripts à inclure à partir d'un dossier
	*	@param String $folder Chemin du/des scripts à inclure
	*	@param Boolean $recursif L'ajout de dossier est-il récursif
	*/
	public function addFolder($folder, $recursif = true){

		if (!file_exists($folder))
			return true;

		$currentFile = ''; // On enregistre le chemin du fichier en cours de parcours

		foreach (scandir($folder) as $item) {

			$currentFile = $folder.'/'.$item;

			// On ignore les dossiers "." et ".."
			if ($item == '.' || $item == '..')
				continue;

			// Si on tombe sur un fichier
			if (is_file($folder . "/" . $item)) {

				if(strstr($item, '.js')){

					$this->addScript(array($currentFile));
				}

			} else {

				if($recursif){

					// On tombe sur un dossier alors on reboucle
					$this->addFolder($currentFile, $recursif);
				}
			}
		}
	}

	/**
	*	Helper pour savoir si le chemin renseigné
	*	est un dossier, un fichier ou la racine
	*	@param String $path Chemin à tester
	*/
	protected function pathType($path){
		if(strstr($path, '.js')){
			return 'file';
		} elseif($path == '.') {
			return 'root';
		} else {
			return 'folder';
		}
	}

	/**
	*	Génère une liste de <scripts />
	*/
	private function renderScriptList(){

		$r = null;

		foreach($this->scripts as $script){

			$r .= $this->renderScriptTag($script[0])."\n";

		}

		return $r;
	}


	// TO DO
	/**
	*	Génère une balise <script />
	*	@param  String $src   Lien vers le fichier
	*	@param  String $cdn   Paramètre de l'inclusion conditionnelle (défaut "null")
	*	@param  String $ident Niveau d'indentation (par défaut 2 tabulatios)
	*	@return String Balise <link />
	*/
	private function renderScriptTag(
		$src   = null,
		$cdn   = null,
		$ident = 0){

		$r = '';

		if($ident !== 0){
			for($i = 0; $i < $ident; $i++){
				$r .= "\t";
			}
		}
		$r .= '<script type="text/javascript" src="/'.$src.'"></script>';;
		return $r;

	}
}
?>
