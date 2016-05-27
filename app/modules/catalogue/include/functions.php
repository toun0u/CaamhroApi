<?php
//fonction de generation des menus avec smarty
function smarty_catalogue_template_assign($smarty, &$smarty_famille, &$familles, &$nav, $hid, $var = '', $link = '', $rootpath = '', $web_root_path = '') {
	global $recursive_mode;
	global $wce_mode;
	global $scriptenv;

	if (isset($familles['tree'][$hid])) {
		if (isset($familles['list'][$hid])) {
			if ($familles['list'][$hid]['depth'] == 0) $localvar = "sw_root{$familles['list'][$hid]['position']}";
			else $localvar = "{$var}sw_heading{$familles['list'][$hid]['depth']}";
		}
		$selprec = 0;

		$nbFam = count($familles['tree'][$hid]);
		$i = 0;

		foreach($familles['tree'][$hid] as $id) {
			$i++;
			$detail = $familles['list'][$id];

			$depth = $detail['depth'] - 1;
			if ($depth == 0) {
				$localvar = "cata{$detail['position']}";
				$rootpath = $localvar;
			}
			else {
				$localvar = "famille{$depth}";
			}
			$locallink = ($link != '') ? "{$link}-{$id}" : "{$id}";

			switch($wce_mode) {
				case 'display';
				default:
					// test if url rewrite activated
					if ($detail['urlrewrite'] != '') {
						// Gestion du lien vers un article WCE
						if ($detail['id_article_wce'] > 0) {
							$script = '/'.$detail['urlrewrite_article'].".html?".http_build_query(array('catafam'=>'/'.$detail['urlrewrite']));
						}
						else {
							$script = $web_root_path.'/'.$detail['urlrewrite']; //.".html";
						}
					} else {
						//$script = $web_root_path.'/catalogue/'.str_replace(' ', '_', $detail['label']).'/'.$detail['id_famille'];
						$script = '?op=catalogue&param='.$detail['id_famille'];
					}
				break;
			}

			$sel = '';

			if (isset($nav[$depth]) && $nav[$depth] == $id) {
				$tpl_path = array(
					'DEPTH' => $depth,
					'LABEL' => $detail['label'],
					'LINK' => $script
					);

				$smarty->assign("path",$tpl_path);
				$tpl_headcur = array(
					'TITLE'         => $detail['label'],
					'ID_FAMILLE'    => $id,
					'POSITION'      => $detail['position'],
					'COLOR'         => $detail['color'],
					'COLOR2'        => $detail['color2'],
					'COLOR3'        => $detail['color3'],
					'COLOR4'        => $detail['color4'],
					'DESCRIPTION'   => isset($detail['description']) ? $detail['description'] : '');

				$smarty->assign("HEADING{$depth}",$tpl_headcur);

				$sel = 'selected';
				$selprec=$id;
			}

			if ($detail['visible']) {
				if (!empty($detail['url'])) $script = $detail['url'];
				if ($depth > 0) {
					if ($selprec>0 && $selprec!=$detail['id']) {
						// on a le suivant
						$detail['selprec']="selected";
						$selprec=0;
					}
					else $detail['selprec']="";

					$smarty_famille[$localvar][$detail['id']]=array(
					'DEPTH'         => $depth,
					'ID_FAMILLE'    => $detail['id'],
					'LABEL'         => $detail['label'],
					'POSITION'      => $detail['position'],
					'IMAGE' 	    => $detail['image'],
					'LINK'          => $script,
					'SEL'           => $sel,
					'COLOR'         => $detail['color'],
					'COLOR2'        => $detail['color2'],
					'COLOR3'        => $detail['color3'],
					'COLOR4'        => $detail['color4'],
					'SELPREC'       => $detail['selprec'],
					'ISLAST'		=> $nbFam==$i,
					);

					if ($sel=="selected") {
						$smarty_famille['SELECTEDHEADING']= array(
						'DEPTH'         => $depth,
						'ID_FAMILLE'    => $detail['id'],
						'LABEL'         => $detail['label'],
						'POSITION'      => $detail['position'],
						'IMAGE' 	    => $detail['image'],
						'COLOR'         => $detail['color'],
						'COLOR2'        => $detail['color2'],
						'COLOR3'        => $detail['color3'],
						'COLOR4'        => $detail['color4'],
						'LINK'          => $script
						);

					}
				}
				else {
					$smarty_famille[$localvar]=array(
					'DEPTH'         => $depth,
					'ID_FAMILLE'    => $detail['id'],
					'LABEL'         => $detail['label'],
					'POSITION'      => $detail['position'],
					'IMAGE' 	    => $detail['image'],
					'COLOR'         => $detail['color'],
					'COLOR2'        => $detail['color2'],
					'COLOR3'        => $detail['color3'],
					'COLOR4'        => $detail['color4'],
					'LINK'          => $script,
					'SEL'           => $sel,
					'ISLAST'		=> $nbFam==$i,
					);
				}

				if ($depth == 0 || (isset($recursive_mode[$depth]) && $recursive_mode[$depth] == 'prof')) {
					if (isset($familles['tree'][$id])) {
						if ($depth > 0) {
							smarty_catalogue_template_assign($smarty, $smarty_famille[$localvar][$detail['id']], $familles, $nav, $id, "{$localvar}.", $locallink, $rootpath, $web_root_path);
						}
						else {
							smarty_catalogue_template_assign($smarty, $smarty_famille[$localvar], $familles, $nav, $id, "{$localvar}.", $locallink, $rootpath, $web_root_path);
						}
					}
				}
			}
		}

		if (isset($familles['list'][$hid])) {
			$depth = $familles['list'][$hid]['depth'];
			if ($depth > 0  && isset($nav[$depth-1]) && $nav[$depth-1] == $hid && !(isset($recursive_mode[$depth]) && $recursive_mode[$depth] == 'prof'))
			{
				if ($link!='' && isset($nav[$depth])) $link .= "-$nav[$depth]";
				elseif (isset($nav[$depth])) $link = "$nav[$depth]";

				if (isset($nav[$depth]) && isset($familles['tree'][$nav[$depth]])) smarty_catalogue_template_assign($smarty,$smarty_famille,$familles, $nav, $nav[$depth], '', $link,$rootpath);
			}
		}

	}
}

function catalogue_logoproduit($detail) {
	$logo = (isset($detail['marque_logo']) && $detail['marque_logo'] != '' && file_exists("./photos/{$detail['marque_logo']}")) ? "<img src=\"./modules/catalogue/miniature.php?ref=".substr($detail['marque_logo'],0,-4)."&size=150\" alt=\"{$detail['marque']}\">" : '';
	return($logo);
}

function catalogue_detailproduit($detail, $familys = array(), $showStock = true) {
	return '';

	// global $oCatalogue;
	// $detail_produit = '';
	// $description = '';
	// $detail1 = '';
	// $detail2 = '';
	// $detail3 = '';

	// if ($detail['detail1'] != '') $detail1 = "<u>". catalogue_cleanstring($detail['detail1']) ."</u>&nbsp;:&nbsp;";
	// if ($detail['libelle1']!='') $detail1 .= catalogue_cleanstring($detail['libelle1']);

	// if ($detail['detail2'] != '') $detail2 = "<u>". catalogue_cleanstring($detail['detail2']) ."</u>&nbsp;:&nbsp;";
	// if ($detail['libelle2']!='') $detail2 .= $detail['libelle2'];

	// if ($detail['detail3'] != '') $detail3 = "<u>". catalogue_cleanstring($detail['detail3']) ."</u>&nbsp;:&nbsp;";
	// if ($detail['libelle3']!='') $detail3 .= catalogue_cleanstring($detail['libelle3']);

	// if ($description != '') $detail_produit = catalogue_cleanstring($description) ."<br/>";

	// if ($detail['description'] != '') {
	//     $description = str_replace('','<br/>',$detail['description']);
	//     if (substr($description,0,1) == '#') $description = '';
	// }

	// $detail_produit .= $detail1;
	// if ($detail_produit != '') $detail_produit .= '<br/>';
	// if ($detail2 != '') $detail_produit .= $detail2;
	// if ($detail_produit != '' && $detail2 != '' && $detail3 != '') $detail_produit .= '<br/>';
	// if ($detail3 != '') $detail_produit .= $detail3;


	// if ($detail['marqueLibelle'] != '') $detail_produit .= "<br/><u>Marque</u>&nbsp;:&nbsp;". $detail['marqueLibelle'];
	// $detail_produit = str_replace('<br/><br/>','<br/>', $detail_produit);

	// if ($detail['stock'] != '' && $oCatalogue->getParams('cata_show_stocks') && $_SESSION['session_connected']) {
	//     if($detail['stock'] < 0) $detail['stock'] = 0;
	//     if ($showStock) {
	//         if ($detail['stock'] <= 0 && $detail['page'] > 0 && $detail['page'] <= 700) {
	//             $detail_produit .= "<br/><b><u>Stock</u>&nbsp;:&nbsp; Disponible sous 48 heures</b>";
	//         } else {
	//             $detail_produit .= "<br/><b><u>Stock</u>&nbsp;:&nbsp; ". $detail['stock'] ."</b>";
	//         }
	//         if (isBlandan($detail['PREF'])) {
	//             $detail_produit .= '<br/>Ce produit est disponible au magasin situ&eacute; au 99 rue du Sergent Blandan &agrave; Nancy,<br/>Il peut vous &ecirc;tre livr&eacute; sous 72h.';
	//         }
	//     }
	// }

	// if ($detail['accroche'] != '') $detail_produit .= '<br/><br/><a class="accroche">'. $detail['accroche'] .'</a>';

	// if ($detail['page'] > 0) {
	//     if ($detail['page'] <= 700) {
	//         $detail_produit .= "<br/><br/>Page ". $detail['page'] ." du catalogue g&eacute;n&eacute;ral";
	//     } else {
	//         $detail_produit .= "<br/><br/>Absent du catalogue g&eacute;n&eacute;ral";
	//     }
	// }
	// if ($detail['page_scol'] > 0) {
	//     if ($detail['page'] <= 228) {
	//         $detail_produit .= "<br/>Page ". $detail['page_scol'] ." du catalogue scolaire";
	//     } else {
	//         $detail_produit .= "<br/>Absent du catalogue scolaire";
	//     }
	// }

	// if (sizeof($detail['fams']) && sizeof($familys)) {
	//     if (sizeof($detail['fams']) > 1) {
	//         $detail_produit .= '<br/><br/><u>Cet article est disponible dans les rubriques suivantes :</u>';
	//     } else {
	//         $detail_produit .= '<br/><br/><u>Cet article est disponible dans la rubrique suivante :</u>';
	//     }

	//     foreach ($detail['fams'] as $famId) {
	//         $detail_produit .= '<br/>';
	//         foreach (explode(';', $familys['list'][$famId]['parents'].';'.$famId) as $idParent) {
	//             if ($idParent > 1) {
	//                 if ($familys['list'][$idParent]['depth'] > 2) {
	//                     $detail_produit .= '&nbsp;'.chr(187).'&nbsp;<a href="catalogue_'.cata_makeLinkLabel($familys['list'][$idParent]['label']).'_rub'.$idParent.'.html">'.$familys['list'][$idParent]['label'].'</a>';
	//                 } else {
	//                     $detail_produit .= $familys['list'][$idParent]['label'];
	//                 }
	//             }
	//         }
	//     }
	// }

	// if ($description != '') $detail_produit .= '<br/><br/>'. $description;

	// return($detail_produit);
}


function catalogue_getresizecoef($imagefile, $wmax = 0, $hmax = 0) {
	$filename_array = explode('.',$imagefile);
	$extension = strtolower($filename_array[sizeof($filename_array)-1]);

	switch ($extension) {
		case 'jpg':
		case 'jpeg':
			$imgsrc = ImageCreateFromJPEG($imagefile);
			break;
		case 'png':
			$imgsrc = ImageCreateFromPng($imagefile);
			break;
		case 'gif':
			$imgsrc = imagecreatefromgif($imagefile);
			break;
		default:
			return(0);
			break;
	}

	$w = imagesx($imgsrc);
	$h = imagesy($imgsrc);

	$coef = 0;

	if ($wmax) $coef = $w/$wmax;
	elseif ($hmax && $hmax/$w > $coef) $coef = $w/$hmax;

	return($coef);
}

function catalogue_makebutton($val, $link, $width, $confirm = false) {
	if ($confirm) $link = "if (confirm('Confirmez-vous l\'opération \'".addslashes($val)."\'')) ".$link;

	//$res = '<button type="button" onclick="javascript: '.$link.'">'.$val.'</button>';
	$res = '<div class="buttons"><a href="javascript: '.$link.'">'.$val.'</a></div>';

	return $res;
}

function catalogue_makegfxbutton($val, $img, $link, $width, $confirm = false, $class = '') {
	if ($confirm) $link = "if (confirm('Confirmez-vous l\'opération \'".addslashes($val)."\'')) ".$link;

	$res = '<div class="buttons"><a';
	if ($class !='') $res .= ' class="'.$class.'"';
	$res .= ' href="javascript: '.$link.'">'.$val.' '.$img.'</a></div>';

	return $res;
}

function catalogue_makeinvalidbutton($val, $img, $width) {
	$res = '<div class="buttons"><a style="filter:alpha(opacity:50);-moz-opacity:0.5;opacity:0.5;">'.$val.' '.$img.'</a></div>';
	// $res = '<div class="buttons"><a href="javascript: '.$link.'" style="filter:alpha(opacity:50);-moz-opacity:0.5;opacity:0.5;">'.$val.' '.$img.'</a></div>';

	return $res;
}

function catalogue_formateprix($prix) {
	return(number_format(round($prix, 2), 2, ',', ' '));
}

function catalogue_afficherprix($prix, $taux_tva = 20) {
	if ($_SESSION['catalogue']['ttc']) return $prix * (1 + ($taux_tva / 100));
	else return $prix;
}

