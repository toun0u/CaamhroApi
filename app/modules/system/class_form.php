<?php

namespace Dims {

	require_once(DIMS_APP_PATH . "/modules/system/class_view.php");
	require_once(DIMS_APP_PATH . "/include/class_form_tokenizer.php");

	class form {

		private $name;
		private $id;
		private $action;
		private $method;
		private $enctype;
		private $validation;
		private $submit_value;
		private $back_url;
		private $back_name;
		private $continue;
		private $force_continue;
		private $additional_js;
		private $include_actions_block;
		private $extended_controls;
		private $additionnal_attributes;
		private $ajax_submit;
		private $ajax_undo;
		private $tokenizer;
		private $label_width;
		private $global_message_error;
		private $ref_attribute;

		private $object; //objet Dims qui est eventuellement concerné par le formulaire
		private $tpl; //le template d'affichage du

		const DEFAULT_LAYOUT = 'modules/system/default_views/forms/default_form.tpl.php'; //template par défaut

		private $blocks;//liste des sections visuelles du formulaire

		public function __construct($params = array()){

			$default_params = array();
			$default_params['name'] = 'dims_form';
			$default_params['object'] = null;
			$default_params['tpl'] = null;
			$default_params['action'] = '#';
			$default_params['method'] = 'POST';
			$default_params['submit_value'] = 'ok';
			$default_params['back_name'] = 'Annuler';
			$default_params['back_url'] = '#';
			$default_params['continue'] = false;
			$default_params['force_continue'] = false;
			$default_params['additional_js'] = '';
			$default_params['enctype'] = false;
			$default_params['validation'] = true;
			$default_params['include_actions'] = true;
			$default_params['extended_controls'] = null;
			$default_params['additionnal_attributes'] = null;
			$default_params['ajax_submit'] = false;
			$default_params['ajax_undo'] = false;
			$default_params['label_width'] = 0.2;
			$default_params['global_message_error'] = addslashes($_SESSION['cste']['PLEASE_VERIFY_FIELDS']);
			$default_params['ref_attribute'] = 'name';//l'attribut DOM sur lequel le dims_valid_form s'appuie pour afficher le petit message d'erreur sous le champ

			$p = array_merge($default_params, $params);//Merge des paramètres

			$this->tokenizer = new \FormToken\TokenField;

			$this->setName($p['name']);
			$this->setObject($p['object']);
			$this->setId($p['name']);//par défaut on place le même id que le nom du formulaire
			$this->setAction($p['action']);
			$this->setMethod($p['method']);
			$this->setEnctype($p['enctype']);
			$this->setValidation($p['validation']);
			$this->setSubmitValue($p['submit_value']);
			$this->setBackUrl($p['back_url']);
			$this->setBackName($p['back_name']);
			$this->setContinue($p['continue']);
			$this->setForceContinue($p['force_continue']);
			$this->setAdditionalJS($p['additional_js']);
			$this->setIncludeActionsBlock($p['include_actions']);
			$this->setExtendedControls($p['extended_controls']);
			$this->setAdditionnalAttributes($p['additionnal_attributes']);
			$this->setAjaxSubmit($p['ajax_submit']);
			$this->setAjaxUndo($p['ajax_undo']);
			$this->setLabelWidth($p['label_width']);
			$this->setGlobalMessageError($p['global_message_error']);
			$this->setRefAttribute($p['ref_attribute']);

			if( ! is_null($p['tpl']) )
				$this->setTpl($p['tpl']); //hérité de la classe view
			else $this->setTpl(DIMS_APP_PATH . self::DEFAULT_LAYOUT);

			$this->blocks = array();
			$this->addBlock('default');//quoi qu'il arrive on ajoute un bloc par défaut pour le formulaire

			//Traitement particulier pour l'id_globalobject de l'objet
			if(isset($this->object) && !$this->object->isNew() && isset($this->object->fields['id_globalobject'])){
				$this->tokenizer->field('id_globalobject', $this->object->get('id_globalobject'));
			}
			if(isset($this->object)){
				foreach($this->object->getAllLightAttributes() as $attr => $val){
					$this->tokenizer->field($attr, $val);
				}
			}

			if( $this->isForceContinueEnabled() || ( ( ( isset($this->object) && $this->object->isNew() ) || is_null($this->object) ) && $this->isContinueEnabled() ) ){
				$this->tokenizer->field('continue');
			}
		}

