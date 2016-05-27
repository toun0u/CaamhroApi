<?php

/**
 * Description of historisable
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 *
 */
require_once 'dims_controller_historique.php';
require_once 'dims_globalizable.php';
interface dims_historisable extends dims_globalizable{

    /**
     * La meilleure façon d'implémenter getHistory est la suivante :
     * $tab_action = array() ;

        if(isset($this->fields['id_globalobject'])){
            if($this->fields['id_globalobject'] != 0){
                $tab_action = dims_controller_historique::build_historique($this->fields['id_globalobject'], $this->getDB());
            }else{
                //TODO ERROR ID_globalobject = 0
            }
        }else{
            //TODO ERROR ID_globalobject is unset
        }
        return $tab_action ;
     */
    public function getHistory() ;


    /**
     * @param type $actiontype : Type de l'action à enregister : le type de l'action
     * doit faire référence à un des actiontype disponible en statique
     * @param type $tab_globalid : liste des objets modifiés par l'action
     * @param type $commentaire : commentaire saisi par l'utilisateur par rapport
     * à l'action
     * @param type $id_user : id de l'utilisateur qui a effectué l'action
     * @param type $id_module : id du module dans lequelle l'action a été réalisée
     * @param type $id_workspace : id du workspace dans lequelle l'action a été
     * réalisé
     */
    public function addHistory($actiontype, $tab_globalid, $commentaire,
            $id_user, $id_module, $id_workspace, $comment_label = "");


    public function getDB();
}

?>
