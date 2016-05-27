<?php
/**
*	StyleManager
*	Permet de gérer un ensemble de styles, compressés en production
*	@author Baptiste Wallerich @ Netlor
*	@author Kévin Henry @ Netlor
*	@package Includes
*/
class StylesManager extends AssetsManager {

	/**
	*	Tableau contenant les styles à inclure en développement
	*/
	public $styles = array();

	/**
	*	Fichier de configuration décodé
	*/
	public $outputs;

	/**
	*	Adresse du dossier des styles
	*/
	public static $stylesPath;

		/**
		*	Adresse du dossier de compression
		*/
		public static $minPath;

		/**
		*	Adresse du dossier contenant les librairies
		*/
		public static $libsPath;

	/**
	*	Points d'entrée
	*/
	private $entryPoints = array();


	/**
	*	Initialisation de la classe
	*/
	function __construct(){

		// Appel au constructeur parent
		parent::__construct();

		// Initialisation de la racine des styles
		self::$stylesPath = parent::$assetsPath.'stylesheets/';

		// Initialisation de la racine des styles
		self::$minPath    = self::$stylesPath.'min/';

		// Initialisation du dossier des librairies
		self::$libsPath   = self::$stylesPath.'common/';

 		// Inclusion des styles à partir du fichier de configuration
		$this->loadConfig();
	}

	/**
	*	Charge le fichier de configuration
	*/
	private function loadConfig(){
		$this->outputs = json_decode(file_get_contents(parent::$assetsPath.'styles_outputs.json'), true);
		if(is_null($this->outputs)){
			echo "Erreur de syntaxe dans styles_outputs.json<br />";
			echo '<a href="http://jsonlint.com/">Vérificateur de syntaxe</a>';
		}
	}

