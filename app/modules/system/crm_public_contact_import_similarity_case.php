<?php

//dans le cas où ...
if(!isset($convmeta) || !isset($_SESSION['dims']['contact_fields_mode'])) {
    //on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
    $sql = 	"
                SELECT      mf.*,mc.label as categlabel, mc.id as id_cat,
                            mb.protected,mb.name as namefield,mb.label as titlefield
                FROM        dims_mod_business_meta_field as mf
                INNER JOIN	dims_mb_field as mb
                ON			mb.id=mf.id_mbfield
                RIGHT JOIN  dims_mod_business_meta_categ as mc
                ON          mf.id_metacateg=mc.id
                WHERE         mf.id_object = :idobject
                AND			mc.admin=1
                AND			mf.used=1
                ORDER BY    mc.position, mf.position
                ";
    $rs_fields=$db->query($sql, array(
        ':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
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

        $_SESSION['dims']['contact_fields_mode'][$fields['id']]=$fields['mode'];

        // enregistrement de la conversion
        $convmeta[$fields['namefield']]=$fields['id'];
    }
}


//on test si on est deja passe dans le formulaire (dans ce cas on ne recharge pas les infos en session)
$form = dims_load_securvalue("form", dims_const::_DIMS_CHAR_INPUT, true, true, true);
$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true);

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
    $sql = "SELECT id,firstname, lastname, email FROM dims_mod_business_contact WHERE 1";
    $res = $db->query($sql);
    if($db->numrows()>0){
        while($data = $db->fetchrow($res))
            $_SESSION['dims']['DB_CONTACT'][$data['id']] = $data;
    }

    //on met en session la liste des contacts issus de la table d'import
    if($from == 'other_user') {
		$sql_vsim = "   SELECT        ci.*,u.id_contact as id_ct_from
                    FROM           dims_mod_business_contact_import as ci
					INNER JOIN		dims_user as u
					ON				u.id=ci.id_user_create
                    WHERE          id_importer = :iduser
                    AND            id_importer != id_user_create";
	}
	else {
		$sql_vsim = "   SELECT     ci.*,u.id_contact as id_ct_from
                    FROM           dims_mod_business_contact_import as ci
					INNER JOIN		dims_user as u
					ON				u.id=ci.id_user_create
                    WHERE          id_user_create = :iduser
                    AND            id_workspace = :idworkspace ";
	}

    $res_vsim = $db->query($sql_vsim, array(
        ':iduser'       => $_SESSION['dims']['userid'],
        ':idworkspace'  => $_SESSION['dims']['workspaceid']
    ));
    $cpt_sim = 0;

    while($tab_ct_imp = $db->fetchrow($res_vsim)) {
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
			if (trim($tab_contact['lastname'])!='' && trim($tab_contact['firstname'])!='') {
				$lev_nom = levenshtein(strtoupper($tab_contact_new['lastname']), strtoupper($tab_contact['lastname']));
				$coef_nom = $lev_nom - (ceil(strlen($tab_contact_new['lastname'])/4));

				$lev_pre = levenshtein(strtoupper($tab_contact_new['firstname']), strtoupper($tab_contact['firstname']));
				$coef_pre = $lev_pre - (ceil(strlen($tab_contact_new['firstname'])/4));

				$coef_tot = $coef_nom + $coef_pre;

				if($coef_nom<=1 && $coef_tot < 2) {
					$_SESSION['dims']['import_contact_similar'][$id_imp][] = $tab_contact['id'];
				}
			}
        }
		unset($_SESSION['dims']['ALL_ENTS']);
		//on va chercher des similitudes eventuelles sur les entreprises
		if(!isset($_SESSION['dims']['ALL_ENTS'])) {
			//si la session n'existe pas, on y place toutes les entreprises en vu de la comparaison
			$sql_e = "SELECT id, intitule FROM dims_mod_business_tiers ORDER BY intitule";
			$res_e = $db->query($sql_e);
			while($tab_e = $db->fetchrow($res_e)) {
				$_SESSION['dims']['ALL_ENTS'][$tab_e['id']] = $tab_e;
			}
		}
		//on compare la valeur courante avec les entreprises en session
		foreach($_SESSION['dims']['ALL_ENTS'] as $id_entc => $tab_ent) {
			if ($tab_ent['intitule']!="") {

				$lev_nom = levenshtein(strtoupper($tab_contact_new['company']), strtoupper($tab_ent['intitule']));
				$coef_nom = $lev_nom - (ceil(strlen($tab_contact_new['company'])/4));

				$coef_tot = $coef_nom;

				if($coef_tot < 2) {

					//on stock les entreprises similaires en base
					$ent_sim = new tiers_similar();
					$ent_sim->init_description();
					if ($id_imp>0) {
						$res_tmp=$db->query("SELECT id
                                            FROM dims_mod_business_contact_import_ent_similar
                                            WHERE id_contact= :idcontact
                                            AND id_ent_similar= :identsimilar
                                            AND id_user= :iduser
                                            AND id_workspace= :idworkspace ", array(
                                ':idcontact'    => $id_imp,
                                ':identsimilar' => $id_entc,
                                ':iduser'       => $_SESSION['dims']['userid'],
                                ':idworkspace'  => $_SESSION['dims']['workspaceid']
                        ));
						if ($db->numrows($res_tmp)==0) {
							$ent_sim->fields['id_contact'] = $id_imp;
							$ent_sim->fields['ent_intitule'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_imp]['company'];
							$ent_sim->fields['id_ent_similar'] = $id_entc;
							$ent_sim->fields['intitule_ent_similar'] = $tab_ent['intitule'];
							$ent_sim->fields['id_user'] = $tab_contact_new['id_user_create'];
							$ent_sim->fields['id_workspace'] = $tab_contact_new['id_workspace'];
							$ent_sim->save();
						}
					}
				}

				if ($lev_nom==0) {
					$_SESSION['dims']['IMPORT_CONTACT'][$id_imp]['exist_ent']=$id_entc;
				}
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

    $ent_import=dims_load_securvalue("ent_import", dims_const::_DIMS_NUM_INPUT, true, true, true);
    if ($ent_import>0) $_SESSION['dims']['import_id_ent']=$ent_import;
    else $_SESSION['dims']['import_id_ent']=0;

    //Traitement des similarite des entreprises
    $sql_s =    "SELECT id, id_contact, ent_intitule, id_ent_similar, intitule_ent_similar
                FROM dims_mod_business_contact_import_ent_similar
                WHERE id_workspace = :idworkspace
                AND id_user = :iduser
                ORDER BY id_contact, id_ent_similar";
    $res_s = $db->query($sql_s, array(
        ':iduser'       => $_SESSION['dims']['userid'],
        ':idworkspace'  => $_SESSION['dims']['workspaceid']
    ));
    if($db->numrows($res_s) > 0) {
        $_SESSION['dims']['IMPORT_ENT_SIMILARITY'] = array();
        while($tab_s = $db->fetchrow($res_s)) {
            $_SESSION['dims']['IMPORT_ENT_SIMILARITY'][$tab_s['id_contact']][$tab_s['id']] = $tab_s;
        }
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

            //Ce n'est pas un ajout l'utilisateur existe deja la personne la reconnue dans la liste
            if($id_user_similar != 0 && $id_user_similar != -1 && $id_user_similar != -2 ){

                ///////////////////////////////////
                //   C'est une MODIF de contact  //
                ///////////////////////////////////

                //on regarde si on a déjà un layer pour ce contact dans cet espace de travail
                $sql_l = "SELECT * FROM dims_mod_business_contact_layer WHERE id = :id AND type_layer = 1 AND id_layer = :idlayer ";
                $res_l = $db->query($sql_l, array(
                    ':id'       => $id_user_similar,
                    ':idlayer'  => $_SESSION['dims']['workspaceid']
                ));
                if($db->numrows($res_l) > 0) {
                    //on va mettre à jour le layer avec les infos importees
                    $sql_maj = "UPDATE dims_mod_business_contact_layer SET ";
                    $param = array();
                    $maj = 0;

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email'] != '') {
                            $sql_maj .= "email = :email ";
                            $maj = 1;
                            $param[':email'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['address'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "address = :address ";
                            $maj = 1;
                            $param[':address'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['address'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['city'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "city = :city ";
                            $maj = 1;
                            $param[':city'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['city'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['cp'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "postalcode = :postalcode ";
                            $maj = 1;
                            $param[':postalcode'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['cp'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['country'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "country = :country ";
                            $maj = 1;
                            $param[':country'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['country'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['fax'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "fax = :fax ";
                            $maj = 1;
                            $param[':fax'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['fax'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "phone = :phone ";
                            $maj = 1;
                            $param[':phone'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['mobile'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "mobile = :mobile ";
                            $maj = 1;
                            $param[':mobile'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['mobile'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email2']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['email2']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email2'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "email2 = :email2 ";
                            $maj = 1;
                            $param[':email2'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email2'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email3']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['email3']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email3'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "email3 = :email3 ";
                            $maj = 1;
                            $param[':email3'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email3'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone2'] != '') {
                            if($maj == 1) $sql_maj .= ", ";
                            $sql_maj .= "phone2 = :phone2 ";
                            $maj = 1;
                            $param[':phone2'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone2'];
                        }
                    }

                    if($maj == 1) {
                        $sql_maj .= ", id_user = :iduser "; //pour obtenir l'id user qui modifie la fiche
                        $param[':iduser'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_user_create'];
                    }

                    $sql_maj .= " WHERE id = :id AND type_layer = 1 AND id_layer = :idlayer ";
                        $param[':id'] = $id_user_similar;
                        $param[':idlayer'] = $_SESSION['dims']['workspaceid'];

                    if($maj == 1) {
                        $db->query($sql_maj, $param);
                    }
                }
                else {
                    //on va créer un nouveau layer
                    $ct_layer = new contact_layer();
					$ct_layer->init_description();

					$ct_layer->fields['id'] = $id_user_similar;
                    $ct_layer->fields['type_layer'] = 1;
					$ct_layer->fields['id_layer'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_workspace'];
					$ct_layer->fields['id_user']=$_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_user_create'];
                    $ct_layer->fields['id_workspace']=$_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_workspace'];
                    $ct_layer->fields['id_module']= $_SESSION['dims']['moduleid'];


                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email'] != '') {
                            $ct_layer->fields['email'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['address'] != '') {
                            $ct_layer->fields['address'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['address'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['city'] != '') {
                            $ct_layer->fields['city'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['city'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['cp'] != '') {
                            $ct_layer->fields['postalcode'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['cp'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['country'] != '') {
                            $ct_layer->fields['country'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['country'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['fax'] != '') {
                            $ct_layer->fields['fax'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['fax'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone'] != '') {
                            $ct_layer->fields['phone'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['mobile'] != '') {
                            $ct_layer->fields['mobile'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['mobile'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email2']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['email2']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email2'] != '') {
                            $ct_layer->fields['email2'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email2'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email3']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['email3']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email3'] != '') {
                            $ct_layer->fields['email3'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['email3'];
                        }
                    }

                    if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone2'] != '') {
                            $ct_layer->fields['phone2'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['phone2'];
                        }
                    }

					if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']]) && isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['civilite']) ) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 1) {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['civilite'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['civilite'];
						}
					}

					if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']]) && !isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['civilite']) && isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['titre'])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 1) {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['civilite'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['titre'];
						}
					}

                    $ct_layer->fields['id_user_create'] = $_SESSION['dims']['userid'];
                    $ct_layer->save();


                }

                //on cree le lien entre les contacts
                if ($_SESSION['dims']['userid']>0) {
                    $ctlink = new ctlink();
                    $ctlink->fields['id_contact1']= $id_user_similar;
                    $ctlink->fields['id_contact2']= $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_ct_from'];
                    $ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
                    $ctlink->fields['type_link']="business";
                    $ctlink->fields['link_level']=2;
                    $ctlink->fields['time_create']=date("YmdHis");
                    $ctlink->fields['id_ct_user_create']=$_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_ct_from'];
                    $ctlink->fields['id_user']=$_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_user_create'];
                    $ctlink->fields['id_workspace']=$_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_workspace'];
                    $ctlink->save();
                }

                if($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['company'] != '') {
                    if( $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == 0 || $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == '' ) {

                        $ntiers = new tiers();
                        $ntiers->init_description();
                        $ntiers->fields['intitule'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['company'];
                        $ntiers->fields['intitule_search'] = strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['company']);
                        $ntiers->setugm();
                        $ntiers->fields['date_creation'] = date("YmdHis");

                        $id_new_ent = $ntiers->save();
                    }

                    //Ce contact a une entreprise qui porte exactement le meme nom dans le site ?
					if (!isset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent']) || $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent']<0) {
						$_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent']=0;
					}

                    $sql = "SELECT id
                            FROM dims_mod_business_tiers_contact
                            WHERE id_contact = :idcontact
                            AND id_tiers = :idtiers ";

                    $res = $db->query($sql, array(
                        ':idcontact' => $id_user_similar,
                        ':idtiers' => $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'],
                    ));
                    //si aucun lien n'existe, on le cré
                    if($db->numrows($res) == 0 || ( $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == 0 || $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == '' )){
                        //echo "On attache la personne a cette entreprise.<br/>";

                        $new_link = new tiersct();
                        $new_link->init_description();

                        if( $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == 0 || $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == '' )
                            $new_link->fields['id_tiers'] = $id_new_ent;
                        else
                            $new_link->fields['id_tiers'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'];

                        $new_link->fields['id_contact'] = $id_user_similar;
                        $new_link->fields['id_ct_user_create'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_ct_from'];
                        $new_link->fields['link_level'] = 2;
                        $new_link->fields['date_create'] = date("YmdHis");
                        $new_link->fields['id_workspace'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_workspace'];
                        $new_link->fields['id_user'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_user_create'];

                        $new_link->save();
                    }
                }

                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                //on supprime le contact de la table d'import
                $supp_imp = new contact_import();
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

            }elseif ($id_user_similar == 0){

                //////////////////////////////////
                //   C'est un AJOUT de contact  //
                //////////////////////////////////

                $contact = new contact();
                $contact->init_description();

                $contact->fields['firstname'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['firstname'];
                $contact->fields['lastname'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['lastname'];

                $data = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change];

                //on cree un layer
                $ct_layer = new contact_layer();
                $ct_layer->init_description();

                $ct_layer->fields['type_layer'] = 1;
                $ct_layer->fields['id_layer'] = $data['id_workspace'];
				$ct_layer->fields['id_user']=$data['id_user_create'];
                $ct_layer->fields['id_workspace']=$data['id_workspace'];
                $ct_layer->fields['id_module']= $_SESSION['dims']['moduleid'];

                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['email'] = $data['email'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['email'] = $data['email'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email2']])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['email2']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['email2'] = $data['email2'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['email2'] = $data['email2'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email3']])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['email3']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['email3'] = $data['email3'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['email3'] = $data['email3'];
                    }
                }

                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']]) && isset($data['address'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['address'] = $data['address'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['address'] = $data['address'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']]) && isset($data['cp'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['postalcode'] = $data['cp'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['postalcode'] = $data['cp'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']]) && isset($data['city'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['city'] = $data['city'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['city'] = $data['city'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']]) && isset($data['phone'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                            $contact->fields['phone'] = $data['phone'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['phone'] = $data['phone'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']]) && isset($data['phone2'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone2']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['phone2'] = $data['phone2'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['phone2'] = $data['phone2'];
                    }
                }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']]) && isset($data['fax'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['fax']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['fax'] = $data['fax'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['fax'] = $data['fax'];
                    }
                }

					if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']])) {

						if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 1 && $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['civilite'] != '') {
                            $ct_layer->fields['civilite'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['civilite'];
                        }
                    }
                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']]) && isset($data['civilite'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['civilite'] = $data['civilite'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['civilite'] = $data['civilite'];
                    }
                }
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']]) && !isset($data['civilite']) && isset($data['titre'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['civilite']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['civilite'] = $data['titre'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['civilite'] = $data['titre'];
                    }
				}

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']]) && isset($data['mobile'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['mobile']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['mobile'] = $data['mobile'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['mobile'] = $data['mobile'];
                    }
                }

                if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']]) && isset($data['country'])) {
                    if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
                        //c'est un champ generique -> on enregistre dans contact
                        $contact->fields['country'] = $data['country'];
                    }
                    else {
                        //c'est un champ metier -> on enregistre dans un layer
                        $ct_layer->fields['country'] = $data['country'];
                    }
                }

                $contact->fields['id_user_create'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_user_create'];
                $ct_layer->fields['id_user_create'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['id_user_create'];

                $id_new_contact = $contact->save();

                $ct_layer->fields['id'] = $id_new_contact;
                $ct_layer->save();

                if(!empty($data['comment'])) {

                    // on a un commentaire
                    $cmt = new commentaire();
                    $cmt->fields['id_contact']=$id_new_contact;
                    $cmt->fields['id_user_ct']=$data['id_ct_from'];
                    $cmt->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
                    $cmt->fields['commentaire']=$data['comment'];
                    $cmt->fields['date_create']=date("YmdHis");
                    $cmt->fields['com_level']=1; // generique, voir si pas personnel
                    $cmt->fields['id_user']=$data['id_user_create'];
                    $cmt->fields['id_workspace']=$data['id_workspace'];
                    $cmt->fields['id_module']= $_SESSION['dims']['moduleid'];
                    $cmt->save();
                }

                // on crée le lien intelligence avec la personne
                if ($_SESSION['dims']['userid']>0) {
                    $ctlink = new ctlink();
                    $ctlink->fields['id_contact1'] = $id_new_contact;
                    $ctlink->fields['id_contact2']=$data['id_ct_from'];
                    $ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
                    $ctlink->fields['type_link']="business";
                    $ctlink->fields['link_level']=2;
                    $ctlink->fields['time_create']=date("YmdHis");
                    $ctlink->fields['id_ct_user_create']=$data['id_ct_from'];
                    $ctlink->fields['id_user']=$data['id_user_create'];
                    $ctlink->fields['id_workspace']=$data['id_workspace'];
                    $ctlink->save();
                }

                if($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['company'] != '') {
                    if( $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == 0 || $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == '' ) {
                        $ntiers = new tiers();
                        $ntiers->init_description();
                        $ntiers->fields['intitule'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['company'];
                        $ntiers->fields['intitule_search'] = strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['company']);
                        $ntiers->setugm();
                        $ntiers->fields['date_creation'] = date("YmdHis");

                        $id_new_ent = $ntiers->save();
                    }
                    //echo "On attache la personne a cette entreprise.<br/>";

                        $new_link = new tiersct();
                        $new_link->init_description();

                        if( $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == 0 || $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'] == '' )
                            $new_link->fields['id_tiers'] = $id_new_ent;
                        else
                            $new_link->fields['id_tiers'] = $_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]['exist_ent'];

                        $new_link->fields['id_contact'] = $id_new_contact;
                        $new_link->fields['id_ct_user_create'] = $data['id_ct_from'];
                        $new_link->fields['link_level'] = 2;
                        $new_link->fields['date_create'] = date("YmdHis");
                        $new_link->fields['id_workspace'] = $data['id_workspace'];
                        $new_link->fields['id_user'] = $data['id_user_create'];

                        $new_link->save();
                }

                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                //on supprime le contact de la table d'import
                $supp_imp = new contact_import();
                $supp_imp->open($id_user_to_change);
                $supp_imp->delete();

                /*
                $tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                $user_id = array_keys($tab_contact_new);
                if(count($user_id) > 0){
                    $user_id = $user_id[0];
                }*/
				if (!empty($_SESSION['dims']['import_contact_similar'])) {
					$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
				}
				else {
					// on commence le premier
					$tab_contact_new = array_slice($_SESSION['dims']['IMPORT_CONTACT'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
					$tab_contact_new=array();
				}

                $_SESSION['dims']['import_current_user_id'] = $user_id;
                $_SESSION['dims']['import_current_similar'] = $tab_contact_new;

                $_SESSION['dims']['import_contact_similar_count']++;

            }elseif ($id_user_similar == -1){ //on passe juste au suivant
                unset($_SESSION['dims']['import_contact_similar'][$id_user_to_change]);
                unset($_SESSION['dims']['IMPORT_CONTACT'][$id_user_to_change]);

                /*$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                $user_id = array_keys($tab_contact_new);
                if(count($user_id) > 0){
                    $user_id = $user_id[0];
                }*/
				if (!empty($_SESSION['dims']['import_contact_similar'])) {
					$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
				}
				else {
					// on commence le premier
					$tab_contact_new = array_slice($_SESSION['dims']['IMPORT_CONTACT'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
					$tab_contact_new=array();
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
                $supp_imp = new contact_import();
                $supp_imp->open($id_user_to_change);
                $supp_imp->delete();

                /*$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
                $user_id = array_keys($tab_contact_new);
                if(count($user_id) > 0){
                    $user_id = $user_id[0];
                }*/
				if (!empty($_SESSION['dims']['import_contact_similar'])) {
					$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
				}
				else {
					// on commence le premier
					$tab_contact_new = array_slice($_SESSION['dims']['IMPORT_CONTACT'],0,1,true);
					$user_id = array_keys($tab_contact_new);
					if(count($user_id) > 0){
						$user_id = $user_id[0];
					}
					$tab_contact_new=array();
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
    //unset($_SESSION['dims']['import_current_user_id']);
	if(!isset($_SESSION['dims']['import_current_user_id'])){

        //Dans ce cas on va chercher le premier import a traiter
		if (!empty($_SESSION['dims']['import_contact_similar'])) {
			$tab_contact_new = array_slice($_SESSION['dims']['import_contact_similar'],0,1,true);
			$user_id = array_keys($tab_contact_new);
			if(count($user_id) > 0){
				$user_id = $user_id[0];
			}
		}
		else {
			// on commence le premier
			$tab_contact_new = array_slice($_SESSION['dims']['IMPORT_CONTACT'],0,1,true);
			$user_id = array_keys($tab_contact_new);
			if(count($user_id) > 0){
				$user_id = $user_id[0];
			}
			$tab_contact_new=array();
		}


        //On met en session pour la suite
        $_SESSION['dims']['import_current_user_id'] = $user_id;
        $_SESSION['dims']['import_current_similar'] = $tab_contact_new;
        //dims_print_r($_SESSION['dims']['import_current_user_id']);
        //dims_print_r($_SESSION['dims']['import_current_similar']);


    }else{//Sinon on prend celui en cours
        $user_id = $_SESSION['dims']['import_current_user_id'];
    }
}

//if (isset($user_id)) {
    //Si on a le compte de contact a traiter, c'est qu'on a fini. On passe a l'etape 4
	//echo $_SESSION['dims']['import_count_contact_similar'] ." ".$_SESSION['dims']['import_contact_similar_count'];
    if($_SESSION['dims']['import_count_contact_similar'] >= $_SESSION['dims']['import_contact_similar_count']){

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

		$content_contact_import .= '    <tr style="background:#C2C2C2;border-bottom:1px solid #738CAD;">
										<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_DIMS['cste']['_IMPORT_TAB_NEW_CONTACT'].'</td>
										<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
										<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$user_id]['lastname']).'</td>
										<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_SESSION['dims']['IMPORT_CONTACT'][$user_id]['firstname'].'</td>
										<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">'.$_SESSION['dims']['IMPORT_CONTACT'][$user_id]['email'].'</td>
										<td style="background:#C2C2C2;border-bottom:1px solid #738CAD;border-top:1px solid #738CAD;">&nbsp;</td>
								</tr>';

    if (isset($_SESSION['dims']['import_current_similar'][$user_id])) {


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

            /*if ($okfirstname && $okname) $linkoption='onclick="javascript:document.valider_similitude.submit();" ';
            else*/ $linkoption="";
			if ($okfirstname && $okname) $linkoption="checked";
			else $linkoption="";

            $content_contact_import .= '    <td><input type="radio" '.$linkoption.'  name="similar_contact" value="'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['id'].'"/></td>
                                            <td>'.$samname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['lastname'].'</td>
                                            <td>'.$samfirstname."&nbsp;".$_SESSION['dims']['DB_CONTACT'][$similar_contact]['firstname'].'</td>
                                            <td>'.$_SESSION['dims']['DB_CONTACT'][$similar_contact]['email'].'</td>
                                            <td>&nbsp;</td>
                                    </tr>';
            $token->field("similar_contact");
        }
    }
		// à ajouter dans le radio bouton :  onclick="javascript:document.valider_similitude.submit();"
        $content_contact_import .= '<tr>
                                            <td style="border-top:1px solid #738CAD;">&nbsp;</td>
                                            <td style="border-top:1px solid #738CAD;"><input type="radio" name="similar_contact" value="0"/></td>
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
		/*
        $content_contact_import = '<table>
                                        <tr>
                                            <td>'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_FIN'].'
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=5');").'
                                            </td>
                                        </tr>
                                    </table>';*/
		dims_redirect('./admin.php?cat='._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=5");
    }

//}


?>
