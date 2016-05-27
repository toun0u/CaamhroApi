<?php
//On recupere la variable ACTION passée en GET
//$action=dims_load_securvalue("action",_DIMS_CHAR_INPUT,true,true);
//$refresh=dims_load_securvalue("refresh",_DIMS_CHAR_INPUT,true,true);

//Si action est égal a "list" on affiche la list
    //On va creer la liste des adresses accesible par l'utilisateur
    //On compte le nombre de contact de la personne
    $sql = "SELECT COUNT(*) AS nb_contact
            FROM dims_contact";

    $res = $db->query($sql);
    $count_res = $db->fetchrow($res);

    //On affiche le titre
    echo  '			<div id="dims_contact">';

    // choix du mode de sélection
    echo '			<div class="midb16">Liste des contacts <span class="infos_contact">('.$count_res['nb_contact'].')</span></div>';

    echo '          <div class="filter">
                        <a class="filter_contact" href="#" title="Tous"  onclick="javascript:refresh_list_contact(\'\');">Tous</a> &nbsp;';

                    //Affichage des lettres pour filtrer
                    $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
                    for($i=0; $i<26; $i++)
                    {
                        $sql = "SELECT COUNT(*) AS nb_res
                                FROM dims_contact c
                                WHERE c.id_workspace = :workspaceid
                                AND (c.lastname LIKE :letter OR c.firstname LIKE :letter )";
                        $filter = $db->query($sql, array(
                            ':workspaceid'  => $_SESSION['dims']['workspaceid'],
                            ':letter'       => $letter[$i]."%"
                        ));
                        $filter_count = $db->fetchrow($filter);
                        if($filter_count['nb_res']>1){$s="s";}else{$s="";}
                        if($filter_count['nb_res']>0){$text_filter = "<strong>".$letter[$i]."</strong>";$text_title = $letter[$i]." : ".$filter_count['nb_res']." adresse".$s;}else{
                            $text_filter = $letter[$i];$text_title = $letter[$i];}

                        echo '<a class="filter_contact" href="#" title="'.$text_title.'" onclick="javascript:refresh_list_contact(\''.$letter[$i].'\');">'.$text_filter.'</a> ';
                    }

    echo '          </div>';
    echo '          <div class="filter">
                        <form action="#" style="display:inline;"><input type="text"  value="Recherche" onsubmit="javascript:return search_contact_list();" id="q"/></form><img src="./common/img/search.png" onclick="javascript:search_contact_list();" style="cursor:pointer;border:none;">';
    // Sécurisation du formulaire par token
    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
    $token = new FormToken\TokenField;
    $tokenHTML = $token->generate();
    echo $tokenHTML;
    echo '              <input type="button" value="CREER UN CONTACT" onclick="javascript:form_create_contact();" id="search_button"/>
                    </div>
                    ';
	//<input type="button" value="FILTRAGE" onclick="javascript:form_adv_search_contact('.$_SESSION['dims']['currentheadingid'].');" id="search_button"/>
    echo '          <div id="list_contact">';
    echo '              <table width="100%" cellpadding="0" cellspacing="0">';
                        //On récupère les adresses des contacts de l'utilisateur
                        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil

                        $params = array(':workspaceid' => $_SESSION['dims']['workspaceid']);
                        $sql = "SELECT c.*
                                FROM dims_contact c
                                WHERE c.id_workspace = :workspaceid ";
                        if(($filter=dims_load_securvalue("filter",_DIMS_CHAR_INPUT,true,true))&&($filter != "undefined"))
                        {
                            $sql .= " AND (c.lastname LIKE :filter OR c.firstname LIKE :filter )";
                            $params[':filter'] = $filter."%" ;
                        }
                        $sql.= " ORDER BY c.lastname";
                        $res = $db->query($sql, $params );

                        //On boucle pour afficher tout les contacts
                        while($tab_res = $db->fetchrow($res))
                        {
                            //On génére la liste des contacts
                            if($filter){
                                $tab_res['firstname'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:#04699D">$1</span>$2',$tab_res['firstname']);
                                 $tab_res['lastname'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:#04699D">$1</span>$2',$tab_res['lastname']);
                            }

                            if($tab_res['note']==""){$tab_res['note']="Aucune note";}

                            //On affiche les renseignements du contact et la photo si présente.
                            echo '<tr class="line_contact">
                                    <td><img src="./common/img/arbo.gif" border="0" style="float:right;margin-top:10px;"><a href="#" onclick="javascript:show_profil('.$tab_res['id'].',\''.$filter.'\');">'.$tab_res['firstname'].' '.$tab_res['lastname'].'</a></td>';

                            //On affiche les liens avec le contact
                            /*echo '  <td class="lien_contact">';
                                    if($tab_res['ami']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_ami.jpg" alt="(A)" title="Relation amicale"/>';}
                                    if($tab_res['loisirs']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_loisirs.jpg" alt="(L)" title="Relation &eacute;v&eacute;nementielle"/>';}
                                    if($tab_res['pro']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_pro.jpg" alt="(P)" title="Relation professionnelle"/>';}
                            </td>*/echo '
                                </tr>';
                        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
    echo '              </table>
                    </div>
                </div>';

?>