	/**
	*	Fusionne les fichiers css pour n'en servir qu'un seul
	*
	*/
	public function merge($debug = false, $colors = null, $config = null){

		echo $colors->getColoredString("\n-- Mise en production des styles --\n\n", 'blue');

		// Alias
		$compass = self::$assetsPath;
		$styles  = self::$stylesPath;

		// Compass me this
		echo "0. Compilation compass, soyez patient !\n\n";
		chdir($compass);
		exec("compass compile -c $config --force", $o);
		sleep(0.5);
		echo "[ ".$colors->getColoredString('OK', 'green')." ]\n\n";

		echo "1. Recherche des fichiers à compresser\n\n";

			$front = $styles.'frontoffice';
			if(file_exists($styles.'frontoffice')){
				$front = scandir($front);
				unset($front[0]); unset($front[1]);
			}
			$back = $styles.'backoffice';
			if(file_exists($styles.'backoffice')){
				$back = scandir($back);
				unset($back[0]); unset($back[1]);
			}
			$modules = $styles.'modules';
			if(file_exists($styles.'modules')){
				$modules = scandir($modules);
				unset($modules[0]); unset($modules[1]);
			}

		if($debug == "true"){
			echo count($front)." templates frontoffice\n";
			echo count($back)." templates backoffice\n";
			echo count($modules)." modules\n\n";
		}

		echo "2. Création de l'arborescence\n\n";sleep(0.5);

		$dirs = array(
			'min/',
			'min/frontoffice',
			'min/backoffice',
			'min/modules',
			'min/common'
		);
		$c = 0;
		foreach($dirs as $dir){

			if(!is_dir(self::$stylesPath.$dir)){

				mkdir(self::$stylesPath.$dir);
				echo "[ ".$colors->getColoredString('INFO', 'yellow')." ] ";
				echo "Dossier ".self::$stylesPath.$dir.' créé'."\n";
				$c++;
			}
		}
		if($c == 0){
			echo "[ ".$colors->getColoredString('INFO', 'yellow')." ] L'arborescence existait déjà\n";
		}
		echo "[ ".$colors->getColoredString('OK', 'green')." ]\n\n";

		echo "3. Fusion des fichiers des templates frontoffice\n\n";sleep(0.5);
		if(!empty($front) && is_array($front)){
			foreach($front as $loadMe){

				// Analyse du fichier de configuration
				$this->loadRessource('frontoffice', $loadMe);

				// Fusion des styles
				$r = '';
				foreach($this->styles as $style){
					if(empty($style[2])			// Si ce n'est pas un fix IE
					&& $style[1] == 'screen'){  // Si ce n'est pas un media particulier
						if($debug == "true") $r .= "\n".'/* '.str_replace('/var/www/dims/app/www/', '', $style[0]).' */'."\n\n";
						$r .= file_get_contents($style[0]);
					}
				}

				// Création du dossier lors de l'ajout de nouveau templates
				if(!is_dir(self::$stylesPath.'min/frontoffice/'.$loadMe)){

					if(!mkdir(self::$stylesPath.'min/frontoffice/'.$loadMe)){
						echo "[ERREUR] Impossible de créer le dossier : ".self::$stylesPath.'min/frontoffice/'.$loadMe;
						return false;
					}
				}

				file_put_contents(self::$stylesPath.'min/frontoffice/'.$loadMe.'/'.$loadMe.'.min.css', $r);
				if($debug == "true"){
					echo '['.$colors->getColoredString('OK','green').'] ';
					echo 'min/frontoffice/'.$loadMe.'/'.$loadMe.'.min.css'."\n";
				}

				$this->clean();

			}
		}
		echo "[ ".$colors->getColoredString('OK', 'green')." ]\n\n";

		echo "4. Fusion des fichiers des templates backoffice\n\n";
		if(!empty($back) && is_array($back)){
			foreach($back as $loadMe){

				$this->loadRessource('backoffice', $loadMe);

				$r = '';
				foreach($this->styles as $style){
					if(empty($style[2])			// Si ce n'est pas un fix IE
					&& $style[1] == 'screen'){  // Si ce n'est pas un media particulier

						if($debug == "true") $r .= "\n".'/* '.str_replace('/var/www/dims/app/www/', '', $style[0]).' */'."\n\n";
						$r .= file_get_contents($style[0]);
					}
				}

				// Création du dossier lors de l'ajout de nouveau templates
				if(!is_dir(self::$stylesPath.'min/backoffice/'.$loadMe)){

					if(!mkdir(self::$stylesPath.'min/backoffice/'.$loadMe)){
						echo "[ERREUR] Impossible de créer le dossier : ".self::$stylesPath.'min/backoffice/'.$loadMe;
						return false;
					}
				}

				file_put_contents(self::$stylesPath.'min/backoffice/'.$loadMe.'/'.$loadMe.'.min.css', $r);
				if($debug == "true"){
					echo '['.$colors->getColoredString('OK','green').'] ';
					echo ' min/backoffice/'.$loadMe.'/'.$loadMe.'.min.css'."\n";
				}

				$this->clean();

			}
		}
		echo "[ ".$colors->getColoredString('OK', 'green')." ]\n\n";

		echo "5. Fusion des fichiers des modules\n\n";
		if(!empty($modules) && is_array($modules)){
			foreach($modules as $loadMe){
				$this->loadRessource('modules', $loadMe);//cyril&thomas : fix seul le system avait des styles ...
				$r = '';
				foreach($this->styles as $style){
					if(empty($style[2])			// Si ce n'est pas un fix IE
					&& $style[1] == 'screen'){  // Si ce n'est pas un media particulier
						if($debug == "true") $r .= "\n".'/* '.str_replace('/var/www/dims/app/www/', '', $style[0]).' */'."\n\n";
						$r .= file_get_contents($style[0]);
					}
					if(!is_dir(self::$stylesPath.'min/modules/'.$loadMe)){
						if(!mkdir(self::$stylesPath.'min/modules/'.$loadMe)){
							echo "[ERREUR] Impossible de créer le dossier : ".self::$stylesPath.'min/modules/'.$loadMe;
							return false;
						}
					}
					file_put_contents(self::$stylesPath.'min/modules/'.$loadMe.'/'.$loadMe.'.min.css', $r);
					if($debug == "true"){
						echo '['.$colors->getColoredString('OK','green').'] ';
						echo ' min/modules/'.$loadMe.'/'.$loadMe.'.min.css'."\n";
					}

					$this->clean();

				}
			}
		}
		echo "[ ".$colors->getColoredString('OK', 'green')." ]\n\n";

		echo "6. Compression des librairies communes\n\n";

		$d = scandir(self::$stylesPath.'common');
		unset($d[0]); unset($d[1]);
		$common_libs = $d;

		foreach ($common_libs as $loadMe){

			$this->addFolder(self::$stylesPath.'common/'.$loadMe);
			//var_dump($this->styles); die();

			$r = '';
			foreach($this->styles as $style){
				if(empty($style[2])			// Si ce n'est pas un fix IE
				&& $style[1] == 'screen'){  // Si ce n'est pas un media particulier

					if($debug == "true") $r .= "\n".'/* '.str_replace('/var/www/dims/app/www/', '', $style[0]).' */'."\n\n";
					$r .= file_get_contents($style[0]);
				}
			}

			if(!is_dir(self::$stylesPath.'min/common/'.$loadMe)){

				if(!mkdir(self::$stylesPath.'min/common/'.$loadMe)){
					echo "[ERREUR] Impossible de créer le dossier : ".self::$stylesPath.'min/common/'.$loadMe;
					return false;
				}
			}
			file_put_contents(self::$stylesPath.'min/common/'.$loadMe.'/'.$loadMe.'.min.css', $r);
			if($debug == "true"){
				echo '['.$colors->getColoredString('OK','green').'] ';
				echo 'min/common/'.$loadMe.'/'.$loadMe.'.min.css'."\n";
			}
			$this->clean();

		}
		echo "[ ".$colors->getColoredString('OK', 'green')." ]\n\n";



		//var_dump($common_libs); die();



	}

