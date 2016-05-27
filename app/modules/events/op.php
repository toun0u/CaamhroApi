<?php
dims_init_module('system');
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once(DIMS_APP_PATH . '/modules/system/include/business.php');

$dims_op=dims_load_securvalue('dims_op',dims_const::_DIMS_CHAR_INPUT,true,true);

if ($dims_op!="") {
	switch($dims_op) {
		case 'object':
		case 'object_properties':
		case 'refreshDesktop':
					$moduleid=$_SESSION['dims']['current_object']['id_module'];
					$objectid=$_SESSION['dims']['current_object']['id_object'];
					$recordid=$_SESSION['dims']['current_object']['id_record'];

					switch($objectid) {
						case dims_const::_SYSTEM_OBJECT_ACTION:
						case dims_const::_SYSTEM_OBJECT_EVENT:

							$obj=new action();
							$obj->open($recordid);

							$_SESSION['dims']['current_object']['label']=$obj->fields['libelle'];
							$_SESSION['dims']['current_object']['id_workspace']=$obj->fields['id_workspace'];
							$_SESSION['dims']['current_object']['id_user']=$obj->fields['id_user'];
							$_SESSION['dims']['current_object']['timestp_modify']=$obj->fields['timestp_modify'];

							$_SESSION['dims']['current_object']['cmd']=array();

							// calcul de diff?rence de jour
							$annee = substr($obj->fields['datejour'], 0, 4); // on r?cup?re le jour
							$mois = substr($obj->fields['datejour'], 5, 2); // puis le mois
							$jour = substr($obj->fields['datejour'], 8, 2);

							if (DIMS_DATEFORMAT==dims_const::DIMS_DATEFORMAT_FR)
									$datecumul=$jour."/".$mois."/".$annee;
							else
									$datecumul=$annee."/".$mois."/".$jour;

							$timestamp = mktime(0, 0, 0, $mois, $jour, $annee);
							$maintenant=time();
							$ecart_secondes = $timestamp-$maintenant;
							$ecart=floor($ecart_secondes / (60*60*24));

							$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
							$elem['src']="./common/img/view.png";
							$elem['link']= dims_urlencode("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_PLANNING."&dims_desktop=block&dims_action=public&cat=-1&dayadd=".$ecart."&actionid=".$recordid);
							$_SESSION['dims']['current_object']['cmd'][]=$elem;

							// check responsable
							$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
							$isadminevent=$dims->isModuleTypeEnabled('events') && ($currentworkspace['activeevent'] || $currentworkspace['activeeventstep']);

							if ($isadminevent || dims_isadmin()) {
									$elem['name']=$_DIMS['cste']['_DIMS_LABEL_EDIT'];
									$elem['src']="./common/img/configure.png";
									$elem['link']= dims_urlencode("/admin.php?dims_moduleid=".$obj->fields['id_module']."&dims_mainmenu=events&submenu=8&dims_desktop=block&dims_action=public&action=add_evt&id=".$recordid);
									$_SESSION['dims']['current_object']['cmd'][]=$elem;
							}

							break;
					}
					$detailobject_description = ob_get_contents();

					ob_end_clean();
					break;
		case 'events_xsd':
		case 'events':
					require_once(DIMS_APP_PATH . "/modules/events/public_events.php");
					break;
				case 'event_subscribe':
					//<link rel="stylesheet" type="text/css" href="./common/modules/events/include/design.css" />
					?>


					<!--[if IE 7]>
									<link rel="stylesheet" type="text/css" href="./common/modules/events/include/styles_ie.css" title="styles" />
					<![endif]-->

					<!--<script language="JavaScript" type="text/JavaScript" src="/js/functions.js"></script>
					<script type="text/javascript" src="/js/prototype.js"></script>
					<script type="text/javascript" src="/js/effects.js"></script>
					<script type="text/javascript" src="/js/scriptaculous.js"></script>-->
					<?
					$id_action=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,true);
					require_once DIMS_APP_PATH . '/modules/events/cms_event_form_niv1.php';
					die();
					break;

				case 'save_niv1_admin':
					require_once DIMS_APP_PATH . '/modules/events/cms_event_save_niv1_admin.php';
					break;

				case 'event_subscribe_admin':
					//<link rel="stylesheet" type="text/css" href="./common/modules/events/include/design.css" />
					?>


					<!--[if IE 7]>
									<link rel="stylesheet" type="text/css" href="./common/modules/events/include/styles_ie.css" title="styles" />
					<![endif]-->

					<!--<script language="JavaScript" type="text/JavaScript" src="/js/functions.js"></script>
					<script type="text/javascript" src="/include/prototype.js"></script>
					<script type="text/javascript" src="/include/effects.js"></script>
					<script type="text/javascript" src="/include/scriptaculous.js"></script>-->
					<?
					$_SESSION['dims']['addidtiers']=0;
					$_SESSION['dims']['addidcontact']=0;
					$id_action=dims_load_securvalue('id_event',dims_const::_DIMS_NUM_INPUT,true,true,true);
					require_once DIMS_APP_PATH . '/modules/events/cms_event_form_niv1_admin.php';
					die();
					break;

				case 'event_subscribe_admin_search_init':
					$_SESSION['dims']['addidtiers']=0;
					$_SESSION['dims']['addidcontact']=0;
					break;

				case 'event_subscribe_admin_search':
					ob_end_clean();
					if (!isset($_SESSION['dims']['addidtiers'])) $_SESSION['dims']['addidtiers']=0;
					if (!isset($_SESSION['dims']['addidcontact'])) $_SESSION['dims']['addidcontact']=0;
					//event_subscribe_admin_search
					$type=dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
					$firstname=dims_load_securvalue('firstname',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$lastname=dims_load_securvalue('lastname',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$company=dims_load_securvalue('company',dims_const::_DIMS_CHAR_INPUT,true,true,true);
					$idtiers=dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['addidtiers']);
					$idcontact=dims_load_securvalue('idcontact',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['addidcontact']);
					$lstcontact="0";


					if ($idtiers>0 || $idcontact>0) {
						// on traite de la sélection
						echo "<input type=\"hidden\" name=\"id_tiers\" value=\"".$idtiers."\">";
						echo "<input type=\"hidden\" name=\"id_contact\" value=\"".$idcontact."\">";

						if ($idcontact>0) {
							// on affiche la personne sélectionnée
							$contact = new contact();
							$contact->open($idcontact);

							require_once(DIMS_APP_PATH . '/modules/system/class_contact_layer.php');
							$contactl = new contact_layer();
							$contactl->open($idcontact);

							$_SESSION['dims']['addidcontact']=$idcontact;
							echo $_DIMS['cste']['_DIMS_LABEL_CT_LINKED']." ".$contact->fields['firstname']." ".$contact->fields['lastname'];

							// on construit ce qui va permettre de mettre des valeurs dans les champs
							echo '<script type="text/javascript">';

							$lastname=$contactl->fields['lastname'];
							$firstname=$contactl->fields['firstname'];
							$email=$contactl->fields['email'];
							$phone=$contactl->fields['phone'];
							$address=$contactl->fields['address'];
							$postalcode=$contactl->fields['postalcode'];
							$city=$contactl->fields['city'];
							$country=$contactl->fields['country'];

							if ($lastname=='') $lastname=$contact->fields['lastname'];
							if ($firstname=='') $firstname=$contact->fields['firstname'];
							if ($email=='') $email=$contact->fields['email'];
							if ($phone=='') $phone=$contact->fields['phone'];
							if ($address=='') $address=$contact->fields['address'];
							if ($postalcode=='') $postalcode=$contact->fields['postalcode'];
							if ($city=='') $city=$contact->fields['city'];
							if ($country=='') $country=$contact->fields['country'];


							echo '$(\'#0_firstname\').val("'.str_replace(array('"',"\n","\r"), " ", $lastname).'");';
							echo '$(\'#0_lastname\').val("'.str_replace(array('"',"\n","\r"), " ", $firstname).'");';
							echo '$(\'#0_email\').val("'.str_replace(array('"',"\n","\r"), " ", $email).'");';
							echo '$(\'#0_phone\').val("'.str_replace(array('"',"\n","\r"), " ", $phone).'");';
							echo '$(\'#0_address\').val("'.str_replace(array('"',"\n","\r"), " ", $address).'");';
							echo '$(\'#0_postalcode\').val("'.str_replace(array('"',"\n","\r"), " ", $postalcode).'");';
							echo '$(\'#0_city\').val("'.str_replace(array('"',"\n","\r"), " ", $city).'");';
							echo '$(\'#0_country\').val("'.str_replace(array('"',"\n","\r"), " ", $country).'");';

							echo "</script>";




							// option reinit du mot de passe
							echo "<br><input type=\"checkbox\" value=\"reinit_passwd\">Réinitiliser le mot de passe";
						}


						if ($idtiers>0) {
							$tiers	= new tiers();
							$tiers->open($idtiers);
							$_SESSION['dims']['addidtiers']=$idtiers;
							echo " - ".$tiers->fields['intitule'];
						}

						// annulation
							echo "<a href=\"javascript:void(0);\" onclick=\"javacript:annulSelData();\"><img src=\"./common/img/delete.png\" style=\"border:0px;\"></a>";
					}

					if ($firstname!='' || $lastname!='' ) {
						// on recupere les contacts
						$tab_corresp = array();

						if ($firstname!='' || $lastname!='') {
							$sql = 'SELECT
										ct.id as id_contact,
										ct.lastname,
										ct.firstname
									FROM
										dims_mod_business_contact ct';

							$ress = $db->query($sql);

							if($db->numrows($ress) > 0) {
								$nom	= strtoupper($lastname);
								$prenom = strtoupper($firstname);
								$longnom=strlen($nom);
								$oknom=$longnom>0;
								$longprenom=strlen($prenom);
								$okprenom=$longprenom>0;


								while($rslt = $db->fetchrow($ress)) {

									$lev_nom = 0;
									$lev_pre = 0;

									$coef_nom = 0;
									$coef_pre = 0;

									$coef_tot = 0;

									if ($oknom) {

										$lev_nom = levenshtein($nom, substr(strtoupper($rslt['lastname']),0,$longnom));
										$coef_nom = $lev_nom; //- (ceil(strlen($nom)/4));
									}

									if ($okprenom) {
										$lev_pre = levenshtein($prenom, substr(strtoupper($rslt['firstname']),0,$longprenom));
										$coef_pre = $lev_pre;// - (ceil(strlen($prenom)/4));
									}
									$coef_tot = $coef_nom + $coef_pre;
									/*
									$lev_nom2 = 0;
									$lev_pre2 = 0;

									$coef_nom2 = 0;
									$coef_pre2 = 0;

									$coef_tot2 = 0;

									$lev_nom2 = levenshtein($nom, strtoupper($rslt['firstname']));
									$coef_nom2 = $lev_nom2 - (ceil(strlen($nom)/4));

									$lev_pre2 = levenshtein($prenom, strtoupper($rslt['lastname']));
									$coef_pre2 = $lev_pre2 - (ceil(strlen($prenom)/4));

									$coef_tot2 = $coef_nom2 + $coef_pre2;
									*/

									if($coef_tot < 2 ) {
										$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
										$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
										$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
										$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
										$lstcontact.=",".$rslt['id_contact'];

									}

								}
								sort($tab_corresp);
							}
						}
					}

					$tabents=array();// tab d'ent connues

					// recherche entreprise
					if ($idtiers==0 ) {
						$listtiers=array();

						// construction des entreprises rattachees
						$param = array();
						$sql = 'SELECT distinct t.id,t.intitule
								from dims_mod_business_tiers as t
								';
						if ($company!=''){
							$sql.=" WHERE intitule like :company";
							$param[':company'] = "%$company%";
						} else
							$sql='';

						if ($sql!='') {
							$sql.=' AND intitule !=""';

							$ress = $db->query($sql, $param);
							$class="trl2";

							print ("<b>{$_DIMS['cste']['_DIMS_SEARCH_RESULT']} : ".$db->numrows($ress)."</b><br>");
							print ("<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
							print ('<tr style="font-weight:bold;">');
							print ("<td width=\"40%\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td>");
							print ("<td width=\"30%\" style=\"text-align:center;\">Select company</td>");

							print ("</tr>");

							if($db->numrows($ress) > 0) {
								while ($f=$db->fetchrow($ress)) {

									echo '<tr class="'.$class.'">';

									echo "<td style=\"width:20%;\">".$f['intitule']."</td><td><a href=\"javascript:void(0);\" onclick=\"javascript:eventSelData(".$f['id'].",0);\"><img src=\"./common/img/add_factory.png\"/></a></td>";

									echo "</tr>";
									$listtiers[$f['id_contact']][$f['id']]=$f;

									 $class = ($class == 'trl1') ? 'trl2' : 'trl1';
								}
							}



							echo "</table>";
						}
					 }

					 if ($idcontact==0) {
						$listtiers=array();

						// construction des entreprises rattachees
						$sql = 'SELECT distinct c.id, c.firstname,c.lastname,c.email
									from dims_mod_business_contact as c
									';

						if ($lstcontact!='' && $lstcontact != '0') {
							$sql.=" AND tc.id_contact in ('".$lstcontact."') AND id_contact >0";
						}


						if ($sql!='') {

							/*	  $ress = $db->query($sql);

							if($db->numrows($ress) > 0) {
								while ($f=$db->fetchrow($ress)) {
									$listtiers[$f['id_contact']][$f['id']]=$f;

									if ($lstcontact==0) {
										$tab_corresp[$f['id']]['id'] = $f['id'];
										$tab_corresp[$f['id']]['lastname']	 = $f['lastname'];
										$tab_corresp[$f['id']]['firstname']  = $f['firstname'];
									}
								}
							}*/

							$nb_corresp = count($tab_corresp);

							if ($nb_corresp > 0) {

								print ("<b>{$_DIMS['cste']['_DIMS_SEARCH_RESULT']} : $nb_corresp </b><br>");
								print ("<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">");
								print ('<tr style="font-weight:bold;">');
								print ("<td width=\"20%\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</td>");
								print ("<td width=\"20%\">".$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']."</td>");
								print ("<td width=\"30%\" style=\"text-align:center;\">Select contact</td>");

								print ("</tr>");

								if ($company !='') $stylediv="block";
								else $stylediv="none";
								$class="trl2";
								$longcompany=strlen($company);

								foreach ($tab_corresp as $corresp){

									print ('<tr class="'.$class.'">');
									print ("<td>".$corresp['lastname']."</td>");
									print ("<td>".$corresp['firstname']."</td>");

									echo "<td style=\"text-align:center;\">";
									echo "<a href=\"javascript:void(0);\" onclick=\"javascript:eventSelData(".$idtiers.",".$corresp['id_contact'].");\"><img src=\"./common/img/add_user.png\"/></a>";
									echo "</td></tr>";


									//echo "</td></tr>";
									$class = ($class == 'trl1') ? 'trl2' : 'trl1';
								}

								print ("</table>");

							}else{
										echo $_DIMS['cste']['_DIMS_LABEL_NO_RESP'];

							}
						}
					 }
					die();
					break;
	}
}
?>
