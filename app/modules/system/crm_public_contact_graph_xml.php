<?php
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
require_once DIMS_APP_PATH . '/modules/system/class_action.php';

// traitement de la fonction
// analyse l'element courant, recherche de nouvelles liaisons qu'elle n'aurait pas deja
// recherche aussi si la destination ne peut etre obtenu, si oui => results
// si aucun on sort

function ParcoursRecursif($src,$distance,&$idparcours,&$nbparcours,&$lstparcours,&$results,$matrix_tiers,$dests,$destcontacts,$tierscontacts) {
	// on test si le parcours existe deja ou non
	if ($distance==0) {
		$nbparcours++;
		$lstparcours[$nbparcours]['elements'][0]=$src;
		$lstparcours[$nbparcours]['cour']=1;
		$lstparcours[$nbparcours]['ids'][$src]=1;
		$lstparcours[$nbparcours]['found']=false;
		$idparcours=$nbparcours;
		$distance++;
	}

	$first=true;
	// on recherche les nouveaux liens avec la source donn�e
	// test si src = dest
	if (isset($dests[$src])) {

		// on regarde si dans les personnes concernes on a du monde dans la dest (en fait forcement)
		foreach ($tierscontacts[$src] as $c) {
			if (isset($destcontacts[$c])) {
				$e=array();
				$e['id_tiers']=$src;
				$e['id_contact']=$c;
				$e['link_level']=2;
				$lstparcours[$idparcours]['linkstiers'][]=$e;

			}
			else {
				// on regarde pour eventuellemen reli� les elements du niveau courant � au tiers
			}
		}
		$results[]=$lstparcours[$idparcours];
	}
	else {
		$copyparcours=$lstparcours[$idparcours];
		//dims_print_r($matrix_tiers[$src]);die();
		foreach ($matrix_tiers[$src] as $suiv=>$data) {

			// 1er test, verification si $src existe pas deja dans ids courant
			if ($suiv!=$src && (!isset($lstparcours[$idparcours]['ids'][$suiv]) || isset($dests[$suiv]))  && $data['nb']>0) {
				//echo $suiv." ".$data['nb']."<br>";
				if (!isset($lstparcours[$idparcours]['ids'][$suiv])) {
					$lstparcours[$nbparcours]=$copyparcours;
					$lstparcours[$nbparcours]['elements'][$lstparcours[$nbparcours]['cour']]=$suiv;
					$lstparcours[$nbparcours]['links'][$lstparcours[$nbparcours]['cour']-1]=$matrix_tiers[$src][$suiv]['links'];
					//$lstparcours[$nbparcours]['found']=false;
					// on alimente les liens
					$lstparcours[$nbparcours]['cour']++;
					$lstparcours[$nbparcours]['ids'][$suiv]=1;
				}

				if (isset($dests[$suiv])) {
					// on regarde si dans les personnes concernes on a du monde dans la dest (en fait forcement)
					if (!$lstparcours[$idparcours]['found']) {
						foreach ($tierscontacts[$suiv] as $c) {
							//if (isset($destcontacts[$c])) {
								$e=array();
								$e['id_tiers']=$suiv;
								$e['id_contact']=$c;
								$e['link_level']=2;
								$lstparcours[$idparcours]['linkstiers'][]=$e;
							//}
						}
						//echo "result ".$idparcours."<br>";
						$lstparcours[$idparcours]['found']=true;
						$results[]=$lstparcours[$idparcours];
					}
				}
				else {
					if ($first) {
						$first=false;
					}
					else {
						// on cree un parcours paralelle
						if (!$lstparcours[$idparcours]['found']) {
							//echo "para ".$idparcours."<br>";
							$nbparcours++;
							// on duplique le parcours courant
							$lstparcours[$nbparcours]=$lstparcours[$idparcours];
						}
					}
					// on appelle en recursif
					ParcoursRecursif($suiv,$distance++,$idparcours,$nbparcours,$lstparcours,$results,$matrix_tiers,$dests,$destcontacts,$tierscontacts);
				}
			}
		}
	}
}

$links=array();
$linksindirect=array();
$contacts=array();
$contactsprimary=array();
$lstct=array();
$lstctrelais=array();
$lstctcompare=array();

$lsttiers=array();
$lsttiersprimary=array();
$lsttierscompare=array();
$linktiers=array();
$linktiersindirect=array();
$linksplus=array();

$tiers=array();
$tiersprimary=array();

$iscontact=false;
$tiers_id=0;
$contact_id=0;