function catalogue_makelink($scriptenv, $label, $order, $reverse, $sel_order, $op = 'catalogue', $params = '') {
	$link = "<a class=\"titre_panier\" href=\"$scriptenv?op=$op";
	if (trim($params) != '') $link .= $params;
	$link .= "&order=$order";

	// On compare les anciens op / order / reverse pour savoir si on retourne ou non
	if (isset($_SESSION['catalogue']['oporder'])) {
		if ($_SESSION['catalogue']['oporder']['op'] ==  $op && $_SESSION['catalogue']['oporder']['order'] ==  $order) $reverse = !$reverse;
		else $reverse = 0;
	}
	if ($reverse) $link .= "&reverse=$reverse";
	else $reverse = 0;

	($sel_order == $order) ? $img = " (<img src=\"./modules/catalogue/img/fleche{$reverse}.gif\" alt=\"\" border=\"0\">)" : $img = "";
	$link .= "\">{$label}{$img}</a>";

	return $link;
}

function catalogue_getbudget() {
	global $db;
	// On va chercher id_client
	//die($_SESSION['catalogue']['root_group']);
	$sql = "
		SELECT c.*, d.limite_budget
		FROM dims_mod_vpc_client c, dims_mod_vpc_client_detail d
		WHERE d.id_group = ".$_SESSION['catalogue']['root_group']."
		AND c.CREF = d.CREF";
	$db->query($sql);
	$row = $db->fetchrow();
	$_SESSION['catalogue']['client'] = $row['CREF'];
	$_SESSION['catalogue']['chan'] = $row['CCHAN']; // code client regroupé
	$_SESSION['catalogue']['tar'] = $row['CCODTAR']; // code client regroupé
	($row['CVARB1']) ? $_SESSION['catalogue']['varb1'] = $row['CVARB1'] : $_SESSION['catalogue']['varb1'] = -1; // code client regroupé

	if ($row['limite_budget'] == 1 || is_null($row['limite_budget'])) { // budget limité
		// On regarde si l'utilisateur a un budget personnel

		// Si un budget est défini pour l'utilisateur
		$user = new user();
		$user->open($_SESSION['dims']['userid']);
		if ($user->fields['limite_budget'] == 1) {
			$sql = "
				SELECT *
				FROM dims_mod_vpc_user_budget
				WHERE id_user = {$_SESSION['dims']['userid']}
				AND en_cours = 1";
			$db->query($sql);
			if ($row = $db->fetchrow()) {
				$_SESSION['catalogue']['budget']['val'] = $row['valeur'];
				$_SESSION['catalogue']['budget']['code'] = $row['code'];
				$_SESSION['catalogue']['budget']['id'] = $row['id'];
				$_SESSION['catalogue']['budget']['credit'] = $_SESSION['catalogue']['budget']['val'] - getminimummoney_user($_SESSION['dims']['userid']);
			} else {
				$_SESSION['catalogue']['budget']['val'] = 0;
				$_SESSION['catalogue']['budget']['code'] = '';
				$_SESSION['catalogue']['budget']['id'] = '';
				$_SESSION['catalogue']['budget']['credit'] = 0;
			}
		}

		// Sinon, on regarde si un budget est défini pour le service
		else {
			$sql = "
				SELECT *
				FROM dims_mod_vpc_budget
				WHERE id_group = {$_SESSION['catalogue']['groupid']}
				AND en_cours = 1";
			$db->query($sql);
			if ($row = $db->fetchrow()) {
				$_SESSION['catalogue']['budget']['val'] = $row['valeur'];
				$_SESSION['catalogue']['budget']['code'] = $row['code'];
				$_SESSION['catalogue']['budget']['id'] = $row['id'];
				$_SESSION['catalogue']['budget']['credit'] = $_SESSION['catalogue']['budget']['val'] - getminimummoney($_SESSION['catalogue']['groupid']);
			} else {
				$_SESSION['catalogue']['budget']['val'] = 0;
				$_SESSION['catalogue']['budget']['code'] = '';
				$_SESSION['catalogue']['budget']['id'] = '';
				$_SESSION['catalogue']['budget']['credit'] = 0;
			}
		}
	} else { // pas de limite
		$_SESSION['catalogue']['budget']['val'] = -1;
		$_SESSION['catalogue']['budget']['code'] = '';
		$_SESSION['catalogue']['budget']['id'] = '';
		$_SESSION['catalogue']['budget']['credit'] = -1;
	}
}


function catalogue_getbudget_user($userid = -1, $groupid = -1) {
	$budget = array();

	if ($userid != -1) {
		global $db;

		// On va chercher id_client
		$sql = "
			SELECT c.CREF, c.CCHAN, c.CCODTAR, d.limite_budget
			FROM dims_mod_vpc_client c, dims_mod_vpc_client_detail d
			WHERE d.id_group = {$_SESSION['catalogue']['root_group']}
			AND c.CREF = d.CREF";
		$db->query($sql);
		$row = $db->fetchrow();
		$_SESSION['catalogue']['client'] = $row['CREF'];
		$_SESSION['catalogue']['chan'] = $row['CCHAN']; // code client regroupé
		$_SESSION['catalogue']['tar'] = $row['CCODTAR']; // code client regroupé

		if ($row['limite_budget'] == 1 || is_null($row['limite_budget'])) { // budget limité
			// Si un budget est défini pour l'utilisateur
			$user = new user();
			$user->open($userid);
			if ($user->fields['limite_budget'] == 1) {
				$sql = "
					SELECT *
					FROM dims_mod_vpc_user_budget
					WHERE id_user = $userid
					AND en_cours = 1";
				$db->query($sql);
				if ($row = $db->fetchrow()) {
					$budget['val'] = $row['valeur'];
					$budget['code'] = $row['code'];
					$budget['id'] = $row['id'];
					$budget['credit'] = $budget['val'] - getminimummoney_user($userid);
				} else {
					$budget['val'] = 0;
					$budget['code'] = '';
					$budget['id'] = '';
					$budget['credit'] = 0;
				}
			}

			// Sinon, on regarde si un budget est défini pour le service
			else {
				$sql = "
					SELECT *
					FROM dims_mod_vpc_budget b
					WHERE b.id_group = $groupid
					AND en_cours = 1";
				$db->query($sql);
				if ($row = $db->fetchrow()) {
					$budget['val'] = $row['valeur'];
					$budget['code'] = $row['code'];
					$budget['id'] = $row['id'];
					$budget['credit'] = $budget['val'] - getminimummoney($groupid);
				} else {
					$budget['val'] = 0;
					$budget['code'] = '';
					$budget['id'] = '';
					$budget['credit'] = 0;
				}
			}
		} else { // pas de limite
			$budget['val'] = -1;
			$budget['code'] = '';
			$budget['id'] = '';
			$budget['credit'] = -1;
		}
	} else {
		$budget['val'] = 0;
		$budget['code'] = '';
		$budget['id'] = '';
		$budget['credit'] = 0;
	}

	return $budget;
}


// ADMIN
function getavailablemoney($id_group) {
	/*
	* Le budget max qu'on peut affecter à un groupe est le budget du groupe parent
	* - somme des commandes du groupe parent
	* - la somme des budgets distribués aux groupes frères
	* - la somme des budgets affectés aux utilisateurs du groupe
	*/

	global $db;

	$group = new group();
	$group->open($id_group);

	$parents = explode(';',$group->fields['parents']);
	$parent = $parents[count($parents)-1];

	$sql = "
		SELECT *
		FROM dims_mod_vpc_budget
		WHERE id_group = $parent
		AND id_client = '{$_SESSION['catalogue']['client']}'
		AND en_cours = 1";
	$db->query($sql);
	$fields = $db->fetchrow();

	// Somme de départ
	$money = $fields['valeur'];

	if ($_SESSION['catalogue']['limite_budget'] == 1) {
		// Somme des commandes du groupe parent
		$sql = "
			SELECT c.total_ht
			FROM dims_mod_vpc_cmd c,dims_mod_vpc_budget b
			WHERE c.id_group = $parent
			AND c.ref_client = '{$_SESSION['catalogue']['client']}'
			AND c.etat = 'validee'
			AND c.id_budget = b.id
			AND b.id_client = '{$_SESSION['catalogue']['client']}'
			AND b.en_cours = 1";
		$db->query($sql);
		while($fields = $db->fetchrow()) {
			$money -= $fields['total_ht'];
		}
	}

	// Somme des budgets affectés aux groupes frères
	$brothers = implode(',',$group->getgroupbrotherslite());

	if ($brothers) {
		$sql = "
			SELECT *
			FROM dims_mod_vpc_budget
			WHERE id_client = '{$_SESSION['catalogue']['client']}'
			AND id_group IN ($brothers)
			AND en_cours = 1";
		$db->query($sql);
		while($fields = $db->fetchrow()) {
			$money -= $fields['valeur'];
		}
	}

	// Somme des budgets affectés aux utilisateurs du groupe parent
	$sql = "
		SELECT ub.valeur
		FROM dims_group_user gu, dims_mod_vpc_user_budget ub
		WHERE gu.id_group = $parent
		AND gu.id_user = ub.id_user
		AND ub.en_cours = 1";
	$db->query($sql);
	while ($fields = $db->fetchrow()) {
		$money -= $fields['valeur'];
	}

	return $money;
}

function getminimummoney($id_group) {
	/*
	* La somme minimum à affecter est la somme déjà affectée par le groupe
	* + la somme des commandes déjà passées
	* + la somme des budgets affectés aux utilisateurs du groupe
	*/

	global $db;

	$money = 0;

	$group = new group();
	$group->open($id_group);

	$childs = implode(',',$group->getgroupchildrenlite(1));

	// Somme des budgets répartis dans les sous-groupes directs
	if ($childs) {
		$sql = "
			SELECT *
			FROM dims_mod_vpc_budget
			WHERE id_group IN ($childs)
			AND id_client = '{$_SESSION['catalogue']['client']}'
			AND en_cours = 1";
		$db->query($sql);
		while ($fields = $db->fetchrow()) {
			$money += $fields['valeur'];
		}
	}

	if ($_SESSION['catalogue']['limite_budget'] == 1) {
		// Somme des commandes sur le budget
		$sql = "
			SELECT c.total_ht
			FROM dims_mod_vpc_cmd c,dims_mod_vpc_budget b
			WHERE c.id_group = $id_group
			AND c.ref_client = '{$_SESSION['catalogue']['client']}'
			AND c.etat = 'validee'
			AND c.id_budget = b.id
			AND b.id_client = '{$_SESSION['catalogue']['client']}'
			AND b.en_cours = 1";
		$db->query($sql);
		while ($fields = $db->fetchrow()) {
			$money += $fields['total_ht'];
		}
	}

	// Somme des budgets affectés aux utilisateurs
	$sql = "
		SELECT ub.valeur
		FROM dims_group_user gu, dims_mod_vpc_user_budget ub
		WHERE gu.id_group = $id_group
		AND gu.id_user = ub.id_user
		AND ub.en_cours = 1";
	$db->query($sql);
	while ($fields = $db->fetchrow()) {
		$money += $fields['valeur'];
	}
	return $money;
}

function getrealbudget($id_group) {
	global $db;
	$sql = "
		SELECT valeur
		FROM dims_mod_vpc_budget
		WHERE id_group = $id_group
		AND id_client = '{$_SESSION['catalogue']['client']}'
		AND en_cours = 1";
	$db->query($sql);
	$row = $db->fetchrow();

	$money = $row['valeur'];

	$group = new group();
	$group->open($id_group);

	$childs = implode(',',$group->getgroupchildrenlite(1));

	// Somme des budgets répartis dans les sous-groupes directs
	if ($childs) {
		$sql = "
			SELECT *
			FROM dims_mod_vpc_budget
			WHERE id_group IN ($childs)
			AND id_client = '{$_SESSION['catalogue']['client']}'
			AND en_cours = 1";
		$db->query($sql);
		while ($fields = $db->fetchrow()) {
			$money -= $fields['valeur'];
		}
	}

	// Somme des budgets affectés aux utilisateurs
	$sql = "
		SELECT ub.valeur
		FROM dims_group_user gu, dims_mod_vpc_user_budget ub
		WHERE gu.id_group = $id_group
		AND gu.id_user = ub.id_user
		AND ub.en_cours = 1";
	$db->query($sql);
	while ($fields = $db->fetchrow()) {
		$money -= $fields['valeur'];
	}

	return $money;
}

function getminimummoney_user($id_user) {
	/*
	* La somme minimum à affecter est la somme des commandes déjà passées
	*/

	global $db;

	$money = 0;

	include_once $_SERVER['DOCUMENT_ROOT'].'modules/system/class_user.php';
	$user = new user();
	$user->open($id_user);
	if ($user->fields['limite_budget'] == 1) {
		// Somme des commandes sur le budget
		$sql = "
			SELECT c.total_ht
			FROM dims_mod_vpc_cmd c, dims_group_user g, dims_mod_vpc_user_budget b
			WHERE g.id_user = $id_user
			AND g.id_group = c.id_group
			AND c.etat = 'validee'
			AND c.id_budget = b.id
			AND b.id_user = g.id_user
			AND b.en_cours = 1";
		$db->query($sql);
		while ($fields = $db->fetchrow()) {
			$money += $fields['total_ht'];
		}
	}

	return $money;
}

function catalogue_cleanstring($str) {
	global $specialchars;
	return(strtr($str,$specialchars,"                               "));
	return($str);
}

function catalogue_budgetlog($id_budget, $id_group, $id_action, $code = "", $valeur = "") {
	if (!is_null($id_budget) && !is_null($id_group) && !is_null($id_action)) {
		$actions = array(
		1 => "Création",
		2 => "Modification",
		3 => "Affectation",
		4 => "Cloture",
		5 => "Reconduction automatique",
		6 => "Budget rectifié"
		);

		($id_action == 5) ? $timestamp = date("Ymd") ."000000" : $timestamp = dims_createtimestamp();

		include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/include/class_budget_log.php';
		$blog = new budget_log();
		$blog->fields['id_budget'] = $id_budget;
		$blog->fields['user_name'] = $_SESSION['catalogue']['client_firstname'] ." ". $_SESSION['catalogue']['client_lastname'];
		$blog->fields['id_group'] = $id_group;
		$blog->fields['action'] = $actions[$id_action];
		$blog->fields['code'] = $code;
		$blog->fields['valeur'] = $valeur;
		$blog->fields['non_bloquant'] = $_SESSION['catalogue']['budget_non_bloquant'];
		$blog->fields['timestp'] = $timestamp;
		$blog->save();
	}
}

