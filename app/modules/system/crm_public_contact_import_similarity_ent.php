<?php

//on test si on est deja passe dans le formulaire (dans ce cas on ne recharge pas les infos en session)
$form = dims_load_securvalue("form", dims_const::_DIMS_CHAR_INPUT, true, true, true);
if($form != 'form') {
    /*$_SESSION['dims']['DB_CONTACT'] = array();
    $_SESSION['dims']['IMPORT_CONTACT']=array();
    $_SESSION['dims']['IMPORT_NEW_LINK']=array();
    $_SESSION['dims']['IMPORT_LINK_ENT']=array();
    $_SESSION['dims']['IMPORT_NEW_CT']=array();
    $_SESSION['dims']['IMPORT_KNOWN_CONTACTS']=array();
    $_SESSION['dims']['IMPORT_NEW_EMAIL'] = array();
    unset($_SESSION['dims']['import_current_user_id']);*/

    $_SESSION['dims']['IMPORT_LINK_ENT_SIM'] = array();
    $_SESSION['dims']['IMPORT_NEW_ENT_SIM'] = array();

    //mise en session des infos necessaires pour le case 5
    if($dims->isAdmin() || $dims->isManager()) {
        $user_imp = dims_load_securvalue("user_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
        if(!empty($user_imp)) $_SESSION['dims']['import_id_user'] = $user_imp;
        else $_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];
    }else{
        $_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];
    }

    $ent_import=dims_load_securvalue("ent_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
    if ($ent_import>0) $_SESSION['dims']['import_id_ent']=$ent_import;
    else $_SESSION['dims']['import_id_ent']=0;

    //Traitement des similarite des entreprises
    if(!isset($_SESSION['dims']['IMPORT_ENT_SIMILARITY'])) {
        $sql_s =    "SELECT id, id_contact, ent_intitule, id_ent_similar, intitule_ent_similar
                    FROM dims_mod_business_contact_import_ent_similar
                    WHERE id_workspace = :idworkspace
                    AND id_user = :iduser
                    ORDER BY id_contact, id_ent_similar";
        $res_s = $db->query($sql_s, array(
            ':idworkspace'  => $_SESSION['dims']['workspaceid'],
            ':iduser'       => $_SESSION['dims']['userid']
        ));
        if($db->numrows($res_s) > 0) {
            $_SESSION['dims']['IMPORT_ENT_SIMILARITY'] = array();
            while($tab_s = $db->fetchrow($res_s)) {
                $_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$tab_s['id_contact']][$tab_s['id_ent_similar']] = $tab_s;
            }
        }
    }

    $_SESSION['dims']['import_ent_total_similar'] = count($_SESSION['dims']['IMPORT_ENT_SIMILARITY']); //nombre de cas
    $_SESSION['dims']['import_ent_similar_count'] = 1; //compteur courant
}