$lstctdest=array(); // structure de listing des destinataires

// chargement de l'id du contact ou entreprise
$xml_id=dims_load_securvalue('xml_id', dims_const::_DIMS_CHAR_INPUT, true);

if (substr($xml_id,0,3)=="ct_" || substr($xml_id,0,4)==")ct_") {
	//$xml_id=str_replace(")","",$xml_id);
	$iscontact=true;
	$contact_id=str_replace("ct_","",$xml_id);
	$lstctdest[$contact_id]=$contact_id;
}
else {
	$tiers_id=str_replace("ent_","",$xml_id);
}

// init items for increment arrays
$pos=1;
$postiers=1;

if (!isset($_SESSION['dims']['search_ct_from'])) {
	$user = new user();
	$user->open($_SESSION['dims']['userid']);
	$_SESSION['dims']['search_ct_from']=$user->fields['id_contact'];
}

// construction des espaces en partenariat
$work=new workspace();
$work->open($_SESSION['dims']['workspaceid']);
$lsttierswork=$work->getTiersFromWorkspace(dims_const::_SYSTEM_OBJECT_CONTACT,true);

// on recherche maintenant les relais repartis pour chaque workspace
$matrix_tiers = array();

foreach($lsttierswork as $i=>$d1) {
	foreach($lsttierswork as $j=>$d1) {
		if ($i!=$j) {
			$elem=array();
			$elem['nb']=0;
			$elem['links']=array();
			$elem['contacts']=array(); // 3eme dimension de la matrice
			$matrix_tiers[$i][$j]=$elem;
		}
	}
}

// on construit la base des possibles
$lstrelais=array();
$matrix_contacts = array();
$dests = array();
$destcontacts=array();

$tierscontacts=array();

foreach($lsttierswork as $w=>$data) {
	$works=new workspace();
	$works->open($data['id']);
	$elems=$works->getusers(true);

	$tierscontacts[$works->fields['id_tiers']]=$elems;
	//dims_print_r($elems);die();
	if (empty($lstctrelais)) {
		$lstctrelais=$elems;
	}
	else {
		$lstctrelais+=$elems;
	}

	foreach ($elems as $j=>$ct_id) {
		// on a des id_contacts rattach�s au workspace courant
		$matrix_contacts[$ct_id][$data['id_tiers']]=$data['id_tiers'];
	}
}

if (empty($lstctrelais)) {
	$lstctrelais[]=0;
}
// ajout des personnes non relais qui seraient en relation avec la personne recherch�e
// on alimente la structure de destination

$lstdirectusers=array();
$sql_pp = "	SELECT	id,
						id_contact1,
						id_contact2,
						link_level
				FROM	dims_mod_business_ct_link
				WHERE	(id_contact1 not in (".implode(',',$lstctrelais).") AND id_contact2= :contactid )
				OR		(id_contact2 in (".implode(',',$lstctrelais).") AND id_contact1= :contactid )
				AND		id_object = :idobject
				AND		link_level <=2
				ORDER BY time_create DESC";

$res_pp = $db->query($sql_pp, array(
	':contactid' 	=> $contact_id,
	':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
));
if ($db->numrows($res_pp)) {

	while ($f = $db->fetchrow($res_pp)) {
		if (!isset($links[$f['id']])) $links[$f['id']]=$f;

		if ($contact_id==$f['id_contact1']) {
			$lstdirectusers[$f['id_contact2']]=$f['id_contact2'];
			//$lstctdest[$f['id_contact2']]=$f['id_contact2'];

		}
		else {
			$lstdirectusers[$f['id_contact1']]=$f['id_contact1'];
			//$lstctdest[$f['id_contact1']]=$f['id_contact1'];
		}
	}
}

