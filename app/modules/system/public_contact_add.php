<?php
//On recupere l'action a executer
$action_sup="";
$action_sup = dims_load_securvalue("action_sup",dims_const::_DIMS_CHAR_INPUT,true,true);
$filter="";
$filter=dims_load_securvalue("filter",dims_const::_DIMS_CHAR_INPUT,true,true);


//Aucune action définie, on affiche le formulaire
if($action_sup == ""){
    echo '<div id="onglets"><input type="button" value="Recherche rapide" id="search_button_disabled" disabled="disabled"/> <input type="button" value="Recherche avanc&eacute;e" id="search_button" onclick="javascript:switch_add('.$_SESSION['dims']['currentheadingid'].',2);"/></div>';
    echo '<p class="entete_search" style="margin-left:5px;">Recherche rapide</p>';
    echo '<div id="infos_address">&nbsp;</div>';
    echo '  <table style="margin-left:20px;">
                <tr>
                    <td style="text-align:right;">Nom</td>
                    <td><input type="text" name="address_search_lastname" id="address_search_lastname" id="search_button" size="20"/></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Pr&eacute;nom</td>
                    <td><input type="text" name="address_search_firstname" id="address_search_firstname" id="search_button" size="20"/></td>
                </tr>
            </table>';
    echo '  <input type="button" onclick="javascript:search_contact();" value="RECHERCHER" style="margin-left:200px;margin-top:20px;margin-bottom:16px;" id="search_button"/>';
    echo '<div id="res_search"></div>';
}

