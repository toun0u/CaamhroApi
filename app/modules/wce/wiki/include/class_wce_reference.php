<?php
class wce_reference extends pagination{
	const TABLE_NAME = "dims_mod_wce_reference";

	public function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	public static function getInstance(){
		return new wce_reference();
	}

		public function delete() {

			// on va decaler
			$res=$this->db->query(" UPDATE      ".self::TABLE_NAME."
									SET         position=position-1
									WHERE       position > :position
									AND         id_article = :id_article
									AND         id_module = :id_module
									AND         id_lang = :id_lang",
									array(':position'=>array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT),
											':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
											':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT)));
			parent::delete();
		}

		/*
		 * fonction mise à jour de position
		 */
		public function updatePosition($nom_champ='',$position) {
			if ($nom_champ!='')
				$newposition = dims_load_securvalue($nom_champ,dims_const::_DIMS_NUM_INPUT,true,true);
			else $newposition=$position;

			$db = dims::getInstance()->getDb();

			if ($newposition != $this->fields['position']) {
				if ($newposition<1) $newposition=1;
				else {
					$select = " SELECT      MAX(position) as maxpos
								FROM        ".self::TABLE_NAME."
								WHERE       id_article = :id_article
								AND         id_module = :id_module
								AND         id_lang = :id_lang";
					$res=$db->query($select,
									array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
											':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT)));
					$fields = $this->db->fetchrow($res);
					if ($newposition > $fields['maxpos']) $newposition = $fields['maxpos'];
				}

				$res=$db->query("UPDATE     ".self::TABLE_NAME."
								SET         position = 0
								WHERE       position = :position
								AND         id_article = :id_article
								AND         id_module = :id_module
								AND         id_lang = :id_lang",
								array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
										':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT),
										':position'=>array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT)));

				if ($newposition > $this->fields['position']) {
					//echo "update dims_mod_wce_reference set position=position-1 where position BETWEEN ".($this->fields['position'])." AND {$newposition} AND position>0 and id_article = {$this->fields['id_article']} AND id_module = {$this->fields['id_module']}";die();
					$res=$db->query("UPDATE     ".self::TABLE_NAME."
									SET         position = position-1
									WHERE       position BETWEEN :position AND :newpos
									AND         position > 0
									AND         id_article = :id_article
									AND         id_module = :id_module
									AND         id_lang = :id_lang",
									array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
											':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT),
											':position'=>array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT),
											':newpos'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
				}
				else
				{
					$res=$db->query("UPDATE 	".self::TABLE_NAME."
									SET 		position = position+1
									WHERE 		position BETWEEN :newpos AND :position
									AND 		id_article = :id_article
									AND 		id_module = :id_module
									AND 		id_lang = :id_lang",
									array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
											':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
											':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT),
											':position'=>array('value'=>($this->fields['position']-1),'type'=>PDO::PARAM_INT),
											':newpos'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
				}
				$res=$db->query("UPDATE 	".self::TABLE_NAME."
								SET 		position = :position
								WHERE 		position = 0
								AND 		id_article = :id_article
								AND 		id_module = :id_module
								AND 		id_lang = :id_lang",
								array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
										':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
										':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT),
										':position'=>array('value'=>$newposition,'type'=>PDO::PARAM_INT)));
				$this->fields['position'] = $newposition;
			}
		}

		/*
		 * Fonction permettant de fournir la valeur maximum de la position de référence liée à un article
		 */
		public function getMaxPosition() {
			$db = dims::getInstance()->getDb();
			$select = "	select 	MAX(position) as maxpos
						from 	".self::TABLE_NAME."
						where 	id_article = :id_article
						AND 	id_module = :id_module
						AND		id_lang = :id_lang";
			$res=$db->query($select,
							array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
									':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
									':id_lang'=>array('value'=>$this->fields['id_lang'],'type'=>PDO::PARAM_INT)));

			if ($this->db->numrows($res)>0) {
				$fields = $this->db->fetchrow($res);
				return($fields['maxpos']);
			}
			else {
				return (0);
			}
		}
}
?>