if (!empty($lstdirectusers)) {
	// on va regarder ceux qui ont quand meme plus qu'une liaison autre que le contact recherche
	$listrelais=implode(',',$lstctrelais);
	$list=implode(',',$lstdirectusers);
	$sql_pp = "	SELECT	id,
						id_contact1,
						id_contact2,
						link_level
				FROM	dims_mod_business_ct_link
				WHERE	(id_contact1 in (".$listrelais.") AND id_contact2 in (".$list.") and id_contact1 != :contactid )
				OR		(id_contact2 not in (".$listrelais.") AND id_contact2 in (".$list.") and id_contact2 != :contactid )
				AND		id_object = :idobject
				AND		link_level <=2
				ORDER BY time_create DESC";


	$lstdirectusers=array();
	$res_pp = $db->query($sql_pp, array(
		':contactid' 	=> $contact_id,
		':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
	));
	if ($db->numrows($res_pp)) {
			while ($f = $db->fetchrow($res_pp)) {
					if (!isset($links[$f['id']])) $links[$f['id']]=$f;

					if ($contact_id==$f['id_contact1']) {
							$lstdirectusers[$f['id_contact2']]=$f['id_contact2'];
							$lstctdest[$f['id_contact2']]=$f['id_contact2'];
							//$dests+=$matrix_contacts[$f['id_contact2']];
			//		$destcontacts[$f['id_contact2']]=$f['id_contact2'];
					}
					else {
							$lstdirectusers[$f['id_contact1']]=$f['id_contact1'];
							$lstctdest[$f['id_contact1']]=$f['id_contact1'];
							//$dests+=$matrix_contacts[$f['id_contact1']];
			//		$destcontacts[$f['id_contact1']]=$f['id_contact1'];
					}
			}
	}

}

// on va tt de suite rechercher la liste des contacts potentiels
if ($tiers_id>0) {
	// on regroupe les contacts direct
	$sql_pp = "	SELECT	id,
						id_contact1,
						id_contact2,
						link_level
				FROM	dims_mod_business_ct_link
				WHERE	(id_contact1 in (".implode(',',$lstctrelais).") AND id_contact2 in (".implode(',',$lstctdest)."))
				OR		(id_contact2 in (".implode(',',$lstctrelais).") AND id_contact1 in (".implode(',',$lstctdest)."))
				AND		id_object = :idobject
				AND		link_level <=2
				ORDER BY time_create DESC";

	$res_pp = $db->query($sql_pp, array(
		':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
	));

	if ($db->numrows($res_pp)) {
		while ($f = $db->fetchrow($res_pp)) {
			if (!isset($links[$f['id']])) $links[$f['id']]=$f;
		}

		// on unset la ref existante
		//unset($links[$contact_id]);
	}

	// on alimente la structure de destination
	$sql_pp = "	SELECT	distinct
					id_tiers,
					id_contact,
					link_level
			FROM	dims_mod_business_tiers_contact
					WHERE	id_tiers= :idtiers
					AND		link_level <=2";

	$res_pp = $db->query($sql_pp, array(
		':idtiers' 	=> $tiers_id
	));
	if ($db->numrows($res_pp)) {
		while ($f = $db->fetchrow($res_pp)) {
			//if (!isset($links[$f['id']])) $links[$f['id']]=$f;
			if (in_array($f['id_contact'],$lstctrelais)) {
				$dests+=$matrix_contacts[$f['id_contact']];
				$destcontacts[$f['id_contact']]=$f['id_contact'];
				$linktiers[$f['id_tiers']."_".$f['id_contact']]=$f;
			}
			else {
				// autre contact
				$sql_pp = "	SELECT	id,
							id_contact1,
							id_contact2,
							link_level
					FROM	dims_mod_business_ct_link
					WHERE	(id_contact1 in (".implode(',',$lstctrelais).") AND id_contact2= :contactid )
					OR		(id_contact2 in (".implode(',',$lstctrelais).") AND id_contact1= :contactid )
					AND		id_object = :idobject
					AND		link_level <=2
					ORDER BY time_create DESC";

				$res_pp = $db->query($sql_pp, array(
					':contactid' 	=> $f['id_contact'],
					':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
				));

				if ($db->numrows($res_pp)) {
					while ($z = $db->fetchrow($res_pp)) {
						if (!isset($links[$z['id']])) $links[$z['id']]=$z;
						$e=array();
						$e['id_tiers']=$tiers_id;
						$e['id_contact']=$f['id_contact'];
						$e['link_level']=2;
						$linktiers[$tiers_id."_".$f['id_contact']]=$z;

						if ($f['id_contact']==$z['id_contact1']) {
							// on prend l'autre
							//$dests+=$z['id_contact2'];
							$destcontacts[$z['id_contact2']]=$z['id_contact2'];
						}
						else {
							//$dests+=$z['id_contact1'];
							$destcontacts[$z['id_contact1']]=$z['id_contact1'];
						}
					}

					// on unset la ref existante
					//unset($links[$contact_id]);
				}
			}
		}
	}

	$lsttiers[$tiers_id]=$tiers_id;
}

// on recherche parmis les liaisons actives
$sql_pp = "	SELECT	id,
						id_contact1,
						id_contact2,
						link_level
				FROM	dims_mod_business_ct_link
				WHERE	id_contact1 in (".implode(',',$lstctrelais).")
				AND		id_contact2 in (".implode(',',$lstctrelais).")
				AND		id_object = :idobject
				AND		link_level >0
				AND		link_level <=2
				ORDER BY time_create DESC";