$id_contact = dims_load_securvalue("id_contact", dims_const::_DIMS_CHAR_INPUT, false, true, true);
$id_ent_sim = dims_load_securvalue("id_ent_sim", dims_const::_DIMS_CHAR_INPUT, false, true, true);
//Si on envois un id d'utilisateur qu'on a traité
if($id_contact != ""){
    switch($id_ent_sim) {
        case '0':
            //on est dans le cas ou on cree une entreprise
            $_SESSION['dims']['IMPORT_NEW_ENT_SIM'][$id_contact] = $_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$id_contact];

            //on supprime les donnes de la table et de la session
            $sql_ds =   "DELETE FROM dims_mod_business_contact_import_ent_similar
                        WHERE id_workspace = :idworkspace
                        AND id_user = :iduser
                        AND id_contact = :idcontact ";
            $db->query($sql_ds, array(
                ':idworkspace'  => $_SESSION['dims']['workspaceid'],
                ':iduser'       => $_SESSION['dims']['userid'],
                ':idcontact'    => $id_contact
            ));

            unset($_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$id_contact]);

            //on incremente le compteur
            $_SESSION['dims']['import_ent_similar_count']++;
            break;
        case '-1' :
            //on est dans le cas ou on passe a la ligne suivante
            unset($_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$id_contact]);

            //on incremente le compteur
            $_SESSION['dims']['import_ent_similar_count']++;

            break;
        case '-2' :
            //on est dans le cas ou on supprime la ligne cournante
            //on supprime les donnes de la table et de la session
            $sql_ds =   "DELETE FROM dims_mod_business_contact_import_ent_similar
                        WHERE id_workspace = :idworkspace
                        AND id_user = :iduser
                        AND id_contact = :idcontact ";
            $db->query($sql_ds, array(
                ':idworkspace'  => $_SESSION['dims']['workspaceid'],
                ':iduser'       => $_SESSION['dims']['userid'],
                ':idcontact'    => $id_contact
            ));

            unset($_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$id_contact]);

            //on incremente le compteur
            $_SESSION['dims']['import_ent_similar_count']++;
            break;

        default :
            //on est dans le cas ou on lie a une entreprise presente dans la base
            //on verifie d'abord que le lien n'existe pas
            $sql = "SELECT count(id) AS exist_link
                    FROM dims_mod_business_tiers_contact
                    WHERE id_tiers = :idtiers
                    AND id_contact = :idcontact ";
            $res = $db->query($sql, array(
                ':idtiers'      => $id_ent_sim,
                ':idcontact'    => $id_contact
            ));
            $data = $db->fetchrow($res);
            if($data['exist_link'] == 0){
                //echo "On attache la personne a cette entreprise.<br/>";
                //voir IMPORT_NEW_LINK dans outlook_switch !!
                $_SESSION['dims']['IMPORT_LINK_ENT_SIM'][$id_contact]['ent_to_link'] = $id_ent_sim;
            }

            //on supprime les donnes de la table et de la session
            $sql_ds =   "DELETE FROM dims_mod_business_contact_import_ent_similar
                        WHERE id_workspace = :idworkspace
                        AND id_user = :iduser
                        AND id_contact = :idcontact ";
            $db->query($sql_ds, array(
                ':idworkspace'  => $_SESSION['dims']['workspaceid'],
                ':iduser'       => $_SESSION['dims']['userid'],
                ':idcontact'    => $id_contact
            ));

            unset($_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$id_contact]);

            //on incremente le compteur
            $_SESSION['dims']['import_ent_similar_count']++;
            break;
    }
}else{ //Aucun id d'utilisateur n'a été envoyé, surement le premier appel de la page
    //Si la session qui défini le contact en cours de traitement
    //echo "Aucun id d'utilisateur n'a été envoyé.<br/>";
    //if(!isset($_SESSION['dims']['import_current_user_id'])){
    //    //Dans ce cas on va chercher le premier import a traiter
    //    //dims_print_r($_SESSION['dims']['import_contact_similar']);
    //    $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
    //    $user_id = array_keys($tab_contact_new);
    //    if(count($user_id) > 0){
    //        $user_id = $user_id[0];
    //    }
    //    //On met en session pour la suite
    //    $_SESSION['dims']['import_current_user_id'] = $user_id;
    //    $_SESSION['dims']['import_current_similar'] = $tab_contact_new;
    //}else{//Sinon on prend celui en cours
    //    $user_id = $_SESSION['dims']['import_current_user_id'];
    //}
}

