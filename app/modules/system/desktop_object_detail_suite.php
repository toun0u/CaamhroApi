<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// on ajoute les personnes liées à ce contact

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
$inworkspace = '';
foreach($lstworkpaces as $key => $tab) {
	$inworkspace .= "'".$key."',";
}
$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

$tab_link = array();
$tab_other_lk = array();
$_SESSION['contact']['current_last_modify'] = array();

$countdirect = 0;
//recherche des liens entre personnes sens 1
$params = array();
$sql_li1 = "SELECT			c.id as id_pers, c.lastname, c.firstname,c.photo,
							l.*,
							byc.lastname as by_lastname, byc.firstname as by_firstname,
                                                        u.id as iduser
			FROM			dims_mod_business_contact c
			INNER JOIN		dims_mod_business_ct_link l
			ON				l.id_contact2 = c.id
			AND 			l.id_workspace IN (".$db->getParamsFromArray(explode(',', $inworkspace), 'inworkspace', $params).")
			LEFT JOIN		dims_mod_business_contact byc
			ON				byc.id = l.id_ct_user_create
            LEFT JOIN               dims_user as u
            ON                      u.id_contact = c.id
			WHERE			l.id_contact1 = :idcontact1
			AND				id_object = :idobject ";
$params[':idcontact1'] = $contact_id;
$params[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;
if ($ct_filter!='') {
	$sql_li1.=" AND ( c.lastname like :ctfilter OR c.firstname like :ctfilter ) ";
	$params[':ctfilter'] = "%".$ct_filter."%";
}

if(isset($upname) && $upname == 1 ) {
	$sql_li1 .= " ORDER BY		c.lastname DESC, c.firstname, l.link_level, l.type_link";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
	$sql_li1 .= " ORDER BY		c.lastname ASC, c.firstname, l.link_level, l.type_link";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
	$sql_li1 .= " ORDER BY		l.type_link DESC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
	$sql_li1 .= " ORDER BY		l.type_link ASC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
	$sql_li1 .= " ORDER BY		by_lastname DESC, by_firstname DESC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
	$sql_li1 .= " ORDER BY		by_lastname ASC, by_firstname ASC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li1 .= " ORDER BY		l.time_create DESC, c.lastname, c.firstname";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}

//UPDATE `dims_user` SET `id_contact` = '5498' WHERE `dims_user`.`id` =142;
$res_li1 = $db->query($sql_li1, $params );
while($tab_li1 = $db->fetchrow($res_li1)) {
	//lien de type personnel (que l'on fait � partir du bouton 'Ajouter � votre liste de contacts')
	if($tab_li1['link_level'] == 3 && ($search_linkct_type==0 || $search_linkct_type==3)) {
		$tab_li1['type']='private';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
		$prout= true;
	}
	//lien de type metier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
	if($tab_li1['link_level'] == 2 && ($search_linkct_type==0 || $search_linkct_type==2)) {
		$tab_li1['type']='workspace';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
		$prout= true;
	}

	//liens de type g�n�rique (ceux que l'on fait a partir du formualire de gestion des liens dans la fiche contact)
	if($tab_li1['link_level'] == 1 && ($search_linkct_type==0 || $search_linkct_type==1)) {
		$tab_li1['type']='public';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
		$prout= true;
	}

        if ($tab_li1['iduser']>0) {
            $countdirect++;
        }
}

//echo count($tab_link['between_pers']);

//un lien est bidirectionnel, il faut donc rechercher � partir de contact2 aussi
$params = array();
$sql_li2 = "SELECT			c.id as id_pers, c.lastname, c.firstname,c.photo,
							l.*,
							byc.lastname as by_lastname, byc.firstname as by_firstname,
                                                        u.id as iduser
			FROM			dims_mod_business_contact c
			INNER JOIN		dims_mod_business_ct_link l
			ON				l.id_contact1 = c.id
			AND 			l.id_workspace IN (".$db->getParamsFromArray(explode(',', $inworkspace), 'inworkspace', $params).")
			LEFT JOIN		dims_mod_business_contact byc
			ON				byc.id = l.id_ct_user_create
                        LEFT JOIN               dims_user as u
                        ON                      u.id_contact = c.id
			WHERE			l.id_contact2 = :idcontact2
			AND				id_object = :idobject ";
$params[':idcontact1'] = $contact_id;
$params[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;

if ($ct_filter!='') {
	$sql_li2.=" AND ( c.lastname like :ctfilter OR c.firstname like :ctfilter ) ";
	$params[':ctfilter'] = "%".$ct_filter."%";
}
if(isset($upname) && $upname == 1 ) {
	$sql_li2 .= " ORDER BY		c.lastname DESC, c.firstname, l.link_level, l.type_link";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
	$sql_li2 .= " ORDER BY		c.lastname ASC, c.firstname, l.link_level, l.type_link";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
	$sql_li2 .= " ORDER BY		l.type_link DESC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
	$sql_li2 .= " ORDER BY		l.type_link ASC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
	$sql_li2 .= " ORDER BY		by_lastname DESC, by_firstname DESC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
	$sql_li2 .= " ORDER BY		by_lastname ASC, by_firstname ASC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li2 .= " ORDER BY		l.time_create DESC, c.lastname, c.firstname";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}

$res_li2 = $db->query($sql_li2, $params);
while($tab_li2 = $db->fetchrow($res_li2)) {
	//lien de type personnel (que l'on fait � partir du bouton 'Ajouter � votre liste de contacts')
	if($tab_li2['link_level'] == 3 && ($_SESSION['dims']['user']['id_contact'] == $tab_li2['id_contact1']  || $_SESSION['dims']['user']['id_contact'] == $tab_li2['id_contact2']) && ($search_linkct_type==0 || $search_linkct_type==3))  {
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='private';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}
	//lien de type m�tier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
	if($tab_li2['link_level'] == 2 && $tab_li2['id_workspace'] == $_SESSION['dims']['workspaceid'] && ($search_linkct_type==0 || $search_linkct_type==2)) {
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='workspace';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}
	elseif($tab_li2['link_level'] == 2) {
		//on cr� un tableau pour indiquer les autres workspaces ayant des valeurs
		//$tab_other_lk[$tab_li2['id_workspace']]['nb_link']++;
		//$tab_other_lk[$tab_li1['id_workspace']]['id_user'] = $tab_li2['id_user'];
		$tab_li2['type_link']='';
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='workspace';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}//liens de type g�n�rique (ceux que l'on fait a partir du loc de gestion des liens dans la fiche contact)
	if($tab_li2['link_level'] == 1 && ($search_linkct_type==0 || $search_linkct_type==1)) {
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='public';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}

        if ($tab_li2['iduser']>0) {
            $countdirect++;
        }
}

$_SESSION['contact']['current_last_modify'] = $tab_other_lk;
$list_wkspce = $dims->getWorkspaces();

$count = 0;
if(!empty($tab_link['between_pers'])) $count = count($tab_link['between_pers']);

$linkcontacts=dims_urlencode("/admin.php?dims_mainmenu=9&cat=0&action=307&part=303&dims_from=home&contact_id=".$contact_id);

$url= urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/admin-light.php?dims_op=socialbrowser&xml_id="));
$src_w=0;
$logo='';
$src_h=0;
$path=realpath('./')."./common/templates/backoffice".$_SESSION['dims']['currentworkspace']['admin_template']."/logo/socialnetwork_".$_SESSION['dims']['currentlang'].".png";

if (file_exists($path)) {
        $size = GetImageSize($path);
        $src_w = $size[0];
        $src_h = $size[1];
        $logo=urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."./common/templates/backoffice".$_SESSION['dims']['currentworkspace']['admin_template']."/logo/socialnetwork_".$_SESSION['dims']['currentlang'].".png"));
}

$linknetwork= '&nbsp;&nbsp;<a href="/admin-light.php?dims_mainmenu=9&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_GRAPH.'&max_visible=1000&max_ops=10&logowidth='.$src_w.'&logoheight='.$src_h.'&logo='.$logo.'&xml_id=ct_'.$contact_id.'&url='.$url.'" target="_blank"><img src="./common/img/public.png" alt="" border="0">&nbsp;
    '.$_DIMS['cste']['_DIMS_LABEL_SOCIAL_NETWORK']." ".$contact->fields['firstname']." ".$contact->fields['lastname'].'</a>';

$viewallcontacts="&nbsp;<a href=\"".$linkcontacts."\"><img src=\"./common/img/view.png\" style=\"border:0px;\">&nbsp;View all contacts</a>";

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_CONT']." (".$count.") - ".$viewallcontacts." ".$linknetwork, "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkp');", ""); ?>

<div id="lkp" style="display:block;">

<?

foreach($tab_link['between_pers'] as $id_perstoview => $tab_pers) {

    if ($tab_pers['iduser']>0) { // lien direct
        // on check la photo

        if($tab_pers['photo'] == "" || !file_exists(DIMS_WEB_PATH.'data/photo_cts/contact_'.$tab_pers['id_pers'].'/photo60'.$tab_pers['photo'].'.png')) {
                $photo ='<img src="./common/img/contact.gif">';
        }
        else {
                $photo = '<img style="height:38px;" src="'._DIMS_WEBPATHDATA.'/photo_cts/contact_'.$tab_pers['id_pers'].'/photo60'.$tab_pers['photo'].'.png" onclick="javascript:affichePhoto(\'big_photo\');" title="'.$_DIMS['cste']['_DIMS_TITLE_DISP_PHOTO'].'"/>';
        }
     echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                '.$photo.'
                <br><input type="button" onclick="" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'"/>
        </div>';
    }
}

/*
<table width="100%" cellpadding="0" cellspacing="3">
	<tr>
		<td>
			<div id="affiche_link_1" style="display:block;width:100%;height:250px;overflow:auto;">
				<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
					<tbody>
						<?
							if(!empty($tab_link['between_pers'])) {
						?>
							<tr class="trl1" style="font-size:12px;">
								<td style="width: 1%;"/>
								<td style="width: 45%;"><? echo $_DIMS['cste']['_DIMS_LABEL_PERSONNE']; ?></td>
								<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></td>
								<td style="width: 19%;"><? echo $_DIMS['cste']['_DIMS_OPTIONS']; ?></td>
							</tr>
						<?
								$class_col = 'trl1';
								foreach($tab_link['between_pers'] as $id_perstoview => $tab_pers) {

									if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
									$date_c = dims_timestamp2local($tab_pers['time_create']);
									if(!empty($tab_pers['date_deb'])) $date_deb = dims_timestamp2local($tab_pers['date_deb']); else $date_deb['date'] = "-";
									if(!empty($tab_pers['date_fin'])) $date_fin = dims_timestamp2local($tab_pers['date_fin']); else $date_fin['date'] = "-";
										echo '	<tr class="'.$class_col.'">
													<td></td>
													<td style="cursor: default;" id="tickets_title_3">
														<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'</a>
													</td>
													<td style="cursor: default;" id="tickets_title_3">
														<a href="'.dims_urlencode('admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create']).'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
													</td>';

										if (($tab_pers['type']=='workspace' && $tab_pers['id_workspace']==$_SESSION['dims']['workspaceid']) || ($tab_pers['type']=='public' && ($tab_pers['id_user'] == $_SESSION['dims']['userid'] || $tab_pers['id_ct_user_create'] == $_SESSION['dims']['user']['id_contact']))) {
											echo '
												<td align="center" style="cursor: default;" id="tickets_title_3">
													 <a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_inf_'.$tab_pers['id'].'\');"><img src="./common/img/view.png" style="border:0px;" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"/></a>

												</td>
											</tr>
											<tr class="'.$class_col.'"><td colspan="5">
											<div id="div_inf_'.$tab_pers['id'].'" style="display:none;">
												<table width="100%">
													<tr>
														<td align="right" width="25%">'.$_DIMS['cste']['_DIMS_LABEL_CREATE_ON'].' :
														</td>
														<td align="left">'.$date_c['date'].'
														</td>
													</tr>
													<tr>
														<td align="right">'.$_DIMS['cste']['_BEGIN'].' :
														</td>
														<td align="left">'.$date_deb['date'].'
														</td>
													</tr>
													<tr>
														<td align="right">'.$_DIMS['cste']['_END'].' :
														</td>
														<td align="left">'.$date_fin['date'].'
														</td>
													</tr>
													<tr>
														<td align="right">'.$_DIMS['cste']['_DIMS_COMMENTS'].' :
														</td>
														<td align="left">'.$tab_pers['commentaire'].'
														</td>
													</tr>
												</table>
											</div>
											</td></tr>
											';
										}
										else {
											echo '<td align="center" style="cursor: default;" id="tickets_title_3">&nbsp;</td></tr>';
										}
								}
							}
							else {
								echo '<tr class="trl1"><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
							}
						?>

				   </tbody>
			   </table>
			</div>
		</td>
	</tr>
</table>

<?
 */
// on construit avec des boutons

?>
</div>
<? echo $skin->close_simplebloc();

echo "<br>";
// on s'occupe des tags maintenant
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;clear:both;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
//selection des groupe dont fait parti le contact
	$sqlg = "SELECT			distinct t.id,
							t.type,
							t.tag as label,
							t.private,
							t.id_user as id_user_create,
							t.id_workspace,
							l.id_record,
							l.id_tag
				FROM        dims_tag as t
				INNER JOIN  dims_tag_index as l
				ON          l.id_tag = t.id
				AND         l.id_tag = t.id
			AND             l.id_record = :idrecord
			AND				l.id_object = :idobject
			WHERE           ((t.id_workspace = :idworkspace and private=0) OR (t.id_user = :iduser and private=1)) or t.type>0";
	/// modification de selection sur le type et non le module_id
	$resg = $db->query($sqlg, array(
		':idrecord'		=> $_SESSION['dims']['current_object']['id_record'],
		':idobject'		=> $_SESSION['dims']['current_object']['id_object'],
		':idworkspace'	=> $_SESSION['dims']['workspaceid'],
		':iduser'		=> $_SESSION['dims']['userid']
	));
	$selectedtags=array();
	if($db->numrows($resg) > 0) {
		$content .= '<table width="100%">';
		while($f = $db->fetchrow($resg)) {

                    // remplacement de la langue
                    if (isset($_DIMS['cste'][$f['label']])) {
                            $f['label'] = $_DIMS['cste'][$f['label']];
                    }
                    echo "<span style=\"float:left;margin:10px;\"><a href=\"javascript:void(0);\" onclick=\"javascript:updateSearchTag(".$f['id'].",1);\" style='font-weight:none;font-size:".$size."px;margin:2px;'>".ucfirst(strtolower($f['label']))."</a></span>";
                }
        }
echo $skin->close_simplebloc();


?>