$res_pp = $db->query($sql_pp, array(
	':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
));
if ($db->numrows($res_pp)) {
	while ($f = $db->fetchrow($res_pp)) {
		// on alimente la matrice
		$ctfrom=$f['id_contact1'];
		$ctto=$f['id_contact2'];

		foreach ($matrix_contacts[$ctfrom] as $src) {
			foreach ($matrix_contacts[$ctto] as $dest) {
				if ($src!=$dest) {
					if ($src<$dest) {
						$key=$src."_".$dest;
					}
					else {
						$key=$dest."_".$src;
					}

					$matrix_tiers[$src][$dest]['nb']++;
					$matrix_tiers[$dest][$src]['nb']++;
					$matrix_tiers[$dest][$src]['links'][]=$f;
					$matrix_tiers[$src][$dest]['links'][]=$f;
				}
			}
		}
	}
}

$correspcontact=array();
// on alimente les liaisons entre les elements
if ($contact_id>0 || $tiers_id>0) {

	/*
	echo "<table width=\"100%\" cellpadding=\"1\">";
	$ci=0;
	foreach ($matrix_tiers as $i => $elem1) {
		if($ci==0) {
			echo "<tr style=\"background:#ABABAB;\"><td>&nbsp;</td>";
			foreach ($matrix_tiers as $ii => $t1) {
				echo "<td>".$ii."</td>";
			}
			echo "</tr>";
			$ci=1;
		}
		echo "<tr style=\"background:#ABABAB;\">";
		$ji=0;
		foreach ($matrix_tiers as $j => $elem2) {
			if ($ji==0) {
				echo "<td>".$i."</td>";
				$ji=1;
			}
			echo "<td>".$matrix_tiers[$i][$j]['nb']."</td>";
		}
		echo "</tr>";
	}
	echo "</table>";die();
	*/

	// recherche des elements permettant l'acc�s a la personne concern�e
	if ($contact_id>0) {

		// on alimente la structure de destination
		$sql_pp = "	SELECT	id,
								id_contact1,
								id_contact2,
								link_level
						FROM	dims_mod_business_ct_link
						WHERE	(id_contact1 in (".implode(',',$lstctrelais).") AND id_contact2 in (".implode(',',$lstctdest)."))
						OR		(id_contact2 in (".implode(',',$lstctrelais).") AND id_contact1 in (".implode(',',$lstctdest)."))
						AND		id_object = :idobject
						AND		link_level <=2
						ORDER BY time_create DESC";
		//echo $sql_pp;
		$res_pp = $db->query($sql_pp, array(
			':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
		));
		if ($db->numrows($res_pp)) {
			while ($f = $db->fetchrow($res_pp)) {
				if (!isset($links[$f['id']])) $links[$f['id']]=$f;

				if (isset($lstctdest[$f['id_contact1']])) {
					//$dests[$f['id_contact2']]=$f['id_contact2'];
					if (isset($matrix_contacts[$f['id_contact2']])) {
						$dests+=$matrix_contacts[$f['id_contact2']];
					}
					$destcontacts[$f['id_contact2']]=$f['id_contact2'];

					// on ajoute les liens indirects du destinataires
					//if ($lstctdest[$f['id_contact1']]!=$contact_id) {
						// on a un relai � ajouter
					$correspcontact[$f['id_contact1']][$f['id_contact2']]=$f['id_contact2'];
					//}
				}
				else {
					if (isset($matrix_contacts[$f['id_contact1']])) {
						$dests+=$matrix_contacts[$f['id_contact1']];
					}
					$destcontacts[$f['id_contact1']]=$f['id_contact1'];

					$correspcontact[$f['id_contact2']][$f['id_contact1']]=$f['id_contact1'];
				}


			}
		}
	}
	//dims_print_r($correspcontact);
	// on prepare les structures pour construire les parcours disponibles
	$lstparcours=array();
	$results=array();
	$idparcours=0; // indice courant du parcours
	$nbparcours=0; // nb total de parcours a traiter
	$pos=0;
	// on commence par alimenter plusieurs parcours pour autant de contact direct avec le d�part
	//foreach ($matrix_contacts[$_SESSION['dims']['search_ct_from']] as $src) {

	$src=$work->fields['id_tiers'];
	$lsttiers[$src]=$src;
	$f['id_tiers']=$src;
	$f['id_contact']=$_SESSION['dims']['search_ct_from'];
	$f['link_level']=2;
	$lstctcompare[$f['id_contact']]=$pos++;

	//if ($tiers_id>0) {
	if (!isset($linktiers[$src."_".$_SESSION['dims']['search_ct_from']])) {
		$linktiers[$src."_".$_SESSION['dims']['search_ct_from']]=$f;
	}

	$lstct[$_SESSION['dims']['search_ct_from']]=$_SESSION['dims']['search_ct_from'];

	// appel de la fonction recursive
	ParcoursRecursif($src,0,$idparcours,$nbparcours,$lstparcours,$results,$matrix_tiers,$dests,$destcontacts,$tierscontacts);
	//dims_print_r($results);die();
	if (!empty($results)) {
			$alltiers=array();
			// on check les entreprises pour améliorer le scoring
			foreach ($results as $result) {
				if (isset($result['linkstiers'])) {
					foreach ($result['linkstiers'] as $k=>$f) {
						$idt=$f['id_tiers'];
						if (!isset($alltiers[$idt])) {
							$alltiers[$idt]=$idt;
						}
					}
				}
			}

			//dims_print_r($alltiers);die();
		foreach ($results as $result) {

			foreach($result['elements'] as $id=>$tier) {
				if (!isset($lsttiers[$tier]) && isset($alltiers[$tier])) {
					$lsttiers[$tier]=$tier;
				}

				if ($id>0) {

					// on ajoute les liens entre les deux
					// on regarde si on connait des personnes en direct ou non = > si oui on cree le lien sur ceux sinon non
					// on fusionne les personnes en relation entre elles,
					$istierslinking=true;
					if (isset($matrix_tiers[$result['elements'][$id-1]][$tier]['links']) && !empty($matrix_tiers[$result['elements'][$id-1]][$tier]['links'])) {

						foreach ($matrix_tiers[$result['elements'][$id-1]][$tier]['links'] as $link) {
							/*if ($link['id_contact2']!=$_SESSION['dims']['search_ct_from'] &&
									$link['id_contact1']!=$_SESSION['dims']['search_ct_from']) {*/
							if (!isset($destcontacts[$link['id_contact1']]) && !isset($destcontacts[$link['id_contact2']])) {
								$istierslinking=false;
								$e=array();
								$e['id_tiers']=$result['elements'][$id-1];
								$e['link_level']=2;

								if (isset($tierscontacts[$result['elements'][$id-1]][$link['id_contact1']])) {
									$e['id_contact']=$link['id_contact2'];
									if (!isset($lstct[$e['id_contact']])) {
										$lstct[$e['id_contact']]=$e['id_contact'];
										$lstctcompare[$e['id_contact']]=$pos++;
									}
								}
								else {
									$e['id_contact']=$link['id_contact1'];
									if (!isset($lstct[$e['id_contact']])) {
										$lstct[$e['id_contact']]=$e['id_contact'];
										$lstctcompare[$e['id_contact']]=$pos++;
									}
								}

								if ($id>1 && !isset($linktiers[$e['id_tiers']."_".$e['id_contact']])) {
									$linktiers[$e['id_tiers']."_".$e['id_contact']]=$e;
								}

								//destination
								$e=array();
								$e['id_tiers']=$tier;
								$e['link_level']=2;

								if (isset($tierscontacts[$tier][$link['id_contact1']])) {
									$e['id_contact']=$link['id_contact2'];
								}
								else {
									$e['id_contact']=$link['id_contact1'];
								}

								if ($e['id_contact']!=$_SESSION['dims']['search_ct_from']) {
									if (!isset($linktiers[$e['id_tiers']."_".$e['id_contact']]))
									$linktiers[$e['id_tiers']."_".$e['id_contact']]=$e;
								}
							}
						}
					}

					/*if ($istierslinking) {
						// liaison directe entres entreprises
						$f=array();
						$f['id_tiers']=$result['elements'][$id-1];
						$f['id_tiers2']=$tier;
						$f['link_level']=2;
						if (!isset($linktiers['t'.$f['id_tiers']."_".$f['id_tiers2']]))
							$linktiers['t'.$f['id_tiers']."_".$f['id_tiers2']]=$f;
					}*/
				}

				// on regarde les liens directs pour l'instant
				foreach ($result['links'][$id] as $f) {
					if (isset($destcontacts[$f['id_contact2']]) || isset($destcontacts[$f['id_contact1']])) {
						// on ajoute un lien direct si on a la personne en ref de destination finale
						if (!isset($links[$f['id']])) $links[$f['id']]=$f;
					}
					elseif ($id==0) {
						// on vient avec la personne
						// on doit lier avec l'entite et non la personne du lien
						/*
						$e=array();
						$e['id_tiers']=$result['elements'][$id+1];
						$e['id_contact']=$_SESSION['dims']['search_ct_from'];
						$e['link_level']=2;
						if (!isset($linktiers[$e['id_tiers']."_".$e['id_contact']]))
							$linktiers[$e['id_tiers']."_".$e['id_contact']]=$e;
						 */
						$e=array();
						$e['id_contact1']=$_SESSION['dims']['search_ct_from'];
						if ($_SESSION['dims']['search_ct_from']!=$f['id_contact2'])
							$e['id_contact2']=$f['id_contact2'];
						else
							$e['id_contact2']=$f['id_contact1'];
						$e['link_level']=2;
						$links[]=$e;

						$e=array();
						$e['id_tiers']=$result['elements'][$id+1];
						if ($_SESSION['dims']['search_ct_from']!=$f['id_contact2'])
							$e['id_contact']=$f['id_contact2'];
						else
							$e['id_contact']=$f['id_contact1'];
						$e['link_level']=2;
						if (!isset($linktiers[$e['id_tiers']."_".$e['id_contact']]))
							$linktiers[$e['id_tiers']."_".$e['id_contact']]=$e;
					}
					//if (isset($destcontacts[$f['id_contact2']])) {
						// on cree le lien avec le contact2
						$e=array();
						$e['id_tiers']=$result['elements'][$id+1];
						if ($_SESSION['dims']['search_ct_from']!=$f['id_contact2'])
							$e['id_contact']=$f['id_contact2'];
						else
							$e['id_contact']=$f['id_contact1'];
						$e['link_level']=2;

						if (!isset($linktiers[$e['id_tiers']."_".$e['id_contact']]))
							$linktiers[$e['id_tiers']."_".$e['id_contact']]=$e;

						$lstct[$f['id_contact2']]=$f['id_contact2'];
						$lstctcompare[$f['id_contact2']]=$pos++;

						// test si la destination est mise + lien

						if (!isset($lstct[$contact_id])) {
							$lstct[$contact_id]=$contact_id;
							$lstctcompare[$contact_id]=$pos++;
						}

						// ajout du lien
						/*
						$e=array();
						$e['id_contact1']=$contact_id;
						$e['id_contact2']=$f['id_contact2'];
						$e['link_level']=2;
						$links[]=$e;*/
					//}
					/*elseif (isset($destcontacts[$f['id_contact1']])) {
						$e=array();
						//echo $result['elements'][$id+1];die();
						$e['id_tiers']=$result['elements'][$id+1];
						$e['id_contact']=$f['id_contact1'];
						$e['link_level']=2;
						$linktiers[]=$e;

						$lstct[$f['id_contact1']]=$f['id_contact1'];
						$lstctcompare[$f['id_contact1']]=$pos++;

						// test si la destination est mise + lien
						if (!isset($lstct[$contact_id])) {
							$lstct[$contact_id]=$contact_id;
							$lstctcompare[$contact_id]=$pos++;
						}

						// ajout du lien
						$e=array();
						$e['id_contact1']=$contact_id;
						$e['id_contact2']=$f['id_contact1'];
						$e['link_level']=2;
						$links[]=$e;
					}*/
				}
				// fin du link
			}

			// on regarde les liens finaux si il y	en a
			if (isset($result['linkstiers'])) {
				foreach ($result['linkstiers'] as $k=>$f) {
					// on cree le lien avec le contact et le tiers
					$e=array();
					$e['id_tiers']=$f['id_tiers'];
					$e['id_contact']=$f['id_contact'];
					$e['link_level']=2;

					if (!isset($linktiers[$e['id_tiers']."_".$e['id_contact']]))
						$linktiers[$e['id_tiers']."_".$e['id_contact']]=$e;

					// ajout du lien avec le contact recherche
					$e=array();

					// on a peut etre un contact indirect
					if (isset($correspcontact[$contact_id][$f['id_contact']])) {
						echo $f['id_contact']."<br>";
						$e['id_contact1']=$contact_id;
						$e['id_contact2']=$f['id_contact'];
						$e['link_level']=2;
						$links[$f['id']]=$e;
					}
					else {
						// on passe par un relai de contact
						foreach ($correspcontact as $ctid=>$contactind) {
							if (isset($contactind[$f['id_contact']])) {
								//echo "la ".$f['id_contact']."<br>";
								$e=array();
								$e['id_contact1']=$ctid;
								$e['id_contact2']=$f['id_contact'];
								$e['link_level']=2;
								$links[$f['id']]=$e;

								if (!isset($lstct[$ctid])) {
									$lstct[$ctid]=$ctid;
									$lstctcompare[$ctid]=$pos++;
								}
							}
						}

					}

					if (!isset($lstct[$f['id_contact']])) {
						$lstct[$f['id_contact']]=$f['id_contact'];
						$lstctcompare[$f['id_contact']]=$pos++;
					}
				} // fin du foreach


			}// fin du test sur linktiers

		}

		// test si la destination est mise + lien


	}
}

		if (!isset($lstct[$contact_id])) {
			$lstct[$contact_id]=$contact_id;
			$lstctcompare[$contact_id]=$pos++;
		}