function catalogue_getallrubs($scolaire = false) {
	global $db;

	$sc = ($scolaire) ? '_sc' : '';

	$a_rubs = array();
	for ($level = 0; $level <= 3; $level++) {
		$db->query("SELECT * FROM dims_mod_vpc_rub{$level}{$sc} ORDER BY libelle");
		while ($row = $db->fetchrow()) {
			if (trim($row['libelle']) != '') {
				$rub['id'] = $level.sprintf("%07s", $row['id']);
				if ($row['id_rub0'] != '') $rub['id_rub0'] = '0'.sprintf("%07s", $row['id_rub0']);
				if ($row['id_rub1'] != '') $rub['id_rub1'] = '1'.sprintf("%07s", $row['id_rub1']);
				if ($row['id_rub2'] != '') $rub['id_rub2'] = '2'.sprintf("%07s", $row['id_rub2']);
				if ($row['id_rub3'] != '') $rub['id_rub3'] = '3'.sprintf("%07s", $row['id_rub3']);

				// id_parent
				if ($row['id_rub'.($level-1)] != '') $id_parent = $rub['id_parent'] = ($level-1).sprintf("%07s", $row['id_rub'.($level-1)]);
				// parents
				$rub['parents'] = (isset($rub['id_parent'])) ? $rub['id_parent'] : '';
				while (isset($a_rubs['list'][$id_parent]['id_parent'])) {
					$rub['parents'] = "{$a_rubs['list'][$id_parent]['id_parent']};{$rub['parents']}";
					$id_parent = $a_rubs['list'][$id_parent]['id_parent'];
				}
				// depth
				$rub['depth'] = $level + 1;
				// label
				$rub['label'] = ucfirst(strtolower($row['libelle']));

				$a_rubs['list'][$rub['id']] = $rub;
				$a_rubs['tree'][$rub["id_rub".($level-1)]][] = $rub['id'];
			}
		}
	}

	// On vide les enfants de la rubrique coupée pour être sûr de pas la déplacer dans un de ses enfants
//  if (!empty($_SESSION['catalogue']['rubcut_id']) && isset($a_rubs['tree'][$_SESSION['catalogue']['rubcut_id']])) unset($a_rubs['tree'][$_SESSION['catalogue']['rubcut_id']]);

	return $a_rubs;
}

function catalogue_buildtree($a_rubs, $fromrid = '', $str = '') {
	global $rubid;
	global $scriptenv;

	// si on passe par une fontion ajax, completer le scriptenv
	if (!strstr($scriptenv, '?')) $scriptenv.= '?dims_moduleid='._MODULEID_CATALOGUE;

	$rubsel = $a_rubs['list'][$rubid];

	$html = '';

	if (isset($a_rubs['tree'][$fromrid])) {
		$c = 0;
		foreach ($a_rubs['tree'][$fromrid] as $rid) {
			$rub = $a_rubs['list'][$rid];
			$isrubsel = ($rubid == $rid);
			$isrubopened = strstr($rubsel['parents'], "{$rub['id_parent']};{$rub['id']}");
			$islast = ($c == sizeof($a_rubs['tree'][$fromrid])-1);

			$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/empty.gif\"></div>", $str);
			$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/line.gif\"></div>", $decalage);

			$numrub = '';
			if ($rub['parents'] != '') {
				foreach (array_merge(explode(';', $rub['parents']), array($rid)) as $parent) {
					if ($numrub != '') $numrub .= '.';

					$parent = substr($parent, 1);

					if (strstr($parent, 'S')) {
						$parent = str_replace('S', '', $parent);
						$numrub .= 'S'.intval($parent);
					} else {
						$numrub .= intval($parent);
					}
				}
			}
			if ($numrub != '') $numrub = "($numrub) ";


			($isrubsel) ? $style_sel = 'bold' : $style_sel = 'none';

			$icon = 'folder';
			$new_str = ''; // decalage pour les noeuds suivants
			if ($rub['depth'] == 1) {
				$icon = 'base';
			} else {
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)
			}

			// Mise en avant de la rubrique coupée
//          $color = ($_SESSION['catalogue']['rubcut_id'] == $rid) ? ' color:#b60000;': '';

//          $link = "<a style=\"font-weight:{$style_sel};{$color}\" href=\"$scriptenv&op=admin_arbo&rubid={$rub['id']}\">";
			$link = "<a style=\"font-weight:{$style_sel};\" href=\"$scriptenv&op=admin_arbo&rubid={$rub['id']}\">";
			$link_div ="<a onclick=\"javascript:catalogue_showrub('{$rid}','{$new_str}');\" href=\"javascript:void(0);\">";

			if ($rub['depth'] > 1) {
				$last = 'joinbottom';
				if ($islast) $last = 'join';
				if (isset($a_rubs['tree'][$rid])) {
					if ($islast)
						($isrubsel || $isrubopened) ? $last = 'minus' : $last = 'plus';
					else
						($isrubsel || $isrubopened) ? $last = 'minusbottom' : $last = 'plusbottom';
				}
				$decalage .= "<div style=\"float:left;\" id=\"{$rub['id']}_plus\">{$link_div}<img border=\"0\" src=\"./modules/catalogue/img/{$last}.gif\"></a></div>";
			}

			$html_rec = '';
			if ($isrubsel || $isrubopened || $rub['depth'] == 1) $html_rec = catalogue_buildtree($a_rubs, $rid, $new_str);

			$display = ($html_rec == '') ? 'none' : 'block';

			//      <div style=\"float:left;\">{$decalage}<div><img src=\"./modules/catalogue/img/{$icon}.gif\"></div></div>
			$html .= "
				<div style=\"clear:left;\" style=\"padding:0px;\">
					<div style=\"float:left;\">{$decalage}<img src=\"./modules/catalogue/img/{$icon}.gif\"></div>
					<div style=\"float:left;white-space:nowrap;\">{$link}".$numrub.dims_strcut($rub['label'],35)."</a>&nbsp;&nbsp;</div>
				</div>
				<div style=\"clear:left;display:$display;\" id=\"{$rub['id']}\" style=\"padding:0px;\">$html_rec</div>";
			$c++;
		}
	}

	return $html;
}

function catalogue_buildtree_scolratt($a_rubs, $fromrid = '', $str = '', $scolaire = false) {
	global $rubid;
	global $rubid_sc;
	global $scriptenv;

	$sc = ($scolaire) ? '_sc' : '';
	$rubid = ${'rubid'.$sc};

	$rubsel = $a_rubs['list'][$rubid];

	$html = '';

	if (isset($a_rubs['tree'][$fromrid])) {
		$c = 0;
		foreach ($a_rubs['tree'][$fromrid] as $rid) {
			$rub = $a_rubs['list'][$rid];
			$isrubsel = ($rubid == $rid);
			$isrubopened = strstr($rubsel['parents'], "{$rub['id_parent']};{$rub['id']}");
			$islast = ($c == sizeof($a_rubs['tree'][$fromrid])-1);

			$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/empty.gif\"></div>", $str);
			$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/line.gif\"></div>", $decalage);


			($isrubsel) ? $style_sel = 'bold' : $style_sel = 'none';

			$icon = 'folder';
			$new_str = ''; // decalage pour les noeuds suivants
			if ($rub['depth'] == 1) {
				$icon = 'base';
			} else {
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)
			}

			$link = "<a style=\"font-weight:{$style_sel};\" href=\"$scriptenv&op=admin_scolratt&rubid{$sc}={$rub['id']}\">";
			$link_div ="<a onclick=\"javascript:catalogue_showrub('{$fid}','{$new_str}');\" href=\"#\">";

			if ($rub['depth'] > 1) {
				$last = 'joinbottom';
				if ($islast) $last = 'join';
				if (isset($a_rubs['tree'][$rid])) {
					if ($islast)
						($isrubsel || $isrubopened) ? $last = 'minus' : $last = 'plus';
					else
						($isrubsel || $isrubopened) ? $last = 'minusbottom' : $last = 'plusbottom';
				}
				$decalage .= "<div style=\"float:left;\" id=\"{$rub['id']}_plus\">{$link_div}<img border=\"0\" src=\"./modules/catalogue/img/{$last}.gif\"></a></div>";
			}

			$html_rec = '';
			if ($isrubsel || $isrubopened || $rub['depth'] == 1) $html_rec = catalogue_buildtree_scolratt($a_rubs, $rid, $new_str, $scolaire);

			$display = ($html_rec == '') ? 'none' : 'block';

			$action = '';
			if ($isrubsel) {
				if ($scolaire) {
					$action = "<a href=\"$scriptenv&op=admin_scolratt&action=keepsel&rubid{$sc}={$rub['id']}\"><img src=\"./modules/catalogue/img/ajouter.gif\" border=\"0\"/></a>";
				} else {
					$action = "<a href=\"$scriptenv&op=admin_scolratt&action=dropsel&rubid{$sc}={$rub['id']}\"><img src=\"./modules/catalogue/img/maj.gif\" border=\"0\" /></a>";
				}
			}

			$html .= "
				<div style=\"clear:left;\" style=\"padding:0px;\">
					<div style=\"float:left;\">{$decalage}<img src=\"./modules/catalogue/img/{$icon}.gif\"></div>
					<div style=\"float:left;white-space:nowrap;\">{$link}".dims_strcut($rub['label'],50)."</a>&nbsp;&nbsp;{$action}</div>
				</div>
				<div style=\"clear:left;display:$display;\" id=\"{$rub['id']}\" style=\"padding:0px;\">$html_rec</div>";
			$c++;
		}
	}

	return $html;
}

// renvoie la liste de tous les articles de la famille scolaire (sous-familles comprises)
function catalogue_getscolarticles() {
	global $db;
	$a_articles = array();

	$profondeur = substr($_SESSION['catalogue']['scolratt'], 0, 1);
	$famId = intval(substr($_SESSION['catalogue']['scolratt'], 1));

	$db->query("SELECT DISTINCT(PREF) FROM dims_mod_vpc_article_rub_sc WHERE rub{$profondeur} = $famId");
	while ($row = $db->fetchrow()) {
		$a_articles[] = $row['PREF'];
	}
	return $a_articles;
}