//if (isset($user_id)) {
    //Si on a le compte de contact a traiter, c'est qu'on a fini. On passe a l'etape 4
    if($_SESSION['dims']['import_ent_total_similar'] >= $_SESSION['dims']['import_ent_similar_count']){
        $content_contact_import = "<p style='font-weight:bold;text-align:center;font-size:14px;'>".$_DIMS['cste']['_DIMS_LABEL_CONTACT']." ".$_SESSION['dims']['import_contact_similar_count']."/".$_SESSION['dims']['import_count_contact_similar']."</p><br/>";

        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $content_contact_import .= '<form action="./admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_OUTLOOK.'&part='._BUSINESS_TAB_IMPORT_OUTLOOK.'&op=3&form=form" method="post" id="valider_similitude" name="valider_similitude">
                                    <input type="hidden" name="contact_id" value="'.$user_id.'"/>
                                    <table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px">
                                        <tr class="trl1" style="font-size:12px;">
                                                <td style="width:10%;">&nbsp;</td>
                                                <td style="width: 5%;">&nbsp;</td>
                                                <td style="width: 20%;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
                                                <td style="width: 20%;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
                                                <td style="width: 20%;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
                                                <td style="width: 25%;">&nbsp;</td>
                                        </tr>';
        $token->field("contact_id", $user_id);

    if (isset($_SESSION['dims']['import_current_similar'][$user_id])) {
        $content_contact_import .= '    <tr style="background:#C2C2C2;border-bottom:1px solid #738CAD;">
                                                <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_TAB_NEW_CONTACT'].'</td>
                                                <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
                                                <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['lastname']).'</td>
                                                <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_SESSION['dims']['IMPORT_CONTACT'][$user_id]['firstname'].'</td>
                                                <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_SESSION['dims']['IMPORT_CONTACT'][$user_id]['email'].'</td>
                                                <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
                                        </tr>';
        $lastnamecompare=strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['lastname']);
        $firstnamecompare=strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['firstname']);
        $okname=false;
        $okfirstname=false;

        $rowspan = count($_SESSION['dims']['import_current_similar'][$user_id]);
        foreach($_SESSION['dims']['import_current_similar'][$user_id] AS $similar_contact){
            //$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
            if ($lastnamecompare==strtoupper($_SESSION['dims']['DB_CONTACT'][$similar_contact]['lastname'])) {
                $okname=true;
                $samname="<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
            }
            else $samname="<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";

            if ($firstnamecompare==strtoupper($_SESSION['dims']['DB_CONTACT'][$similar_contact]['firstname'])) {
                $okfirstname=true;
                $samfirstname="<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
            }
            else $samfirstname="<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";

            $content_contact_import .= '<tr style="background-color:#C2D6EB;">';
            if($rowspan == 1) $content_contact_import .= '<td>'.$_DIMS['cste']['_IMPORT_TAB_SIMILAR_CONTACT_SINGLE'].'</td>';
            if($rowspan > 1){ $content_contact_import .= '<td rowspan="'.$rowspan.'">'.$_DIMS['cste']['_IMPORT_TAB_SIMILAR_CONTACT'].'</td>';$rowspan = 0;}

            if ($okfirstname && $okname) $linkoption='onclick="javascript:document.valider_similitude.submit();" ';
            else $linkoption="";

            $content_contact_import .= '    <td><input type="radio" '.$linkoption.'  name="similar_contact" value="'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['id'].'"/></td>
                                            <td>'.$samname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['lastname'].'</td>
                                            <td>'.$samfirstname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['firstname'].'</td>
                                            <td>'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['email'].'</td>
                                            <td>'.$_DIMS['cste']['_DIMS_IMPORT_CT_SAME'].'</td>
                                    </tr>';
            $token->field("similar_contact");
        }
    }
        $content_contact_import .= '<tr>
                                            <td style="border-top:1px solid #738CAD;">&nbsp;</td>
                                            <td style="border-top:1px solid #738CAD;"><input type="radio" name="similar_contact" value="0" onclick="javascript:document.valider_similitude.submit();"/></td>
                                            <td colspan="4" style="border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_NEW_SIMILAR_CONTACT'].'</td>
                                    </tr>
                                    <tr>
                                            <td>&nbsp;</td>';
        $token->field("similar_contact");

        if ($okfirstname && $okname) $content_contact_import .='<td><input type="radio" name="similar_contact" value="-1"/></td>';
        else $content_contact_import .='<td><input type="radio" name="similar_contact" value="-1" checked="checked"/></td>';

        $content_contact_import .='<td colspan="4">'.$_DIMS['cste']['_IMPORT_NEXT_SIMILAR_CONTACT'].'</td>
                                    </tr>';
        $content_contact_import .= '<tr><td>&nbsp;</td><td><input type="radio" name="similar_contact" value="-2"/></td>
                                    <td colspan="4">'.$_DIMS['cste']['_IMPORT_SUPPR_SIMILAR_CONTACT'].'</td></tr>';
        $content_contact_import .= '</table><br/><br/>';
        $content_contact_import .= '<div style="text-align:center;">
                                        '.dims_create_button($_DIMS['cste']['_DIMS_VALID'], "./common/img/publish.png", "dims_getelem('valider_similitude').submit();").'
                                    </div>';
        $tokenHTML = $token->generate();
        $content_contact_import .= $tokenHTML;
        $content_contact_import .= '</form>';
    }

    else {
//        echo "contact_similar";
//		dims_print_r($_SESSION['dims']['import_contact_similar']);
//
//		echo "new_link";
//		dims_print_r($_SESSION['dims']['IMPORT_NEW_LINK']);
//
//		echo "connus (normalement vide)";
//		dims_print_r($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']);
//
//		echo "new_email";
//		dims_print_r($_SESSION['dims']['IMPORT_NEW_EMAIL']);
//
//		echo "contact";
//		dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);
//
//		echo "new contact";
//		dims_print_r($_SESSION['dims']['IMPORT_NEW_CT']);

        $content_contact_import = '<table>
                                        <tr>
                                            <td>Les imports sont termin&eacute;s
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=5');").'
                                            </td>
                                        </tr>
                                    </table>';
    }

//}


?>