// construction de la structure des personnes
if (!empty($lstct)) {
	if (empty($lstct)) $lstct[]=0;

	$sql_pp = "	SELECT	id,
						lastname,
						firstname,
						photo
				FROM	dims_mod_business_contact
				where id in (".implode(',',$lstct).")";

	$res_pp = $db->query($sql_pp);
	if ($db->numrows($res_pp)) {
		while ($f = $db->fetchrow($res_pp)) {
			$contacts[$lstctcompare[$f['id']]]=$f;
		}
	}
	ksort($contacts);
}

$tiers=array();
// construction de la structure des tiers
if (!empty($lsttiers)) {
	if (empty($lsttiers)) $lsttiers[]=0;

	$sql_pp = "	SELECT	id,
						intitule,
						photo
				FROM	dims_mod_business_tiers
				where id in (".implode(',',$lsttiers).")";

	$res_pp = $db->query($sql_pp);
	if ($db->numrows($res_pp)>0) {
		while ($f = $db->fetchrow($res_pp)) {
			$tiers[]=$f;
		}
	}
}

// generation du flux XML
$filexml=_DIMS_PATHDATA._DIMS_SEP."users"._DIMS_SEP.$_SESSION['dims']['userid'].".xml";

$content= '<?xml version="1.0" encoding="UTF-8"?><graph>';
$nbct=sizeof($contactsprimary)+sizeof($contacts);
$nbtiers=sizeof($tiersprimary)+sizeof($tiers);

