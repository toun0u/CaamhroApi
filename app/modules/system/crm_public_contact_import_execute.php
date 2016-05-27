<?php


//echo "contact_similar";
		//dims_print_r($_SESSION['dims']['import_contact_similar']);
		//
		//echo "new_link";
		//dims_print_r($_SESSION['dims']['IMPORT_NEW_LINK']);
		//
		//echo "connus (normalement vide)";
		//dims_print_r($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']);
		//
		//echo "new_email";
		//dims_print_r($_SESSION['dims']['IMPORT_NEW_EMAIL']);
		//
		//echo "contact";
		//dims_print_r($_SESSION['dims']['IMPORT_CONTACT']);
		//
		//echo "new contact";
		//dims_print_r($_SESSION['dims']['IMPORT_NEW_CT']);
		//
		//echo "ici"; die();

		$user=new user();

		$user->open($_SESSION['dims']['import_id_user']);
		// tres tres important
		if (isset($user->fields['id_contact'])) $import_id_ct=$user->fields['id_contact'];
		else $import_id_ct=0;

		$content_contact_import = '';

		$mod=$dims->getModule($_SESSION['dims']['moduleid']);
		$id_module_type=$mod['id_module_type'];

		// construction des links existants
		$lstlinks=array();
		$res=$db->query("SELECT id_contact2
						FROM dims_mod_business_ct_link
						WHERE id_contact1= :idcontact
						AND id_object = :idobject
						AND type_link='business'", array(
				':idcontact'	=> $import_id_ct,
				':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
		));
		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$lstlinks[$f['id_contact2']]=1;
			}
		}

		$res=$db->query("SELECT id_contact1
						FROM dims_mod_business_ct_link
						WHERE id_contact2= :idcontact
						AND id_object = :idobject
						AND type_link='business'", array(
				':idcontact'	=> $import_id_ct,
				':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
		));
		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$lstlinks[$f['id_contact1']]=1;
			}
		}
		// construction des links existants entre contact et entreprise
		$lsttierslinks=array();
		$res=$db->query("SELECT id_contact from dims_mod_business_tiers_contact where id_tiers= :idtiers ", array(
				':idtiers'	=> $_SESSION['dims']['import_id_ent']
		));

		if ($db->numrows($res)>0) {
			while ($f=$db->fetchrow($res)) {
				$lsttierslinks[$f['id_contact']]=1;
			}
		}

		// on met a jour les rattachements des personnes (case 1)
		foreach($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'] AS $data['id'] => $data){
			//$contact->open($data['exist']);
			$id_contact=$data['exist'];
			// on crée le lien intelligence avec la personne
			if ($import_id_ct>0 && !isset($lstlinks[$id_contact])) { // rajout du test de liaison pour eviter les doublons

				$ctlink = new ctlink();
				$ctlink->fields['id_contact1']=$import_id_ct;
				$ctlink->fields['id_contact2']=$id_contact;
				$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
				$ctlink->fields['type_link']="business";
				$ctlink->fields['link_level']=2;
				$ctlink->fields['time_create']=date("YmdHis");
				$ctlink->fields['id_ct_user_create']=$import_id_ct;
				$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
				$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
				$ctlink->save();
			}

			// si on a choisit de lier cette personne a une entreprise on crée aussi le lien entreprise
			if ($_SESSION['dims']['import_id_ent']>0 && !isset($lsttierslinks[$id_contact])) { // ajout test de liaison pour eviter les doublons
				$new_link = new tiersct();
				$new_link->init_description();
				$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
				$new_link->fields['id_contact'] = $id_contact;
				if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
				$new_link->fields['id_ct_user_create'] = $import_id_ct;//$_SESSION['dims']['import_id_user'];
				$new_link->fields['link_level'] = 2;
				$new_link->fields['date_create'] = date("YmdHis");
				$new_link->fields['date_deb'] = '';
				$new_link->fields['date_fin'] = '';
				$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$new_link->save();
			}

			if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
				foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
					$res=$db->query("SELECT id_tag
									FROM dims_tag_index
									WHERE id_tag= :idtag
									AND id_record= :idrecord
									AND id_user= :iduser
									AND id_module_type= :idmoduletype
									AND id_object= :idobject ", array(
							':idtag'		=> $idtag,
							':idrecord'		=> $id_contact,
							':iduser'		=> $_SESSION['dims']['import_id_user'],
							':idmoduletype'	=> $id_module_type,
							':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
					));
					if ($db->numrows($res)==0) {
						$tagi = new tag_index();
						$tagi->fields['id_tag']=$idtag;
						$tagi->fields['id_record']=$id_contact;
						$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
						$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$tagi->fields['id_module_type']=$id_module_type;
						$tagi->save();
					}
				}
			}
		}
		//On met a jour les emails (case 3)
		if (isset($_SESSION['dims']['IMPORT_NEW_EMAIL']) && count($_SESSION['dims']['IMPORT_NEW_EMAIL']) > 0) {
			foreach($_SESSION['dims']['IMPORT_NEW_EMAIL'] AS $id_contact => $email){
				$contact = new contact;
				$contact->open($id_contact);

				//on va chercher le layer concerne pour le mettre a jour si le champ email est  un champ metier ()
				$res=$db->query("SELECT id,type_layer,id_layer
								FROM dims_mod_business_contact_layer
								WHERE id= :id
								AND type_layer=1
								AND id_layer= :idlayer ", array(
						':id'			=> $id_contact,
						':idlayer'		=> $_SESSION['dims']['workspaceid']
				));
				$ct_layer = new contact_layer();
				if($db->numrows($res) > 0 ) {
					$sel_layer = $db->fetchrow($res);

					//on charge le layer
					$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);

					if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$contact->fields['email'] = $email;
						$contact->save();
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['email'] = $email;
						$ct_layer->save();
					}
				}

				// on crée le lien intelligence avec la personne
				if ($import_id_ct>0 && !isset($lstlinks[$id_contact])) { // rajout du test de liaison pour eviter les doublons
					$ctlink = new ctlink();
					$ctlink->fields['id_contact1']=$import_id_ct;
					$ctlink->fields['id_contact2']=$id_contact;
					$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
					$ctlink->fields['type_link']="business";
					$ctlink->fields['link_level']=2;
					$ctlink->fields['time_create']=date("YmdHis");
					$ctlink->fields['id_ct_user_create']=$import_id_ct;
					$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ctlink->save();
				}

				// si on a choisit de lier cette personne a une entreprise on crée aussi le lien entreprise
				if ($_SESSION['dims']['import_id_ent']>0 && !isset($lsttierslinks[$id_contact])) { // ajout test de liaison pour eviter les doublons
					$new_link = new tiersct();
					$new_link->init_description();
					$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
					$new_link->fields['id_contact'] = $id_contact;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $import_id_ct; //$_SESSION['dims']['import_id_user'];
					$new_link->fields['link_level'] = 2;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['date_deb'] = '';
					$new_link->fields['date_fin'] = '';
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$new_link->save();
				}

				if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
					foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
						$res=$db->query("SELECT id_tag
										FROM dims_tag_index
										WHERE id_tag= :idtag
										AND id_record= :idrecord
										AND id_user= :iduser
										AND id_module_type= :idmoduletype
										AND id_object= :idobject ", array(
							':idtag'		=> $idtag,
							':idrecord'		=> $id_contact,
							':iduser'		=> $_SESSION['dims']['import_id_user'],
							':idmoduletype'	=> $id_module_type,
							':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
					));
						if ($db->numrows($res)==0) {
							$tagi = new tag_index();
							$tagi->fields['id_tag']=$idtag;
							$tagi->fields['id_record']=$id_contact;
							$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
							$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
							$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
							$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
							$tagi->fields['id_module_type']=$id_module_type;
							$tagi->save();
						}
					}
				}
			}
		}

        //Creation des liens
		if (isset($_SESSION['dims']['IMPORT_NEW_LINK']) && count($_SESSION['dims']['IMPORT_NEW_LINK']) > 0) {

			foreach($_SESSION['dims']['IMPORT_NEW_LINK'] AS $key => $data){
				$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_tiers= :idtiers AND id_contact= :idcontact ";
				$res = $db->query($sql, array(
					':idtiers'		=> $data['exist_ent'],
					':idcontact'	=> $key
				));
				if($db->numrows($res)==0) {
					$new_link = new tiersct();
					$new_link->init_description();

					$new_link->fields['id_tiers'] = $data['exist_ent'];
					$new_link->fields['id_contact'] = $key;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $import_id_ct;
					$new_link->fields['link_level'] = 2;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['date_deb'] = '';
					$new_link->fields['date_fin'] = '';
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

					$new_link->save();

					$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
				}
			}
		}

        //Creation des contacts (issu du case 4)
		//ne sera plus trés utilise car ne prend en compte que les contacts non directement reconnus (etape 1);
		//et les contacts n'ayant aucune similitude (case 2 / 3) (ce qui est peu probable)
		if (isset($_SESSION['dims']['IMPORT_CONTACT'])) {

			foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $data['id'] => $data){
				$id_import = dims_load_securvalue("contact_import_".$data['id'], dims_const::_DIMS_CHAR_INPUT, false, true, true);
				if($id_import){
					$contact = new contact();
					$contact->init_description();

					//on cree un layer
					$ct_layer = new contact_layer();
					$ct_layer->init_description();

					$ct_layer->fields['type_layer'] = 1;
					$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
					$ct_layer->fields['id_user']=$_SESSION['dims']['userid'];
					$ct_layer->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ct_layer->fields['id_module']= $_SESSION['dims']['moduleid'];

					if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['firstname']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['firstname']] == 0) {
							// test on insert de toute facon dans la base générique
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['firstname'] = $data['firstname'];
						}
						//c'est un champ generique -> on enregistre dans contact
						$contact->fields['firstname'] = $data['firstname'];
					}
					if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['lastname']])) {
						if($_SESSION['dims']['contact_fields_mode'][$convmeta['lastname']] == 0) {
							// test on insert de toute facon dans la base générique
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['lastname'] = $data['lastname'];
						}
						//c'est un champ generique -> on enregistre dans contact
						$contact->fields['lastname'] = $data['lastname'];
					}
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
							if(!empty($data['address2'])) $contact->fields['address'] .= $data['address2'];
							if(!empty($data['address3'])) $contact->fields['address'] .= $data['address3'];
						}
						else {
							//c'est un champ metier -> on enregistre dans un layer
							$ct_layer->fields['address'] = $data['address'];
							if(!empty($data['address2'])) $ct_layer->fields['address'] .= $data['address2'];
							if(!empty($data['address3'])) $ct_layer->fields['address'] .= $data['address3'];
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

					$contact->fields['id_user_create'] = $import_id_ct;
					$ct_layer->fields['id_user_create'] = $import_id_ct;

					$id_new_contact = $contact->save();
					$ct_layer->fields['id'] = $id_new_contact;
					$ct_layer->save();

					if(!empty($data['comment'])) {

						// on a un commentaire
						$cmt = new commentaire();
						$cmt->fields['id_contact']=$id_new_contact;
						$cmt->fields['id_user_ct']=$import_id_ct;
						$cmt->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$cmt->fields['commentaire']=$data['comment'];
						$cmt->fields['date_create']=date("YmdHis");
						$cmt->fields['com_level']=1; // generique, voir si pas personnel
						$cmt->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$cmt->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$cmt->fields['id_module']= $_SESSION['dims']['moduleid'];
						$cmt->save();
					}

					//$content_contact_import .= "1 import de contact terminé.<br/>";
					// on crée le lien intelligence avec la personne
					if ($import_id_ct>0  && !isset($lstlinks[$id_new_contact])) {
						$ctlink = new ctlink();
						$ctlink->fields['id_contact1']=$import_id_ct;
						$ctlink->fields['id_contact2']=$id_new_contact;
						$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$ctlink->fields['type_link']="business";
						$ctlink->fields['link_level']=2;
						$ctlink->fields['time_create']=date("YmdHis");
						$ctlink->fields['id_ct_user_create']=$import_id_ct;
						$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$ctlink->save();

					}
					//Si on a trouvé une entreprise qui porte exactement le meme nom que la sienne,
					//on l'associe a cette entreprise.

					if($data['company'] != '') {
						// on refait un controle de l'existence eventuelle d'une entreprise
						$sql_e = "SELECT id, intitule FROM dims_mod_business_tiers ORDER BY intitule";
						$res_e = $db->query($sql_e);
						while($tab_ent = $db->fetchrow($res_e)) {
							if ($tab_ent['intitule']!="") {
								$lev_nom = levenshtein(strtoupper($data['company']), strtoupper($tab_ent['intitule']));

								if ($lev_nom==0) { //  on a trouve depuis une entreprise importée portant le meme nom
									$data['exist_ent']=$tab_ent['id'];
								}
							}
						}

						if( $data['exist_ent'] == 0 || $data['exist_ent'] == '' ) {
							$ntiers = new tiers();
							$ntiers->init_description();
							$ntiers->fields['intitule'] = $data['company'];
							$ntiers->fields['intitule_search'] = strtoupper($data['company']);
							$ntiers->setugm();
							$ntiers->fields['date_creation'] = date("YmdHis");

							$id_new_ent = $ntiers->save();
						}

						//Ce contact a une entreprise qui porte exactement le meme nom dans le site ?
						$sql = "SELECT id FROM dims_mod_business_tiers_contact WHERE id_contact = :idcontact AND id_tiers = :idtiers ";
						$res = $db->query($sql, array(
							':idcontact'	=> $id_new_contact,
							':idtiers'		=> $data['exist_ent']
						));
						//si aucun lien n'existe, on le cré
						if($db->numrows($res) == 0 || ( $data['exist_ent'] == 0 || $data['exist_ent'] == '' )){
							//echo "On attache la personne a cette entreprise.<br/>";

							$new_link = new tiersct();
							$new_link->init_description();

							if( $data['exist_ent'] == 0 || $data['exist_ent'] == '' )
								$new_link->fields['id_tiers'] = $id_new_ent;
							else
								$new_link->fields['id_tiers'] = $data['exist_ent'];

							$new_link->fields['id_contact'] = $id_new_contact;
							$new_link->fields['id_contact'] = $id_new_contact;
							if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
							$new_link->fields['id_ct_user_create'] = $import_id_ct;//$_SESSION['dims']['import_id_user'];
							$new_link->fields['link_level'] = 2;
							$new_link->fields['date_create'] = date("YmdHis");
							$new_link->fields['date_deb'] = '';
							$new_link->fields['date_fin'] = '';
							$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

							$new_link->save();

							$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
						}
					}

					// si on a choisit de lier cette personne a une entreprise on crée aussi le lien entreprise
					if ($_SESSION['dims']['import_id_ent']>0 && !isset($lsttierslinks[$id_new_contact])) {
						$new_link = new tiersct();
						$new_link->init_description();
						$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
						$new_link->fields['id_contact'] = $id_new_contact;
						if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
						$new_link->fields['id_ct_user_create'] = $import_id_ct; //$_SESSION['dims']['import_id_user'];
						$new_link->fields['link_level'] = 2;
						$new_link->fields['date_create'] = date("YmdHis");
						$new_link->fields['date_deb'] = '';
						$new_link->fields['date_fin'] = '';
						$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$new_link->save();
					}
					//$content_contact_import .= "<br/>";
					// attachement des tags
					if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
						foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
							$res=$db->query("SELECT id_tag
											FROM dims_tag_index
											WHERE id_tag= :idtag
											AND id_record= :idrecord
											AND id_user= :iduser
											AND id_module_type= :idmoduletype
											AND id_object= :idobject ", array(
									':idtag'		=> $idtag,
									':idrecord'		=> $id_new_contact,
									':iduser'		=> $_SESSION['dims']['import_id_user'],
									':idmoduletype'	=> $id_module_type,
									':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
							));
							if ($db->numrows($res)==0) {
								$tagi = new tag_index();
								$tagi->fields['id_tag']=$idtag;
								$tagi->fields['id_record']=$id_new_contact;
								$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
								$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
								$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
								$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
								$tagi->fields['id_module_type']=$id_module_type;
								$tagi->save();
							}
						}
					}

				}else{
					unset($_SESSION['dims']['IMPORT_CONTACT'][$data['id']]);
				}
			}
		}
		//Creation de nouveaux contact é partir des contacts avec similitudes (case 3)
		if (isset($_SESSION['dims']['IMPORT_NEW_CT'])) {
			foreach($_SESSION['dims']['IMPORT_NEW_CT'] AS $key => $data){
				$contact = new contact();
				$contact->init_description();
				//on cree un layer
				$ct_layer = new contact_layer();
				$ct_layer->init_description();

				$ct_layer->fields['type_layer'] = 1;
				$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
				$ct_layer->fields['id_user']=$_SESSION['dims']['userid'];
				$ct_layer->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
				$ct_layer->fields['id_module']= $_SESSION['dims']['moduleid'];

				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['firstname']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['firstname']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$contact->fields['firstname'] = $data['firstname'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['firstname'] = $data['firstname'];
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['lastname']])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['lastname']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$contact->fields['lastname'] = $data['lastname'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['lastname'] = $data['lastname'];
					}
				}
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
						if(!empty($data['address2'])) $contact->fields['address'] .= $data['address2'];
						if(!empty($data['address3'])) $contact->fields['address'] .= $data['address3'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['address'] = $data['address'];
						if(!empty($data['address2'])) $ct_layer->fields['address'] .= $data['address2'];
						if(!empty($data['address3'])) $ct_layer->fields['address'] .= $data['address3'];
					}
				}
				if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']]) && isset($data['postalcode'])) {
					if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
						//c'est un champ generique -> on enregistre dans contact
						$contact->fields['postalcode'] = $data['postalcode'];
					}
					else {
						//c'est un champ metier -> on enregistre dans un layer
						$ct_layer->fields['postalcode'] = $data['postalcode'];
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

				$contact->fields['id_user_create'] = $import_id_ct;
				$ct_layer->fields['id_user_create'] = $import_id_ct;

				$contact->save();
				$id_new_contact = $contact->fields['id'];
				$ct_layer->fields['id'] = $id_new_contact;
				$ct_layer->save();

				// gestion des commentaires
				if(!empty($data['comment'])) {
					$res=$db->query("SELECT id_contact from dims_mod_business_commentaire where id_user_ct= :iduser and id_contact= :idcontact ", array(
						':iduser'		=> $import_id_ct,
						':idcontact'	=> $id_new_contact
					));
					if ($db->numrows($res)==0) {
						// on a un commentaire
						$cmt = new commentaire();
						$cmt->fields['id_contact']=$id_new_contact;
						$cmt->fields['id_user_ct']=$import_id_ct;
						$cmt->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
						$cmt->fields['commentaire']=$data['comment'];
						$cmt->fields['date_create']=date("YmdHis");
						$cmt->fields['com_level']=2; //metier
						$cmt->fields['id_user']=$_SESSION['dims']['import_id_user'];
						$cmt->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$cmt->fields['id_module']= $_SESSION['dims']['moduleid'];
						$cmt->save();
					}
				}

				// on crée le lien intelligence avec la personne
				if ($import_id_ct>0  && !isset($lstlinks[$id_new_contact])) { // verification des doublons
					$ctlink = new ctlink();
					$ctlink->fields['id_contact1']=$import_id_ct;
					$ctlink->fields['id_contact2']=$id_new_contact;
					$ctlink->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
					$ctlink->fields['type_link']="business";
					$ctlink->fields['link_level']=2;
					$ctlink->fields['time_create']=date("YmdHis");
					$ctlink->fields['id_ct_user_create']=$import_id_ct;
					$ctlink->fields['id_user']=$_SESSION['dims']['import_id_user'];
					$ctlink->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
					$ctlink->save();
				}
				$_SESSION['dims']['IMPORT_CONTACT'][$key] = $data;

				//Si on a trouvé une entreprise qui porte exactement le meme nom que la sienne,
				//on l'associe a cette entreprise.

				if($data['exist_ent'] != 0){
					$res=$db->query("SELECT id_contact from dims_mod_business_tiers_contact where id_tiers= :idtiers and id_contact=  :idcontact ", array(
						':idtiers'		=> $data['exist_ent'],
						':idcontact'	=> $id_new_contact
					));
					if ($db->numrows($res)==0) {
						$new_link = new tiersct();
						$new_link->init_description();

						$new_link->fields['id_tiers'] = $data['exist_ent'];
						$new_link->fields['id_contact'] = $id_new_contact;
						if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
						$new_link->fields['id_ct_user_create'] = $import_id_ct; //$_SESSION['dims']['import_id_user'];
						$new_link->fields['link_level'] = 2;
						$new_link->fields['date_create'] = date("YmdHis");
						$new_link->fields['date_deb'] = '';
						$new_link->fields['date_fin'] = '';
						$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

						$new_link->save();

						//on remet les infos en session pour le afficher dans le tableau recapitulatif
						$_SESSION['dims']['IMPORT_LINK_ENT'][$data['exist_ent']][] = $data;
					}
				}

				// creation lien entreprise
				if ($_SESSION['dims']['import_id_ent']>0 && !isset($lsttierslinks[$id_new_contact])) {
					$new_link = new tiersct();
					$new_link->init_description();
					$new_link->fields['id_tiers'] = $_SESSION['dims']['import_id_ent'];
					$new_link->fields['id_contact'] = $id_new_contact;
					if(isset($imp->fields['professionnal'])) $new_link->fields['function'] = $data['professionnal'];
					$new_link->fields['id_ct_user_create'] = $import_id_ct; //$_SESSION['dims']['import_id_user'];
					$new_link->fields['link_level'] = 2;
					$new_link->fields['date_create'] = date("YmdHis");
					$new_link->fields['date_deb'] = '';
					$new_link->fields['date_fin'] = '';
					$new_link->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$new_link->save();
				}

				if (isset($_SESSION['dims']['tag_temp']) && !empty($_SESSION['dims']['tag_temp'])) {
					foreach($_SESSION['dims']['tag_temp'] as $idtag=>$t) {
						$res=$db->query("SELECT id_tag
										FROM dims_tag_index
										WHERE id_tag= :idtag
										AND id_record= :idrecord
										AND id_user= :iduser
										AND id_module_type= :idmoduletype
										AND id_object= :idobject ", array(
								':idtag'		=> $idtag,
								':idrecord'		=> $id_new_contact,
								':iduser'		=> $_SESSION['dims']['import_id_user'],
								':idmoduletype'	=> $id_module_type,
								':idobject'		=> dims_const::_SYSTEM_OBJECT_CONTACT
							));
						if ($db->numrows($res)==0) {
							$tagi = new tag_index();
							$tagi->fields['id_tag']=$idtag;
							$tagi->fields['id_record']=$id_new_contact;
							$tagi->fields['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
							$tagi->fields['id_module']= $_SESSION['dims']['moduleid'];
							$tagi->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
							$tagi->fields['id_user']=$_SESSION['dims']['import_id_user'];
							$tagi->fields['id_module_type']=$id_module_type;
							$tagi->save();
						}
					}
				}
				//on remet les infos en session pour le afficher dans le tableau recapitulatif
				$_SESSION['dims']['IMPORT_CONTACT'][$key] = $data;
			}
		}
        unset($_SESSION['dims']['IMPORT_NEW_LINK']);
        unset($_SESSION['dims']['IMPORT_NEW_CT']);

        $content_contact_import .= "<p style='text-align:center;font-size:16px;font-weight:bold;'>".$_DIMS['cste']['_IMPORT_COMPLETE']."</p><br/>";
        $content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_IMPORTED_CONTACTS']."</p><br/>";

        $content_contact_import .= '<div style="text-align:center;width:100%;">
                                            '.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=1');").'
                                        </div>';

        $content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">';
        $content_contact_import .= '    <tr style="background:#CECECE;">
                                            <td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
                                            <td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
                                            <td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
                                        </tr>';
        $color2 = "#738CAD";$color1 = "#F1F1F1";$color = '';
        foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $new_ct){
                if($color == $color1) $color = $color2 ; else $color = $color1;
                $content_contact_import .= '<tr style="background:'.$color.';">
                                                <td style="border-bottom:1px solid #738CAD;">'.$new_ct['lastname'].'</td>
                                                <td style="border-bottom:1px solid #738CAD;">'.$new_ct['firstname'].'</td>
                                                <td style="border-bottom:1px solid #738CAD;">'.$new_ct['email'].'</td>
                                            </tr>';
        }

        $content_contact_import .= '</table>';


        $content_contact_import .= "<p>".$_DIMS['cste']['_IMPORT_LINKED_CONTACTS']."</p><br/>";
        $content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">';
        $content_contact_import .= '    <tr style="background:#CECECE;">
                                            <td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
                                            <td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
                                            <td style="border-bottom:1px solid #738CAD;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
                                        </tr>';

        $color2 = "#738CAD";$color1 = "#F1F1F1";$color = '';
        foreach($_SESSION['dims']['IMPORT_LINK_ENT'] AS $new_ct_link){
            $rowspan = count($new_ct_link);
            if($color == $color1) $color = $color2 ; else $color = $color1;
            foreach($new_ct_link AS $ct_link){
                $content_contact_import .= '<tr style="background:'.$color.'">';
                if($rowspan > 1){ $content_contact_import .= '    <td rowspan="'.$rowspan.'">'.$ct_link['company'].'</td>'; $rowspan = 0;}
                if($rowspan == 1){ $content_contact_import .= '    <td>'.$ct_link['company'].'</td>';$rowspan = 0;}
                $content_contact_import .= '    <td>'.$ct_link['lastname'].'</td>';
                $content_contact_import .= '    <td>'.$ct_link['firstname'].'</td>';
                $content_contact_import .= '</tr>';
            }
        }

        $content_contact_import .= '</table>';
        $content_contact_import .= '<div style="text-align:center;">
                                            '.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=1');").'
                                        </div>';

		unset($_SESSION['dims']['import_current_similar'], $_SESSION['dims']['import_current_user_id'], $_SESSION['dims']['import_count_contact_similar']);
		unset($_SESSION['dims']['import_contact_similar_count'],$_SESSION['dims']['import_contact_similar']);
		unset($_SESSION['dims']['IMPORT_CONTACT']);
		unset($_SESSION['dims']['IMPORT_NEW_LINK']);
		unset($_SESSION['dims']['IMPORT_LINK_ENT']);
		unset($_SESSION['dims']['IMPORT_NEW_CT']);
		unset($_SESSION['dims']['IMPORT_IGNORED_CONTACT']);
		unset($_SESSION['dims']['IMPORT_KNOWN_CONTACTS']);
		unset($_SESSION['dims']['RL']);
		unset($_SESSION['dims']['DB_CONTACT']);
		unset($_SESSION['dims']['import_id_user']);


?>
