<?php
//On recupere la variable ACTION passée en GET
$action=dims_load_securvalue("action",dims_const::_DIMS_CHAR_INPUT,true,true);
$refresh=dims_load_securvalue("refresh",dims_const::_DIMS_CHAR_INPUT,true,true);

// ACTION : AFFICHER LISTE ADDRESSES
//###################################

//Si action est égal a "list" on affiche la list
if($refresh != 1){
    //On va creer la liste des adresses accesible par l'utilisateur

    echo '<td class="colonne_50">';

    //On compte le nombre de contact de la personne
    $sql = "SELECT COUNT(*) AS nb_contact
            FROM dims_mod_private_user_contact dmpc
            WHERE dmpc.id_user = :iduser ";
    $res = $db->query($sql, array(
        ':iduser' => $_SESSION['dims']['userid']
    ));
    $count_res = $db->fetchrow($res);

    //On affiche le titre
    echo  '     <div id="dims_address">
                    <div class="titre_address">CARNET D\'ADRESSES <span class="infos_address">('.$count_res['nb_contact'].')</span></div>';

    echo '          <div class="filter">
                        <a class="filter_address" href="#" title="Tous"  onclick="javascript:refresh_list_address('.$_SESSION['dims']['currentheadingid'].',\'\');">Tous</a> &nbsp;';

                    //Affichage des lettres pour filtrer
                    $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
                    for($i=0; $i<26; $i++)
                    {
                        $sql = "SELECT COUNT(*) AS nb_res
                                FROM dims_user du
                                INNER JOIN dims_mod_private_user_contact dmpc ON dmpc.id_contact = du.id
                                WHERE dmpc.id_user = :iduser
                                AND (du.lastname LIKE :letter OR du.firstname LIKE :letter )";
                        $filter = $db->query($sql, array(
                            ':iduser' => $_SESSION['dims']['userid'],
                            ':letter' => $letter[$i]."%"
                        ));
                        $filter_count = $db->fetchrow($filter);
                        if($filter_count['nb_res']>1){$s="s";}else{$s="";}
                        if($filter_count['nb_res']>0){$text_filter = "<strong>".$letter[$i]."</strong>";$text_title = $letter[$i]." : ".$filter_count['nb_res']." adresse".$s;}else{
                            $text_filter = $letter[$i];$text_title = $letter[$i];}

                        echo '<a class="filter_address" href="#" title="'.$text_title.'" onclick="javascript:refresh_list_address('.$_SESSION['dims']['currentheadingid'].',\''.$letter[$i].'\');">'.$text_filter.'</a> ';
                    }

    echo '          </div>';
    echo '          <div class="filter"><input type="button" value="AJOUTER UNE ADRESSE" onclick="javascript:form_search_address('.$_SESSION['dims']['currentheadingid'].');" id="search_button"/> <input type="button" value="FILTRAGE" onclick="javascript:form_adv_search_address('.$_SESSION['dims']['currentheadingid'].');" id="search_button"/></div>';
    echo '          <div id="list_contact">';
    echo '              <table width="100%" cellpadding="0" cellspacing="0">';
                        //On récupère les adresses des contacts de l'utilisateur
                        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil
                        $param = array(
                            ':iduser' => $_SESSION['dims']['userid']
                        );
                        $sql = "SELECT du.lastname, du.firstname, du.id,
                                dmpc.ami, dmpc.pro, dmpc.loisirs, dmpc.note
                                FROM dims_user du
                                INNER JOIN dims_mod_private_user_contact dmpc ON dmpc.id_contact = du.id
                                WHERE dmpc.id_user = :iduser
                                AND du.id != :iduser ";
                        if(($filter=dims_load_securvalue("filter",dims_const::_DIMS_CHAR_INPUT,true,true))&&($filter != "undefined"))
                        {
                            $sql .= " AND (du.lastname LIKE :filter OR du.firstname LIKE :filter )";
                            $param[':filter'] = $filter."%";
                        }
                        $sql.= " ORDER BY du.lastname";
                        $res = $db->query($sql, $param);
                        //On boucle pour afficher tout les contacts
                        while($tab_res = $db->fetchrow($res))
                        {
                            //On génére la liste des contacts
                            if($filter){
                                $tab_res['firstname'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:white">$1</span>$2',$tab_res['firstname']);
                                 $tab_res['lastname'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:white">$1</span>$2',$tab_res['lastname']);
                            }

                            if($tab_res['note']==""){$tab_res['note']="Aucune note";}

                            //On affiche les renseignements du contact et la photo si présente.
                            echo '<tr class="line_contact">
                                    <td style="background:url(\'./common/modules/sitecom/gfx/photo.png\');" width="40px;"><a href="#" onclick="javascript:show_profil('.$_SESSION['dims']['currentheadingid'].','.$tab_res['id'].',\''.$filter.'\');">&nbsp;</a></td>
                                    <td><a href="#" onclick="javascript:show_profil('.$_SESSION['dims']['currentheadingid'].','.$tab_res['id'].',\''.$filter.'\');" style="padding-left:10px;" class="note">'.$tab_res['firstname'].' '.$tab_res['lastname'].'<span class="note_perso">'.$tab_res['note'].'</span></a></td>';

                            //On affiche les liens avec le contact
                            echo '  <td class="lien_contact">';
                                    if($tab_res['ami']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_ami.jpg" alt="(A)" title="Relation amicale"/>';}
                                    if($tab_res['loisirs']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_loisirs.jpg" alt="(L)" title="Relation &eacute;v&eacute;nementielle"/>';}
                                    if($tab_res['pro']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_pro.jpg" alt="(P)" title="Relation professionnelle"/>';}
                            echo '  </td>
                                </tr>';
                        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
    echo '              </table>
                    </div>
                    <div class="list_footer">&nbsp;</div>
                </div>
        </td>
        <td>&nbsp;</td>
        <td class="colonne_50">
            <div id="profil_address"></div>
        </td>';
}


// ACTION : RAFRAICHIR LIST ADRESSES
//###################################

//Si action est égal a "refresh"
if($refresh == 1){
     //On compte le nombre de contact de la personne
    $sql = "SELECT COUNT(*) AS nb_contact
            FROM dims_mod_private_user_contact dmpc
            WHERE dmpc.id_user = :iduser ";
    $res = $db->query($sql,array(
        ':iduser' => $_SESSION['dims']['userid']
    ));
    $count_res = $db->fetchrow($res);

    //On affiche le titre
    echo  '         <div class="titre_address">CARNET D\'ADRESSES <span class="infos_address">('.$count_res['nb_contact'].')</span></div>';
                    if($count_res['nb_contact']>1){$s="s";}else{$s="";}
    echo '          <div class="filter">
                        <a class="filter_address" href="#" title="Tous : '.$count_res['nb_contact'].' adresse'.$s.'" onclick="javascript:refresh_list_address('.$_SESSION['dims']['currentheadingid'].',\'\');">Tous</a> &nbsp;';
                    //Affichage des lettres pour filtrer
                    $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
                    for($i=0; $i<26; $i++)
                    {
                        $sql = "SELECT COUNT(*) AS nb_res
                                FROM dims_user du
                                INNER JOIN dims_mod_private_user_contact dmpc ON dmpc.id_contact = du.id
                                WHERE dmpc.id_user = :iduser
                                AND (du.lastname LIKE :letter OR du.firstname LIKE :letter )";
                        $filter = $db->query($sql, array(
                            ':iduser' => $_SESSION['dims']['userid'],
                            ':letter' => $letter[$i]."%"
                        ));
                        $filter_count = $db->fetchrow($filter);
                        if($filter_count['nb_res']>1){$s="s";}else{$s="";}
                        if($filter_count['nb_res']>0){$text_filter = "<strong>".$letter[$i]."</strong>";$text_title = $letter[$i]." : ".$filter_count['nb_res']." adresse".$s;}else{
                            $text_filter = $letter[$i];$text_title = $letter[$i];}

                        echo '<a class="filter_address" href="#" title="'.$text_title.'" onclick="javascript:refresh_list_address('.$_SESSION['dims']['currentheadingid'].',\''.$letter[$i].'\');">'.$text_filter.'</a> ';
                    }

    echo '          </div>';
    echo '          <div class="filter"><input type="button" value="AJOUTER UNE ADRESSE" onclick="javascript:form_search_address('.$_SESSION['dims']['currentheadingid'].');" id="search_button"/> <input type="button" value="FILTRAGE" onclick="javascript:form_adv_search_address('.$_SESSION['dims']['currentheadingid'].');" id="search_button"/></div>';
    echo '          <div id="list_contact">';
    echo '              <table width="100%" cellpadding="0" cellspacing="0">';
                        //On récupère les adresses des contacts de l'utilisateur
                        //Voir pour récuperer l'image une fois l'upload d'image OK dans le profil
                        $param = array(
                            ':iduser' => $_SESSION['dims']['userid'],
                        );
                        $sql = "SELECT du.lastname, du.firstname, du.id, dmpc.ami,
                                dmpc.pro, dmpc.loisirs, dmpc.note
                                FROM dims_user du
                                INNER JOIN dims_mod_private_user_contact dmpc
                                ON dmpc.id_contact = du.id
                                WHERE dmpc.id_user = :iduser
                                AND du.id != :iduser ";
                        if(($filter=dims_load_securvalue("filter",dims_const::_DIMS_CHAR_INPUT,true,true))&&($filter != "undefined"))
                        {
                            $sql .= " AND (du.lastname LIKE :filter OR du.firstname LIKE :filter )";
                            $param[':filter'] = $filter."%";
                        }
                        $sql.= " ORDER BY du.lastname";

                        $res = $db->query($sql, $param );
                        //On boucle pour afficher tout les contacts
                        while($tab_res = $db->fetchrow($res))
                        {
                            //On génére la liste des contacts
                            if($filter){
                                $tab_res['firstname'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:white">$1</span>$2',$tab_res['firstname']);
                                 $tab_res['lastname'] = preg_replace('#^('.$filter.'){1}(.+)$#','<span style="color:white">$1</span>$2',$tab_res['lastname']);
                            }

                            if($tab_res['note']==""){$tab_res['note']="Aucune note";}else{$tab_res['note']=nl2br($tab_res['note']);}

                            //On affiche les renseignements du contact et la photo si présente.
                            echo '<tr class="line_contact">
                                    <td style="background:url(\'./common/modules/sitecom/gfx/photo.png\');" width="40px;">&nbsp;</td>
                                    <td style="padding-left:10px;"><a href="#" onclick="javascript:show_profil('.$_SESSION['dims']['currentheadingid'].','.$tab_res['id'].');" class="note">'.$tab_res['firstname'].' '.$tab_res['lastname'].'<span class="note_perso">'.$tab_res['note'].'</span></a></td>';

                            //On affiche les liens avec le contact
                            echo '  <td class="lien_contact">';
                                    if($tab_res['ami']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_ami.jpg" alt="(A)" title="Relation amicale"/>';}
                                    if($tab_res['loisirs']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_loisirs.jpg" alt="(L)" title="Relation &eacute;v&eacute;nementielle"/>';}
                                    if($tab_res['pro']==true){echo '<img src="./common/templates/frontofficesitecom/gfx/lien_pro.jpg" alt="(P)" title="Relation professionnelle"/>';}
                            echo '  </td>
                                </tr>';
                        }

    //On ferme les balises et on rajoute un div
    //Dans lequel on mettra le profil de l'utilisateur selectionné.
    echo '              </table>
                    </div>
                <div class="list_footer">&nbsp;</div>
            </div>';
}

?>