function catalogue_setsdtarticles($rubId, $a_articles) {
	global $db;

	$profondeur = substr($rubId, 0, 1);
	$famId = intval(substr($rubId, 1));

	// ouverture de la famille concernee
	include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/class_article.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/class_rub'.$profondeur.'.php';
	$objRub = "rub{$profondeur}";
	$rub = new $objRub();
	$rub->open($famId);

	$idRub0 = (empty($rub->fields['id_rub0'])) ? 0 : $rub->fields['id_rub0'];
	$idRub1 = (empty($rub->fields['id_rub1'])) ? 0 : $rub->fields['id_rub1'];
	$idRub2 = (empty($rub->fields['id_rub2'])) ? 0 : $rub->fields['id_rub2'];
	$idRub3 = (empty($rub->fields['id_rub3'])) ? 0 : $rub->fields['id_rub3'];

	${'idRub'.$profondeur} = $rub->fields['id'];

	// creation d'une famille de niveau 4 identique a celle ouverte
	include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/class_rub4.php';
	$rub4 = new rub4();
	$rub4->fields['id_rub0'] = $idRub0;
	$rub4->fields['id_rub1'] = $idRub1;
	$rub4->fields['id_rub2'] = $idRub2;
	$rub4->fields['id_rub3'] = $idRub3;
	$rub4->fields['libelle'] = $rub->fields['libelle'];
	$rub4->fields['libelle2'] = '';
	$rub4->fields['photo'] = '';
	$rub4->save();

	$idRub4 = $rub4->fields['id'];

	foreach ($a_articles as $ref) {
		$art = new article();
		$art->open($ref);
		$db->query("
			DELETE FROM dims_mod_vpc_article_rub
			WHERE   PREF = '{$art->fields['PREF']}'
			AND     rub0 = $idRub0
			AND     rub1 = $idRub1
			AND     rub2 = $idRub2
			AND     rub3 = $idRub3
			AND     rub4 = $idRub4");
		$db->query("
			INSERT INTO dims_mod_vpc_article_rub SET
				PREF = '{$art->fields['PREF']}',
				rub0 = $idRub0,
				rub1 = $idRub1,
				rub2 = $idRub2,
				rub3 = $idRub3,
				rub4 = $idRub4");
	}

	// suppression de la famille de l'arbre scolaire traitee
	$profondeur = substr($_SESSION['catalogue']['scolratt'], 0, 1);
	$famId = intval(substr($_SESSION['catalogue']['scolratt'], 1));

	include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/class_rub'.$profondeur.'_sc.php';
	$objRub = "rub{$profondeur}_sc";
	$rub = new $objRub();
	$rub->open($famId);
	$rub->delete();
}

function cata_getgroupclients() {
	global $db;

	$user = new user();
	$user->open($_SESSION['dims']['userid']);
	$groups = $user->getgroups(true);

	$a_groups = array();
	$rs = $db->query("
		SELECT  code
		FROM    dims_group g
		WHERE   g.id_group = ".key($groups));
	while ($row = $db->fetchrow($rs)) {
		$a_groups[] = "'{$row['code']}'";
	}

	return $a_groups;
}

// construction de l'arbre des familles sur le site => seulement les familles qui ont des articles
function cata_getfamilys($maxDepth = 0) {
	include_once DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';

	$dims = dims::getInstance();

	$mods = $dims->getModuleByType('catalogue');
	$catalogue_moduleid = $mods[0]['instanceid'];
	$_SESSION['catalogue']['moduleid'] = $catalogue_moduleid;

	$oCatalogue = new catalogue();
	$oCatalogue->open($catalogue_moduleid);
	$oCatalogue->loadParams();

	if (!$oCatalogue->getParams('cata_visible_not_connected') && !$_SESSION['dims']['connected']) {
		return array();
	}

	$cache = false;
	$cached = false;
	$timeCache = '';
	$apcKey = '_FAMS_'.md5($_SERVER['HTTP_HOST']);
	if (isset($_SESSION['catalogue']['global_filter'])) {
		$apcKey .= '_GF_'.$_SESSION['catalogue']['global_filter']['filter_id'];
	}

	if (APC_EXTENSION_LOADED) {
		$timeCache = apc_fetch($apcKey.'_TIME', $cached);
	}

	if (!$cached || (time() - $timeCache) > APC_CACHE_TIME) {
		include_once DIMS_APP_PATH."/modules/catalogue/include/class_famille.php";
		include_once DIMS_APP_PATH."/modules/catalogue/include/class_article_famille.php";
		include_once DIMS_APP_PATH."/modules/catalogue/include/class_article.php";

		include_once DIMS_APP_PATH."/modules/catalogue/include/class_param.php";
		$defLang = cata_param::getDefaultLang();

		$db = $dims->getDb();


		$familys = array();

		// liste des familles qui ont des articles
		$a_families = array();
		$sql = 'SELECT DISTINCT af.`id_famille` FROM `'.cata_article_famille::TABLE_NAME.'` AS af';

		// si un marché est en cours, on regarde si il est restrictif
		if (isset($_SESSION['catalogue']['market'])) {
			$market = cata_market::getByCode($_SESSION['catalogue']['market']['code']);
			if ($market->hasRestrictions()) {
				$sql .= ' WHERE af.`id_article` IN ('.implode(',', $market->getRestrictions()).')';
			}
		}

		// Si un filtre global est activé, on limite la vue des familles
		if (isset($_SESSION['catalogue']['global_filter'])) {
			$sql .= '
				INNER JOIN 	`'.article::TABLE_NAME.'` AS a
				ON 			a.`id` = af.`id_article`
				AND 		a.`fields'.$_SESSION['catalogue']['global_filter']['filter_id'].'` = '.$_SESSION['catalogue']['global_filter']['filter_value'].'
				AND 		a.`published` = '.article::ARTICLE_PUBLISHED.'
				AND 		a.`status` = "'.article::STATUS_OK.'"
				AND 		a.`putarif_0` > 0';
		}

		$rs = $db->query($sql);
		while ($row = $db->fetchrow($rs)) {
			$a_families[$row['id_famille']] = 1;
		}

		$select = "
			SELECT  f.*, a.urlrewrite AS urlrewrite_article
			FROM    ".cata_famille::TABLE_NAME." f
			LEFT JOIN 	dims_mod_wce_article a
			ON 			a.id = f.id_article_wce
			WHERE   f.id_lang = $defLang ";
		if ($maxDepth > 0) {
			$select .= " AND  f.depth <= $maxDepth";
		}
		$select .= " ORDER BY f.depth ASC, f.position";

		$rewriting=array();
		$rewritingid=array();
		$_SESSION['urlrewrite'] = array();

		$result = $db->query($select);
		while ($fields = $db->fetchrow($result)) {
			// construction du heading rewriting
			if (isset($rewriting[$fields['id_parent']])) {
				$fields['urlrewrite'] = $rewriting[$fields['id_parent']].'/'.$fields['urlrewrite'];
				$rewritingid[$fields['id']] = $fields['id'];
			}
			else {
				$rewritingid[$fields['id']] = $fields['id'];
			}
			$rewriting[$fields['id']] = $fields['urlrewrite'];
			$_SESSION['urlrewrite'][$fields['urlrewrite']] = $rewritingid[$fields['id']];
			$fields['urlrewrite'].=".html";

			$familys['list'][$fields['id']] = $fields;
			$familys['tree'][$fields['id_parent']][] = $fields['id'];

			$parents = explode(';',$familys['list'][$fields['id']]['parents']);

			if (isset($parents[0])) unset($parents[0]);

			$parents[] = $fields['id'];

			$familys['list'][$fields['id']]['nav'] = implode('-',$parents);

			// maj de la couleur de la famille
			if ($fields['color'] == '' && isset($familys['list'][$fields['id_parent']]) && $familys['list'][$fields['id_parent']]['color'] != '') {
				$familys['list'][$fields['id']]['color'] = $familys['list'][$fields['id_parent']]['color'];
				$familys['list'][$fields['id']]['color2'] = $familys['list'][$fields['id_parent']]['color2'];
				$familys['list'][$fields['id']]['color3'] = $familys['list'][$fields['id_parent']]['color3'];
				$familys['list'][$fields['id']]['color4'] = $familys['list'][$fields['id_parent']]['color4'];
			}
		}

		// nettoyage le l'arbre avec la suppression des familles
		// qui n'appartiennent pas a l'adherent
		if (isset($familys['list'])) {
			foreach ($familys['list'] as $famId => $fam) {
				cata_cleanFamilys($familys, $famId, $a_families);
			}
		}


		$_SESSION['catalogue']['familys'] = $familys;

		if (APC_EXTENSION_LOADED) {
			apc_store($apcKey, $familys);
			apc_store($apcKey.'_REWRITE', $_SESSION['urlrewrite']);
			apc_store($apcKey.'_TIME', time());
		}
	}
	else {
		$familys = apc_fetch($apcKey);
		$_SESSION['urlrewrite'] = apc_fetch($apcKey.'_REWRITE');

		// On remet les familles en session si elles y sont pas encore
		if (!isset($_SESSION['catalogue']['familys'])) {
			$_SESSION['catalogue']['familys'] = $familys;
		}
	}

	return $familys;
}


function cata_cleanFamilys(&$familys, $famId, $a_families) {
	if(!empty($familys['list'][$famId])) {
		$fam = $familys['list'][$famId];

		if (empty($familys['tree'][$famId]) && !isset($a_families[$famId])) {
			// on supprime la famille
			unset($familys['list'][$famId]);
			$k = array_search($famId, $familys['tree'][$fam['id_parent']]);
			if ( $k !== false ) {
				unset($familys['tree'][$fam['id_parent']][$k]);
				if(empty($familys['tree'][$fam['id_parent']])) {
					cata_cleanFamilys($familys, $fam['id_parent'], $a_families);
				}
			}
		}
	}
}

// construction de l'arbre des familles en admin => toutes les familles
function cata_getfamilys_adm() {
	global $db;
	//global $familyId;

	$familys = array();

	$select = "
		SELECT	f.*, COUNT(id_article) AS nbart
		FROM	dims_mod_cata_famille f

		LEFT JOIN	dims_mod_cata_article_famille af
		ON			af.id_famille = f.id

		WHERE   f.id_module = {$_SESSION['dims']['moduleid']}

		GROUP BY f.id
		ORDER BY f.id_parent, f.position";
	$result = $db->query($select);
	while ($fields = $db->fetchrow($result)) {
		$familys['list'][$fields['id']] = $fields;
		$familys['tree'][$fields['id_parent']][] = $fields['id'];
	}

	//// look for groupid
	//if (!isset($familyId)) {
	//  if (!isset($_SESSION['catalogue']['familyId'])) $_SESSION['catalogue']['familyId'] = $familys['tree'][1][0];
	//  $familyId = $_SESSION['catalogue']['familyId'];
	//}
	return($familys);
}


function cata_build_familys_list($familys, $fromfid = 0) {
	if (isset($familys['tree'][$fromfid])) {
		foreach ($familys['tree'][$fromfid] as $fid) {
			$family = $familys['list'][$fid];

			$a_parents = array();
			foreach (explode(';', $family['parents'].';'.$fid) as $parent) {
				if (isset($familys['list'][$parent])) {
					$a_parents[$parent] = "\"{$familys['list'][$parent]['label']}\"";
				}
			}
			echo "\"$fid\";".implode(';', $a_parents)."\n";

			cata_build_familys_list($familys, $fid);
		}
	}
}

function cata_setFamilysView($familys, $a_fams) {
	$a_famsView = array();

	// recuperation des familles parentes
	foreach ($a_fams as $famId => $empty) {
		if (isset($familys['list'][$famId])) {
			foreach ( explode(';', $familys['list'][$famId]['parents']) as $parentId ) {
				if (!isset($a_fams[$parentId])) {
					$a_fams[$parentId] = 1;
				}
			}
		}
	}

	// recuperation de la vue complete des familles
	foreach ( $familys['list'] as $famId => $fam ) {
		if ( isset($a_fams[$famId]) ) {
			$a_famsView[$famId] = $familys['list'][$famId];
		}
	}

	return $a_famsView;
}

function cata_makeLinkLabel($str) {
	$search     = array(' ', '/');
	$replace    = array('_');
	return $libellelien = '/catalogue/'.urlencode(dims_convertaccents(str_replace($search, $replace, $str)));
}

function cata_getChildren($familys, $famIds) {
	$a_fam = array();
	foreach ($famIds as $famId) {
		if (is_array($familys['tree'][$famId])) {
			$a_fam = array_merge($a_fam, $familys['tree'][$famId]);
		}
	}
	return $a_fam;
}

function cata_getAllChildren($familys, $famIds, &$a_fam = array()) {
	if (is_array($famIds)) {
		foreach ($famIds as $famId) {
			if (isset($familys['tree'][$famId]) && is_array($familys['tree'][$famId])) {
				$a_fam = array_merge($a_fam, $familys['tree'][$famId]);
				cata_getAllChildren($familys, $familys['tree'][$famId], $a_fam);
			}
		}
	}
	return $a_fam;
}

function cata_getRootFams($familys, $a_famIds, $depth) {
	$a_fam = array();
	if ($depth < 2) $depth = 2;

	if (is_array($a_famIds)) {
		// recherche des familles parentes
		foreach (array_keys($a_famIds) as $famId) {
			if (!isset($familys['list'][$famId])) {
				unset($a_famIds[$famId]);
			}

			foreach (explode(';', $familys['list'][$famId]['parents'].';'.$famId) as $fam) {
				if ($fam > 0) {
					if ( $familys['list'][$fam]['depth'] = $depth ) {
						if (!isset($a_fam[$fam])) $a_fam[$fam] = 0;
						$a_fam[$fam] += $a_famIds[$famId];
						continue;
					}
					// si on a pas la profondeur necessaire, on prend le niveau d'avant
					elseif ( $familys['list'][$fam]['depth'] = $depth - 1 ) {
						if (!$a_fam[$fam]) $a_fam[$fam] = 0;
						$a_fam[$fam] += $a_famIds[$famId];
						continue;
					}
				}
			}
		}
	}

	// si on a qu'une famille on rentre dedans
	if (sizeof($a_fam) == 1) {
		$a_fam = cata_getRootFams($familys, $a_famIds, $depth + 1);
	}

	if (!sizeof($a_fam)) {
		return $a_famIds;
	} else {
		return $a_fam;
	}

}

/*
// renvoie la liste des filtres COMMUNS a toutes les familles
function cata_getCommonFilters($a_fam) {
	if (sizeof($a_fam)) {
		global $db;

		$a_models = array();
		// foreach ($a_fam as $idFam) {
		//     $a_models[$idFam] = array();
		// }

		$rs = $db->query("SELECT * FROM dims_mod_cata_modele WHERE id_famille IN (".implode(',', $a_fam).")");
		while ($row = $db->fetchrow($rs)) {
			$a_models[$row['id_famille']] = $row;
		}

		$a_filters = array();
		foreach ($a_models as $id_famille => $row) {
			$tmp_filters = array();
			for ($i = 1; $i <= 200; $i++) {
				//if ($row["field{$i}"] && !array_key_exists($i, $tmp_filters)) {
				if ($row["field{$i}"]) {
					$tmp_filters[$i] = array();
				}
			}
			if (sizeof($a_filters)) {
				$a_filters = array_intersect_key($a_filters, $tmp_filters);
			} else {
				$a_filters = $tmp_filters;
			}
		}
		return $a_filters;
	}
	else {
		return array();
	}
}

function cata_getFilterValues(&$a_filters, &$a_idArt, $post = array()) {
	$a_idFields = array_keys($a_filters);
	if (sizeof($a_idFields)) {
		global $db;
		$s_lstFields = '';

		foreach ($a_idFields as $idField) {
			if ($s_lstFields != '') $s_lstFields .= ',';
			$s_lstFields .= "field{$idField}";
		}

		$rs = $db->query("
			SELECT  id_article_1 as id_article, $s_lstFields
			FROM    dims_mod_cata_article_lang
			WHERE   id_article_1 IN (".implode(',', $a_idArt).")
			");
		while ($row = $db->fetchrow($rs)) {
			foreach ($row as $k => $v) {
				// recuperation de la valeur du filtre
				if ($v != '') {
					$idField = str_replace('field', '', $k);

					if (is_numeric($idField)) {
						$a_filters[$idField]['values'][$v] = '';
					}
				}

				// filtrage de l'article
				if (!empty($post[$k]) && $post[$k] != $v) {
					unset ($a_idArt[$row['id_article']]);
				}
			}
		}
	}
	// nettoyages des filtres vides
	foreach ($a_filters as $idFilter => $values) {
		if (!sizeof($values)) {
			unset($a_filters[$idFilter]);
		}
	}
}
*/

function loadCorrespKeywords() {
	if (!isset($_SESSION['catalogue']['keywords_corresp'])) {
		global $db;
		$rs = $db->query("SELECT * FROM dims_mod_vpc_keyword_corresp");
		while ($row = $db->fetchrow($rs)) {
			$_SESSION['catalogue']['keywords_corresp'][$row['keyword']] = $row['corresp'];
		}
	}
}

function cata_loadSuiviCommande() {
	global $db;

	//if (isset($_SESSION['catalogue']['suivi_commandes'])) {
	//  unset($_SESSION['catalogue']['suivi_commandes']);
	//}

	if (!isset($_SESSION['catalogue']['suivi_commandes'])) {
		$a_etats = array(
			'livcomp'   => 0,
			'livpart'   => 1,
			'trait'     => 2,
			'encours'   => 3,
			'val1'      => 4,
			'val2'      => 5,
			'bloq'      => 6
			);

		// nombre de jours d'historique
		$nbJours = 5;

		$jour   = date("d");
		$mois   = date("m");
		$annee  = date("Y");

		$_SESSION['catalogue']['suivi_commandes'] = array();
		$rs = $db->query("
			SELECT  c.id, c.etat, c.numcmd, c.date_validation, c.libelle, c.total_ttc
			FROM    dims_mod_vpc_cmd c
			WHERE   c.etat != 'en_cours'
			AND     c.ref_client = '{$_SESSION['catalogue']['client']}'
			ORDER BY date_validation DESC
			");
		while ($row = $db->fetchrow($rs)) {
			$imgSuivi = '';
			$ts_dateval = cata_ts2unixts($row['date_validation']);

			switch ($row['etat']) {
				case 'validee':
					if (!empty($row['numcmd'])) {
						$rsdet = $db->query("
							SELECT  qte, qte_liv, qte_rel
							FROM    dims_mod_vpc_cmd_detail
							WHERE   id_cmd = {$row['id']}
							");
						$livpart = false;
						$livcomp = true;
						while ($rowdet = $db->fetchrow($rsdet)) {
							$livpart = $livpart || ($rowdet['qte_liv'] + $rowdet['qte_rel']);
							$livcomp = $livcomp && ($rowdet['qte_liv'] == $rowdet['qte']);
						}

						if ($livpart) {
							if ($livcomp) {
								if (mktime(0, 0, 0, $mois, $jour, $annee) - $ts_dateval <= (86400 * $nbJours)) {
									$imgSuivi = 'livcomp';
								}
							} else {
								$imgSuivi = 'livpart';
							}
						} else {
							if (mktime(0, 0, 0, $mois, $jour, $annee) - $ts_dateval <= (86400 * 30)) {
								$imgSuivi = 'trait';
							}
						}
					} else {
						if (mktime(0, 0, 0, $mois, $jour, $annee) - $ts_dateval <= (86400 * $nbJours)) {
							$imgSuivi = 'encours';
						}
					}
					break;
				case 'en_cours1':
					$imgSuivi = 'val1';
					break;
				case 'en_cours2':
					$imgSuivi = 'val2';
					break;
				case 'refusee':
					if (mktime(0, 0, 0, $mois, $jour, $annee) - $ts_dateval <= (86400 * $nbJours)) {
						$imgSuivi = 'bloq';
					}
					break;
			}

			if ($imgSuivi != '') {
				$_SESSION['catalogue']['suivi_commandes'][$row['date_validation']][$row['id']] = $row;
				$_SESSION['catalogue']['suivi_commandes'][$row['date_validation']][$row['id']]['etatSuivi'] = $imgSuivi;
			}

		}

		krsort($_SESSION['catalogue']['suivi_commandes']);
	}
}

// Convertit un timestamp DIMS en timestamp UNIX
function cata_ts2unixts($timestamp) {
	if (strlen($timestamp) == 14) {
		$year   = intval(substr($timestamp, 0, 4));
		$month  = intval(substr($timestamp, 4, 2));
		$day    = intval(substr($timestamp, 6, 2));
		$hour   = intval(substr($timestamp, 8, 2));
		$minute = intval(substr($timestamp, 10, 2));
		$second = intval(substr($timestamp, 12, 2));

		return mktime($hour, $minute, $second, $month, $day, $year);
	} else {
		return 0;
	}
}

function printr($array) {
	dims_print_r($array);
}

function isBlandan($ref) {
	if ( strlen($ref) == 6 && is_numeric($ref) && substr($ref, 0, 1) == '9' ) {
		return true;
	} else {
		return false;
	}
}

function cata_arrayEncode($string) {
	$from = array('+');
	$to = array('__PLUS__');
	return str_replace($from, $to, $string);
}

function cata_arrayDecode($string) {
	$from = array('__PLUS__');
	$to = array('+');
	return str_replace($from, $to, $string);
}

function catalogue_load_prices() {
	$dims = dims::getInstance();
	$db = $dims->getDb();
	$ts = dims_createtimestamp();

	unset($_SESSION['catalogue']['grille_tarif'],
		  $_SESSION['catalogue']['remise_type_article'],
		  $_SESSION['catalogue']['remise']);
	$_SESSION['catalogue']['prix_net'] = array();

	// Chargement des params pour connaitre la règle de calcul
	$mods = $dims->getModuleByType('catalogue');
	$catalogue_moduleid = $mods[0]['instanceid'];

	$oCatalogue = new catalogue();
	$oCatalogue->open($catalogue_moduleid);
	$oCatalogue->loadParams();
	$_SESSION['catalogue']['regles_remises'] = $oCatalogue->getParams('regles_remises');


	if (isset($_SESSION['catalogue']['code_client'])) {
		// Chargement des prix nets du client
		catalogue_load_prixnets('C', $_SESSION['catalogue']['code_client']);

		// Chargement des remises par type de produit
		$sql = "SELECT 	type_article, remise
				FROM 	dims_mod_cata_remise_type_article
				WHERE 	code_client = '{$_SESSION['catalogue']['code_client']}'
				OR 		code_client = ''";
		$rs = $db->query($sql);
		if($db->numrows($rs)){
			while($row = $db->fetchrow($rs)){
				$_SESSION['catalogue']['remise_type_article'][$row['type_article']] = $row['remise'];
			}
		}

		// Remise par défaut
		$_SESSION['catalogue']['remise'] = 0;

		// Chargement de la remise du client
		$tmp_rem = catalogue_load_remises('C', $_SESSION['catalogue']['code_client']);
		if (isset($tmp_rem[$_SESSION['catalogue']['code_client']])) {
			$_SESSION['catalogue']['remise'] = $tmp_rem[$_SESSION['catalogue']['code_client']];
		}

		// Chargement de la remise du marché
		if (isset($_SESSION['catalogue']['market'])) {
			$tmp_rem = catalogue_load_remises('M', $_SESSION['catalogue']['market']['code']);
			if (isset($tmp_rem[$_SESSION['catalogue']['market']['code']])) {
				$_SESSION['catalogue']['remise'] = $tmp_rem[$_SESSION['catalogue']['market']['code']];
			}
		}
	}

	// Chargement des prix nets du marché
	if (isset($_SESSION['catalogue']['market'])) {
		// On les passe en 2e et on écrase l'éventuel existant pour que ce soit toujours
		// celui-ci qui soit pris même si un PN est défini sur le client
		catalogue_load_prixnets('M', $_SESSION['catalogue']['market']['code']);
	}

	// // Chargement des promotions
	// include_once $_SERVER['DOCUMENT_ROOT'].'modules/catalogue/class_promotion.php';

	// $sql = '
	// 	SELECT	DISTINCT(id)
	// 	FROM	dims_mod_cata_promotions promo
	// 	WHERE	promo.active = 1
	// 	AND		'.$ts.' BETWEEN promo.date_debut AND promo.date_fin';
	// $rs = $db->query($sql);
	// while ($row = $db->fetchrow($rs)) {
	// 	$promo = new cata_promotion();
	// 	$promo->open($row['id']);

	// 	if ((!sizeof($promo->clients) && trim($promo->fields['code']) == "") || in_array($_SESSION['catalogue']['client'], array_keys($promo->clients))) {
	// 		foreach ($promo->articles as $ref_article => $prix) {
	// 			$ref_article = "'$ref_article'";
	// 			if (!isset($_SESSION['catalogue']['promo']['unlocked'][strtoupper($ref_article)]) || $_SESSION['catalogue']['promo']['unlocked'][strtoupper($ref_article)] > $prix) {
	// 				$_SESSION['catalogue']['promo']['unlocked'][strtoupper($ref_article)] = $prix;
	// 			}
	// 		}
	// 	}
	// 	else {
	// 		foreach ($promo->articles as $ref_article => $prix) {
	// 			$ref_article = "'$ref_article'";
	// 			if (!isset($_SESSION['catalogue']['promo']['locked'][$promo->fields['code']][strtoupper($ref_article)]) || $_SESSION['catalogue']['promo']['locked'][$promo->fields['code']][strtoupper($ref_article)] > $prix) {
	// 				$_SESSION['catalogue']['promo']['locked'][$promo->fields['code']][strtoupper($ref_article)] = $prix;
	// 			}
	// 		}
	// 	}
	// }

	// Chargement des tarifs quantitatifs
	catalogue_load_quantitatifs("", "0", $ts);

	// Chargement des tarifs quantitatifs du client
	if (isset($_SESSION['catalogue']['code_client'])) {
		catalogue_load_quantitatifs('C', $_SESSION['catalogue']['code_client'], $ts);
	}

	// Chargement des tarifs quantitatifs du marché
	if (isset($_SESSION['catalogue']['market'])) {
		catalogue_load_quantitatifs("M", $_SESSION['catalogue']['market']['code'], $ts);
	}
}

function catalogue_load_remises($type, $code_tm) {
	$db = dims::getInstance()->getDb();

	$tmp_rem = array();
	$sql = "SELECT * FROM `dims_mod_cata_remises`";
	if ($type == 'C') {
		$sql .= " WHERE (`code_tm` = '".$code_tm."' || ISNULL(`code_tm`))";
		$code_tm = (empty($tmp_rem[$code_tm])) ? '0' : $code_tm;
	}
	else {
		$sql .= " WHERE `code_tm` = '".$code_tm."'";
	}
	$sql .= " AND `type` = '".$type."'";
	$db->query($sql);
	if ($db->numrows()) {
		while ($row = $db->fetchrow()) {
			foreach ($row as $key => $val) $row[$key] = (empty($val)) ? '0' : $val;
			$tmp_rem[$row['code_tm']][$row['fam']][$row['ssfam']][$row['colonne_tarif']] = $row['remise'];
		}
	}

	return $tmp_rem;
}

function catalogue_load_prixnets($type, $code_cm) {
	$db = dims::getInstance()->getDb();

	$sql = "SELECT reference, puht FROM dims_mod_cata_prix_nets WHERE code_cm = '".$code_cm."' AND type = '".$type."'";
	$rs = $db->query($sql);
	if ($db->numrows($rs)) {
		while ($row = $db->fetchrow($rs)) {
			$_SESSION['catalogue']['prix_net_'.strtolower($type)][$row['reference']] = $row['puht'];
		}
	}
}

function catalogue_load_quantitatifs($type, $code_cm, $ts) {
	$db = dims::getInstance()->getDb();

	$prix_qte_type = ($type == '') ? 'prix_qte' : 'prix_qte_'.strtolower($type);

	$rs = $db->query('
		SELECT	*
		FROM	dims_mod_cata_tarqte
		WHERE 	`type` = "'.$type.'"
		AND 	`code_cm` = "'.$code_cm.'"
		AND 	( '.$ts.' BETWEEN datedeb AND datefin OR (datedeb = \'\' AND datefin = \'\') )
		ORDER BY reference, qtedeb DESC');
	if ($db->numrows($rs)) {
		if (!isset($_SESSION['catalogue'][$prix_qte_type])) {
			$_SESSION['catalogue'][$prix_qte_type] = array();
		}
		while ($row = $db->fetchrow($rs)) {
			$_SESSION['catalogue'][$prix_qte_type][$row['reference']][$row['qtedeb']] = $row['puqte'];
		}
	}
}

function catalogue_getprixarticle($article, $artqte = 1, $brut = 0, $id_cmd = -1) {
	if (defined('_CATA_VARIANTE') && function_exists('variante_catalogue_getprixarticle')) {
		return call_user_func_array('variante_catalogue_getprixarticle', func_get_args());
	}
	else {
		// Chargement des données en session
		if (!isset($_SESSION['catalogue']['prix_net'])) {
			catalogue_load_prices();
		}

		$db = dims::getInstance()->getDb();
		//$ts = dims_createtimestamp();

		if ($brut) {
			$prix = $article->getPrix();
		}
		else {
			// Calcul du prix du client

			// Recherche de la remise
			$remise = catalogue_getRemise($article);
			$prix = $article->getprix() * (1 - $remise / 100);

			switch ($_SESSION['catalogue']['regles_remises']) {
				case cata_param::REGLE_CALCUL_PRIORITE_MOINS_CHER:
					// recherche de prix net
					// On teste les 2 prix nets
					if (
							isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()])
						&&	$_SESSION['catalogue']['prix_net_c'][$article->getReference()] < $prix
					) {
						$prix = $_SESSION['catalogue']['prix_net_c'][$article->getReference()];
					}
					if (
							isset($_SESSION['catalogue']['prix_net_m'][$article->getReference()])
						&&	$_SESSION['catalogue']['prix_net_m'][$article->getReference()] < $prix
					) {
						$prix = $_SESSION['catalogue']['prix_net_m'][$article->getReference()];
					}

					// Recherche d'un prix quantitatif
					if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					break;
				case cata_param::REGLE_CALCUL_PRIORITE_MARCHE:
					// recherche de prix net
					// On teste d'abord le prix net du marché
					if ( isset($_SESSION['catalogue']['prix_net_m'][$article->getReference()]) ) {
						$prix = $_SESSION['catalogue']['prix_net_m'][$article->getReference()];
					}
					elseif ( isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()]) ) {
						$prix = $_SESSION['catalogue']['prix_net_c'][$article->getReference()];
					}

					// Recherche d'un prix quantitatif
					if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					break;
				case cata_param::REGLE_CALCUL_PRIORITE_CLIENT:
					// recherche de prix net
					// On teste d'abord le prix net du client
					if ( isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()]) ) {
						$prix = $_SESSION['catalogue']['prix_net_c'][$article->getReference()];
					}
					elseif ( isset($_SESSION['catalogue']['prix_net_m'][$article->getReference()]) ) {
						$prix = $_SESSION['catalogue']['prix_net_m'][$article->getReference()];
					}

					// Recherche d'un prix quantitatif
					if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]) ) {
						foreach ($_SESSION['catalogue']['prix_qte'][$article->fields['reference']] as $qte => $puqte) {
							if ($artqte >= $qte && $puqte < $prix) {
								$prix = $puqte;
								break;
							}
						}
					}
					break;
			}
		}

		// // Application des promotions
		// if (!$brut) {
		// 	if (
		// 		isset($_SESSION['catalogue']['promo']['unlocked']["'".strtoupper($article->getReference())."'"]) &&
		// 		$_SESSION['catalogue']['promo']['unlocked']["'".strtoupper($article->getReference())."'"] < $prix
		// 	) {
		// 		$prix = $_SESSION['catalogue']['promo']['unlocked']["'".strtoupper($article->getReference())."'"];
		// 	}
		// }

		return round($prix, 2);
	}
}

