<?php

/**
 * Description of import_type_champs_fichier_modele
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class import_type_champs_fichier_modele extends dims_data_object{
    const TABLE_NAME = "dims_import_type_champs_fichier_modele";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getLibelle(){
        return $this->getAttribut('libelle', parent::TYPE_ATTRIBUT_STRING);
    }

    public function getPhpValue(){
        return $this->getAttribut('php_value', parent::TYPE_ATTRIBUT_STRING);
    }

    public function setLibelle($libelle, $save = false){
        $this->setAttribut('libelle', parent::TYPE_ATTRIBUT_STRING, $libelle, $save);
    }

    public function setPhpValue($php_value, $save = false){
        $this->setAttribut('php_value', parent::TYPE_ATTRIBUT_STRING, $php_value, $save);
    }

    /**
     * Retourne la liste de tous les types de champs
     * @complexity (requete=1, longueur=n)
     * @return array indexé import_type_champs_fichier_modele
     * Exemple de résultat : $res[id_type_champs]= $type_champs
     */
    public static function getListeTypeChamps(){
        $liste_type_champs = array ();
        $db = dims::getInstance()->getDB();

        $sql = "SELECT * FROM ".self::TABLE_NAME."
            ";

        $res = $db->query($sql);
        while($row = $db->fetchrow($res)){
            $type_champs = new import_type_champs_fichier_modele();
            $type_champs->openWithFields($row);
            $liste_type_champs[$type_champs->getId()] = $type_champs ;
        }

        return $liste_type_champs ;
    }
}

?>
