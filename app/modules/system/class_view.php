<?php
/*
* Cyril / 04/09/2012 ->  Gestion des vues de dims. 1ère étape dans la gestion d'un vrai MVC
* Intégration de méthode de construction d'élément HTML
*/
class view {
	const DEFAULT_YIELD = 'default';

	private $tpl;
	private $static_version_file;
	private $template_webpath;
	private $styles_manager;
	private $scripts_manager;

	private $flash_messages;

	private static $view = null;

	/*-------------------- Récupération des chemins d'accès aux fichiers du module -------------*/

	public function __construct(&$tab_flash = array()){
		$this->tpl = array();
		$this->static_version_file = 1;
		$this->template_webpath = '';

		$this->styles_manager = new StylesManager;
		$this->scripts_manager = new ScriptsManager;
		$this->flash_messages = &$tab_flash;
	}

	/**
	*	Singleton
	*/
	public static function getInstance(){
		if(self::$view == null){
			self::setInstance(new view());
		}
		return self::$view;
	}

	/**
	*	Singleton
	*/
	public static function setInstance(view $v){
		if($v != null){
			self::$view = $v ;
		}else{
		   self::$view = new view();
		}
	}

	public function initFlashStructure(&$tab){
		$this->flash_messages = &$tab;
	}

	public function flash($message, $class_css = ''){
		if(!isset($this->flash_messages)) $this->flash_messages = array();
		$this->flash_messages[] = array('message' => $message, 'class' => $class_css);
	}

	public function flushFlash(){
		$temp = $this->flash_messages;
		$this->flash_messages = array();
		return $temp;
	}

	/**
	*	Change le chemin vers un template
	*/
	public function set_tpl_webpath($path){
		$this->template_webpath = $path;
	}

	/**
	*	Définit un numéro de version pour la mise en cache
	*/
	public function set_static_version($version){
		$this->static_version_file = $version;
	}

	/**
	*	Retourne le chemin vers un fichier statique avec un numéro de version
	*	pour la mise en chache
	*/
	public function getTemplateWebPath($file = '') {
		$webPath = '/common/'.$this->template_webpath;

		if(!empty($file)) {
			$webPath .= $file.'?'.$this->get_static_version();
		}

		return $webPath;
	}

	/**
	*	Retourne le chemin vers un fichier ($file) contenant une vue
	*/
	public function getTemplatePath($file = '') {
		return DIMS_APP_PATH.$this->template_webpath.$file;
	}

	/**
	*	Retourne 1 gg wp
	*/
	public function get_static_version() {
		return $this->static_version_file;
	}

	/**
	*	Retourne une instance du gestionnaire de styles
	*/
	public function getStylesManager() {
		return $this->styles_manager;
	}

	/**
	*	Retourne une instance du gestionnaire de scripts
	*/
	public function getScriptsManager() {
		return $this->scripts_manager;
	}

	/* -----------------------------------------------	FONCTION DEDIEES A LA GESTION DES TPLS ---*/

	/**
	*	Définit le layout principal du template
	*/
	public function setLayout($path){
		$this->tpl['layout']['path'] = $path;
	}

	/**
	*	Assigne une variable à la vue
	*/
	public function assign($key, $value){
		$this->tpl['vars'][$key] = $value;
	}

	public function has($key){
		return isset($this->tpl['vars'][$key]);
	}

	//Fonction permettant de récupérer la valeur associée à une clef du tpl
	public function get($key){
		return (isset($this->tpl['vars'][$key])) ? $this->tpl['vars'][$key] : null;
	}

	// Store le tpl.php à afficher dans la zone de contenu dynamique yield
	// Par défaut, le yield utilisé est le yield self::DEFAULT_YIELD
	public function render($path, $yield = self::DEFAULT_YIELD){
		$this->tpl['layout']['yields'][$yield] = $path;
	}

	//Dans une vue TPL, permet d'aller chercher une vue partielle directement et de l'inclure à l'endroit de l'appel
	public function partial($path, $object = array()){
		if(!empty($path)) include $path;
	}

	//function qui sera utilisée dans les layout pour inclure les yields correspondant à chaque zone dynamique
	public function yields($yield = self::DEFAULT_YIELD){
		if(!empty($this->tpl['layout']['yields'][$yield]) && file_exists($this->getTemplatePath($this->tpl['layout']['yields'][$yield])))
			include $this->getTemplatePath($this->tpl['layout']['yields'][$yield]);
	}

	// Test existence of a yield section
	public function isYieldable($yield = self::DEFAULT_YIELD) {
		return (!empty($this->tpl['layout']['yields'][$yield]) && file_exists($this->getTemplatePath($this->tpl['layout']['yields'][$yield])));
	}

	public function clear($yield = null){
		if(is_null($yield)) unset($this->tpl['layout']['yields']);
		else unset($this->tpl['layout']['yields'][$yield]);
	}

	//function qui sera utilisée dans les layout pour inclure les render correspondant à chaque zone dynamique
	public function compute(){
		if(file_exists($this->getTemplatePath($this->tpl['layout']['path']))){
			include $this->getTemplatePath($this->tpl['layout']['path']);
			$this->debugConsole();
		}else
			echo "don t exists ".$this->tpl['layout']['path'];
	}

	//fonction permettant de compiler une vue et de la retourner dans une chaîne de caractère
	public function compile(){
		ob_start();
		$this->compute();
		$compilation = ob_get_contents();
		ob_end_clean();
		return $compilation;
	}

	public function debugConsole(){
		if(defined('_VIEW_DEBUG') && _VIEW_DEBUG){
			ob_start();
			dims_print_r($this->tpl);
			$consoleTpl = ob_get_contents();
			ob_end_clean();
			?>
			<script type="text/javascript">
				var _view_console = window.open("","Console : <?= $this->get_static_version(); ?>","width=680,height=600,resizable,scrollbars=yes");
				_view_console.document.write('<?= str_replace(array("'","\n"),array("\'","\\<br />"),$consoleTpl); ?>');
			</script>
			<?php
		}
	}

	public function image_tag($path, $with_tpl_path = true, $attributes = array()){
		if($with_tpl_path) $src = $this->getTemplateWebPath($path);
		else $src = $path;
		$img = '<img src="'.$src.'" ';
		foreach($attributes as $attr => $val){
			$img .= $attr.'="'.$val.'" ';
		}
		$img .= '/>';
		return $img;
	}
}
?>
