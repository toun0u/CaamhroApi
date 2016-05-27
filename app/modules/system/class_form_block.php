<?php
namespace Dims {
	require_once(DIMS_APP_PATH . "/modules/system/class_view.php");

	class form_block{
		private $id;
		private $fields;
		private $title;
		private $classes; //classes css additionnelles
		private $layout;
		private $form;
		private $is_actionnable; //indique si ce bloc contient les boutons d'actions du formulaire
		private $grid;
		private $label_width;
		private $header_level;

		const DEFAULT_BLOCK_LAYOUT = '/modules/system/default_views/forms/default_block_layout.tpl.php'; //template par défaut

		public function __construct($id, $title = '', $layout = '' , $classes = '', $labelWidth = 0.2, $header_level = 3){
			$this->setId($id);
			$this->setTitle($title);
			$this->setClasses($classes);
			$this->setLabelWidth($labelWidth);
			$this->setHeaderLevel($header_level);
			$this->fields = array();
			$this->form = null;
			$this->is_actionnable = false;
			$this->grid = array();

			if(!empty($layout)) $this->setLayout($layout);
			else $this->setLayout(DIMS_APP_PATH . self::DEFAULT_BLOCK_LAYOUT);
		}

		public function setId($val){
			$this->id = $val;
		}

		public function setTitle($val){
			$this->title = $val;
		}

		public function setClasses($val){
			$this->classes = $val;
		}

		public function setLabelWidth($val){
			$this->label_width = $val;
		}

		public function setHeaderLevel($val){
			$this->header_level = $val;
		}

		//sera utilisé rarement, c'est principalement lié au fait de pouvoir présenter les boutons d'action du formulaire dans ce bloc
		public function setForm($val){
			if( ! is_null($val) ) $this->form = $val;
		}

		//méthode permettant d'indiquer au bloc que c'est lui qui doit inclure les bouton d'actions du formulaire
		public function setActionnable($val, $form = null){
			if( ! is_null($form) ) $this->form = $form;
			$this->is_actionnable = $val;
		}

		public function setLayout($val){
			$this->layout = $val;
		}

		public function getId(){
			return $this->id;
		}

		public function getTitle(){
			return $this->title;
		}

		public function getClasses(){
			return $this->classes;
		}

		public function getLabelWidth(){
			return $this->label_width;
		}

		public function getHeaderLevel(){
				return $this->header_level;
			}

		public function getForm(){
			return $this->form;
		}

		public function isActionnable(){
			return $this->is_actionnable;
		}

		public function getFields(){
			return $this->fields;
		}

		public function getGrid(){
			return $this->grid;
		}

		public function getLayout(){
			return $this->layout;
		}

		/* Note : row = null est pour maintenir la compatibilité avec les anciens formulaires */
		public function addField($type, $name, $id = null, $html, $label = '', $mandatory = false, $revision = null, $row = null, $col = 1){
			if( empty($label) ) $label = $name;
			if(is_null($id) || empty($id)){
				$raw_id = $name;
			}
			else{
				$raw_id = $id;
			}

			//Gestion de nom écrasement par nom pour les objets de type radio boutons par exemple
			$max = 0;
			if(isset($this->fields[$name])) $max = count($this->fields[$name]);

			$this->fields[$name][$max]['type'] = $type;
			$this->fields[$name][$max]['id'] = $raw_id;
			$this->fields[$name][$max]['label'] = $label;
			$this->fields[$name][$max]['html'] = $html;
			$this->fields[$name][$max]['mandatory'] = $mandatory;
			$this->fields[$name][$max]['revision'] = $revision;

			if( is_null($row) ){
				//détection du max row
				$m = 1;
				foreach($this->grid as $r => $cols){
					if($r > $m) $m = $r;
				}
				$row = $m + 1;//En gros on rajoute une ligne à chaque nouveau champ
			}
			$this->grid[$row][$col]['name'] = $name;// On fera le pivot sur le nom du champ qui est la référence ultime ici
			$this->grid[$row][$col]['idx'] = $max;
		}

		public function get_field($name){
			if( isset($this->fields[$name]) )
				return $this->fields[$name];
			else return null;
		}
		public function get_field_type($name, $idx = 0){
			if(isset($this->fields[$name][$idx]['type']))
				return $this->fields[$name][$idx]['type'];
			else return null;
		}
		public function get_field_label($name, $idx = 0){
			if(isset($this->fields[$name][$idx]['label']))
				return $this->fields[$name][$idx]['label'];
			else return null;
		}

		public function get_field_html($name, $idx = 0){
			if(isset($this->fields[$name][$idx]['html']))
				return $this->fields[$name][$idx]['html'];
			else return null;
		}

		public function get_field_html_by_id($name, $id){
			if(isset($this->fields[$name])){
				foreach($this->fields[$name] as $idx => $infos){
					if($infos['id'] == $id){
						return $this->fields[$name][$idx]['html'];
					}
				}
			}
			return null;
		}

		public function get_field_id($name, $idx = 0){
			if(isset($this->fields[$name][$idx]['id']))
				return $this->fields[$name][$idx]['id'];
			else return null;
		}

		public function show(){
			include $this->getLayout();
		}

	}
}
