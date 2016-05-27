<?php
//On recupere la variable ACTION passée en GET
//$action=dims_load_securvalue("action",_DIMS_CHAR_INPUT,true,true);
//$refresh=dims_load_securvalue("refresh",_DIMS_CHAR_INPUT,true,true);

//Si action est égal a "list" on affiche la list
    //On va creer la liste des adresses accesible par l'utilisateur
    //On compte le nombre de ent de la personne
    $sql = "SELECT COUNT(*) AS nb_ent
            FROM dims_ent";

    $res = $db->query($sql);
    $count_res = $db->fetchrow($res);

    //On affiche le titre
    echo  '			<div id="dims_contact">';

    // choix du mode de sélection
    echo '			<div class="midb16">Liste des entreprises <span class="infos_ent">('.$count_res['nb_ent'].')</span></div>';

    echo '          <div class="filter">
                        <a class="filter_contact" href="#" title="Tous"  onclick="javascript:refresh_list_ent(\'\');">Tous</a> &nbsp;';

                    //Affichage des lettres pour filtrer
                    $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
                    for($i=0; $i<26; $i++)
                    {
                        $sql = "SELECT COUNT(*) AS nb_res
                                FROM dims_ent e
                                WHERE e.id_workspace = :workspaceid
                                AND (e.label LIKE :letter )";
                        $filter = $db->query($sql, array(
                            ':workspaceid'  => $_SESSION['dims']['workspaceid'],
                            ':letter'       => $letter[$i]."%"
                        ));
                        $filter_count = $db->fetchrow($filter);
                        if($filter_count['nb_res']>1){$s="s";}else{$s="";}
                        if($filter_count['nb_res']>0){$text_filter = "<strong>".$letter[$i]."</strong>";$text_title = $letter[$i]." : ".$filter_count['nb_res']." adresse".$s;}else{
                            $text_filter = $letter[$i];$text_title = $letter[$i];}

                        echo '<a class="filter_contact" href="#" title="'.$text_title.'" onclick="javascript:refresh_list_ent(\''.$letter[$i].'\');">'.$text_filter.'</a> ';
                    }

    echo '          </div>';
    echo '          <div class="filter">
                        <form action="#" style="display:inline;"><input type="text"  value="Recherche" onsubmit="javascript:return search_ent_list();" id="q"/></form><img src="./common/img/search.png" onclick="javascript:search_ent_list();" style="cursor:pointer;border:none;">';
    // Sécurisation du formulaire par token
    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
    $token = new FormToken\TokenField;
    $tokenHTML = $token->generate();
    echo $tokenHTML;
    echo '              <input type="button" value="NOUVELLE ENTREPRISE" onclick="javascript:form_create_ent();" id="search_button"/>
                    </div>
                    ';
	//<input type="button" value="FILTRAGE" onclick="javascript:form_adv_search_ent('.$_SESSION['dims']['currentheadingid'].');" id="search_button"/>
    echo '          <div id="list_contact">';
    echo '              <table width="100%" cellpadding="0" cellspacing="0">';
                        //On récupère les adresses des ents de l'utilisateur
                        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil
                        $params =array();
                        $sql = "SELECT *
                                FROM dims_ent
                                WHERE id_workspace = :workspaceid ";
                        $params[':workspaceid'] = $_SESSION['dims']['workspaceid'];
                        if(($filter=dims_load_securvalue("filter",_DIMS_CHAR_INPUT,true,true))&&($filter != "undefined"))
                        {
                            $sql .= " AND (label LIKE :filter )";
                            $params[':filter'] = $filter."%";
                        }
                        $sql.= " ORDER BY label";
                        $res = $db->query($sql, $params);

                        //On boucle pour afficher tout les ents
                        while($tab_res = $db->fetchrow($res))
                        {
                            //On génére la liste des ents
                            if($filter){
                                $tab_res['label'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:#04699D">$1</span>$2',$tab_res['label']);
                            }

                            //On affiche les renseignements du ent et la photo si présente.
                            echo '<tr class="line_contact">
                                    <td><a class="alone" href="#" onclick="javascript:show_entview('.$tab_res['id'].');"><img src="./common/img/arbo.gif" border="0" style="float:right;margin-top:10px;"><a href="#" onclick="javascript:show_ent('.$tab_res['id'].',\''.$filter.'\');" style="padding-left:10px;" class="note">'.$tab_res['label'].'</a></td>';

                           echo '</tr>';
                        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
    echo '              </table>
                    </div>
                </div>';

?>