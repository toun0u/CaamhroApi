<?php

/**
 * Description of dims_historique
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 *
 */
class dims_historique {
    private $action_type ;
    private $action_commentaire;
    private $action_nb_commentaires;
    private $action_id;
    private $action_id_user;
    private $action_timestp_modify;
    private $action_globalobject_origin;

    private $matrix_globalobject_tiers ;

    private $user_lastname;
    private $user_firstname ;

    private $link_from ;
    private $link_to ;
    private $link_type ;


    public function __construct($action_globalobject_origin,
                                $action_id,
                                $action_type,
                                $action_commentaire,
                                $action_nb_commentaires,
                                $action_id_user,
                                $action_timestp_modify,
                                $matrix_globalobject_tiers,
                                $user_lastname,
                                $user_firstname,
                                $link_from,
                                $link_to,
                                $link_type
            ) {
        $this->action_type = $action_type;
        $this->action_commentaire = $action_commentaire;
        $this->action_nb_commentaires = $action_nb_commentaires;
        $this->action_id = $action_id;
        $this->action_id_user = $action_id_user;
        $this->action_timestp_modify = $action_timestp_modify;
        $this->action_globalobject_origin = $action_globalobject_origin;

        $this->matrix_globalobject_tiers = $matrix_globalobject_tiers ;

        $this->user_lastname = $user_lastname;
        $this->user_firstname = $user_firstname ;

        $this->link_from = $link_from ;
        $this->link_to = $link_to ;
        $this->link_type = $link_type ;
        ;
    }

    public function getActionType(){
        return $this->action_type ;
    }

    public function getActionCommentaire(){
        return $this->action_commentaire ;
    }

    public function getActionNbCommentaires(){
        return $this->action_nb_commentaires ;
    }

    public function getActionId(){
        return $this->action_id ;
    }

    public function getActionIdUser(){
        return $this->action_id_user ;
    }

    public function getActionTimestpModify(){
        return $this->action_timestp_modify ;
    }

    public function getActionGlobalObjectOrigin(){
        return $this->action_globalobject_origin ;
    }

    public function getMatrixGlobalObjectTiers(){
        return $this->matrix_globalobject_tiers ;
    }

    public function getUserLastname(){
        return $this->user_lastname ;
    }

    public function getUserFirstname(){
        return $this->user_firstname ;
    }

    public function getLinkFrom(){
        return $this->link_from ;
    }

    public function getLinkTo(){
        return $this->link_to;
    }

    public function getLinkType(){
        return $this->link_type ;
    }



}

?>
