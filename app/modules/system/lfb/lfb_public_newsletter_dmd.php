<?php
require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');

//Chargement de l'id
$id_nl = 0;

$id_nl = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true, false, $_SESSION['dims']['newsletter']['id_nl']);

$subaction = null;

$subaction = dims_load_securvalue('subaction', dims_const::_DIMS_CHAR_INPUT, true, true);

$newsletter = new newsletter();
$newsletter->open($id_news);

if(!$newsletter->new) {
	switch($subaction) {
		default:
			require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_dmd_list.php';
			break;

		case 'add':
			require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_newsletter_dmd_attach.php';
			break;

		case 'del':
			$id_dmd = dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if($id_dmd != 0) {
				$inscription = new newsletter_inscription();

				$inscription->open($id_dmd);

				$inscription->delete();
			}

			dims_redirect('admin.php?action='._NEWSLETTER_VIEW_DMDINSC.'&id_news='.$id_nl);

			break;

		case 'attach_contact';

			if($convmeta == '' || !isset($_SESSION['dims']['contact_fields_mode'])) {
			//on construit la liste des champs generiques afin d'enregistrer les infos contact directement dans la table contact ou dans un layer
				$sql =	"
							SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
										mb.protected,mb.name as namefield,mb.label as titlefield
							FROM		dims_mod_business_meta_field as mf
							INNER JOIN	dims_mb_field as mb
							ON			mb.id=mf.id_mbfield
							RIGHT JOIN	dims_mod_business_meta_categ as mc
							ON			mf.id_metacateg=mc.id
							WHERE		  mf.id_object = :idobject
							AND			mc.admin=1
							AND			mf.used=1
							ORDER BY	mc.position, mf.position
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
			if(isset($_POST['id_dmd']) && !empty($_POST['id_dmd']) &&
			   isset($_POST['id_contact']) && !empty($_POST['id_contact'])) {
				$inscription = new newsletter_inscription();
				$inscription->open(dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true, true));

				if(!$inscription->new) {
					$contact = new contact();
					$ct_layer = new contact_layer();
					if($_POST['id_contact'] != -1) {
						$maj_ct = 0;
						$maj_ly = 0;
						$contact->open(dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true, true));

						// recherche si layer pour workspace
						$res=$db->query("SELECT id,type_layer,id_layer
										FROM dims_mod_business_contact_layer
										WHERE id= :id
										AND type_layer=1
										AND id_layer= :idlayer ", array(
								':id'		=> $contact->fields['id'],
								':idlayer'	=> $_SESSION['dims']['workspaceid']
						));

						if($db->numrows($res) > 0 ) {
							//echo "select id,type_layer,id_layer from dims_mod_business_contact_layer where id=".$contact->fields['id']." and type_layer=1 and id_layer=".$_SESSION['dims']['workspaceid']; die();
							$sel_layer = $db->fetchrow($res);
							//on charge le layer
							$ct_layer->open($sel_layer['id'],$sel_layer['type_layer'],$sel_layer['id_layer']);
						}
						else {
							//on cree un layer
							$ct_layer->init_description();
							$ct_layer->fields['id'] = $contact->fields['id'];
							$ct_layer->fields['type_layer'] = 1;
							$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];
						}

						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(empty($contact->fields['address']   ) && !empty($inscription->fields['adresse'])) {
									$contact->fields['address'] = $inscription->fields['adresse'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(empty($ct_layer->fields['address']	) && !empty($inscription->fields['adresse'])) {
									$ct_layer->fields['address'] = $inscription->fields['adresse'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(empty($contact->fields['postalcode']) && !empty($inscription->fields['cp'])) {
									$contact->fields['postalcode'] = $inscription->fields['cp'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(empty($ct_layer->fields['postalcode']) && !empty($inscription->fields['cp'])) {
									$ct_layer->fields['postalcode'] = $inscription->fields['cp'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(empty($contact->fields['city']	   ) && !empty($inscription->fields['ville'])) {
									$contact->fields['city'] = $inscription->fields['ville'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(empty($ct_layer->fields['city']		) && !empty($inscription->fields['ville'])) {
									$ct_layer->fields['city'] = $inscription->fields['ville'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(empty($contact->fields['country']   ) && !empty($inscription->fields['pays'])) {
									$contact->fields['country'] = $inscription->fields['pays'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(empty($ct_layer->fields['country']	) && !empty($inscription->fields['pays'])) {
									$ct_layer->fields['country'] = $inscription->fields['pays'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(empty($contact->fields['phone']	   ) && !empty($inscription->fields['tel'])) {
									$contact->fields['phone'] = $inscription->fields['tel'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(empty($ct_layer->fields['phone']		) && !empty($inscription->fields['tel'])) {
									$ct_layer->fields['phone'] = $inscription->fields['tel'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(empty($contact->fields['email']	   ) && !empty($inscription->fields['email'])) {
									$contact->fields['email'] = $inscription->fields['email'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(empty($ct_layer->fields['email']		) && !empty($inscription->fields['email'])) {
									$ct_layer->fields['email'] = $inscription->fields['email'];
									$maj_ly = 1;
								}
							}
						}

						if($maj_ly == 1) {
							$ct_layer->save();
						}
					}
					else {

						//on cree un layer
						$ct_layer->init_description();
						$ct_layer->fields['type_layer'] = 1;
						$ct_layer->fields['id_layer'] = $_SESSION['dims']['workspaceid'];

						//cas particulier : nom et prenom toujours dans la table contact
						$contact->fields['lastname']	= (!empty($inscription->fields['nom'])) ? $inscription->fields['nom'] : '';
						$contact->fields['firstname']	= (!empty($inscription->fields['prenom'])) ? $inscription->fields['prenom'] : '';

						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['address']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['address']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(!empty($inscription->fields['adresse'])) {
									$contact->fields['address'] = $inscription->fields['adresse'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(!empty($inscription->fields['adresse'])) {
									$ct_layer->fields['address'] = $inscription->fields['adresse'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['postalcode']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(!empty($inscription->fields['cp'])) {
									$contact->fields['postalcode'] = $inscription->fields['cp'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(!empty($inscription->fields['cp'])) {
									$ct_layer->fields['postalcode'] = $inscription->fields['cp'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['city']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['city']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(!empty($inscription->fields['ville'])) {
									$contact->fields['city'] = $inscription->fields['ville'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(!empty($inscription->fields['ville'])) {
									$ct_layer->fields['city'] = $inscription->fields['ville'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['country']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['country']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(!empty($inscription->fields['pays'])) {
									$contact->fields['country'] = $inscription->fields['pays'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(!empty($inscription->fields['pays'])) {
									$ct_layer->fields['country'] = $inscription->fields['pays'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['phone']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(!empty($inscription->fields['tel'])) {
									$contact->fields['phone'] = $inscription->fields['tel'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(!empty($inscription->fields['tel'])) {
									$ct_layer->fields['phone'] = $inscription->fields['tel'];
									$maj_ly = 1;
								}
							}
						}
						if(isset($_SESSION['dims']['contact_fields_mode'][$convmeta['email']])) {
							if($_SESSION['dims']['contact_fields_mode'][$convmeta['email']] == 0) {
								//c'est un champ generique -> on enregistre dans contact
								if(!empty($inscription->fields['email'])) {
									$contact->fields['email'] = $inscription->fields['email'];
									$maj_ct = 1;
								}
							}
							else {
								//c'est un champ metier -> on enregistre dans un layer
								if(!empty($inscription->fields['email'])) {
									$ct_layer->fields['email'] = $inscription->fields['email'];
									$maj_ly = 1;
								}
							}
						}
					}
					$id_ct = $contact->save();
					if($maj_ly == 1) {
						$ct_layer->fields['id'] = $contact->fields['id'];
						$ct_layer->save();
					}

					$to[0]['name']	   = $contact->fields['lastname'].' '.$contact->fields['firstname'];
					$to[0]['address']  = $contact->fields['email'];

					$subscribed = new news_subscribed();

					$subscribed->fields['id_newsletter']		= $id_nl;
					$subscribed->fields['id_contact']			= $id_ct;
					$subscribed->fields['date_inscription']		= date('YmdHis');
					$subscribed->fields['date_desinscription']	= '';
					$subscribed->fields['etat']					= 1;

					$subscribed->save();

					//mail
					$from	= array();
					$to		= array();
					$subject= '';
					$content= '';

					$subject = 'Registration to LFB newsletters'.

					$content = 'Hello '.$contact->fields['firstname'].' '.$contact->fields['lastname'].'<br /><br />';
					$content.= 'Thank you for your registration.<br /> You will receive our next newsletter shortly.';
					$content.= '<br /><br />';
					$content.= 'Luxembourg For Business<br />';

					$from[0]['name']   = $_SESSION['dims']['user']['lastname'].' '.$_SESSION['dims']['user']['firstname'];
					$from[0]['address']= $_SESSION['dims']['user']['email'];

					dims_send_mail($from, $to,$subject,$content);

					$inscription->delete();
				}
			}

			dims_redirect('admin.php?action='._NEWSLETTER_VIEW_DMDINSC.'&id_news='.$id_nl);
			break;
	}
}
else {
	echo '<p>';
	echo $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER_NONE'];
	echo '</p>';
	echo dims_create_button($_SESSION['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&init=1\'');
}

?>