		public function setName($val){
			$this->name = $val;
		}
		public function setObject($val){
			$this->object = $val;
		}
		public function setId($val){
			$this->id = $val;
		}
		public function setAction($val){
			$this->action = $val;
		}
		public function setMethod($val){
			$this->method = $val;
		}
		public function setEnctype($val){
			$this->enctype = $val;
		}
		public function setValidation($val){
			$this->validation = $val;
		}

		public function setTpl($val){
			$this->tpl = $val;
		}
		public function setSubmitValue($val){
			$this->submit_value = $val;
		}
		public function setBackUrl($val){
			$this->back_url = $val;
		}
		public function setBackName($val){
			$this->back_name = $val;
		}
		public function setContinue($val){
			$this->continue = $val;
		}
		public function setForceContinue($val){
			$this->force_continue = $val;
		}
		public function setAdditionalJS($val){
			$this->additional_js = $val;
		}
		public function setIncludeActionsBlock($val){
			$this->include_actions_block = $val;
		}
		public function setExtendedControls($val){
			$this->extended_controls = $val;
		}
		public function setAdditionnalAttributes($val){
			$this->additionnal_attributes = $val;
		}
		public function setAjaxSubmit($val){
			$this->ajax_submit = $val;
		}
		public function setAjaxUndo($val){
			$this->ajax_undo = $val;
		}
		public function setLabelWidth($val){
			$this->label_width = $val;
		}
		public function setGlobalMessageError($val){
			$this->global_message_error = $val;
		}

		public function setRefAttribute($val){
			$this->ref_attribute = $val;
		}

		public function getName(){
			return $this->name;
		}

		public function getObject(){
			return $this->object;
		}
		public function getId(){
			return $this->id;
		}
		public function getAction(){
			return $this->action;
		}
		public function getMethod(){
			return $this->method;
		}
		public function isEnctype(){
			return $this->enctype;
		}
		public function isValidationEnabled(){
			return $this->validation;
		}
		public function getSubmitValue(){
			return $this->submit_value;
		}
		public function getBackUrl(){
			return $this->back_url;
		}
		public function getBackName(){
			return $this->back_name;
		}
		public function isContinueEnabled(){
			return $this->continue;
		}
		public function isForceContinueEnabled(){
			return $this->force_continue;
		}
		public function getAdditionalJS(){
			return $this->additional_js;
		}
		public function includesActionsBlock(){
			return $this->include_actions_block;
		}

		public function getExtendedControls(){
			return $this->extended_controls;
		}
		public function getAdditionalAttributes(){
			return ! is_null($this->additionnal_attributes) ? $this->additionnal_attributes : '';
		}
		public function getAjaxSubmit(){
			return $this->ajax_submit;
		}
		public function getAjaxUndo(){
			return $this->ajax_undo;
		}
		public function getLabelWidth(){
			return $this->label_width;
		}

		public function getTpl(){
			return $this->tpl;
		}

		public function getGlobalMessageError(){
			return $this->global_message_error;
		}

		public function getRefAttribute(){
			return $this->ref_attribute;
		}
		public function addBlock($id, $title = '', $layout = '', $classes = '', $header_level = 3){
			$block = new form_block($id, $title, $layout, $classes,$this->getLabelWidth(), $header_level);
			$this->blocks[$id] = $block;

			$fields = $block->getFields();
			foreach ($fields as $f) {
				$this->tokenizer->field($f);
			}

			return $block;
		}

		public function getBlocks(){
			return $this->blocks;
		}

		public function getBlock($id){
			return $this->blocks[$id];
		}

		public function displayActionsBlock($tpl = null){
			if( is_null($tpl)) $tpl = DIMS_APP_PATH . 'modules/system/default_views/forms/block_actions.tpl.php';
			include $tpl;
		}

