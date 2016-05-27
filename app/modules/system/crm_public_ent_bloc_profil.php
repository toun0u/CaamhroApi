<?php
	if(!empty($ent_id))		{
		$ct = $ent_id;
		$action = _BUSINESS_TAB_ENT_FORM;
				$recordid=$ent_id;
				echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_CURPROFIL'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_zoom.png','26px', '26px', '-17px', '-5px', "$tabscriptenv&action=".$action."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct, '', '');
	}

	//


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . '/modules/system/class_tiers_layer.php');
$ent = new tiers();

$ent->open($recordid);
$_SESSION['business']['tiers_id']=$recordid;

$entworkspace = new tiers_layer();
$entworkspace->init_description();

$entuser = new tiers_layer();
$entuser->init_description();

if (isset($ent->fields['id']) && $ent->fields['id']>0) {
	// requete selection layer
	$sql =	"
			SELECT		*
			FROM		dims_mod_business_tiers_layer
			WHERE		id = :id
			AND			((type_layer<=1 and id_layer= :idfrom )
			OR			(type_layer=2 and id_layer= :userid )";

	// a ajouter : dimension partage
	$sqlshare = "select		*
				from		dims_share
				where		id_module=1
				and			id_object= :idobject
				and			id_record= :idrecord
				and			(type_from=0
				and			id_from= :idfrom
				and			level_from=0)
				OR			(type_share=1 and id_from= :idfrom )
				OR			(type_share=2 and id_from= :userid )";

	$res=$db->query($sqlshare, array(
		':idobject'	=> dims_const::_SYSTEM_OBJECT_TIERS,
		':idrecord'	=> $ent->fields['id'],
		':idfrom'	=> $_SESSION['dims']['workspaceid'],
		':userid'	=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
			// test si share actif pour l'espace courant
			if ($f['type_from']==0 && $f['id_from']==$_SESSION['dims']['workspaceid'] && $f['level_from']==0) {
				$shareactive=true;
			}
			else {
				// on peut charger les autres valeurs
				$sql .= " OR (type_share=".$f['type_share']." and id_share=".$f['id_share'].")";
			}
		}
	}

	// on termine la requete par le tri
	$sql.=")			ORDER BY	timestp_modify,type_layer";

	$rs=$db->query($sql, array(
		':id'	=> $ent->fields['id'],
		':idfrom'	=> $_SESSION['dims']['workspaceid'],
		':userid'	=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($rs)>0) {

		while ($f=$db->fetchrow($rs)) {
			$layers[$f['type_layer']]=$f;
			$owner=false;

			// ouverture de l'objet concerne
			if ($f['type_layer']==1) {
				if ($f['id_layer']==$_SESSION['dims']['workspaceid']) {
					$entworkspace->open($ent->fields['id'],1,$f['id_layer']);
					$owner=true;
				}
				else {

				}
			}
			else {
				if ($f['id_layer']==$_SESSION['dims']['userid']) {
					$entuser->open($ent->fields['id'],2,$f['id_layer']);
					$owner=true;
				}
			}

			// on remplit les champs courants pour le layer concerne
			if ($owner) {
				foreach($f as $name=>$val) {
					if ($val!="" && isset($convmeta[$name])) {
						$idmeta=$convmeta[$name];
						if (!isset($_SESSION['dims']['tiers_fields_view'][$idmeta]) || isset($_SESSION['dims']['tiers_fields_view'][$idmeta]) && $_SESSION['dims']['tiers_fields_view'][$idmeta]!=2) {
							$_SESSION['dims']['tiers_fields_view'][$idmeta]=$f['type_layer']; // public
						}

						if ($f['type_layer']==1) {
							$entworkspace->fields[$name]=$val;
						}
						else {
							$entuser->fields[$name]=$val;
						}
					}
				} // end of foreach
			} // end of owner
		}
	}
}

$lstfield=$ent->getDynamicFields();

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td width="100%" style="vertical-align:top;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
					<tr >
						<td style="" width="100%">
							<?
							//echo $ent->fields['intitule'];
							// recherche des champs de telephone
							foreach($lstfield as $k=>$f) {

								$label=$f['labelfield'];
								if (isset($_DIMS['cste'][$label])) {
									$label=$_DIMS['cste'][$label];
								}
								else {
									$label=$f['name'];
								}
								$value="";

								if ($f['format']!='phone' && $f['format']!='email') {
									if (isset($ent->fields[$f['namefield']]) && $ent->fields[$f['namefield']]!='')	$value.= ($ent->fields[$f['namefield']])."<br>";
									if (isset($entworkspace->fields[$f['namefield']]) && $entworkspace->fields[$f['namefield']]!='')	$value.= ($entworkspace->fields[$f['namefield']])."<br>";
									if (isset($entuser->fields[$f['namefield']]) && $entuser->fields[$f['namefield']]!='')	$value.= ($entuser->fields[$f['namefield']])."<br>";
									if ($f['namefield']=='photo') {
										$value=str_replace("<br>","",$value);
										echo "<span style='clear:both;float:left;width:80px;margin-top:3px;'>".$label."</span>";

										if(file_exists(DIMS_WEB_PATH.'data/photo_ent/ent_'.$ent->fields['id'].'/photo100'.$value.'.png')) {
											echo "<span style='float:left;'>";
											echo '<img src="'._DIMS_WEBPATHDATA.'photo_ent/ent_'.$ent->fields['id'].'/photo100'.$value.'.png"/>';

											echo "</span>";
										}

									}
									else if ($value!='') {
										echo "<span style='clear:both;float:left;width:80px;margin-top:3px;font-weight:bold'>".$label."</span>";
										echo "<span style='float:left;'>".dims_strcut($value,250);
										echo "</span>";
									}
								}
							}

							?>
						</td>
						<td style="width:50%">
							<table style="width:100%" cellpadding="0" cellspacing="0">
								<tr>
									<td style="vertical-align:top;">
									<?
									// recherche des champs de telephone
									foreach($lstfield as $k=>$f) {

										if ($f['format']=='phone') {

											$label=$f['labelfield'];
											if (isset($_DIMS['cste'][$label])) {
												$label=$_DIMS['cste'][$label];
											}
											else {
												$label=$f['name'];
											}

											echo "<span style='clear:both;float:left;width:80px;margin-top:3px;'>".$label."</span>";

											echo "<span style='float:left;'>";

											if (isset($ent->fields[$f['namefield']]) && $ent->fields[$f['namefield']]!='')	echo '<img src="./common/img/all.png">&nbsp;'.dims_format_phone($ent->fields[$f['namefield']])."<br>";
											if (isset($entworkspace->fields[$f['namefield']]) && $entworkspace->fields[$f['namefield']]!='')	echo '<img src="./common/img/users.png">&nbsp;'.dims_format_phone($entworkspace->fields[$f['namefield']])."<br>";
											if (isset($entuser->fields[$f['namefield']]) && $entuser->fields[$f['namefield']]!='')	echo '<img src="./common/img/user.png">&nbsp;'.dims_format_phone($entuser->fields[$f['namefield']])."<br>";

											echo "</span>";
										}
									}

									// recherche des champs emails
									foreach($lstfield as $k=>$f) {

										if ($f['format']=='email') {

											$label=$f['labelfield'];
											if (isset($_DIMS['cste'][$label])) {
												$label=$_DIMS['cste'][$label];
											}
											else {
												$label=$f['name'];
											}

											echo "<span style='clear:both;float:left;width:50px;margin-top:3px;'>".$label."</span>";
											echo "<span style='float:left;'>";

											if (isset($ent->fields[$f['namefield']]) && $ent->fields[$f['namefield']]!='')	echo '<img src="./common/img/all.png">&nbsp;<a href="mailto:'.$ent->fields[$f['namefield']].'">'.$ent->fields[$f['namefield']]."</a><br>";
											if (isset($entworkspace->fields[$f['namefield']]) && $entworkspace->fields[$f['namefield']]!='')	echo '<img src="./common/img/users.png">&nbsp;<a href="mailto:'.$entworkspace->fields[$f['namefield']].'">'.$entworkspace->fields[$f['namefield']]."</a><br>";
											if (isset($entuser->fields[$f['namefield']]) && $entuser->fields[$f['namefield']]!='')	echo '<img src="./common/img/user.png">&nbsp;<a href="mailto:'.$entuser->fields[$f['namefield']].'">'.$entuser->fields[$f['namefield']]."</a><br>";

											echo "</span>";
										}
									}
									?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<!--<td align="center" style="padding:3px;vertical-align:top;">
				<img src="./common/img/photo_user.png"/>
			</td>-->
		</tr>
	</tbody>
</table>

<?
if(!empty($ent_id)){
	echo $skin->close_widgetbloc();
}
?>