if ($nbct>0 || $nbtiers>0) {
	$content.='<nodes count="'.($nbct+$nbtiers).'">';

	// on construit la balise de tableaux pour les contacts
	foreach($contacts as $k => $contact) {
		//if ($j<100) {
		if($contact['photo'] == "") {
			$photo=$dims->getProtocol().$dims->getHttpHost().'./common/img/photo_user.png';
		}
		else {
			$filephoto=DIMS_WEB_PATH.'data/photo_cts/contact_'.$contact['id'].'/photo60'.$contact['photo'].'.png';
			$photo="";
			if (file_exists($filephoto)) {
				$photo= $dims->getProtocol().$dims->getHttpHost().'/data/photo_cts/contact_'.$contact['id'].'/photo60'.$contact['photo'].'.png';
			}
			else $photo=$dims->getProtocol().$dims->getHttpHost().'./common/img/photo_user.png';
		}

		$path=str_replace('admin-light.php','admin.php',$dims->getUrlPath());
		$linkct=dims_urlencode($path.'?dims_workspaceid='.$_SESSION['dims']['workspaceid'].'&dims_desktop=block&dims_mainmenu=9&cat=0&action=301&contact_id='.$contact['id'],true);
		if ($contact['id']==$_SESSION['dims']['search_ct_from']) {
			$content.= utf8_encode('<Node id="ct_'.$contact['id'].'" firstname="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$contact['firstname']))).'" surname="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),strtoupper($contact['lastname'])))).'"
				label="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$contact['firstname']))).' '.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),strtoupper($contact['lastname'])))).'"
				bgcolor="0x9cc888" type="P" url_photo="'.$photo.'" url_file="'.$linkct.'" weight="10"/>');
		}
		elseif($contact['id']==$contact_id) {
			$content.= utf8_encode('<Node id="ct_'.$contact['id'].'" firstname="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$contact['firstname']))).'" surname="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),strtoupper($contact['lastname'])))).'"
				label="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$contact['firstname']))).' '.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),strtoupper($contact['lastname'])))).'"
				bgcolor="0xdeb45a" type="P" url_photo="'.$photo.'" url_file="'.$linkct.'" weight="10"/>');
		}
		else {
			$content.= utf8_encode('<Node id="ct_'.$contact['id'].'" firstname="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$contact['firstname']))).'" surname="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),strtoupper($contact['lastname'])))).'"
				label="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$contact['firstname']))).' '.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),strtoupper($contact['lastname'])))).'"
				bgcolor="0x555555" type="P" url_photo="'.$photo.'" url_file="'.$linkct.'" weight="10"/>');
		}
	}

	// on construit la balise de tableaux pour les tiers
	foreach($tiers as $k => $t) {
		if($t['photo'] == "") {
			$photo=$dims->getProtocol().$dims->getHttpHost().'./common/img/photo_ent.png';
		}
		else {
			$filephoto=DIMS_WEB_PATH.'data/photo_ent/ent_'.$t['id'].'/photo100'.$t['photo'].'.png';
			$photo="";
			if (file_exists($filephoto)) {
				$photo= $dims->getProtocol().$dims->getHttpHost().'/data/photo_ent/ent_'.$t['id'].'/photo100'.$t['photo'].'.png';
			}
			else $photo=$dims->getProtocol().$dims->getHttpHost().'./common/img/photo_ent.png';
		}
		$path=str_replace('admin-light.php','admin.php',$dims->getUrlPath());
		$linkt=dims_urlencode($path.'?dims_workspaceid='.$_SESSION['dims']['workspaceid'].'&dims_desktop=block&dims_mainmenu=9&cat=0&action=401&part=402&id_ent='.$t['id'],true);

		$content.= utf8_encode('<Node id="ent_'.$t['id'].'" firstname="" surname="" label="'.addslashes(dims_convertaccents( str_replace(array('&','"'), array('and',''),$t['intitule']))).'"
				bgcolor="0x515b9" type="T" url_photo="'.$photo.'" url_file="'.$linkt.'" weight="10"/>');

	}
	$content.='</nodes>';
}

