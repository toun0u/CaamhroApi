<?php

/**
 * Description of import_linked
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class import_linked extends dims_data_object{
    const TABLE_NAME = "dims_mod_assurance_import_linked";

    const TYPE_ASSUR_OBJET_CONTRAT_COLLECTIF = 1 ;
    const TYPE_ASSUR_OBJET_ASSURE = 2 ;
    const TYPE_ASSUR_OBJET_POLICE = 3 ;
    const TYPE_ASSUR_OBJET_CLIENT = 4 ;
    const TYPE_ASSUR_OBJET_COUVERTURE_POLICE = 5 ;
    const TYPE_ASSUR_OBJET_FAMILLE = 6 ;
    const TYPE_ASSUR_OBJET_ACCOUNT = 7 ;

    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getIdImport() {
        return $this->getAttribut("id_import", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdTupleTemp() {
        return $this->getAttribut("id_tuple_temp", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdObject() {
        return $this->getAttribut("id_object", self::TYPE_ATTRIBUT_KEY);
    }

    public function getIdTypeObject() {
        return $this->getAttribut("type_object", self::TYPE_ATTRIBUT_KEY);
    }

    /**
     *
     * @param array $list_id_tuple : Liste des tuples de la table temp à ajouter
     * @param type $id_import : Reference de l'a table'import dont les tuples sont tirés
     * @param int $type_object : Entier qui représente l'objet (constant)
     * @param array $list_id_objets : Tableau des id des objets à lier indexé par
     * l'id du tuple
     * Exemple : $list_id_objets[1] = 2
     */
    public static function insertListLinked(array $list_id_tuple, $id_import, $type_object, $list_id_objets){
        if(!empty($list_id_tuple)){
            $db = dims::getInstance()->getDb();

            $sql = "INSERT INTO ".self::TABLE_NAME.
                " (id,id_import,id_tuple_temp,id_object,type_object) VALUES ";
            foreach ($list_id_tuple as $id_tuple) {
                if(isset($list_id_objets[$id_tuple])){
                    if(is_array($list_id_objets[$id_tuple])){
                        foreach ($list_id_objets[$id_tuple] as $id_linked) {
                            $sql .= "(NULL,'".$id_import."',".$id_tuple.",".$id_linked.",".$type_object.")";
                        }
                    }else{
                        $sql .= "(NULL, '".$id_import."', ".$id_tuple.", ".$list_id_objets[$id_tuple].",".$type_object."),";
                    }
                }
            }
            $sql = substr($sql, 0, strlen($sql)-1);
            $db->query($sql);
        }
    }

    /**
     *
     * @param type $list_id_tuple : Liste des tuples de la table temp à ajouter
     * @param type $id_import : Reference de l'import dont les tuples sont tirés
     * @paramn int $type_column : Type de la colonne pour laquelle on souhaite obtenir les valeurs
     * Si ce param est laissé vide alors on remonte tous les types.
     * @return array d'id objet indexé par type_column si
     * le param associé est défini a 0 ou laissé vide et/ou par id_tuple
     * Exemple : $list_linked[1] = 2 OU $list_linked[1][3] = 2
     */
    public static function getListLinked(array $list_id_tuple, $id_import, $type_column = 0){
        $list_res = array();
        if(empty($list_id_tuple)){
            return array();
        }else{
           if($type_column == 0){
               $list_res = self::getListLinkedForAllColumn($list_id_tuple, $id_import) ;
           }else{
               $list_res = self::getListLinkedForColumn($list_id_tuple, $id_import, $type_column);
           }
           return $list_res ;
        }
    }

    private static function getListLinkedForColumn(array $list_id_tuple, $id_import, $type_column){
       $list_linked = array();

       $db = dims::getInstance()->getDb();

       $sql = "SELECT * FROM ".self::TABLE_NAME."
           WHERE id_tuple_temp IN (".  implode(',', $list_id_tuple).")
           AND id_import = '".$id_import."'
           AND type_object = ".$type_column."
           ";

       $res = $db->query($sql);
       while($row = $db->fetchrow($res)){
           $list_linked[$row['id_tuple_temp']] = $row['id_object'];
       }

       return $list_linked;
    }

    private static function getListLinkedForAllColumn(array $list_id_tuple, $id_import){
       $list_linked = array();

       if(!empty($list_id_tuple)){
           $db = dims::getInstance()->getDb();

           $sql = "SELECT * FROM ".self::TABLE_NAME."
               WHERE id_tuple_temp IN (".implode(',',$list_id_tuple).")
               AND id_import = '".$id_import."'
               ";

           $res = $db->query($sql);
           while($row = $db->fetchrow($res)){
               $list_linked[$row['type_object']][$row['id_tuple_temp']] = $row['id_object'];
           }
       }

       return $list_linked;
    }

    public static function deleteRowForListIdTemp($id_import, $list_id_to_delete){
        if(!empty($list_id_to_delete)){
          $db = dims::getInstance()->getDb();

          $sql = "DELETE FROM ".self::TABLE_NAME."
              WHERE id_import = ".$id_import."
              AND id_tuple_temp IN (".implode(',', $list_id_to_delete).")
              ";
          $db->query($sql);
      }
    }
}

?>
