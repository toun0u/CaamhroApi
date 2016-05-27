<?php

/**
 * Description of import_champs_fichier_modele
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class import_champs_fichier_modele extends dims_data_object{
    const TABLE_NAME = "dims_import_champs_fichier_modele";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getLibelle(){
        return $this->getAttribut('libelle', parent::TYPE_ATTRIBUT_STRING);
    }

    public function isObligatoire(){
        return $this->getAttribut('obligatoire', parent::TYPE_ATTRIBUT_BOOLEAN_TINYINT);
    }

    public function getHelpConstant(){
        return $this->getAttribut('help_constant', parent::TYPE_ATTRIBUT_STRING);
    }

    public function getIdTypeChamps(){
        return $this->getAttribut('id_type_champs', parent::TYPE_ATTRIBUT_KEY);
    }

    public function setLibelle($libelle,$save){
        $this->setAttribut('libelle', parent::TYPE_ATTRIBUT_STRING, $libelle, $save);
    }

    public function setObligatoire($obligatoire, $save = false){
        $this->setAttribut('obligatoire', parent::TYPE_ATTRIBUT_BOOLEAN_TINYINT, $obligatoire, $save);
    }

    public function setHelpConstant($help_constant, $save = false){
        $this->setAttribut('help_constant', parent::TYPE_ATTRIBUT_STRING, $help_constant, $save);
    }

    public function setIdTypeChamps($id_type_champs, $save = false){
        $this->setAttribut('id_type_champs', parent::TYPE_ATTRIBUT_KEY, $id_type_champs, $save);
    }

    /**
     * Retourne la liste de tous les champs de tous les fichiers modele
     * @complexity (requete=1, longueur=n)
     * @return array à deux entrées indexées : import_champs_fichier_modele
     * Exemple de résultat : $res[id_type_champs][id_champs] = $champs
     */
    public static function getListChamps(){
        $liste_champs = array ();
        $db = dims::getInstance()->getDB();

        $sql = "SELECT * FROM ".self::TABLE_NAME."
            ";

        $res = $db->query($sql);
        while($row = $db->fetchrow($res)){
            $champs = new import_champs_fichier_modele();
            $champs->openWithFields($row);
            $liste_champs[$champs->getIdTypeChamps()][$champs->getId()] = $champs ;
        }

        return $liste_champs ;
    }
}

?>