	private function clean(){
		$this->styles = array();
	}

	/**
	*	Inclusion conditionnelle des styles du template en fonction de _DIMS_DEBUGMODE
	*/
	public function includeStyles(){

		if(count($this->styles) == 0){
			echo 'Déclaration de <b>"'.$this->entryPoints[0]['nom'].'"</b> introuvable dans la section <b>"'.$this->entryPoints[0]['type'].'"</b> de styles_outputs.json <br>';
			return false;
		}


		// On veut donner une adresse de fichier sur le serveur
		// et non plus sur le système de fichiers
		foreach ($this->styles as $key => $style) {

			$this->styles[$key][0] = str_replace($_SERVER['DOCUMENT_ROOT'],'/', $style[0]);

		}

		// Si on est en prod
		if( ! _DIMS_DEBUGMODE ){

			/**
			*	Lors de la compression des styles
			*	- Garde la trace des styles qui ne sont pas inclus
			*	-
			*
			*/
			//var_dump($this->entryPoints);

			$this->clean();

			$this->entryPoints = array_reverse($this->entryPoints);

			foreach ($this->entryPoints as $entryPoint) {
				$style = $entryPoint['nom'].'.min.css';
				$this->addStyle(self::$minPath.$entryPoint['type'].'/'.$entryPoint['nom'].'/'.$style);
			}

			foreach ($this->styles as $key => $style) {

				$this->styles[$key][0] = str_replace($_SERVER['DOCUMENT_ROOT'],'/', $style[0]);

			}
			return $this->renderLinkList();

		// Si on est en dev
		} else {

			return $this->renderLinkList();

		}
	}