//ACTION : afficher le formulaire de recherche avancee
if($action_sup == "adv_form_search")
{
    echo '<div id="onglets"><input type="button" value="Recherche rapide" id="search_button" onclick="javascript:switch_add('.$_SESSION['dims']['currentheadingid'].',1);"/> <input type="button" value="Recherche avanc&eacute;e" id="search_button_disabled" disabled="disabled"/></div>';
    echo '<p class="entete_search" style="margin-left:5px;">Recherche avanc&eacute;e</p>';
    echo '<div id="infos_address">&nbsp;</div>';
    echo '  <table style="margin-left:20px;">
                <tr>
                    <td style="text-align:right;">Nom</td>
                    <td><input type="text" name="address_search_lastname" id="address_search_lastname" id="search_button" size="20"/></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Pr&eacute;nom</td>
                    <td><input type="text" name="address_search_firstname" id="address_search_firstname" id="search_button" size="20"/></td>
                </tr>
                 <tr>
                    <td style="text-align:right;">Arrondissement</td>
                    <td><input type="text" disabled="disabled" name="arrond_search" id="arrond_map" id="search_button" size="2"/><input type="button" value="X" onclick="javascript:document.getElementById(\'arrond_map\').value = \'\';" id="cancel_button" title="annuler"/></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center">
                        Choisissez un arrondissement<br/>
                        <img src="./common/modules/sitecom/gfx/carteparisarrond.jpg" alt="mapping paris" width="178" height="163" border="0" usemap="#parisarrond"/><br/>
                        <map name="parisarrond" onclick="javascript:document.getElementById(\'address_search_cp\').value = \'\';document.getElementById(\'address_search_cp\').disabled = true;">
                            <area shape="poly" coords="32,57,32,51,41,43,46,43,70,27,74,28,71,44,72,50,47,58,45,64,31,58" title="XVII&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 17"/>
                            <area shape="poly" coords="72,50,76,49,84,53,108,49,113,36,111,34,110,27,75,27,71,44" title="XVIII&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 18"/>
                            <area shape="poly" coords="108,48,112,50,112,59,117,66,134,62,147,57,139,52,135,34,129,27,111,27,111,35,114,35,109,48" title="XIX&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 19"/>
                            <area shape="poly" coords="147,58,152,72,151,100,137,98,134,87,128,85,127,78,117,66,134,61,140,61" title="XX&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 20"/>
                            <area shape="poly" coords="151,101,149,116,145,116,138,121,138,125,131,129,108,99,110,92,121,96,134,97" title="XII&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 12"/>
                            <area shape="poly" coords="131,128,107,142,99,142,96,139,92,140,92,112,102,112,107,109,111,104" title="XIII&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 13"/>
                            <area shape="poly" coords="92,140,79,141,75,137,53,131,69,108,71,108,74,104,92,112,91,112,92,111" title="XIV&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 14"/>
                            <area shape="poly" coords="105,72,117,66,128,78,128,84,133,87,137,98,110,92,109,82" title="XI&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 11"/>
                            <area shape="poly" coords="109,85,94,79,91,90,108,99,110,92" title="IV&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 4"/>
                            <area shape="poly" coords="96,69,94,79,109,85,108,77,105,72" title="III&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 3"/>
                            <area shape="poly" coords="95,69,85,66,73,69,94,79" title="II&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 2"/>
                            <area shape="poly" coords="90,90,85,85,66,78,72,69,94,79" title="Ier"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 1"/>
                            <area shape="poly" coords="72,69,72,51,76,49,84,53,92,51,91,66,85,65" title="IX&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 9"/>
                            <area shape="poly" coords="93,51,109,49,112,50,112,59,117,66,105,72,90,67" title="X&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 10"/>
                            <area shape="poly" coords="71,69,72,50,48,58,45,64,49,77,66,77" title="VIII&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 8"/>
                            <area shape="poly" coords="44,64,49,77,16,117,11,117,5,108,7,103,7,100,11,99,18,78,21,78,31,58" title="XVI&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 16"/>
                            <area shape="poly" coords="80,84,77,92,66,99,63,100,60,97,57,99,42,85,49,78,65,78" title="VII&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 7"/>
                            <area shape="poly" coords="73,103,66,99,63,100,60,98,56,99,42,86,17,117,20,117,20,124,22,126,32,121,52,131,69,108,72,108,74,104" title="XV&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 15"/>
                            <area shape="poly" coords="91,91,83,108,67,99,77,93,81,84,85,86" title="VI&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 6"/>
                            <area shape="poly" coords="91,91,83,108,92,112,100,112,108,106,109,101,106,97" title="V&egrave;me"  href="#" onclick="javascript:document.getElementById(\'arrond_map\').value = 5"/>
                        </map>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right;">Caract&eacute;ristique</td>
                    <td>';

                            $sql =  "SELECT id, label_car FROM dims_car";
                            $result = $db->query($sql);
echo '                  <select id="car" name="car" style="width:200px;">
                            <option value="all">Tous</option>';
                        while($data = $db->fetchrow($result))
                        {
echo '                      <option value="'.$data['id'].'">'.$data['label_car'].'</option>';
                        }
echo '                  </select>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right;">Int&eacute;r&ecirc;t</td>
                    <td>';

                            $sql =  "SELECT id, nom_interest FROM dims_interest";
                            $result = $db->query($sql);
echo '                  <select id="interest" name="interest">
                            <option value="all">Tous</option>';
                        while($data = $db->fetchrow($result))
                        {
echo '                      <option value="'.$data['id'].'">'.$data['nom_interest'].'</option>';
                        }
echo '                  </select>
                    </td>
                </tr>
            </table>';
    echo '  <input type="button" onclick="javascript:search_address2('.$_SESSION['dims']['currentheadingid'].');" value="RECHERCHER" style="margin-left:200px;margin-top:20px;margin-bottom:20px;" id="search_button"/>';
}


//ACTION : rechercher les utilisateurs qui correspondent aux crit&egrave;res
if($action_sup == "search"){
    //On recupere la requete "q"
    $q = urldecode(dims_load_securvalue("q",dims_const::_DIMS_CHAR_INPUT,true,true));
    $data_q = explode('%sep%',$q);

    //On cree la requete correspondante
    $sql = 'SELECT du.id, du.lastname, du.firstname
            FROM dims_user du
            WHERE ';

    $precedent = 0;
    if($data_q[0] != ""){
        $sql .= 'du.lastname REGEXP "'.$data_q[0].'"';
        $precedent = 1;
    }
    if($data_q[1] != ""){
        if($precedent)
            $sql .= ' AND ';
        $sql .= 'du.firstname REGEXP "'.$data_q[1].'"';
        $precedent = 1;
    }

    if($precedent){$sql .= ' AND ';}
    $sql .= 'du.id != '.$_SESSION['dims']['userid'];

    //On affiche le résultat
    $res = $db->query($sql);

    //On compte le nombre de résultat
    $nb_res = mysql_num_rows($res);

echo '<div class="titre_address">R&eacute;sultats <span class="infos_address">('.$nb_res.')</span></div>';
echo '<div id="list_contact">';
echo '  <table width="100%" cellpadding="0" cellspacing="0">';

        while($tab_res = $db->fetchrow($res))
        {
            //On affiche les renseignements du contact et la photo si présente.
            echo '<tr class="line_contact">
                    <td style="background:url(\'./common/modules/sitecom/gfx/photo.png\');" width="40px;">&nbsp;</td>
                    <td style="padding-left:10px;">'.$tab_res['firstname'].' '.$tab_res['lastname'].'</td>';

            //On affiche les liens avec le contact
            echo '  <td class="lien_contact">';
            echo '      <input type="button" value="ajouter" onclick="javascript:add_address('.$_SESSION['dims']['currentheadingid'].','.$tab_res['id'].');" id="search_button" style="margin-right:5px;"/>';
            echo '  </td>
                </tr>';
        }

//On ferme les balises
echo '  </table>';
echo '</div>';
echo '<div class="res_footer">&nbsp;</div>';
}