function catalogue_getRemise($article) {
	$remise = 0;
	if (isset($_SESSION['catalogue']['remise'][$article->fields['fam']])) {
		$remise = $_SESSION['catalogue']['remise'][$article->fields['fam']];
	}
	return $remise;
}

// Enregistre le panier de la session dans un cookie
function panier2cookie() {
	/*
	 * Les cookies sont limités à une taille de 4ko = 4096 octets.
	 * Pour ne pas atteindre cette limite, on découpe la liste d'articles
	 * en les groupant par 100 (ref + qté)
	 * (on estime que ref + qté + caractères de séparation ne dépassent pas les 20 caractères).
	 */

	// Destruction des cookies existants
	if (isset($_COOKIE)) {
		if (isset($_COOKIE['articles']) && sizeof($_COOKIE['articles'])) {
			foreach ($_COOKIE['articles'] as $id_cookie => $s_articles) {
				setcookie("articles[$id_cookie]","",time() - 3600);
			}
		}
		setcookie("montant","",time() - 3600);
	}

	// Articles
	if (isset($_SESSION['catalogue']['panier']) && is_array($_SESSION['catalogue']['panier']['articles'])) {
		$i = 0;
		$compteur = 0;
		$a_articles = array();

		foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $article) {
			$a_articles[] = "$ref;{$article['qte']}";

			$compteur++;
			if ($compteur == 100) { // 100 articles maxi par cookie
				$s_articles = implode('|',$a_articles);
				setcookie("articles[$i]",$s_articles,time() + 3600 * 24 * 30); // Durée d'expiration = 1 mois

				$i++;
				$compteur = 0;
				$a_articles = array();
			}
		}

		$s_articles = implode('|',$a_articles);
		if ($s_articles != "") {
			setcookie("articles[$i]",$s_articles,time() + 3600 * 24 * 30); // Durée d'expiration = 1 mois
		}
	}

	// Montant
	if (isset($_SESSION['catalogue']['panier']['montant']) && $_SESSION['catalogue']['panier']['montant'] != 0) {
		setcookie("montant",$_SESSION['catalogue']['panier']['montant'], time() + 3600 * 24 * 30); // Durée d'expiration = 1 mois
	}
}

