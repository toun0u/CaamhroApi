<?php
//On recupere l'action a executer
$action_sup = dims_load_securvalue("action_sup",dims_const::_DIMS_CHAR_INPUT,true,true);
$filter="";
$filter=dims_load_securvalue("filter",dims_const::_DIMS_CHAR_INPUT,true,true);

//ACTION : RECHERCHER DANS LES ADRESSES DE L'UTILISATEUR
if($action_sup == "adv_search"){
//On recupere la requete "q"


    //On cree la requete correspondante
    $params = array();
    $sql = 'SELECT *
            FROM dims_ent
            WHERE id_workspace = :workspaceid ';
    $params[':workspaceid'] = $_SESSION['dims']['workspaceid'];
    if($q = urldecode(dims_load_securvalue("q",dims_const::_DIMS_CHAR_INPUT,true,true,true))){
        $sql .= ' AND (label LIKE :q1 OR label LIKE :q2 OR label LIKE :q3 )';
        $params[':q1'] = '%'.$q.'%';
        $params[':q2'] = $q.'%';
        $params[':q3'] = '%'.$q;
    }

    //On affiche le résultat
    $res = $db->query($sql, $params);

echo '  <table width="100%" cellpadding="0" cellspacing="0">';
        //On récupère les adresses des contacts de l'utilisateur
        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil

        //On boucle pour afficher tout les contacts
        while($tab_res = $db->fetchrow($res))
        {
            if($tab_res['note']==""){$tab_res['note']="Aucune note";}

            //On affiche les renseignements du contact et la photo si présente.
            echo '<tr class="line_contact">
                    <td><img src="./common/img/arbo.gif" border="0" style="float:right;margin-top:10px;"><a href="#" onclick="javascript:show_ent('.$tab_res['id'].',\'\');" style="padding-left:10px;" class="note">'.$tab_res['label'].'</a></td>
                </tr>';
        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
echo '  </table>';
}
?>