	/**
	*	Ajoute des feuillets de styles au manager
	*	@param String $type ('back', 'front', 'modules') Type de ressource à inclure
	*	@param String $nom  Nom de la ressource à inclure
	*/
	public function loadRessource(
		$type = null,
		$nom  = null,
		$skipModuleDefaultIncludes = false
	){


		/**
		*	Vérifications des paramètres
		*/
		if(empty($type)){

			echo "Veuillez spécifier un type de ressource ('backoffice', 'frontoffice', 'modules') à inclure";
			return false;
		}
		if(empty($nom)){

			echo "Veuillez spécifier le nom de ressource à inclure";
			return false;
		}

		/**
		*	Définition du point d'entrée pour faciliter l'inclusion conditionnelle
		*/
		$this->entryPoints[] = array('nom' => $nom, 'type' => $type);

		/**
		*	Trouve-t'on ce que l'on cherche ?
		*/
		switch($type){

			case 'default':

				// 1. Incustion des librairies par défaut
				$basicIncludes = $this->outputs['default']['includes'];
				foreach($basicIncludes as $includeMe){

					$this->addFolder(self::$stylesPath.$includeMe);
				}

				//  2. Exclusion des librairies par défaut
				$defaultIncludes = $this->outputs['default']['excludes'];
				foreach($defaultIncludes as $excludeMe){

					// 3.1 Dossier
					if($this->pathType($excludeMe) == 'folder'){

						$this->removeFolder(self::$stylesPath.$excludeMe);

					// 3.2 Fichier
					} else {

						$this->removeStyle(self::$stylesPath.$excludeMe);
					}
				}

				// 3. Inclusion des styles propres au module
				$appIncludes = self::$stylesPath.'modules/'.$nom;
				$this->addFolder($appIncludes);

				break; // DAMN

			// Templates
			case 'backoffice':
			case 'frontoffice':

				$found = false;
				$index = 0;
				foreach ($this->outputs['default']['exceptions'][$type] as $key => $tpl) {

					if($key == $nom){
						$found = true;
					}
				}

				// Le template n'existe pas dans le fichier de conf
				if(!$found){

					// Inclusion des librairies par défaut 'includes'
					$basicIncludes = $this->outputs['default']['includes'];
					foreach($basicIncludes as $includeMe){
						$this->addFolder(self::$stylesPath.$includeMe);
					}

					// Exclusion des librairies par défaut
					$defaultIncludes = $this->outputs['default']['excludes'];

					foreach($defaultIncludes as $excludeMe){

						// 3.1 Dossier
						if($this->pathType($excludeMe) == 'folder'){

							$this->removeFolder(self::$stylesPath.$excludeMe);

						// 3.2 Fichier
						} else {
							$this->removeStyle(self::$stylesPath.$excludeMe);
						}
					}

					// On essaie d'inclure le dossier avec les informations dont on dispose
					$appIncludes = self::$stylesPath.$type.'/'.$nom;
					$this->addFolder($appIncludes);

					// var_dump($this->styles); die();

				} else {

					//  1.  Récupérer la clé  du tableau contenant les données
					$app = $this->outputs['default']['exceptions'][$type][$nom];

					//  2.  Inclusion des librairies par défaut 'includes'
					$basicIncludes = $this->outputs['default']['includes'];
					foreach($basicIncludes as $includeMe){
						$this->addFolder(self::$stylesPath.$includeMe);
					}

					//  3.  Exclusion des librairies par défaut
					$defaultIncludes = $this->outputs['default']['excludes'];

					foreach($defaultIncludes as $excludeMe){

						// 3.1 Dossier
						if($this->pathType($excludeMe) == 'folder'){

							$this->removeFolder(self::$stylesPath.$excludeMe);

						// 3.2 Fichier
						} else {

							$this->removeStyle(self::$stylesPath.$excludeMe);
						}
					}
					//var_dump($this->styles); die();

					//  6. Inclusion de styles externes au template
					if(array_key_exists('includes', $app)){
						$appIncludesExternal = $app['includes'];
					}

					if(!empty($appIncludesExternal)){

						foreach($appIncludesExternal as $includeMe){

							if($this->pathType($includeMe) == 'folder'){

								$this->addFolder(self::$stylesPath.$includeMe);

							} else {

								$this->addStyle(self::$stylesPath.$includeMe);
							}
						}
					}

					//  4.  Inclusion par dossier à partir de $type et $name
					$appIncludes = self::$stylesPath.$type.'/'.$nom;
					$this->addFolder($appIncludes);
					//var_dump($this->styles); die();

					//  5.  Exclusion des styles spécifiés
					if(array_key_exists('excludes', $app)){
						$appExcludes = $app['excludes'];
					}
					if(!empty($appExcludes)){

						foreach($appExcludes as $excludeMe){

							if($this->pathType($excludeMe) == 'folder'){

								$this->removeFolder(self::$stylesPath.$excludeMe);

							} else {

								$this->removeStyle(self::$stylesPath.$excludeMe);
							}
						}
					}
					//var_dump($this->styles); die();



					//  7.  Inclusion des fixs
					if(array_key_exists('fixs', $app)){
						$ieFixs = $app['fixs'];
					}

					if(!empty($ieFixs)){

						foreach($ieFixs as $excludeMe){

							$this->removeStyle(self::$stylesPath.$type.'/'.$nom.'/'.$excludeMe[0]);
						}

						foreach($ieFixs as $fix){

							if($this->pathType($fix[0]) == 'folder'){

								echo "Les fixs doivent être des fichiers et non des dossiers.<br />
								Veuillez vérifier la syntaxe du fichier de configuration";
								return false;

							} else {

								// Inclusions des fixs
								$this->addStyle(self::$stylesPath.$type.'/'.$nom.'/'.$fix[0], 'screen', $fix[1]);

							}
						}
					}
					// echo "<h2>Template</h2>";
					// var_dump($this->styles);

					// (6.) Inclusion des styles customs

				}

				break;

			// Modules
			case 'modules':

				$found = false;
				foreach(array_keys($this->outputs) as $key){
					if($key == $nom){
						$found = true;
					}
				}

				// Le module n'existe pas dans le fichier de conf
				// ou son nom a mal été saisi
				if(!$found){

					// 0. Suppression des styles inclus dans default
					$this->removeFolder(self::$stylesPath.$type.'/'.$nom);

					// 1. Inclusions des styles du modules
					$module = self::$stylesPath.$type.'/'.$nom;
					$this->addFolder($module);

				} else {

					// Alias des familles yay
					$mod = $this->outputs[$nom];

					// 0. Suppression des styles inclus dans default
					$this->removeFolder(self::$stylesPath.$type.'/'.$nom);


					// 2. Inclusions supplémentaires
					if(!empty($mod['includes'])){

						foreach($mod['includes'] as $includeMe){

							if($this->pathType($includeMe) == 'folder'){

								$this->addFolder(self::$stylesPath.$includeMe);

							} else {

								$this->addStyle(self::$stylesPath.$includeMe);
							}
						}
					} else {
						$module = self::$stylesPath.$type.'/'.$nom;
						$this->addFolder($module);

					}
					//var_dump($this->styles); die();

					// 3. Exclusions de styles
					if(!empty($mod['excludes'])){

						foreach($mod['excludes'] as $excludeMe){

							if($this->pathType($excludeMe) == 'folder'){

								$this->removeFolder(self::$stylesPath.$excludeMe);

							} else {

								$this->removeStyle(self::$stylesPath.$excludeMe);
							}
						}
					}
					// var_dump($this->styles); die();

					//  4.  Inclusion des fixs
					if(array_key_exists('fixs', $mod)){
						$ieFixs = $mod['fixs'];
					}

					if(!empty($ieFixs)){

						foreach($ieFixs as $excludeMe){

							$this->removeStyle(self::$stylesPath.$type.'/'.$nom.'/'.$excludeMe[0]);
						}

						foreach($ieFixs as $fix){

							if($this->pathType($fix[0]) == 'folder'){

								echo "Les fixs doivent être des fichiers et non des dossiers.<br />
								Veuillez vérifier la syntaxe du fichier de configuration";
								return false;

							} else {

								// Inclusions des fixs
								$this->addStyle(self::$stylesPath.$fix[0], 'screen', $fix[1]);

							}
						}
					}
				}

				break;

			default:

				echo "Veuillez spécifier un type de ressource valide !  ('back', 'front', 'modules', 'default')";
				return false;
				break;
		}
	}