function cookie2session() {
	if (isset($_COOKIE)) {
		// Articles
		if (isset($_COOKIE['articles']) && sizeof($_COOKIE['articles'])) {
			if (!isset($_SESSION['catalogue']['panier'])) $_SESSION['catalogue']['panier'] = array();
			if (!isset($_SESSION['catalogue']['panier']['articles'])) $_SESSION['catalogue']['panier']['articles'] = array();

			foreach ($_COOKIE['articles'] as $s_articles) {
				$a_articles = explode('|',$s_articles);

				foreach ($a_articles as $s_article) {
					$a_article = explode(';',$s_article);
					$_SESSION['catalogue']['panier']['articles'][$a_article[0]]['qte'] = $a_article[1];
				}
			}
		}

		// Montant
		if (isset($_COOKIE['montant'])) {
			$_SESSION['catalogue']['panier']['montant'] = $_COOKIE['montant'];
		}
	}
}

// enregistrement du panier en base de donnees
function panier2bdd() {
	// if (!empty($_SESSION['dims']['userid']) && !empty($_SESSION['catalogue']['panier'])) {
	if (!empty($_SESSION['dims']['userid'])) {
		include_once DIMS_APP_PATH.'/modules/catalogue/include/class_panier.php';
		$panier = new cata_panier();
		$panier->open($_SESSION['dims']['userid']);
		$panier->articles = array();

		$panier->fields['libelle'] = '';
		$panier->fields['id_user'] = $_SESSION['dims']['userid'];
		$panier->fields['id_module'] = $_SESSION['dims']['moduleid'];

		if (isset($_SESSION['catalogue']['panier'])) {
			foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $values) {
				$panier->articles[] = array(
					'ref' 			=> $ref,
					'qte' 			=> $values['qte'],
					'forced_price' 	=> (isset($values['forced_price']) ? $values['forced_price'] : 'NULL')
					);
			}
		}

		$panier->save();
	}
}

// chargement du panier depuis la BDD
function bdd2session() {
	if (!empty($_SESSION['dims']['userid'])) {
		include_once DIMS_APP_PATH.'/modules/catalogue/include/class_panier.php';
		$panier = new cata_panier();
		$panier->open($_SESSION['dims']['userid']);

		foreach ($panier->articles as $art) {
			$_SESSION['catalogue']['panier']['articles'][$art['ref']]['qte'] = $art['qte'];

			if (isset($art['forced_price'])) {
				$_SESSION['catalogue']['panier']['articles'][$art['ref']]['forced_price'] = $art['forced_price'];
			}
		}
		// update_montant_panier();
	}
}

function cart_modRef($artRef, $quantity = 1, $delete = false, $baseTTC = 1, $uventeField = 'uvente', $forced_price = null) {
	global $a_tva;

	if (!isset($_SESSION['catalogue']['panier'])) {
		$_SESSION['catalogue']['panier']['articles']    = array();
		$_SESSION['catalogue']['panier']['montant']     = 0;
	}

	if (empty($artRef)) {
		return $_SESSION['catalogue']['panier'];
	}

	$article = new article();
	$article->findByRef($artRef);

	$moduloQte = $quantity % $article->fields[$uventeField];
	if($moduloQte > 0) {
		$recolisage = true;
		$quantity = $quantity + ($article->fields[$uventeField] - $moduloQte);
	}

	$prix       = catalogue_getprixarticle($article, $quantity);
	$prixaff    = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

	// reference a ajouter au panier
	if ($quantity > 0 && !$delete) {
		if (!isset($_SESSION['catalogue']['panier']['articles'][$artRef]['qte'])) {
			$_SESSION['catalogue']['panier']['articles'][$artRef]['qte'] = 0;
		}

		$_SESSION['catalogue']['panier']['articles'][$artRef]['qte'] += $quantity;

		if (!is_null($forced_price) && $forced_price != '') {
			$_SESSION['catalogue']['panier']['articles'][$artRef]['forced_price'] = round(floatval(str_replace(',', '.', $forced_price)), 2);
		}

		if ($baseTTC) {
			$_SESSION['catalogue']['panier']['montant'] += $quantity * $prixaff;
		} else {
			$_SESSION['catalogue']['panier']['montant'] += $quantity * $prix;
		}
	}

	return $_SESSION['catalogue']['panier'];
}

// Calcule la valeur du panier et la remet en session
function calcul_panier() {
	global $a_tva;

	$montant = 0;

	if (isset($_SESSION['catalogue']['panier']['articles']) && is_array($_SESSION['catalogue']['panier']['articles'])) {
		include_once DIMS_APP_PATH.'/modules/catalogue/class_article.php';

		foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $fields) {
			$article = new article();
			$article->open($ref);

			$prix = catalogue_getprixarticle($article, $fields['qte']);
			$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['PCTVA']]);

			$montant += $prixaff * $fields['qte'];
		}
	}

	$_SESSION['catalogue']['panier']['montant'] = $montant;
}

// Calcule les frais de port en fonction du code postal
function get_fraisport($id_pays = -1, $codepostal = -1, $total = 0, $transporteur_id = 0, $articles = array()) {
	if (defined('_CATA_VARIANTE') && function_exists('variante_get_fraisport')) {
		return call_user_func_array('variante_get_fraisport', func_get_args());
	}
	else {
		$fp_montant 		= 0;
		$fp_franco 			= 0;
		$require_costing 	= 0;

		if ($poids > 0) {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_carrier.php';

			$carrier = new cata_carrier();
			$carrier->open($transporteur_id);
			if ($carrier->fields['id']) {
				$county = (int)substr($codepostal, 0, 2);
				$fp_montant = $carrier->getCarriageAmount($county, $poids);

				// Si on trouve pas de correspondance dans la grille de tarifs
				// on met les frais de port à 0 mais on demande un chiffrage
				if (is_null($fp_montant)) {
					$fp_montant = 0;
					$require_costing = 1;
				}
			}
		}

		return array(
			'fp_montant' 		=> number_format($fp_montant, 2, '.', ''),
			'fp_franco' 		=> number_format($fp_franco, 2, '.', ''),
			'fp_codepostal' 	=> $codepostal,
			'require_costing' 	=> $require_costing
			);
	}
}

function keygen($size = 20) {
	$key = "";
	$letter = "ABCDEFGHIJKMNPQRSTUVWXYZ123456789";

	for ($i = 0; $i < $size; $i++) {
		$key .= $letter[rand(0, strlen($letter) - 1)];
	}
	return $key;
}

function write_cmd_file($id_cmd){
	if (defined('_CATA_VARIANTE') && function_exists('variante_write_cmd_file')) {
		call_user_func_array('variante_write_cmd_file', func_get_args());
	}
	else {

		include_once DIMS_APP_PATH.'modules/catalogue/include/class_exportparams.php';

		$order = new commande();
		$order->open($id_cmd);

		$mods = dims::getInstance()->getModuleByType('catalogue');
		$cataloguemoduleid = $mods[0]['instanceid'];

		$db = dims::getInstance()->getDb();

		$exportparams = exportparams::openbymodule($cataloguemoduleid);

		$fieldsdelimiter = ($exportparams->fields['colseparator'] == exportparams::COLSEP_TAB) ? "\t" : $exportparams->fields['colseparator_other'];

		$headerfields = exportparams::getfieldsnamelist(
			exportparams::filterheaderfields(
				exportparams::filterfields(
					exportparams::getfieldslist(),
					$exportparams->getheaderfields()
				)
			)
		);

		$rowfields = exportparams::getfieldsnamelist(
			exportparams::filterrowfields(
				exportparams::filterfields(
					exportparams::getfieldslist(),
					$exportparams->getrowfields()
				)
			)
		);

		$headerline = array();
		$rowline = array();

	$sql = 'SELECT *, cde.commentaire
			FROM '.commande::TABLE_NAME.' cde
			INNER JOIN '.commande_ligne::TABLE_NAME.' line
			ON line.id_cde = cde.id_cde
			INNER JOIN '.client::TABLE_NAME.' client
			ON client.id_client = cde.id_client
			WHERE cde.id_cde = '.$order->getId();

		$res = $db->query($sql);

		// Ouverture du fichier en ecriture
		if (!is_dir(_CATA_CMD_DIR)) {
			dims_makedir(_CATA_CMD_DIR);
		}
		$fp = fopen(_CATA_CMD_DIR.'/'.$order->getFileName(), "w");

		while($data = $db->fetchrow($res)) {
			// Transform date before using it.
			$datevalidation = dims_timestamp2local($data['date_validation']);
			$datecree = dims_timestamp2local($data['date_cree']);
			$data['date_validation'] = $datevalidation['date'];
			$data['date_cree'] = $datecree['date'];

			if(empty($headerline)) {
				// Build header line only once.
				foreach($headerfields as $fields) {
					if(isset($data[$fields])) {
						$headerline[] = $data[$fields];
					}
				}

				if($exportparams->fields['separatorendline'] == exportparams::ENDLINE_SEP_YES) {
					$headerline[] = '';
				}

				// Write first header line
				fputcsv($fp, array_merge((array)'EC', $headerline), $fieldsdelimiter);

				// Write comment line
				fputcsv($fp, array_merge((array)'COM', (array)$data['commentaire']), $fieldsdelimiter);
			}

			$rowline = array();
			foreach($rowfields as $fields) {
				if(isset($data[$fields])) {
					$rowline[] = $data[$fields];
				}
			}

			if($exportparams->fields['repeatheaders']) {
				$rowline = array_merge($rowline, $headerline);
			} elseif($exportparams->fields['separatorendline'] == exportparams::ENDLINE_SEP_YES) {
				$rowline[] = '';
			}

			fputcsv($fp, array_merge((array)'LC', $rowline), $fieldsdelimiter);
		}

		fclose($fp);
	}
}

function get_cmdfilename($id_cmd) {
	return "{$id_cmd}.txt";
}

function cata_checkEmail($email) {
	if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\+\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
		return false;
	}
	return true;
}

function cata_paginate($varName, $section, $elems) {
	global $a_pagination_per_page;

	//if (isset($_SESSION['catalogue']['pagination'])) {
	//  unset($_SESSION['catalogue']['pagination']);
	//}
	if (!isset($_SESSION['catalogue']['pagination'][$varName][$section]['nbElems'])) {
		$_SESSION['catalogue']['pagination'][$varName][$section] = array(
			'page'      => 0,
			'nbElems'   => $a_pagination_per_page[0]
			);
	}
	return array_slice($elems, $_SESSION['catalogue']['pagination'][$varName][$section]['page'] * $_SESSION['catalogue']['pagination'][$varName][$section]['nbElems'], $_SESSION['catalogue']['pagination'][$varName][$section]['nbElems']);
}

function cata_getPaginationLinks($varName, $section, $nbPages) {
	// creation du lien
	$params = preg_replace('/&p=[0-9]+$/', '', $_SERVER['QUERY_STRING']);

	if ($params != '') {
		$a_query_string = explode('&', $params);
		foreach ($a_query_string as $k => $param) {
			$a_param = explode('=', $param);
			if (
				$a_param[0] == 'urlrewrite' ||
				$a_param[0] == 'pathrewrite' ||
				($a_param[0] == 'op' && $a_param[1] == 'catalogue') ||
				$a_param[0] == 'button_search.x' ||
				$a_param[0] == 'button_search.y'
			) {
				unset($a_query_string[$k]);
			}
		}
		if (sizeof($a_query_string)) {
			$params = implode('&', $a_query_string).'&';
		}
		else {
			$params = '';
		}
	}

	$a_paginationLiens = array();
	if ($_SESSION['catalogue']['pagination'][$varName][$section]['page'] > 0) {
		$a_paginationLiens['precedente'] = array(
			'label'     => 'Précédente',
			'link'      => '?op=catalogue&'.$params.'p='.$_SESSION['catalogue']['pagination'][$varName][$section]['page'],
			'current'   => false
			);
	}
	$insertTPP = false; // TPP = trois petits points ... :)
	for ($c = 1; $c <= $nbPages; $c++) {
		// ajout du lien
		if ( $c == 1 || $c == $nbPages || ( $c <= $_SESSION['catalogue']['pagination'][$varName][$section]['page'] + 3 && $c >= $_SESSION['catalogue']['pagination'][$varName][$section]['page'] - 1 ) ) {
			// Pas de lien pour la page courante
			if ( $c == $_SESSION['catalogue']['pagination'][$varName][$section]['page'] + 1 ) {
				$a_paginationLiens[] = array(
					'label'     => $c,
					'link'      => '',
					'current'   => true
					);
			}
			else {
				$a_paginationLiens[] = array(
					'label'     => $c,
					'link'      => '?op=catalogue&'.$params.'p='.$c,
					'current'   => false
					);
			}
			$insertTPP = false;
		}
		// affichage de '...'
		elseif (!$insertTPP) {
			$a_paginationLiens[] = array(
				'label'     => '...',
				'link'      => '',
				'current'   => false
				);
			$insertTPP = true;
		}
	}
	if ($_SESSION['catalogue']['pagination'][$varName][$section]['page'] != $nbPages - 1) {
		$a_paginationLiens['suivante'] = array(
			'label'     => 'Suivante',
			'link'      => '?op=catalogue&'.$params.'p='.($_SESSION['catalogue']['pagination'][$varName][$section]['page'] + 2),
			'current'   => false
			);
	}
	return $a_paginationLiens;
}

