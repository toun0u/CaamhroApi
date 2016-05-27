<?php
//dans le cas où ...
if(!isset($convmeta) || !isset($_SESSION['dims']['tiers_fields_mode'])) {
    //on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
    $sql = "SELECT      mf.*,mc.label as categlabel, mc.id as id_cat,
						mb.protected,mb.name as namefield,mb.label as titlefield
			FROM        dims_mod_business_meta_field as mf
			INNER JOIN	dims_mb_field as mb
			ON			mb.id=mf.id_mbfield
			RIGHT JOIN  dims_mod_business_meta_categ as mc
			ON          mf.id_metacateg=mc.id
			WHERE       mf.id_object = :idobject
			AND			mc.admin=1
			AND			mf.used=1
			ORDER BY    mc.position, mf.position
			";
    $rs_fields=$db->query($sql, array(
        ':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
    ));

    $rubgen=array();
    $convmeta = array();

    while ($fields = $db->fetchrow($rs_fields)) {
        if (!isset($rubgen[$fields['id_cat']]))  {
            $rubgen[$fields['id_cat']]=array();
            $rubgen[$fields['id_cat']]['id']=$fields['id_cat'];
            $rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
            if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
        }

        // on ajoute maintenant les champs dans la liste
        $fields['use']=0;// par defaut non utilise
        $fields['enabled']=array();
        if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;

        $_SESSION['dims']['tiers_fields_mode'][$fields['id']]=$fields['mode'];

        // enregistrement de la conversion
        $convmeta[$fields['namefield']]=$fields['id'];
    }
}


//on test si on est deja passe dans le formulaire (dans ce cas on ne recharge pas les infos en session)
$form = dims_load_securvalue("form", dims_const::_DIMS_CHAR_INPUT, true, true, true);
if($form != 'form') {
    $_SESSION['dims']['DB_CONTACT'] = array();
    $_SESSION['dims']['IMPORT_CONTACT']=array();
    $_SESSION['dims']['IMPORT_NEW_LINK']=array();
    $_SESSION['dims']['IMPORT_LINK_ENT']=array();
    $_SESSION['dims']['IMPORT_NEW_CT']=array();
    $_SESSION['dims']['IMPORT_KNOWN_CONTACTS']=array();
    $_SESSION['dims']['IMPORT_NEW_EMAIL'] = array();
    unset($_SESSION['dims']['import_current_user_id']);

    //on met en session la liste ces contacts contenus dans la base pour comparaison
    $sql = "SELECT id,intitule FROM dims_mod_business_tiers WHERE 1";
    $res = $db->query($sql);
    if($db->numrows()>0){
        while($data = $db->fetchrow($res))
            $_SESSION['dims']['DB_CONTACT'][$data['id']] = $data;
    }

    //on met en session la liste des contacts issus de la table d'import
    $sql_vsim = "   SELECT         *
                    FROM           dims_mod_business_tiers_import
                    WHERE          id_user = :iduser
                    AND            id_workspace = :idworkspace ";

    $res_vsim = $db->query($sql_vsim, array(
        ':iduser'       => $_SESSION['dims']['userid'],
        ':idworkspace'  => $_SESSION['dims']['workspaceid']
    ));
    $cpt_sim = 0;
    while($tab_ct_imp = $db->fetchrow($res_vim)) {
        $_SESSION['dims']['IMPORT_CONTACT'][$tab_ct_imp['id']] = $tab_ct_imp;
        $cpt_sim++;
    }
    $_SESSION['dims']['import_count_contact_similar'] = $cpt_sim;


    //on recherche les similarite
    $lev_nom = 0;
    $lev_pre = 0;
    $coef_nom = 0;
    $coef_pre = 0;
    $coef_tot = 0;

    $_SESSION['dims']['import_contact_similar'] = array();

    foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $id_imp => $tab_contact_new){
        foreach($_SESSION['dims']['DB_CONTACT'] AS $tab_contact){

            $lev_nom = levenshtein(strtoupper($tab_contact_new['intitule']), strtoupper($tab_contact['intitule']));
            $coef_nom = $lev_nom - (ceil(strlen($tab_contact_new['intitule'])/4));

            $coef_tot = $coef_nom;

            if($coef_tot < 5) {
                    $_SESSION['dims']['import_contact_similar'][$id_imp][] = $tab_contact['id'];
            }
        }
    }
    //dims_print_r($_SESSION['dims']['import_contact_similar']);
    //break;
    $_SESSION['dims']['import_contact_similar_count'] = 1;

    //mise en session des infos necessaires pour le case 5
    if($dims->isAdmin() || $dims->isManager()) {
        $user_imp = dims_load_securvalue("user_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
        if(!empty($user_imp)) $_SESSION['dims']['import_id_user'] = $user_imp;
        else $_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];
    }else{
        $_SESSION['dims']['import_id_user'] = $_SESSION['dims']['userid'];
    }
}