	/**
	*	Ajoute un style à inclure en dev
	*	@param Array $path Style à inclure
	*	@param String $media Media du style à inclure (optionnel)
	*	@param Boolean $conditionnelle Commentaire conditionnel (optionel)
	*/
	public function addStyle($path, $media = 'screen', $conditionnelle = null){
		$style = array($path, $media, $conditionnelle);
		$this->styles[] = $style;
	}

	public function getStyles(){
		return (!empty($this->styles))?$this->styles:array();
	}

	/**
	*	Supprime un style à ne pas inclure
	*	@param String $excludeMe Style à exclure
	*/
	public function removeStyle($excludeMe){
		if(!empty($this->styles)){
			foreach($this->styles as $key => $style){
				if($style[0] == $excludeMe){
					unset($this->styles[$key]);
				}
			}
		}
	}

	/**
	*	Ajoute une liste de styles à inclure à partir d'un dossier
	*	@param String $folder Chemin du/des styles à inclure
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

				if(strstr($item, '.css')){

					$this->addStyle($currentFile);
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
	*	Supprime un dossier de styles à ne pas inclure
	*	@param String $path Chemin vers le dossier à exclure
	*/
	public function removeFolder($path){

		foreach ($this->styles as $key => $style){
			if(strstr($style[0], $path)){
				unset($this->styles[$key]);
			}
		}
	}

