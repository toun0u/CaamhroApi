<?php

/**
 * Description of contact_language
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class contact_language extends dims_data_object{
	const TABLE_NAME = "dims_contact_languages";

	private $language = null ;

	public function __construct() {
	parent::dims_data_object(self::TABLE_NAME, 'id');
	}

	public function getSkill(){
	return $this->getAttribut('skill', parent::TYPE_ATTRIBUT_STRING);
	}

	public function setSkill($skill, $save=false){
	$this->setAttribut('skill', parent::TYPE_ATTRIBUT_STRING, $skill, $save);
	}
	public function getIdLanguage(){
	return $this->getAttribut('id_language', parent::TYPE_ATTRIBUT_KEY);
	}

	public function setIdLanguage($id_language, $save=false){
	$this->setAttribut('id_language', parent::TYPE_ATTRIBUT_KEY, $id_language, $save);
	}
	public function getIdContact(){
	return $this->getAttribut('id_contact', parent::TYPE_ATTRIBUT_KEY);
	}

	public function setIdContact($id_contact, $save=false){
	$this->setAttribut('id_contact', parent::TYPE_ATTRIBUT_KEY, $id_contact, $save);
	}

	public function setLanguage($language){
	$this->language = $language;
	}

	public function getLanguage(){
	if(is_null($this->language)){
		$this->language = new language();
		$this->language->open($this->getIdLanguage());
	}
	return $this->language ;
	}

	/**
	 * Retourne la liste des langages parlé pour un contact (les objets retournés
	 * sont de type contact_language. Le language lié est accessible avec l'accesseur
	 * getLanguage
	 *
	 * @complexity (requete=1, longueur=n*m)
	 *
	 * @param type $id_contact
	 * @param type $sorted - Spécifie si le tableau renvoyé doit être trié par indice
	 * correspondant à l'id de lien entre language et contact
	 * @return contact_language
	 */
	public static function getSpokenLanguagesForContact($id_contact, $sorted = true){
		$list_spoken_languages = array();

		$db = dims::getInstance()->getDb();

		$sql = "SELECT * FROM ".self::TABLE_NAME."
			INNER JOIN ".  language::TABLE_NAME."
			ON ".self::TABLE_NAME.".id_language = ".language::TABLE_NAME.".id
			WHERE ".self::TABLE_NAME.".id_contact = :idcontact ";

		$res = $db->query($sql, array(
			':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
		));

		$separation = $db->split_resultset($res);
		foreach ($separation as $row) {
			$spoken_language = new contact_language() ;
			$spoken_language->openWithFields($row[contact_language::TABLE_NAME]);

			$language = new language();
			$language->openWithFields($row[language::TABLE_NAME]);

			$spoken_language->setLanguage($language);

			if(sorted){
			$list_spoken_languages[$spoken_language->getId()] = $spoken_language ;
			}else{
			$list_spoken_languages[] = $spoken_language ;
			}
		}
		return $list_spoken_languages ;
	}
}
