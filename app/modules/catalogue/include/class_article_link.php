<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_link_type.php";

class article_link extends dims_data_object {
	const TABLE_NAME 			= 'dims_mod_cata_article_link';

	const SYM_LINK				= 1;
	const ASYM_LINK				= 0;


	public function article_link() {
		parent::dims_data_object(self::TABLE_NAME);
	}

	public function save($delete_symmetry = true){
		$id_link = parent::save();
		$other = article_link::findByCouple($this->getArticleTo(), $this->getArticleFrom());
		if( $this->isSymetric() && is_null($other) ){
			#Dans ce cas il faut le créer
			$other = new article_link();
			$other->create($this->getArticleTo(), $this->getArticleFrom(), $this->getType(), true);
		}
		else if( $this->isSymetric() && ! is_null($other) ){
			//on vérifie juste que l'autre a bien la symétrie, sinon on lui rajoute
			if( ! $other->isSymetric() ){
				$other->setSymetric(self::SYM_LINK);
				$other->save();
			}
		}

		if( ! $this->isSymetric() && ! is_null($other) && $delete_symmetry ){
			#Dans ce cas il faut supprimer la symétrie
			$other->delete();
		}
		return $id_link;
	}

	public function create($id_from, $id_to, $type, $symetric = true){
		$this->init_description(true);
		$this->setugm();
		$this->setArticleFrom($id_from);
		$this->setArticleTo($id_to);
		$this->setType($type);
		$this->setSymetric( ($symetric) ? self::SYM_LINK : self::ASYM_LINK );
		return $this->save();
	}

	#Cyril - Important, la colonne symetric indique que les deux lignes existent, c'est tout, il faut quand même créer la seconde ligne SQL le cas échéant, dans un souci de performances
	public function isSymetric(){
		return $this->fields['symetric'];
	}

	#Méthode qui retourne le lien existant sur un couple donné, s'il existe ...
	public static function findByCouple($id_from, $id_to){
		$db = dims::getInstance()->getDb();
		$res = $db->query("SELECT *
						   FROM ".self::TABLE_NAME."
						   WHERE id_article_from = ".$id_from."
						   AND id_article_to = ".$id_to." LIMIT 1");
		$link = null;
		if($db->numrows($res)){
			$fields = $db->fetchrow($res);
			$link = new article_link();
			$link->openFromResultSet($fields);
		}

		return $link;
	}

	public function setArticleFrom($val){
		$this->fields['id_article_from'] = $val;
	}
	public function setArticleTo($val){
		$this->fields['id_article_to'] = $val;
	}
	public function setType($val){
		$this->fields['type'] = $val;
	}
	public function setSymetric($val){
		$this->fields['symetric'] = $val;
	}

	public function getArticleFrom(){
		return $this->fields['id_article_from'];
	}
	public function getArticleTo(){
		return $this->fields['id_article_to'];
	}
	public function getType(){
		return $this->fields['type'];
	}
	public function getSymetric(){
		return $this->fields['symetric'];
	}
}
