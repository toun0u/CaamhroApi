<?php

/**
 * Description of import_correspondance_colonne_champs
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class import_correspondance_colonne_champs  extends dims_data_object{
    const TABLE_NAME = "dims_import_correspondance_colonne_champs";

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdChamps(){
        return $this->getAttribut('id_champs', parent::TYPE_ATTRIBUT_KEY);
    }

    public function getIdFichierModele(){
        return $this->getAttribut('id_fichier_modele', parent::TYPE_ATTRIBUT_KEY);
    }

    public function getLibelleColonne(){
        return $this->getAttribut('libelle_colonne', parent::TYPE_ATTRIBUT_STRING);
    }

    public function setIdChamps($id_champs, $save = false){
        $this->setAttribut('id_champs', parent::TYPE_ATTRIBUT_KEY, $id_champs, $save);
    }

    public function setIdFichierModele($id_fichier_modele, $save = false){
        $this->setAttribut('id_fichier_modele', parent::TYPE_ATTRIBUT_KEY, $id_fichier_modele, $save);
    }

    public function setLibelleColonne($libelle_colonne, $save = false){
        $this->setAttribut('libelle_colonne', parent::TYPE_ATTRIBUT_STRING, $libelle_colonne, $save);
    }

    /**
     * @complexity (requete=1, longueur=n)
     * @param type $id_fichier_modele
     * @return array indexé par id de champs
     * Exemple de résultat : $res[id_champs] = $correspondance
     */
    public static function getListCorrespondanceByIdFichierLazy($id_fichier_modele){
        $liste_correspondance = array ();
        $db = dims::getInstance()->getDB();

        $sql = "SELECT * FROM ".self::TABLE_NAME."
            WHERE id_fichier_modele = :idfichiermodele
              ";

        $res = $db->query($sql, array(
            ':idfichiermodele' => $id_fichier_modele
        ));

        while($row = $db->fetchrow($res)){
            $correspondance = new import_correspondance_colonne_champs();
            $correspondance->openWithFields($row);
            $liste_correspondance[$correspondance->getIdChamps()] = $correspondance ;
        }
        return $liste_correspondance ;
    }

    /**
     * @complexity (requete=1, longueur=n*m)
     * @param type $id_fichier_modele
     * @return array indexé par id de correspondance
     * Exemple de résultat : $res[id_correspondance] = $correspondance
     * L'objet assurance_champs_fichier_assureur est joint à la correspondance
     */
    public static function getListCorrespondanceByIdFichier($id_fichier_modele){
        $liste_correspondance = array ();
        $db = dims::getInstance()->getDB();

        $sql = "SELECT * FROM ".self::TABLE_NAME."
            INNER JOIN ".import_champs_fichier_modele::TABLE_NAME."
            ON ".self::TABLE_NAME.".id_champs = ".import_champs_fichier_modele::TABLE_NAME.".id
            WHERE $id_fichier_modele = :idfichiermodele
              ";
        $res = $db->query($sql, array(
            ':idfichiermodele'  => $id_fichier_modele
        ));

        $separation = $db->split_resultset($res);
        foreach ($separation as $row) {
            $champs = new import_champs_fichier_modele();
            $champs->openWithFields($row[import_champs_fichier_modele::TABLE_NAME]);

            $correspondance = new import_correspondance_colonne_champs();
            $correspondance->openWithFields($row[import_correspondance_colonne_champs::TABLE_NAME]);

            $correspondance->setChamps($champs);

            $liste_correspondance[$correspondance->getId()] = $correspondance ;
        }

        return $liste_correspondance ;
    }

    private $champs = null ;

    public function getChamps(){
        if($this->champs != null){
            $this->champs = new import_champs_fichier_modele();
            $this->champs->open($this->getId());
        }
        return $this->champs;
    }

    public function setChamps(import_champs_fichier_modele $champs){
        $this->champs = $champs;
    }
}

?>