// on construit les links
$nblink=sizeof($links);
$nblinktiers=sizeof($linktiers);
if ($nblink>0 || $nblinktiers>0) {
	$content.='<edges count="'.($nblink+$nblinktiers).'">';

	foreach($links as $l => $link) {
		//if ($link['id_contact1']==$contact_id || $link['id_contact2']==$contact_id) {
			$type=$link['link_level'];
			$content.= utf8_encode('<Edge  source-node="ct_'.$link['id_contact1'].'" target-node="ct_'.$link['id_contact2'].'" type="'.$type.'" weight="3" alpha="100" size="5" url=""/>');
		//}
	}

	foreach($linktiers as $l => $link) {
		//if ($link['id_contact']==$contact_id) {
		if (isset($link['id_contact'])) {
			$type=$link['link_level'];
			$content.= utf8_encode('<Edge  source-node="ent_'.$link['id_tiers'].'" target-node="ct_'.$link['id_contact'].'" type="1" weight="2" alpha="50" size="5" url=""/>');
		}
		else {
			$type=$link['link_level'];
			$content.= utf8_encode('<Edge  source-node="ent_'.$link['id_tiers'].'" target-node="ent_'.$link['id_tiers2'].'" type="1" weight="1" alpha="50" size="10" url=""/>');
		}

		//}
	}
	/*
	foreach($links as $l => $link) {
		if ($link['id_contact1']!=$id_contact && $link['id_contact2']!=$id_contact) {
			$type=$link['link_level'];
			$content.= utf8_encode('<Edge  source-node="ct_'.$link['id_contact1'].'" target-node="ct_'.$link['id_contact2'].'" type="'.$type.'" weight="5" size="5" url=""/>');
		}
	}*/
/*
	foreach($linktiers as $l => $link) {
		if ($link['id_contact']!=$id_contact) {
			$type=$link['link_level'];
			$content.= utf8_encode('<Edge  source-node="ent_'.$link['id_tiers'].'" target-node="ct_'.$link['id_contact'].'" type="'.$type.'" weight="5" size="5" url=""/>');
		}
	}

	foreach($linksindirect as $l => $link) {
		$type=$link['link_level'];
		$content.= utf8_encode('<Edge  source-node="ct_'.$link['id_contact1'].'" target-node="ct_'.$link['id_contact2'].'" type="'.$type.'" weight="5" size="5" url=""/>');
	}
*/
	$content.='</edges>';
}
$content.='</graph>';

// write xml file
file_put_contents($filexml, $content);

ob_end_clean();
header("Content-type: text/xml");
echo $content;
die();

?>
