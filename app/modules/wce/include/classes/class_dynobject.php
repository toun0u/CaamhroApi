<?php
	require_once DIMS_APP_PATH.'/modules/wce/include/classes/class_article_object.php';
	class DynObject {
		public $object;
		public $smarty;
		public $external_params;
		public $path;

		public function __construct($id_object = null, &$smarty = null, $params = array(), $type = null){
			if( ! is_null($id_object) ) {
				$this->object = new article_object();
				$this->object->open($id_object);
			}elseif(!is_null($type)){
				switch ($type) {
					case 'dyn_planning':
						$this->object = new article_object();
						$this->object->init_description();
						$this->object->setNew(false);
						$this->object->fields['template'] = 'planning';
						break;
				}
			}
			if($smarty != null)	$this->setSmartyReference($smarty);
			if( is_null($params) ) $params = array();
			$this->setExternalParams($params);
		}

		public function buildIHM(){
			if(isset($this->object) && !$this->object->isNew() && !empty($this->object->fields['template']) && file_exists(DIMS_APP_PATH.'templates/objects/'.$this->object->fields['template'].'/'.ucfirst($this->object->fields['template']).'Controller.php') ) {
				require_once DIMS_APP_PATH.'templates/objects/'.$this->object->fields['template'].'/'.ucfirst($this->object->fields['template']).'Controller.php';
				$class_name = ucfirst($this->object->fields['template']).'Controller';
				$control = new $class_name;
				$control->setObject($this->object);
				$control->setExternalParams($this->external_params);
				$control->setSmartyReference($this->smarty);
				return $control->buildIHM(); //renvoie un tpl_path à display par SMARTY
			}
			else return null;
		}

		public function setSmartyReference(&$smarty){
			$this->smarty = &$smarty;
		}

		public function setExternalParams($params){
			$this->external_params = $params;
		}

		public function addParam($key, $value){
			$this->external_params[$key] = $value;
		}

		public function getParam($key){
			return isset($this->external_params[$key]) ? $this->external_params[$key] : null;
		}

		public function setTPLPath($path){
			$this->path = $path;
		}

		public function getTPLPath(){
			return $this->path;
		}

		public function setObject($object){
			$this->object = $object;
		}
	}
?>