	/**
	*	Génère une liste de <link />
	*/
	private function renderLinkList(){

		$r = null;

		foreach($this->styles as $style){
			$r .= $this->renderLinkTag(null, $style[0], $style[1], $style[2], 1);
		}

		return $r;
	}

	/**
	*	Génère une balise <link />
	*	@param  String $title Titre du feuillet de style
	*	@param  String $src   Lien vers le fichier
	*	@param  String $media Type de média du CSS (défaut "screen")
	*	@param  String $conditionnel  Paramètre de l'inclusion conditionnelle (défaut "null")
	*	@param  String $ident  Niveau d'indentation (par défaut 2 tabulatios)
	*	@return String Balise <link />
	*/
	private function renderLinkTag(
		$title        = "Style principal",
		$src          = null,
		$media        = "screen",
		$conditionnel = null,
		$ident        = 0){

		$r = '';

		if($ident !== 0){
			for($i = 0; $i < $ident; $i++){
				$r .= "\t";
			}
		}

		if(is_null($conditionnel)){

			$r  .= '<link title="'.$title.'" media="'.$media.'"';
			$r .= ' href="'.$src.'"';
			$r .= ' rel="stylesheet" type="text/css" />'."\n";

			return $r;

		} else {

			$r  .= '<!--[if '.$conditionnel.']>';
			$r .= '<link title="'.$title.'" media="'.$media.'"';
			$r .= ' href="'.$src.'"';
			$r .= ' rel="stylesheet" type="text/css" />';
			$r .= '<![endif]-->'."\n";

			return $r;

		}
	}

	/**
	*	Helper pour savoir si le chemin renseigné
	*	est un dossier, un fichier ou la racine
	*	@param String $path Chemin à tester
	*/
	protected function pathType($path){
		if(strstr($path, '.css')){
			return 'file';
		} elseif($path == '.') {
			return 'root';
		} else {
			return 'folder';
		}
	}

}
?>