//link1 est le op sur la $racine tandis que link2 est le op du prefixe
//$curs est fixé à 1 pour les promos et la recherche (parce que ça commence à fam1 alors que le catalogue commence à fam0
//$account permet d'indiquer si il faut test la connexion pour afficher "mon compte"
function ariane_factory($racine, $prefixe, $link1, $link2, $catalogue, $curs=0, $account=false) {
	$ariane = array();
	$ariane[0]['label'] = 'Accueil';
	$ariane[0]['link'] = '/index.php?op=home';
	$i=1;

	//traitement de $account needed pour afficher Mon compte quand on est connecté c'est pour les fonctions accessibles en mode connecté ou non
	//comme par exemple la saisie rapide ou voir son panier
	if($account && !empty($_SESSION['dims']['connected'])) {
		$ariane[$i]['label'] = 'Mon compte';
		$ariane[$i]['link'] = '/index.php?op=compte';
		$i++;
	}

	//la racine sert à définir si on est dans une page spéciale du type Recherche, promos, Articles A-Z, Toutes les marques, ...
	if(trim($racine) != '') {
		$ariane[$i]['label'] = strip_tags($racine);
		$ariane[$i]['link'] = $link1;
		$i++;
	}

	//le prefixe permet de dire plus précisément ce qu'on fait : Recherche de 'stylo' ou 'Tous les articles de la marque HP'
	if(trim($prefixe)!='') {
		$ariane[$i]['label'] = strip_tags($prefixe);
		$ariane[$i]['link'] = $link2;
		$i++;
	}
	//gestion du catalogue en mode consultation des familles
	if(isset($catalogue)) {
		//famille 1 :
		$continue = true;
		$curseur = $curs;
		$trouve = false;
		while($continue && $curseur<=4) {
			if (isset($catalogue['fam'.$curseur])) {
				$currentLevel = $catalogue['fam'.$curseur];

				$trouve = false;
				foreach($currentLevel as $famille) {
					if($famille['sel'] == 'selected' || $famille['in_path'] == 'selected') {
						$trouve=true;
						$ariane[$i]['label'] = strip_tags($famille['label']);
						$ariane[$i]['link'] = $famille['lien'];
						$i++;
						break;
					}
				}
			}

			if($trouve) $continue=true;
			$curseur++;
		}
	}

	return $ariane;
}

function cata_orderByLabel($a, $b) {
	return strnatcasecmp($a['label'], $b['label']);
}

function cata_getGroups() {
	global $db;

	$groups = array('list' => array(), 'tree' => array());
	$rs = $db->query('SELECT * FROM dims_group ORDER BY depth, label');
	while ($fields = $db->fetchrow($rs)) {
		$groups['list'][$fields['id']] = $fields;
		$groups['tree'][$fields['id_group']][] = $fields['id'];
	}
	return ($groups);
}

function cata_buildGroupsTree($familys, $fromfid = 0, $str = '')
{
	global $familyId;
	global $scriptenv;

	$familysel = $familys['list'][$familyId];

	$html = '';


	if (isset($familys['tree'][$fromfid]))
	{
		$c=0;
		foreach($familys['tree'][$fromfid] as $fid)
		{
			$family = $familys['list'][$fid];
			$isfamilysel = ($familyId == $fid);
			$isfamilyopened = strstr($familysel['parents'], "{$family['id_group']};{$family['id']}");
			$islast = ($c == sizeof($familys['tree'][$fromfid])-1);

			$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/treeicons/empty.gif\"></div>", $str);
			$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/treeicons/line.gif\"></div>", $decalage);


			($isfamilysel) ? $style_sel = 'bold' : $style_sel = 'none';

			$icon = 'folder';
			$new_str = ''; // decalage pour les noeuds suivants
			if ($family['depth'] == 1)
				$icon = 'base';
			else
			{
				if (!$islast) $new_str = $str.'(s)'; // |
				else $new_str = $str.'(b)';  // (vide)
			}

			$link = "<a style=\"font-weight:{$style_sel}\" href=\"$scriptenv?op=clients&action=view&cref=00010101&famId={$family['id']}\">";
			$link_div ="<a onclick=\"javascript:cata_showfamily('{$fid}','{$new_str}');\" href=\"javascript: void(0);\">";

			if ($family['depth']>1)
			{
				$last = 'joinbottom';
				if ($islast) $last = 'join';
				if (isset($familys['tree'][$fid]))
				{
					if ($islast)
						($isfamilysel || $isfamilyopened) ? $last = 'minus' : $last = 'plus';
					else
						($isfamilysel || $isfamilyopened) ? $last = 'minusbottom' : $last = 'plusbottom';
				}
				$decalage .= "<div style=\"float:left;\" id=\"{$family['id']}_plus\">{$link_div}<img border=\"0\" src=\"./modules/catalogue/img/treeicons/{$last}.gif\"></a></div>";
			}

			$html_rec = '';
			if ($isfamilysel || $isfamilyopened || $family['depth'] == 1) $html_rec = cata_buildGroupsTree($familys, $fid, $new_str);

			$template='';

			$display = ($html_rec == '') ? 'none' : 'block';

			$html .=    "
						<div style=\"clear:left;\" style=\"padding:0px;\">
							<div style=\"float:left;\">{$decalage}<img src=\"./modules/catalogue/img/treeicons/{$icon}.gif\"></div>
							<div style=\"float:left;white-space:nowrap;\">{$link}".dims_strcut($family['label'],25)."</a>&nbsp;&nbsp;</div>
						</div>
						<div style=\"clear:left;display:$display;\" id=\"{$family['id']}\" style=\"padding:0px;\">$html_rec</div>
						";
			$c++;
		}
	}

	return $html;
}

function cata_build_tree($familys, $fromfid = 0, $str = '')
{
	$html = '';

	if (isset($familys['list'])) {
		global $scriptenv;

		$familyId = $_SESSION['catalogue']['familyId'];
		$familysel = $familys['list'][$familyId];

		if (isset($familys['tree'][$fromfid]))
		{
			$c=0;
			foreach($familys['tree'][$fromfid] as $fid)
			{
				$family = $familys['list'][$fid];

				$isfamilysel = ($familyId == $fid);
				$isfamilyopened = strstr($familysel['parents'], "{$family['id_parent']};{$family['id_famille']}");

				$islast = ($c == sizeof($familys['tree'][$fromfid])-1);

				$decalage = str_replace("(b)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/treeicons/empty.gif\"></div>", $str);
				$decalage = str_replace("(s)", "<div style=\"float:left;\"><img src=\"./modules/catalogue/img/treeicons/line.gif\"></div>", $decalage);


				($isfamilysel) ? $style_sel = 'bold' : $style_sel = 'none';

				$icon = 'folder';
				$new_str = ''; // decalage pour les noeuds suivants
				if ($family['depth'] == 1)
					$icon = 'base';
				else
				{
					if (!$islast) $new_str = $str.'(s)'; // |
					else $new_str = $str.'(b)';  // (vide)
				}

				$link = "<a style=\"font-weight:{$style_sel}\" href=\"$scriptenv?famId={$family['id_famille']}\">";
				$link_div ="<a onclick=\"javascript:cata_showfamily('{$fid}','{$new_str}');\" href=\"javascript: void(0);\">";

				if ($family['depth']>1)
				{
					$last = 'joinbottom';
					if ($islast) $last = 'join';
					if (isset($familys['tree'][$fid]))
					{
						if ($islast)
							($isfamilysel || $isfamilyopened) ? $last = 'minus' : $last = 'plus';
						else
							($isfamilysel || $isfamilyopened) ? $last = 'minusbottom' : $last = 'plusbottom';
					}
					$decalage .= "<div style=\"float:left;\" id=\"{$family['id_famille']}_plus\">{$link_div}<img border=\"0\" src=\"./modules/catalogue/img/treeicons/{$last}.gif\"></a></div>";
				}

				$html_rec = '';
				if ($isfamilysel || $isfamilyopened || $family['depth'] == 1) $html_rec = cata_build_tree($familys, $fid, $new_str);

				$template='';
				if ($family['id_modele_list']>0)
					$template .= "<img src="._CATA_ICO_MODELE_LIST." alt=\"Fiche\" border=\"0\">";

				if ($family['id_modele']>0)
					$template .= "<img src="._CATA_ICO_MODELE." alt=\"Fiche\" border=\"0\">";

				$display = ($html_rec == '') ? 'none' : 'block';

				$nbart = ($family['nbart']) ? " ({$family['nbart']})" : '';

				$html .= "
					<div style=\"clear:left;\" style=\"padding:0px;\">
						<div style=\"float:left;\">{$decalage}<img src=\"./modules/catalogue/img/treeicons/{$icon}.gif\"></div>
						<div style=\"float:left;white-space:nowrap;\">{$link}{$family['label']}{$nbart}</a>&nbsp;&nbsp;</div>
						<div>$template</div>
					</div>
					<div style=\"clear:left;display:$display;\" id=\"{$family['id_famille']}\" style=\"padding:0px;\">$html_rec</div>
					";
				$c++;
			}
		}
	}

	return $html;
}

/**
* retrieves recursively the whole family tree
*
* @param string restriction
* @param int id of family to explore
* @return array 2 dimensions array containing the family tree. array[level][familyId] = familyDatas
*/
function getfamilystree($id_module = 0, $all = 0) {
	global $db;

	$family = array();
	if ($id_module == 0 && isset($_SESSION['dims']['moduleid'])) $id_module = $_SESSION['dims']['moduleid'];

	$select = "
		SELECT f.*, fl.* FROM dims_mod_cata_famille f
		INNER JOIN dims_mod_cata_famille_lang fl
		ON fl.id_famille_1 = f.id_famille
		WHERE f.id_module = {$id_module}";
	if($all!=1) $select .= " AND f.visible = 1";
	$select .= " ORDER BY f.id_parent, f.position";

	$result = $db->query($select);
	while ($fields = $db->fetchrow($result)) {
		$family['tree'][$fields['id_parent']][] = $fields['id_famille']; //pour récupere l'ordre d'apparition (position)
		$family['list'][$fields['id_parent']][$fields['id_famille']] = $fields;
		$family['list'][$fields['id_parent']][$fields['id_famille']]['allow'] = 1;
	}

	$tree = array();
	$depth = getallfamilysrec($tree, $family, 1, 0);
	return($tree);
}

function getallfamilysrec(&$tree, $family, $depth, $id_family = 1, $allowtree = 1) {
	global $db;

	$depthmax = $depth;
	$array_test = $family['tree'];
	if (array_key_exists($id_family, $array_test)) {
		foreach ($family['tree'][$id_family] as $position => $id_child) {
			$data = $family['list'][$id_family][$id_child];

			if (isset($tempo)) unset($tempo);

			foreach ($data as $label => $value) {
				$tempo[$label] = $value;
			}
			array_push($tree,$tempo);

			$depthgroup = getallfamilysrec($tree, $family, $depth + 1, $data['id_famille'], 0);
			if ($depthgroup > $depthmax) $depthmax = $depthgroup;
		}
	}
}

function cata_updateparents($idfamille = 0, $parents = '', $depth = 1, $suffix = '') {
	global $db;

	$select = "SELECT * FROM dims_mod_cata_famille{$suffix} WHERE id_parent = $idfamille AND id <> $idfamille ORDER BY position";
	$result = $db->query($select);

	if ($parents!='') $parents .= ';';
	$parents .= $idfamille;

	$position = 0;

	while ($fields = $db->fetchrow($result)) {
		$position++;
		$update = "UPDATE dims_mod_cata_famille{$suffix} SET parents = '$parents', depth = $depth, position = $position WHERE id = {$fields['id']}";
		$db->query($update);
		cata_updateparents($fields['id'], $parents, $depth + 1, $suffix);
	}
}

function cata_getallgroups_treeview(&$ar, $idgroup, $depthlimit, $idgroupstop, $depth=1)
{
/*  echo "depthlimit=$depthlimit<br>";
	echo "idgroupstop=$idgroupstop<br>";
	die();*/

	global $db;
	$groups = array();

	$select = "SELECT * FROM dims_group WHERE system = 0 ORDER BY label";
	$result = $db->query($select);
	while ($fields = $db->fetchrow($result))
	{
		$groups[$fields['id_group']][$fields['id']] = $fields;
	}

	return(cata_getallgroupsrec($ar, $groups, $idgroup, $depthlimit, $idgroupstop, $depth));
}

function cata_getallgroupsrec(&$ar, $groups, $idgroup, $depthlimit, $idgroupstop, $depth=1, $line=0, $fullpath='')
{
	global $db;

	$depthmax = $depth;

	if (array_key_exists($idgroup,$groups))
	{
		foreach($groups[$idgroup] as $fields)
		{
			if ($fields['id'] != $idgroup)
			{
				if (!$idgroupstop || $fields['id'] != $idgroupstop)
				{
					$c=count($ar);

					$parents = $fields['parents'];

					$fullpath_group = '';
					//if ($fullpath=='') $fullpath_group = dims_strcut($fields['label'], 20);
					//else $fullpath_group = $fullpath.' | '. dims_strcut($fields['label'], 20);
					if ($fullpath=='') $fullpath_group = $fields['label'];
					else $fullpath_group = $fullpath.' | '. $fields['label'];

					$ar[$c] = array(
					'depth'=>$depth,
					'idparent'=>$idgroup,
					'id'=>$fields['id'],
					'label'=>$fields['label'],
					'fullpath'=>$fullpath_group,
					'line'=>++$line,
					'parents'=>$parents,
					'code'=>$fields['code']
					);

					if ($depth < $depthlimit || !$depthlimit)
					{
						$depthgroup = cata_getallgroupsrec($ar, $groups, $fields['id'], $depthlimit, $idgroupstop, $depth+1, $line, $ar[$c]['fullpath']);
						if ($depthgroup > $depthmax) $depthmax = $depthgroup;
					}
				}
			}
		}
	}

	return $depthmax;
}