//ATTENTION
//contact_id n'est pas un id de contact mais l'id de la table d'import,
//donc id_user_to_change ne doit pas être pris comme key ds les sessions utilisees par le case 5 sauf dans IMPORT_CONTACT
//ATTENTION
$id_user_to_change = dims_load_securvalue("contact_id", dims_const::_DIMS_CHAR_INPUT, false, true, true);
$id_user_similar = dims_load_securvalue("similar_contact", dims_const::_DIMS_CHAR_INPUT, false, true, true);
//Si on envois un id d'utilisateur qu'on a traité
if($id_user_to_change != ""){
    //On cherche si cette utilisateur existe bien dans la table d'import
    if(isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]) && count($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change])>0){

            //Ce n'est pas un ajout l'entreprise existe deja la personne l'a reconnue dans la liste
            if($id_user_similar != 0 && $id_user_similar != -1 && $id_user_similar != -2 ){

                //On regarde si l'email dans la base est rempli
                if($_SESSION['dims']['DB_CONTACT'][$id_user_similar]['email'] == ""){
                    $_SESSION['dims']['IMPORT_NEW_EMAIL'][$id_user_similar] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email'];
                }

                //Ce contact a une entreprise qui porte exactement le meme nom dans le site
                //if($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] != 0){
                $sql = "SELECT count(id) AS exist_link
                        FROM dims_mod_business_tiers_contact
                        WHERE id_tiers = :idtiers
                        AND id_contact =  :idcontact ";
                    $res = $db->query($sql, array(
                        ':idtiers'      => $id_user_similar,
                        ':idcontact'    => $_SESSION['dims']['user']['id_contact']
                    ));
                    $data = $db->fetchrow($res);
                    if($data['exist_link'] == 0){
                        //echo "On attache la personne a cette entreprise.<br/>";
                        $_SESSION['dims']['IMPORT_NEW_LINK'][$id_user_similar] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change];
                        //$_SESSION['dims']['stats_import']['ct_link'][$contact_import->fields['exist_ent']][] = $contact_import->fields;
                    }
                //}

                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                //on supprime le contact de la table d'import
                $supp_imp = new tiers_import();
                $supp_imp->open($id_user_to_change);
                $supp_imp->delete();

                //On cherche le prochain import a traitï¿½
                //dims_print_r($_SESSION['dims']['import_contact_similar']);
                $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                if(count($tab_contact_new)>0){
                    $user_id = array_keys($tab_contact_new);
                    //dims_print_r($user_id);
                    if(count($user_id) > 0){
                        $user_id = $user_id[0];
                    }
                    //On met en session pour la suite
                    $_SESSION['dims']['import_current_user_id'] = $user_id;
                    $_SESSION['dims']['import_current_similar'] = $tab_contact_new;
                }

                $_SESSION['dims']['import_contact_similar_count']++;
            }elseif ($id_user_similar == 0){  //C'est un ajout de contact
                //echo "On ne connait pas le contact.<br/>";

                $_SESSION['dims']['IMPORT_NEW_CT'][$id_user_to_change] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change];


                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                //on supprime le contact de la table d'import
                $supp_imp = new tiers_import();
                $supp_imp->open($id_user_to_change);
                $supp_imp->delete();

                //dims_print_r($_SESSION['dims']['import_contact_similar']);
                $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                $user_id = array_keys($tab_contact_new);
                if(count($user_id) > 0){
                    $user_id = $user_id[0];
                }

                $_SESSION['dims']['import_current_user_id'] = $user_id;
                $_SESSION['dims']['import_current_similar'] = $tab_contact_new;

                $_SESSION['dims']['import_contact_similar_count']++;

            }elseif ($id_user_similar == -1){ //on passe juste au suivant
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);

                $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                $user_id = array_keys($tab_contact_new);
                if(count($user_id) > 0){
                    $user_id = $user_id[0];
                }