		/* Fonction de création d'un input type text */
		public function text_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'text_field';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<input type="text" name="'.$p['name'].'" id="'.$p['id'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) &&  isset($this->object->fields[$p['db_field']]) ){
				$field .= ' value="'.$this->object->fields[$p['db_field']].'" ';
			}
			else if( ! is_null($p['value']) && isset($p['value'])) $field .= ' value="'.$p['value'].'" ';
			if($p['mandatory']) $field .= ' rel="requis" ';
			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';

			$field .= ' '.$p['additionnal_attributes']. ' />';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		public function password_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'password_field';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<input type="password" name="'.$p['name'].'" id="'.$p['id'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) && isset($this->object->fields[$p['db_field']]) ){
				$field .= ' value="'.$this->object->fields[$p['db_field']].'" ';
			}
			else if( ! is_null($p['value']) && !empty($p['value'])) $field .= ' value="'.$p['value'].'" ';
			if($p['mandatory']) $field .= ' rel="requis" ';
			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';

			$field .= ' '.$p['additionnal_attributes']. ' />';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		public function hidden_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'hidden_field';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<input type="hidden" name="'.$p['name'].'" id="'.$p['id'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) && isset($this->object->fields[$p['db_field']]) ){
				$field .= ' value="'.$this->object->fields[$p['db_field']].'" ';
			}
			else if( ! is_null($p['value']) && isset($p['value'])) $field .= ' value="'.$p['value'].'" ';

			$field .= ' '.$p['additionnal_attributes']. ' />';

			$this->tokenizer->field($p['name']);

			return $field;
		}

		/* Fonction de création d'un input type file */
		public function file_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'file_field';
			$default_params['id'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<input type="file" name="'.$p['name'].'" id="'.$p['id'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if($p['mandatory']) $field .= ' rel="requis" ';
			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';

			$field .= ' '.$p['additionnal_attributes']. ' />';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		/* Fonction de création d'une liste déroulante' */
		/*
		* options :	- array(
		*				value=>label,
		*				value2=>label2,
		*				etc
		*			)
		*			- array(
		*				groupe=>array(
		*					value=>label,
		*					value2=>label2,
		*					etc
		*				),
		*				groupe2=>array(
		*					value3=>label3,
		*					value4=>
		*					label4,
		*					etc
		*				),
		*				etc
		*			)
		*/
		public function select_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'select_field';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['options'] = null;
			$default_params['value'] = null;
			$default_params['empty_message'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field = '<select name="'.$p['name'].'" id="'.$p['id'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if($p['mandatory']) $field .= ' rel="requis" ';
			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';
			$field .= ' '.$p['additionnal_attributes']. '>';

			if(!is_null($p['empty_message'])){ //le message peut être vide si c'est la volonté du développeur, on ne teste donc pas le empty
				$field .= '<option value="dims_nan">'.$p['empty_message'].'</option>';
			}

			if( ! is_null($p['options']) && !empty($p['options'])){

				$db_field_exploitable = ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) && isset($this->object->fields[$p['db_field']]);

				$need_value = true;
				if($db_field_exploitable && isset($p['options'][$this->object->fields[$p['db_field']]])){ //si la valeur de l'objet pour cette colonne fait partie des possibles options
					$need_value = false;
				}

				if(is_array(current($p['options']))){ // gestion des groupes
					foreach($p['options'] as $val => $label){
						$field .= '<optgroup label="'.$val.'">';
						foreach($label as $v => $l){
							$selected = '';
							if( $db_field_exploitable && $this->object->fields[$p['db_field']] == $v){
								$selected = ' selected="selected" ';
							}
							else if($need_value && !is_null($p['value']) && ! is_array($p['value']) && $p['value'] == $v) $selected = ' selected="selected" ';
							else if($need_value && !is_null($p['value']) && is_array($p['value']) && isset($p['value'][$v]) ) $selected = ' selected="selected" ';
							$field .= '<option value="'.$v.'" '.$selected.'>'.$l.'</option>';
						}
						$field .= '</optgroup>';
						$selected = '';
					}
				}else{
					foreach($p['options'] as $val => $label){

						$selected = '';
						if( $db_field_exploitable && $this->object->fields[$p['db_field']] == $val){
							$selected = ' selected="selected" ';
						}
						else if($need_value && !is_null($p['value']) && ! is_array($p['value']) && $p['value'] == $val) $selected = ' selected="selected" ';
						else if($need_value && !is_null($p['value']) && is_array($p['value']) && isset($p['value'][$val]) ) $selected = ' selected="selected" ';
						$field .= '<option value="'.$val.'" '.$selected.'>'.$label.'</option>';
					}
				}
			}
			$field .= '</select>';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		/* Fonction de création d'un textarea */
		public function textarea_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'textarea_field';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<textarea type="text" name="'.$p['name'].'" id="'.$p['id'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if($p['mandatory']) $field .= ' rel="requis" ';
			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';
			$field .= ' '.$p['additionnal_attributes']. '>';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) && isset($this->object->fields[$p['db_field']]) ){
				$field .= $this->object->fields[$p['db_field']];
			}
			else if( ! is_null($p['value']) && isset($p['value'])) $field .= $p['value'];
			$field .= '</textarea>';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		//une checkbox ne peut pas être mandatory mais il existe un système de contrôle dans dims_validForm qui checke si dans un groupe de checkbox au moins une est checkée donc le contrôle est porté par la révision.
		public function checkbox_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'checkboxes[]';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['checked'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<input type="checkbox" name="'.$p['name'].'" id="'.$p['id'].'" value="'.$p['value'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) && isset($this->object->fields[$p['db_field']])
				&& $this->object->fields[$p['db_field']] == $p['value']){ //on teste la triple égalité pour s'assurer que la valeur est bien identique en tous points (type inclus)
				$field .= ' checked="checked" ';
			}
			else if( $p['checked'] ) $field .= ' checked="checked" ';

			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';

			$field .= ' '.$p['additionnal_attributes']. ' />';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		//une radiobutton ne peut pas être mandatory mais il existe un système de contrôle dans dims_validForm qui checke si dans un groupe de checkbox au moins une est checkée donc le contrôle est porté par la révision.
		public function radio_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'radios[]';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['checked'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<input type="radio" name="'.$p['name'].'" id="'.$p['id'].'" value="'.$p['value'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) && isset($this->object->fields[$p['db_field']])
				&& $this->object->fields[$p['db_field']] == $p['value']){ //on teste la triple égalité pour s'assurer que la valeur est bien identique en tous points (type inclus)
				$field .= ' checked="checked" ';
			}
			else if( $p['checked'] ) $field .= ' checked="checked" ';

			if( ! is_null($p['revision']) && ! empty($p['revision']) ) $field .= ' rev="'.$p['revision'].'" ';

			$field .= ' '.$p['additionnal_attributes']. ' />';
			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			$this->tokenizer->field($p['name']);

			return $field;
		}

		public function submit_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'submit';
			$default_params['value'] = 'Valider';
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';

			$p = array_merge($default_params, $params);//Merge des paramètres

			$field =  '<input type="submit" name="'.$p['name'].'" value="'.$p['value'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			$field .= ' '.$p['additionnal_attributes']. ' />';

			$this->tokenizer->field($p['name']);

			return $field;
		}

		public function button_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'button';
			$default_params['value'] = 'Click me';
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';

			$p = array_merge($default_params, $params);//Merge des paramètres

			$field =  '<input type="button" name="'.$p['name'].'" value="'.$p['value'].'" ';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			$field .= ' '.$p['additionnal_attributes']. ' />';

			$this->tokenizer->field($p['name']);

			return $field;
		}

		public function simple_text($params = array()){
			$default_params = array();
			$default_params['name'] = 'simple_field';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(is_null($p['id']) || empty($p['id'])) $p['id'] = $p['name'];
			$field =  '<span" name="'.$p['name'].'" id="'.$p['id'].'" '.$p['additionnal_attributes']. '>';
			if( ! is_null($p['classes']) && ! empty($p['classes']) ) $field .= ' class="'.$p['classes'].'" ';
			if( ! is_null($p['db_field']) && ! empty($p['db_field']) && ! is_null( $this->object) && isset($this->object->fields) && array_key_exists($p['db_field'], $this->object->fields) &&  isset($this->object->fields[$p['db_field']]) ){
				$field .= $this->object->fields[$p['db_field']];
			}
			else if( ! is_null($p['value']) && isset($p['value'])) $field .= $p['value'];
			$field .= '</span>';

			if( ! is_null($p['dom_extension']) ) $field .= $p['dom_extension'];

			return $field;
		}

		public function get_header(){
			return '<form name="'.$this->getName() .'" id="'. $this->getId() .'" action="'. $this->getAction() .'" method="' . $this->getMethod() .'" '. (( $this->isEnctype() ) ? 'enctype="multipart/form-data"' : ''). ' '.$this->getAdditionalAttributes().' >';
		}

		public function close_form(){
			$defaultError = addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']);
			$formatMail = addslashes($_SESSION['cste']['WRONG_EMAIL_FORMAT']);
			$globalMessage = $this->getGlobalMessageError();
			$login = addslashes($_SESSION['cste']['LOGIN_ALREADY_USED']);
			$js = '<script type="text/javascript">$(document).ready(function(){';

			$ext_ctrls = '';
			if( ! is_null($this->extended_controls) ){
				$ext_ctrls = ', '.$this->extended_controls;
			}
			if($this->isValidationEnabled()){
				$js .= '$("#'.$this->getId().'").dims_validForm({messages: {	defaultError:	\''.$defaultError.'\',
																				formatMail:	\''.$formatMail.'\',
																				globalMessage:	\''.$globalMessage.'\',
																				login:			\''.$login.'\',
															},
															displayMessages: true,
															refId: \'def\',
															refAttr: \''.$this->getRefAttribute().'\',
															globalId: \'global_message\',
															ajax_submit: '.(($this->getAjaxSubmit())?"true":"false").',
															submit_replace: $("#'.$this->getId().'").parents("div.container_admin.global_content_record:first")
															'.$ext_ctrls.'});';
			}else{
				if($this->getAjaxSubmit()){
					$js .= '$("#'.$this->getId().'").submit(function (event) {
								event.preventDefault();
								$.ajax({
									type: $(this).attr("method"),
									url: $(this).attr("action"),
									data: $(this).serialize(),
									dataType: "html",
									success: function(data){
										if(data != ""){
											$("#'.$this->getId().'").parents("div.container_admin.global_content_record:first").html(data);
										}
									},
								});
							});';
				}
			}
			if($this->getAjaxUndo()){
				$js .= '$("#'.$this->getId().' a.undo").click(function(event){
							event.preventDefault();
							$.ajax({
								type: "POST",
								url: $(this).attr("href"),
								data: {
									"ajax": 1,
								},
								dataType: "html",
								success: function(data){
									if(data != ""){
										$("#'.$this->getId().'").parents("div.container_admin.global_content_record:first").html(data);
									}
								},
							});
						});';
			}
			// retour à la ligne dans le cas ou la dernière ligne du js est un commentaire
			$js .= $this->getAdditionalJS().'
	});</script>';

			return $this->tokenizer->generate().'</form>'.$js;
		}


		/*-------------------------------------------------------------- FONCTIONS D'AJOUT A UN BLOC DE CHAMP ----------------------*/

		public function add_text_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'text_field';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['value'] = null;
			$default_params['id'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete
			$default_params['row'] = null;
			$default_params['col'] = 1;

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['mandatory']				= $p['mandatory'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['dom_extension']			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];

				$this->blocks[$p['block']]->addField('text', $p['name'], $p['id'], $this->text_field($html_params), $p['label'], $p['mandatory'], $p['revision'], $html_params['row'], $html_params['col']);

				$this->tokenizer->field($p['name']);
			}
		}

		public function add_password_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'password_field';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['value'] = null;
			$default_params['id'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['mandatory']				= $p['mandatory'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('password', $p['name'], $p['id'], $this->password_field($html_params), $p['label'], $p['mandatory'], $p['revision'], $html_params['row'], $html_params['col']);

				$this->tokenizer->field($p['name']);
			}
		}

		public function add_hidden_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'hidden_field';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['value'] = null;
			$default_params['id'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];

				$this->blocks[$p['block']]->addField('hidden', $p['name'], $p['id'], $this->hidden_field($html_params), $p['label'], $html_params['row'], $html_params['col']);

				$this->tokenizer->field($p['name']);
			}
		}

		//on oblige à saisir l'id pour que le label pointe sur la bonne checkbox
		public function add_checkbox_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'checkboxes[]';
			$default_params['id'] = 'checkboxes[]';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['value'] = null;
			$default_params['checked'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['checked']					= $p['checked'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('checkbox', $p['name'], $p['id'], $this->checkbox_field($html_params), $p['label'], false, $p['revision'], $html_params['row'], $html_params['col']);//une checkbox ne peut pas être mandatory à elle toute seule

				$this->tokenizer->field($p['name']);
			}
		}

		//on oblige à saisir l'id pour que le label pointe sur le bon radio
		public function add_radio_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'radios[]';
			$default_params['id'] = 'radios[]';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['value'] = null;
			$default_params['checked'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['checked']					= $p['checked'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('radio', $p['name'], $p['id'], $this->radio_field($html_params), $p['label'], false, $p['revision'], $html_params['row'], $html_params['col']);//une checkbox ne peut pas être mandatory à elle toute seule

				$this->tokenizer->field($p['name']);
			}
		}

		public function add_file_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'file_field';
			$default_params['block'] = 'default';
			$default_params['label'] = '';
			$default_params['id'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$this->setEnctype(true);

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['mandatory']				= $p['mandatory'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('file', $p['name'], $p['id'], $this->file_field($html_params), $p['label'], $p['mandatory'], $p['revision'], $html_params['row'], $html_params['col']);

				$this->tokenizer->field($p['name']);
			}
		}

		public function add_select_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'select_field';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['id'] = null;
			$default_params['options'] = null;
			$default_params['value'] = null;
			$default_params['empty_message'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['options']					= $p['options'];
				$html_params['value']					= $p['value'];
				$html_params['empty_message']			= $p['empty_message'];
				$html_params['mandatory']				= $p['mandatory'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('select', $p['name'], $p['id'], $this->select_field($html_params), $p['label'], $p['mandatory'], $p['revision'], $html_params['row'], $html_params['col']);

				$this->tokenizer->field($p['name']);
			}
		}

		public function add_textarea_field($params = array()){
			$default_params = array();
			$default_params['name'] = 'textarea_field';
			$default_params['block'] = 'default';
			$default_params['db_field'] = null;
			$default_params['label'] = '';
			$default_params['value'] = null;
			$default_params['id'] = null;
			$default_params['mandatory'] = false;
			$default_params['revision'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['mandatory']				= $p['mandatory'];
				$html_params['revision']				= $p['revision'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('textarea', $p['name'], $p['id'], $this->textarea_field($html_params), $p['label'], $p['mandatory'], $p['revision'], $html_params['row'], $html_params['col']);

				$this->tokenizer->field($p['name']);
			}
		}

		public function add_simple_text($params = array()){
			$default_params = array();
			$default_params['name'] = 'simple_field';
			$default_params['block'] = 'default';
			$default_params['id'] = null;
			$default_params['db_field'] = null;
			$default_params['value'] = null;
			$default_params['classes'] = '';
			$default_params['additionnal_attributes'] = '';
			$default_params['row'] = null;
			$default_params['col'] = 1;
			$default_params['dom_extension'] = null;#DOM HTML pour accueil les résultats d'un autocomplete

			$p = array_merge($default_params, $params);//Merge des paramètres

			if(isset($this->blocks[$p['block']])){
				$html_params = array();
				$html_params['name']					= $p['name'];
				$html_params['id']						= $p['id'];
				$html_params['db_field']				= $p['db_field'];
				$html_params['value']					= $p['value'];
				$html_params['classes']					= $p['classes'];
				$html_params['additionnal_attributes']	= $p['additionnal_attributes'];
				$html_params['row']						= $p['row'];
				$html_params['col']						= $p['col'];
				$html_params['dom_extension'] 			= $p['dom_extension'];#DOM HTML pour accueil les résultats d'un autocomplete

				$this->blocks[$p['block']]->addField('simpletext', $p['name'], $p['id'], $this->simple_text($html_params), $p['label'], false, null, $html_params['row'], $html_params['col']);
			}
		}

		public function build(){
			include $this->getTpl();
		}
	}
}