//ACTION : rechercher les utilisateurs qui correspondent aux crit&egrave;res
if($action_sup == "adv_search"){
    //On recupere la requete "q"
    $q = urldecode(dims_load_securvalue("q",dims_const::_DIMS_CHAR_INPUT,true,true));
    $data_q = explode('%sep',$q);

    //On cree la requete correspondante
    $sql = 'SELECT du.id, du.lastname, du.firstname
            FROM dims_user du';
    if($data_q[2] != ""){
       $sql .=' INNER JOIN dims_user_address dua
            ON dua.id_users = du.id
            INNER JOIN dims_address da
            ON da.id = dua.id_address ';}
     if($data_q[3] != "all"){
       $sql .=' INNER JOIN dims_user_car duc
            ON duc.id_users = du.id
            INNER JOIN dims_car dc
            ON dc.id = duc.id_car ';}
     if($data_q[4] != "all"){
     $sql .=' INNER JOIN dims_user_interest dui
            ON dui.id_user = du.id
            INNER JOIN dims_interest di
            ON di.id = dui.id_int ';}
    $sql .= ' WHERE du.id != '.$_SESSION['dims']['userid'];
    $sql .= ' AND du.id NOT IN (SELECT id_contact FROM dims_mod_private_user_contact WHERE id_user = '.$_SESSION['dims']['userid'].')';

    if($data_q[0] != ""){
        $sql .= ' AND du.lastname REGEXP "'.$data_q[0].'"';
    }
    if($data_q[1] != ""){
        $sql .= ' AND du.firstname REGEXP "'.$data_q[1].'"';
    }
    if($data_q[2] != ""){
        $sql .= ' AND da.arrondisst="'.$data_q[2].'"';
    }
    if($data_q[3] != "all"){
        $sql .= ' AND dc.id="'.$data_q[3].'"';
    }
    if($data_q[4] != "all"){
        $sql .= ' AND di.id="'.$data_q[4].'"';
    }

    //On affiche le résultat
    $res = $db->query($sql);

    //On compte le nombre de résultat
    $nb_res = mysql_num_rows($res);

echo '<div id="onglets"><input type="button" value="Recherche rapide" id="search_button" onclick="javascript:switch_add('.$_SESSION['dims']['currentheadingid'].',1);"/> <input type="button" value="Retour" id="search_button" onclick="javascript:switch_add('.$_SESSION['dims']['currentheadingid'].',2);"/></div>';
echo '<p class="entete_search" style="margin-left:5px;">Recherche avanc&eacute;e</p>';
echo '<div id="infos_address">&nbsp;</div>';
echo '<div id="res_search">';
echo '  <div class="titre_address">R&eacute;sultats <span class="infos_address">('.$nb_res.')</span></div>';
echo '  <div id="list_contact">
            <table width="100%" cellpadding="0" cellspacing="0">';

        while($tab_res = $db->fetchrow($res))
        {
            //On affiche les renseignements du contact et la photo si présente.
            echo '<tr class="line_contact">
                    <td style="background:url(\'./common/modules/sitecom/gfx/photo.png\');" width="40px;">&nbsp;</td>
                    <td style="padding-left:10px;">'.$tab_res['firstname'].' '.$tab_res['lastname'].'</td>';

            //On affiche les liens avec le contact
            echo '  <td class="lien_contact">';
            echo '      <input type="button" value="ajouter" onclick="javascript:add_address('.$_SESSION['dims']['currentheadingid'].','.$tab_res['id'].');" id="search_button" style="margin-right:5px;"/>';
            echo '  </td>
                </tr>';
        }

        //On ferme les balises et on rajoute un div
        //Dans lequel on mettra le profil de l'utilisateur selectionné.
echo '      </table>
        </div>';
echo '</div>';
}
?>