function cata_wceslideshows_gettpl() {
	$tplList = array();

	$dir = $_SERVER["DOCUMENT_ROOT"].'modules/catalogue/templates/slideshows';

	$handler_dir = opendir($dir);

	while (false !== ($file = readdir($handler_dir))) {
		if(is_file($dir.'/'.$file)) {
			$tplName = $file;

			if ( false !== ($extPos = strrpos($file, '.')) && substr($file, -4) == '.tpl'  ) {
				$tplName = substr($file,0,$extPos);
				$tplList[$tplName] = $tplName;
			}
		}
	}

	sort($tplList);

	return $tplList;
}

function build_cms_family_tree($familys, $fromfid = 0) {
	$html = '';
	if(isset($familys['tree'][$fromfid])) {
		$html .= '<ul>';
		foreach($familys['tree'][$fromfid] as $id) {
			$html .= '<li>';
			$detail = $familys['list'][$id];

			$depth = $detail['depth'] - 1;
			if ($depth >= 0) {
				if ($detail['visible']) {
					$script = '/index.php?op=catalogue&param='.$detail['id_famille'];
					if (!empty($detail['url'])) $script = $detail['url'];

					if ($selprec>0 && $selprec!=$detail['id_famille']) {
						// on a le suivant
						$detail['selprec']="selected";
						$selprec=0;
					}
					else $detail['selprec']="";

					$smarty_famille[$localvar][$detail['id_famille']]=array(
					'DEPTH' => $depth,
					'ID_FAMILLE' => $detail['id_famille'],
					'LABEL' => $detail['label'],
					'POSITION' => $detail['position'],
					'LINK' => $script,
					'COLOR'         => $detail['color'],
					'SELPREC' => $detail['selprec']
					);
					if($depth > 0)
						$html .= '<div><a href="'.$script.'">'.$detail['label'].'</a></div>';
					else
						$html .= '<div>'.$detail['label'].'</div>';
				}
			}
			$html .= build_cms_family_tree($familys, $id);
			$html .= '</li>';
		}
		$html .= '</ul>';
	}
	return $html;
}

function cata_genRewrite($label) {
	$label = str_replace(chr(176), '', $label);
	$label = dims_convertaccents(addslashes(html_entity_decode(mb_strtolower(trim($label)), ENT_COMPAT, 'utf-8')));
	$label = str_replace(array("\\", "\"","'",";",".",",","(",")","%","!"),"",$label);
	$label = str_replace(array(" ","/","&", '_', '+', '?', '–', ':'),"-",$label);
	$label = str_replace(array('____', '___', '__'),"-",$label);
	$label = str_replace(array('----', '---', '--'),"-",$label);
	$label = str_replace('²', '2', $label);

	return $label;
}

function cata_genSmartArtRewrite($label, $suffix) {
	$db = dims::getInstance()->getDb();
	$label = cata_genRewrite($label);
	$rs = $db->query('SELECT `urlrewrite` FROM `dims_mod_cata_article'.$suffix.'` WHERE `urlrewrite` LIKE "'.$label.'%"');
	$count = $db->numrows($rs);
	$label .= ($count == 0) ? '' : '_'.$count;
	return $label;
}

function cata_genSmartFamRewrite($label) {
	global $db;
	$label = cata_genRewrite($label);
	$resexist = $db->query("SELECT urlrewrite FROM dims_mod_cata_famille WHERE urlrewrite LIKE '".$label."%'");
	$suffixe = 0;
	$count = $db->numrows($resexist);
	while ($tab = $db->fetchrow($resexist)) {
		$current = $tab["urlrewrite"];

		if (preg_match('/_[0-9]$/', $current)) {
			$cursuf = substr($current, -1);
			if($cursuf >= $suffixe) $suffixe = $cursuf;
		}
	}
	$label .= ($count==0)?'':'_'.($suffixe+1);
	return $label;
}

function isTherePromotions() {
	if($_SESSION['dims']['connected']) {
		if(!isset($_SESSION['catalogue']['hasPromotion'])) {
			$db = dims::getInstance()->getDb();
			$date_cur = date('Ymd');

			include_once DIMS_APP_PATH."/modules/catalogue/include/class_article.php";

			include_once DIMS_APP_PATH."/modules/catalogue/include/class_param.php";
			$defLang = cata_param::getDefaultLang();

			if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
				$sql = "SELECT  a.*,
						a.page AS numpage,
						af.id_famille,
						m.libelle AS marque_label

						FROM    ".article::TABLE_NAME." a

						LEFT JOIN   dims_mod_cata_article_famille af
						ON          af.id_article = a.id

						LEFT JOIN   dims_mod_cata_marque m
						ON          m.id = a.marque

						WHERE   a.published = 1
						AND     $date_cur BETWEEN a.ddpromo AND a.dfpromo
						AND     a.id_lang = $defLang

						GROUP BY a.reference";
			}
			else {
				$sql = "SELECT  a.*,
						a.page AS numpage,
						af.id_famille,
						m.libelle AS marque_label

						FROM    ".article::TABLE_NAME." a

						LEFT JOIN   dims_mod_cata_article_famille af
						ON          af.id_article = a.id

						LEFT JOIN   dims_mod_cata_marque m
						ON          m.id = a.marque

						WHERE   a.published = 1
						AND     $date_cur BETWEEN a.ddpromo AND a.dfpromo
						AND     a.id_lang = $defLang

						GROUP BY a.reference";
			}
			$res = $db->query($sql);
			$_SESSION['catalogue']['hasPromotion'] = (bool)$db->numrows($res);
		}

		return $_SESSION['catalogue']['hasPromotion'];
	}
	else {
		return false;
	}

}

function cata_getLastPromo() {
	global $db;
	$ts = dims_createtimestamp();

	$db->query("
		SELECT  *
		FROM    dims_mod_cata_promotions
		WHERE   active = 1
		AND     date_debut <= $ts
		AND     date_fin >= $ts
		ORDER BY id
		LIMIT 0,1");
	if ($db->numrows()) {
		$row = $db->fetchrow();
		return $row;
	}
	else {
		return array();
	}
}

function cata_orderByFamily($articles) {
	global $db;

	$a_fam = array();

	$rs = $db->query('
		SELECT  a.reference, af.id_famille
		FROM    dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		WHERE   a.reference IN ('.implode(',', $articles).')

		ORDER BY af.position');
	if ($db->numrows($rs)) {
		while ($row = $db->fetchrow($rs)) {
			// recherche des familles parentes
			$a_parents = explode(';', $_SESSION['catalogue']['familys']['list'][$row['id_famille']]['parents']);
			$fam1 = $_SESSION['catalogue']['familys']['list'][$a_parents[2]]['label'];
			$fam2 = $_SESSION['catalogue']['familys']['list'][$a_parents[3]]['label'];

			$a_fam[$fam1][$fam2][] = strtoupper($row['reference']);
		}
	}

	return $a_fam;
}

function catalogue_getImageDimensions($imagefile) {
	$filename_array = explode('.', $imagefile);
	$extension = strtolower($filename_array[sizeof($filename_array) - 1]);

	switch ($extension) {
		case 'jpg':
		case 'jpeg':
			$imgsrc = ImageCreateFromJpeg($imagefile);
			break;
		case 'png':
			$imgsrc = ImageCreateFromPng($imagefile);
			break;
		case 'gif':
			$imgsrc = imageCreateFromGif($imagefile);
			break;
		default:
			return(0);
			break;
	}

	$w = imagesx($imgsrc);
	$h = imagesy($imgsrc);

	return array('w' => $w, 'h' => $h);
}

function catalogue_format_tel($value) {
	// string to char array
	$value_array = str_split($value);
	$value = '';

	foreach ($value_array as $char) {
		if (is_numeric($char) || $char == '+' || $char == '(' || $char == ')') {
			$value .= $char;
		}
	}

	return $value;
}

function catalogue_display_tel($value) {
	$value = catalogue_format_tel($value);

	// standard size
	if (strlen($value) == 10) {
		$flag = preg_match(_VARIANTE_FORMAT_TEL, $value, $result);
	}
	else {
		$flag = preg_match(_VARIANTE_FORMAT_TELINT, $value, $result);
	}

	if ($flag) {
		if (isset($result[0])) unset($result[0]);
		$value = implode('.',$result);
	}

	return $value;
}

function catalogue_date2see($date, $retour = 'D', $mess_unknow = '') {
	if (($date != '') && ($date > 0)) {
		if (!(strpos($date, '/') === FALSE)) {
			$segment = explode(' ', $date);
			(isset($segment[0])) ? $ma_date = $segment[0] : $ma_date = '00/00/0000';
			(isset($segment[1])) ? $mon_heure = $segment[1] : $mon_heure = '00:00:00';
			$date = dims_local2timestamp($ma_date, $mon_heure);
		}
		$my_date = dims_timestamp2local($date);
		$my_date_return = '';

		$tag = array('D','T','H','/r');
		$data = array($my_date['date'], $my_date['time'], $my_date['time'], '<br>');

		$my_date_return = str_replace($tag, $data, $retour);

		if (trim($my_date_return) == '' || trim($my_date_return) == '//') $my_date_return = $mess_unknow;
		return $my_date_return;
	}
	else {
		return $mess_unknow;
	}
}

function cata_getSiteMap($cata_module_id) {
	global $db;

	$content = "";
	$maxupdatetime = 0;

	// chargement des familles dans toutes les langues
	$familys = cata_getfamilys();

	// generation de la liste des familles
	$select = "
		SELECT  f.id,
				f.date_modify,
				f.urlrewrite
		FROM    dims_mod_cata_famille f
		WHERE   f.id_module = {$cata_module_id}
		AND     f.visible = 1";
	$res=$db->query($select);

	if ($db->numrows($res)) {
		while ($fam = $db->fetchrow($res)) {
			$updatedate=$fam['date_modify'];
			if($updatedate!='')
				$updatedate=substr($updatedate,0,4)."-".substr($updatedate,4,2)."-".substr($updatedate,6,2);
			else
				$updatedate=date("Y-m-d");

			if ($updatedate>$maxupdatetime) $maxupdatetime=$updatedate;

			if ($fam["urlrewrite"]!="") {
				// on a la liste des parents avec lui
				if (isset($familys['list'][$fam['id']]['parents'])) {
					$lsth = $familys['list'][$fam['id']]['parents'].";".$fam['id'];
					$lsth = explode(";", $lsth);
				}
				else {
					$lsth = array();
				}

				$url = stripslashes('<HOSTNAME>/'.$familys['list'][$fam['id']]['urlrewrite']);
			}
			if ($fam['changefreq']=="") $fam['changefreq']="monthly";
			if ($fam['priority']==0) $fam['priority']=0.5;
			$content.="<url><loc>".$url."</loc><lastmod>".$updatedate."</lastmod><changefreq>".$fam['changefreq']."</changefreq><priority>".$fam['priority']."</priority></url>\n";
		}
	}


	// generation de la liste des articles
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';
	$oCatalogue = new catalogue();
	$oCatalogue->open($cata_module_id);
	$oCatalogue->loadParams();
	$id_company = $oCatalogue->getParams('show_stock_from_company');

	$sql = '
		SELECT  a.urlrewrite,
				a.date_modify
		FROM    dims_mod_cata_article a';
	if ($id_company > 0) {
		$sql .= '
			INNER JOIN 	`dims_mod_cata_stocks` s
			ON 			s.`id_company` = '.$id_company.'
			AND 		s.`id_article` = a.`id`
			AND 		s.`held_in_stock` = 1';
	}
	$sql .= '
		WHERE   a.id_module = '.$cata_module_id.'
		AND     a.published = 1';

	$rs = $db->query($sql);
	while ($row = $db->fetchrow($rs)) {
		$url='<HOSTNAME>/article/'.$row['urlrewrite'].'.html';
		$updatedate=$row['date_modify'];
		$updatedate=substr($updatedate,0,4)."-".substr($updatedate,4,2)."-".substr($updatedate,6,2);
		if ($updatedate>$maxupdatetime) $maxupdatetime=$updatedate;

		if ($row['changefreq']=="") $row['changefreq']="weekly";
		if ($row['priority']==0) $row['priority']=0.5;
		$content.="<url><loc>".$url."</loc><lastmod>".$updatedate."</lastmod><changefreq>".$row['changefreq']."</changefreq><priority>".$row['priority']."</priority></url>\n";
	}

	// generation de la page d'accueil
	$content.="<url><loc><HOSTNAME></loc><lastmod>".$maxupdatetime."</lastmod><changefreq>weekly</changefreq><priority>1</priority></url>\n";

	return $content;
}

// retourne l'URI après ajout du param
function cata_addParamToURI($URI, $paramToAdd, $valueToAdd) {
	$aQuery = parse_url($URI);

	$queryParts = isset($aQuery['query']) ? explode('&', $aQuery['query']) : array();
	$queryParts[] = $paramToAdd.'='.$valueToAdd;

	return $aQuery['path'].'?'.implode('&', $queryParts);
}

// retourne l'URI après suppression du param
function cata_dropParamFromURI($URI, $paramToRemove) {
	$aQuery = parse_url($URI);

	$a_params = array();
	if (isset($aQuery['query'])) {
		$queryParts = explode('&', $aQuery['query']);
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			if ($item[0] != $paramToRemove) {
				$a_params[] = $param;
			}
		}
	}

	if (sizeof($a_params)) {
		return $aQuery['path'].'?'.implode('&', $a_params);
	}
	else {
		return $aQuery['path'];
	}

}

// Synchronisation des adresses
function sync_address($params = array()) {
	if (defined('_CATA_VARIANTE') && function_exists('variante_sync_address')) {
		call_user_func_array('variante_sync_address', func_get_args());
	}
}
