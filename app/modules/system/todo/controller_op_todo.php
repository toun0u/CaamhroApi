<?php


/**
 * Description of controller_op_todo
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 */
class controller_op_todo {

    public static function op_todo($todo_op, $params = array()){
        $dims = dims::getInstance();

        switch($todo_op){
            case _OP_SHOW_POPUP_TODO :
                $id_popup = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, true, false);
                $id_user = dims::getInstance()->getUserId();

                $liste_todo = controller_todo::getTodoConcernedForUser($id_user);

                $liste_id_user = array();
                foreach ($liste_todo as $todo) {
                    if(!isset($liste_id_user[$todo->getUserFrom()])){
                        $liste_id_user[$todo->getUserFrom()] = $todo->getUserFrom();
                    }
                    if(!isset($liste_id_user[$todo->getUserBy()])){
                        $liste_id_user[$todo->getUserBy()] = $todo->getUserBy();
                    }
                    if(!isset($liste_id_user[$todo->getUserTo()])){
                        $liste_id_user[$todo->getUserTo()] = $todo->getUserTo();
                    }
                    if(!isset($liste_id_user[$todo->getIdUser()])){
                        $liste_id_user[$todo->getIdUser()] = $todo->getIdUser();
                    }
                }

                $liste_user = user::geListeUserByIdUser($liste_id_user);

                view_todo_factory::buildViewPopupListeTodos($liste_todo, $liste_user, $id_popup);
                break;
            default :
                break;
        }

    }
}

?>
