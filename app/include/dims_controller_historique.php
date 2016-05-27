<?php

/**
 * Description of dims_controller_historique
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 *
 */
require_once 'dims_historique.php';
class dims_controller_historique {

    public static function build_historique($id_go_origin, $db){
        $tab_historique = array();

        //On obtient la liste des action
        $tab_action = dims_action::getActionByIdOrigin($id_go_origin, $db);

        //On prépare les listes des action et des users pour le matching a action_matrix
        $tab_id_action = array();
        $tab_id_user = array();
        foreach ($tab_action as $action) {
            $tab_id_action[] = $action->getID();
            $tab_id_user[] = $action->getIdUser();
        }

        $tab_action_matrix = dims_action_matrix::getActionMatrixFromListIdAction($tab_id_action, $db);

        $tab_user = user::getUserFromListIdUser($tab_id_user, $db);

        /*On prépare la liste des id_globalobject des objets liés aux actions dans
         * dims_action_matrix
         */
        $tab_id_globalobject = array();
        foreach ($tab_action_matrix as $sous_tab_action_matrix){
            foreach ($sous_tab_action_matrix as $action_matrix) {
                $tab_id_globalobject[] = $action_matrix->getId_globalobject();
            }

        }

        $tab_link = dims_globalobject_link::getLinkFromListGO($tab_id_globalobject, $id_go_origin, dims_globalobject_link::$GET_FROM_TO, $db);
        $tab_link2 =    dims_globalobject_link::getLinkFromListGO($tab_id_globalobject, $id_go_origin, dims_globalobject_link::$GET_TO_FROM, $db);

        foreach ($tab_action as $action) {
            if(isset($tab_action_matrix[$action->getID()])){
                foreach ($tab_action_matrix[$action->getID()] as $matrix) {
                    if(isset($tab_user[$action->getIdUser()])){
                        $lastname = $tab_user[$action->getIdUser()]->getLastname();
                        $firstname = $tab_user[$action->getIdUser()]->getFirstname();
                    }else{
                        $lastname = "";
                        $firstname = "";
                    }
                    if(isset($tab_link[$matrix->getId_globalobject()][$action->getID()])
                            && $tab_link[$matrix->getId_globalobject()][$action->getID()]){
                        $go_from = $tab_link[$matrix->getId_globalobject()][$action->getID()]->getId_globalobject_from();
                        $go_to = $tab_link[$matrix->getId_globalobject()][$action->getID()]->getId_globalobject_to();
                        $link_type = $tab_link[$matrix->getId_globalobject()][$action->getID()]->getType();
                    }else{
                        if(isset($tab_link2[$matrix->getId_globalobject()][$action->getID()])
                            && $tab_link2[$matrix->getId_globalobject()][$action->getID()]){
                            $go_from = $tab_link2[$matrix->getId_globalobject()][$action->getID()]->getId_globalobject_from();
                            $go_to = $tab_link2[$matrix->getId_globalobject()][$action->getID()]->getId_globalobject_to();
                            $link_type = $tab_link2[$matrix->getId_globalobject()][$action->getID()]->getType();
                        }else{
                            $go_from = 0;
                            $go_to = 0;
                            $link_type = 0;
                        }
                    }

                    $tab_historique[$action->getID()][] = new dims_historique(
                            $action->getGlobalObjectOrigin(),
                            $action->getID(),
                            $action->getType(),
                            $action->getComment(),
                            $action->getNbComment(),
                            $action->getIdUser(),
                            $action->getTimestpModify(),
                            $matrix->getId_globalobject(),
                            $tab_user[$action->getIdUser()]->getLastname(),
                            $tab_user[$action->getIdUser()]->getFirstname(),
                            $go_from,
                            $go_to,
                            $link_type
                        );
                }
            }
        }
        return $tab_historique ;
    }
}

?>
