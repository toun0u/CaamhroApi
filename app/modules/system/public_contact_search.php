<?php
//On recupere l'action a executer
$action_sup = dims_load_securvalue("action_sup",dims_const::_DIMS_CHAR_INPUT,true,true);
$filter="";
$filter=dims_load_securvalue("filter",dims_const::_DIMS_CHAR_INPUT,true,true);

//ACTION : RECHERCHER DANS LES ADRESSES DE L'UTILISATEUR
if($action_sup == "adv_search"){
//On recupere la requete "q"
    $q = urldecode(dims_load_securvalue("q",dims_const::_DIMS_CHAR_INPUT,true,true,true));

    //On cree la requete correspondante
    $sql = 'SELECT c.*
            FROM dims_contact c
            WHERE c.id_workspace = :workspaceid
            AND (
                c.lastname LIKE :q1
                OR c.lastname LIKE :q2
                OR c.lastname LIKE :q3
                OR c.firstname LIKE :q1
                OR c.firstname LIKE :q2
                OR c.firstname LIKE :q3
                )';

    //On affiche le résultat
    $res = $db->query($sql, array(
        ':workspaceid'  => $_SESSION['dims']['workspaceid'],
        ':q1'           => '%'.$q.'%',
        ':q2'           => $q.'%',
        ':q3'           => '%'.$q
    ));

echo '  <table width="100%" cellpadding="0" cellspacing="0">';
        //On récupère les adresses des contacts de l'utilisateur
        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil

        //On boucle pour afficher tout les contacts
        while($tab_res = $db->fetchrow($res))
        {
            if($tab_res['note']==""){$tab_res['note']="Aucune note";}

            //On affiche les renseignements du contact et la photo si présente.
            echo '<tr class="line_contact">
                    <td><img src="./common/img/arbo.gif" border="0" style="float:right;margin-top:10px;"><a href="#" onclick="javascript:show_profil('.$tab_res['id'].',\'\');">'.$tab_res['firstname'].' '.$tab_res['lastname'].'</a></td>
                </tr>';
        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
echo '  </table>';
}

//ACTION : Afficher résultat rapide recherche entreprise
if($action_sup == "speed_search"){
//On recupere la requete "q"


    //On cree la requete correspondante
    $params = array();
    $sql = 'SELECT e.id, e.label
            FROM dims_ent e
            LEFT JOIN dims_ent_contact ec
            ON ec.id_ent = e.id
            AND ec.id_contact != :userid
            WHERE e.id_workspace = :workspaceid ';
    $params[':userid'] = $_SESSION['dims']['userid'];
    $params[':workspaceid'] = $_SESSION['dims']['workspaceid'];
    if($q = urldecode(dims_load_securvalue("q",dims_const::_DIMS_CHAR_INPUT,true,true,true))){
        $sql .= ' AND (e.label LIKE :q1 OR e.label LIKE :q2 OR e.label LIKE :q3 )';
        $params[':q1'] = '%'.$q.'%';
        $params[':q2'] = $q.'%';
        $params[':q3'] = '%'.$q;
    }

    //On affiche le résultat
    $res = $db->query($sql, $params);

echo '    <label>'._SYSTEM_LABEL_CHOOSE.':</label>
          <select size="5" name="ent_id_ent">';
        //On récupère les adresses des contacts de l'utilisateur
        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil

        //On boucle pour afficher tout les contacts
        while($tab_res = $db->fetchrow($res))
        {
            //On affiche les renseignements du contact et la photo si présente.
            echo '<option value="'.$tab_res['id'].'">'.$tab_res['label'].'</option>';
        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
echo '  </select>';
}
?>