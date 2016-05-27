<?php
class dims_form {
	 private static  $globals	= array("heure"		=> array("for_min" => 0, "for_max" => 23),
										"minute"	=> array("for_min" => 0, "for_max" => 59),
										"jour"		=> array("for_min" => 1, "for_max" => 31),
										"mois"		=> array("for_min" => 1, "for_max" => 12));

	private static $class		= false;
	private static $style		= false;
	private static $javascript	= false;

	//Créer une valeur pour la variable class
	public static function setJavaScript($value){
		self::$javascript	= ' '.$value;
	}

	//Retourne une valeur pour la variable class
	public static function getJavaScript(){
		return self::$javascript;
	}

	//Supprime une valeur pour la variable class
	public static function delJavaScript(){
		self::$javascript	= false;
	}

	//Créer une valeur pour la variable class
	public static function setClass($value){
		self::$class	= ' class="'.$value.'"';
	}

	//Retourne une valeur pour la variable class
	public static function getClass(){
		return self::$class;
	}

	//Supprime une valeur pour la variable class
	public static function delClass(){
		self::$style	= false;
	}

	//Créer une valeur pour la variable style
	public static function setStyle($value){
		self::$style	= ' style="'.$value.'"';
	}

	//Retourne une valeur pour la variable style
	public static function getStyle(){
		return self::$style;
	}

	//Supprime une valeur pour la variable style
	public static function delStyle(){
		self::$style	= false;
	}

	//Ajout une valeur pour la variable global
	public static function setGlobals($key, $value){
		//Vérifie que l'element existe bien
		if (!array_key_exists($key, self::$globals)) {
			//Ajout l'element
			self::$globals[$key]	= $value;
		}
	}

	//Supprime une/des valeurs pour la variable global
	public static function delGlobals(){
		if (func_num_args() > 0) {
			//Liste les arguments de la fonction
			$arg_list = func_get_args();

			foreach ($arg_list as $value){
				//Vérifie que l'element existe bien
				if (array_key_exists($value, $this->globals)) {
					//Supprime l'element
					unset(self::$globals[$value]);
				}
			}
		}
	}

	//Permet de créer une liste option dans un select pour le temps
	public function getTimeOption($type, $name, $selected=false, $debut=false, $fin=false, $pas=1, $multiple=false){
		//Ajout l'annne
		if ($type == "annee") {
			self::setGlobals("annee", array("for_min" => date("Y"), "for_max" => date("Y")));
		}

		if (!array_key_exists($type, self::$globals)) {
			throw new Error_class(array("message" => "var 'type' not initialised for var 'globals'"));
		}

		if ($debut) {
			self::$globals[$type]["for_min"]	= $debut;
		}

		if ($fin) {
			self::$globals[$type]["for_max"]	= $fin;
		}

		if (!isset(self::$globals[$type]["for_min"]) or !isset(self::$globals[$type]["for_max"])) {
			throw new Error_class(array("message" => "var 'global' element 'for_min' or 'for_max' is null"));
		}

		$form	= '<select name="'.$name.'" id="'.$name.'"'.($multiple?' multiple="multiple"':"").self::getStyle().self::getClass().self::getJavaScript().'>';

		$i	= self::$globals[$type]["for_min"];
		if ($i > self::$globals[$type]["for_max"]) {
			while ($i >= self::$globals[$type]["for_max"]){
				switch($type) {
					case "mois":
						$value	= ajout_zero($i);
						$text	= lettre_mois($i);
					break;

					default:
						$value	= ajout_zero($i);
						$text	= ajout_zero($i);
					 break;
				}

				$form	.= '<option value="'.$value.'"'.($selected == $value? ' selected="selected"' : '').'>'.$text.'</option>';

				$i	-= $pas;
			}
		}else{
			while ($i <= self::$globals[$type]["for_max"]){
				switch($type) {
					case "mois":
						$value	= ajout_zero($i);
						$text	= lettre_mois($i);
					break;

					default:
						$value	= ajout_zero($i);
						$text	= ajout_zero($i);
					 break;
				}

				$form	.= '<option value="'.$value.'"'.($selected == $value? ' selected="selected"' : '').'>'.$text.'</option>';

				$i	+= $pas;
			}
		}

		return $form."</select>";
	}

	public function getOption($name, $liste, $selected=false, $multiple=false){
		$form	= '<select name="'.$name.'" id="'.$name.'"'.($multiple?' multiple="multiple"':"").self::getStyle().self::getClass().self::getJavaScript().'>';

		foreach ($liste as $key => $value){
			$form	.= '<option value="'.$value.'"'.($selected == $value ? ' selected="selected"' : '').'> '.$key.' </option>';
		}

		return $form."</select>";
	}

	public function getRadio($name, $liste, $checked=''){
		if (!is_array($liste)) {
			throw new Error_class(array("message" => "var 'liste' is not array"));
		}

		$form	= '';
		foreach ($liste as $key => $value){
			$form	.= '<label><input type="radio" name="'.$name.'" id="'.$name.'" value="'.$value.'"'.($checked == $value? ' checked="checked"' : '').self::getStyle().self::getClass().self::getJavaScript().' /> '.$key.'</label>';
		}

		return $form;
	}

	public function getInputText($name, $value){
			return '<input type="text" name="'.$name.'" id="'.$name.'" value="'.$value.'"'.self::getStyle().self::getClass().self::getJavaScript().' />';
	}

	public static function getInputSubmit($value){
			// Add token before submit button
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("rfid-auth");
			$token->field("dims_login");
			$token->field("dims_password");
			$token->field("dims_email");
			$tokenHTML = $token->generate();
			$smarty->assign('dims_form_token', $tokenHTML);
			return '<input type="submit" value="'.$value.'"'.self::getStyle().self::getClass().self::getJavaScript().' />';
	}

	public function getInputPassword($name, $value){
			return '<input type="password" name="'.$name.'" id="'.$name.'" value="'.$value.'"'.self::getStyle().self::getClass().self::getJavaScript().' />';
	}

	public function getCheckbox($name, $liste, $checked=''){
		if (!is_array($liste)) {
			throw new Error_class(array("message" => "var 'liste' is not array"));
		}
		$form	= '';
		foreach ($liste as $key => $value){
			$form	.= '<label><input type="checkbox" name="'.$name.'" id="'.$name.'" value="'.$value.'"'.self::getStyle().self::getClass().self::getJavaScript().($checked == $value? ' checked="checked"' : '').' /> '.$key.'</label>';
		}

		return $form;
	}
}
?>