//dims_print_r($_SESSION['dims']['import_contact_similar']);
                $_SESSION['dims']['import_current_user_id'] = $user_id;
                $_SESSION['dims']['import_current_similar'] = $tab_contact_new;

                $_SESSION['dims']['import_contact_similar_count']++;
            }
            elseif ($id_user_similar == -2) { //on supprime tout
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
                //on supprime le contact de la table d'import
                $supp_imp = new tiers_import();
                $supp_imp->open($id_user_to_change);
                $supp_imp->delete();

                $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                $user_id = array_keys($tab_contact_new);
                if(count($user_id) > 0){
                    $user_id = $user_id[0];
                }

                $_SESSION['dims']['import_current_user_id'] = $user_id;
                $_SESSION['dims']['import_current_similar'] = $tab_contact_new;

                $_SESSION['dims']['import_contact_similar_count']++;
            }

    }else{//L'utilisateur n'existe pas dans la table des imports
        //On reprend le contact prï¿½cï¿½dent
        //echo "L'utilisateur n'existe pas. On reprend l'import a cette endroit<br/>";
        $user_id = $_SESSION['dims']['import_current_user_id'];
        //echo "L'utilisateur n'existe pas. On reprend l'import a cette endroit $user_id<br/>";
    }
}else{ //Aucun id d'utilisateur n'a été envoyé, surement le premier appel de la page
    //Si la session qui défini le contact en cours de traitement
    //echo "Aucun id d'utilisateur n'a été envoyé.<br/>";
    if(!isset($_SESSION['dims']['import_current_user_id'])){
        //Dans ce cas on va chercher le premier import a traiter
        //dims_print_r($_SESSION['dims']['import_contact_similar']);
        $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
        $user_id = array_keys($tab_contact_new);
        if(count($user_id) > 0){
            $user_id = $user_id[0];
        }
        //On met en session pour la suite
        $_SESSION['dims']['import_current_user_id'] = $user_id;
        $_SESSION['dims']['import_current_similar'] = $tab_contact_new;
    }else{//Sinon on prend celui en cours
        $user_id = $_SESSION['dims']['import_current_user_id'];
    }
}

//if (isset($user_id)) {
    //Si on a le compte de contact a traiter, c'est qu'on a fini. On passe a l'etape 4
    if($_SESSION['dims']['import_count_contact_similar'] >= $_SESSION['dims']['import_contact_similar_count']){
        $content_contact_import = "<p style='font-weight:bold;text-align:center;font-size:14px;'>".$_DIMS['cste']['_DIMS_LABEL_COMPANY']." ".$_SESSION['dims']['import_contact_similar_count']."/".$_SESSION['dims']['import_count_contact_similar']."</p><br/>";

        $content_contact_import .= '<form action="./admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT_ENTREPRISES.'&part='._BUSINESS_TAB_IMPORT_ENTREPRISES.'&op=3&form=form" method="post" id="valider_similitude" name="valider_similitude">';
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("contact_id", $user_id);
        $token->field("similar_contact");
        $tokenHTML = $token->generate();
        $content_contact_import .= $tokenHTML;
        $content_contact_import .= '<input type="hidden" name="contact_id" value="'.$user_id.'"/>
                                    <table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
                                        <tr class="trl1" style="font-size:12px;">
                                                <td style="width:10%;">&nbsp;</td>
                                                <td style="width: 5%;">&nbsp;</td>
                                                <td style="width: 50%;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
                                                <td style="width: 35%;">&nbsp;</td>
                                        </tr>';

    if (isset($_SESSION['dims']['import_current_similar'][$user_id])) {
                $content_contact_import .= '    <tr style="background:#C2C2C2;border-bottom:1px solid #738CAD;">
                                                        <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_TAB_NEW_COMPANY'].'</td>
                                                        <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
                                                        <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['intitule']).'</td>
                                                        <td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
                                                </tr>';
				$intitule=strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['intitule']);
				$okname=false;
				$okfirstname=false;

                $rowspan = count($_SESSION['dims']['import_current_similar'][$user_id]);
                foreach($_SESSION['dims']['import_current_similar'][$user_id] AS $similar_contact){
                    //$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
					if ($intitule==strtoupper($_SESSION['dims']['DB_CONTACT'][$similar_contact]['intitule'])) {
						$okname=true;
						$samname="<img src=\"./common/modules/system/img/ico_point_green.gif\" alt=\"\">";
					}
					else $samname="<img src=\"./common/modules/system/img/ico_point_red.gif\" alt=\"\">";

                    $content_contact_import .= '<tr style="background-color:#C2D6EB;">';
                    if($rowspan == 1) $content_contact_import .= '<td>'.$_DIMS['cste']['_IMPORT_TAB_SIMILAR_TIER_SINGLE'].'</td>';
                    if($rowspan > 1){ $content_contact_import .= '<td rowspan="'.$rowspan.'">'.$_DIMS['cste']['_IMPORT_TAB_SIMILAR_TIER'].'</td>';$rowspan = 0;}

					if ($okfirstname && $okname) $linkoption='onclick="javascript:document.valider_similitude.submit();" ';
					else $linkoption="";

                    $content_contact_import .= '    <td><input type="radio" '.$linkoption.'  name="similar_contact" value="'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['id'].'"/></td>
                                                    <td>'.$samname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['intitule'].'</td>
                                                    <td>'.$_DIMS['cste']['_IMPORT_MY_TIER_IS_SIMILAR'].'</td>
                                            </tr>';
                }
			}
        $content_contact_import .= '<tr>
                                            <td style="border-top:1px solid #738CAD;">&nbsp;</td>
                                            <td style="border-top:1px solid #738CAD;"><input type="radio" name="similar_contact" value="0" onclick="javascript:document.valider_similitude.submit();"/></td>
                                            <td colspan="4" style="border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_NEW_SIMILAR_TIER'].'</td>
                                    </tr>
                                    <tr>
                                            <td>&nbsp;</td>';

        if ($okname) $content_contact_import .='<td><input type="radio" name="similar_contact" value="-1"/></td>';
        else $content_contact_import .='<td><input type="radio" name="similar_contact" value="-1" checked="checked"/></td>';

        $content_contact_import .='<td colspan="4">'.$_DIMS['cste']['_IMPORT_NEXT_SIMILAR_TIER'].'.</td>
                                            </tr>';
        $content_contact_import .= '<tr><td>&nbsp;</td><td><input type="radio" name="similar_contact" value="-2"/></td>
                                    <td colspan="4">'.$_DIMS['cste']['_IMPORT_SUPPR_SIMILAR_CONTACT'].'</td></tr>';
        $content_contact_import .= '</table><br/><br/>';
        $content_contact_import .= '<div style="text-align:right;">
                                        '.dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'], "./common/img/publish.png", "dims_getelem('valider_similitude').submit();").'
                                    </div>
                                    </form>';
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
                                            <td>'.dims_create_button_nofloat($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_ENTREPRISES."&part="._BUSINESS_TAB_IMPORT_ENTREPRISES."&op=5');").'
                                            </td>
                                        </tr>
                                    </table>';
    }

//}


?>
