<?php

// construction des cat�gories et champs dynamiques
$categcour=0;
// 1ere requete pour les champs attaches aux rubriques g�n�riques
$sql =	"	SELECT		mf.*,mc.label as categlabel, mc.id as id_cat
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			AND			mf.id_object = :idobject
			AND			mc.admin=1
			WHERE		mc.id=1
			ORDER BY	mc.position, mf.position
			";

$rubgen=array();
$rubgen_cpteworkspace=0;
$rubgen_cpteworkspace=0;
$lstworkspace=array();
$rs_fields=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));
$color="";
while ($fields = $db->fetchrow($rs_fields)) {
	if (!isset($rubgen[$fields['id_cat']]))  {
		$rubgen[$fields['id_cat']]=array();
		$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
		if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
	}

	// on ajoute maintenant les champs dans la liste
	$fields['use']=0;// par defaut non utilise
	$fields['enabled']=array();
	if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;
}
//dims_print_r($rubgen);;
// construction de la liste des usages
$sql= " SELECT		mu.*,
					mf.id_metacateg,
					w.label as labelworkspace
		from		dims_mod_business_meta_use as mu
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=mu.id_metafield
		AND			mu.id_object= :idobject
		LEFT JOIN	dims_workspace as w
		ON			w.id=mu.id_workspace
		WHERE		mf.id_metacateg = 1";

$res=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
));

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {

		if ($f['sharemode']==1) {
			// on doit verifier si le currentworkspace fait parti de la liste
			if ($_SESSION['dims']['workspaceid']==$f['id_workspace']) {
				// on est dans la selection
				$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=1;
			}

			// on stocke les workspaces qui l'utilisent
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['enabled'][$f['id_workspace']]=$f['id_workspace'];
		}
		else {
			// on est a 2 on met le flag a 2
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=2;
		}

		// construciton de la liste des workspaces
		if (!isset($lstworkspace[$f['id_workspace']])) $lstworkspace[$f['id_workspace']]=$f['labelworkspace'];

		//on veut sacoir s'il y a des workspace pour le partage
		if(!empty($f['id_workspace'])) {
			$rubgen_cpteworkspace++;
		}
	}
}

// construction des valeurs saisies dans les champs en ne tenant compte que des derni�res modifications
$ctfields=array();
$sql= " SELECT		cf.*,
					w.label as labelworkspace,
					u.firstname,u.lastname
		from		dims_mod_business_contact_field as cf
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=cf.id_metafield
		AND			mf.id_metacateg=1
		LEFT JOIN	dims_workspace as w
		ON			w.id=cf.id_workspace
		LEFT JOIN	dims_user as u
		ON			u.id=cf.id_user
		WHERE		cf.id_contact= :idcontact
		AND			lastmodify=1
		ORDER BY	cf.timestp_modify DESC";

$res=$db->query($sql, array(
	':idcontact' => $contact->fields['id']
));

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		// test si champ existe
		if (!isset($ctfields[$f['id_metafield']]))	$ctfields[$f['id_metafield']]=array();

		// test si workspace existe
		if (!isset($ctfields[$f['id_metafield']][$f['id_workspace']])) $ctfields[$f['id_metafield']][$f['id_workspace']]=array();

		// on prend la derni�re modif de chaque personne, le reste en histo
		// on test d'abord si personnel ou non
		if ($f['private'] && $f['id_user']==$_SESSION['dims']['userid'] && !isset($ctfields[$f['id_metafield']][$f['id_workspace']]['private'])) {
			$elem=array();
			$elem['timestp_modify']=$f['timestp_modify'];
			$elem['id_user']=$f['id_user'];
			$elem['value']=$f['value'];
			$elem['id_lang']=$f['id_lang'];
			$elem['username']=$f['lastname']." ".$f['firstname'];
			$ctfields[$f['id_metafield']][$f['id_workspace']]['private']=$elem;
		}
		else {
			// on recup�re que les valeurs des autres en non privees
			if (!isset($ctfields[$f['id_metafield']][$f['id_workspace']]['public']) && !$f['private']) {
				$elem=array();
				$elem['timestp_modify']=$f['timestp_modify'];
				$elem['id_user']=$f['id_user'];
				$elem['value']=$f['value'];
				$elem['id_lang']=$f['id_lang'];
				$elem['username']=$f['lastname']." ".$f['firstname'];

				$ctfields[$f['id_metafield']][$f['id_workspace']]['public']=$elem;
			}
		}
	}
}
?>
<form action="" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "save_contact");
	$token->field("action",_BUSINESS_TAB_CONTACTSTIERS);
	$token->field("ct_address");
	$token->field("ct_postalcode");
	$token->field("ct_city");
	$token->field("ct_phone");
	$token->field("ct_fax");
	$token->field("ct_pers_phone");
	$token->field("ct_mobile");
	$token->field("ct_email");
?>
<input type="hidden" name="op" value="save_contact"/>
<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>"/>
<table width="100%">
	<tr>
		<td align="right" width="40%"><? echo $_DIMS['cste']['_DIMS_LABEL_ADDRESS']; ?> : </td>
		<td align="left" width="30%">
			<input type="text" id="ct_address" size="35" name="ct_address" value="<? echo $contact->fields['address']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right" width="30%"><? echo $_DIMS['cste']['_DIMS_LABEL_CP']; ?> : </td>
		<td align="left" width="20%">
			<input type="text" id="ct_postalcode" size="35" name="ct_postalcode" value="<? echo $contact->fields['postalcode']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_CITY']; ?> : </td>
		<td align="left">
			<input type="text" id="ct_city" size="35" name="ct_city" value="<? echo $contact->fields['city']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_PHONE_WORK']; ?> : </td>
		<td align="left">
			<input type="text" id="ct_phone" size="35" name="ct_phone" value="<? echo $contact->fields['phone']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_FAX_WORK']; ?> : </td>
		<td align="left">
			<input type="text" id="ct_fax" size="35" name="ct_fax" value="<? echo $contact->fields['fax']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_MOBILE']; ?> : </td>
		<td align="left">
			<input type="text" id="ct_pers_phone" size="35" name="ct_pers_phone" value="<? echo $contact->fields['pers_phone']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_MOBILE'];; ?> : </td>
		<td align="left">
			<input type="text" id="ct_mobile" size="35" name="ct_mobile" value="<? echo $contact->fields['mobile']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_EMAIL'];; ?> : </td>
		<td align="left">
			<input type="text" id="ct_email" size="35" name="ct_email" value="<? echo $contact->fields['email']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<?
		$rubcour = current($rubgen);
		if (isset($rubcour['list']) && !empty($rubcour['list']) && $rubgen_cpteworkspace>0) {
	?>
	<tr>
		<td colspan="3" style="border:1px dotted #40567E;">
			<table>
				<?
				$workspaceenabled=array();
				// construction des champs dynamiques pour cette rubrique generique
					// test si utilise ou non
					echo '<tr><td><b><u>'.$_DIMS['cste']['_MOBILE'].'</u> : </b></td></tr>';
					foreach ($rubcour['list'] as $fields) {
						if ($fields['use']>0) {
							if ($fields['option_needed']) $oblig=" *";
							else $oblig="";

							echo "<tr><td align=\"right\" width=\"40%\">".$fields['name'].$oblig."</td>";
							echo "<td width=\"30%\">";

							$replies[$fields['id']] ="";
							// construction du reply eventuel
							/*
							if (isset($contact->fields['field'.$fields['fieldname']]) ) {
								$replies[$fields['id']] = explode('||',$contact->fields['field'.$fields['fieldname']]);
							}
							*/
							$private=false;
							if (isset($ctfields[$fields['id']][$_SESSION['dims']['workspaceid']]['private'])) {
								$private=true;
								$replies[$fields['id']] = explode('||',$ctfields[$fields['id']][$_SESSION['dims']['workspaceid']]['private']['value']);
							}
							else {
								if (isset($ctfields[$fields['id']][$_SESSION['dims']['workspaceid']]['public'])) {
									$replies[$fields['id']] = explode('||',$ctfields[$fields['id']][$_SESSION['dims']['workspaceid']]['public']['value']);

								}
							}

							// appel du rendu
							include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

							echo "</td><td>";
							// test si personnel ou non
							if ($private) {
								echo "<img alt=\"private\" src=\"./common/modules/system/img/cadenas.gif\">";
								$check="checked=\"checked\"";
							}
							else $check="";

							// generation du champ private
							echo "<input type=\"checkbox\" name=\"priv_".$fields['id']."\" $check >Priv&eacute;";
							$token->field("priv_".$fields['id']);
							echo "</td></tr>";

							// on regarde si on a encore des donn�es d'autres espaces partages
							if (isset($ctfields[$fields['id']]) && sizeof($ctfields[$fields['id']])>=1){
								// on a du plus
								echo "<tr><td colspan=\"3\">";
								echo $skin->open_widgetbloc($fields['name'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_zoom.png','26px', '26px', '-15px', '-7px', "$tabscriptenv&action="._BUSINESS_TAB_CONTACT_FORM."&part=3", '', '');
								// on construit le tableau
								echo "<table width=\"100%\" style=\"background-color:#FFFFFF\" cellspacing=\"0\" cellpadding=\"0\">
									<tr><td width=\"28%\">Nom</td><td width=\"8%\">Date</td><td width=\"18%\">Par</td><td width=\"6%\">Langue</td><td width=\"30%\">Valeur</td></tr>";
								$i=1;
								foreach ($ctfields[$fields['id']] as $idwork=>$elemworkspace) {
									foreach ($elemworkspace as $key=>$elem)  {
										if ($key!="private") {
											$datvar=dims_timestamp2local($elem['timestp_modify']);
											$chdate=$datvar['date'];
											if ($i==1) $i=2;
											else $i=1;
											echo "<tr class=\"trl$i\"><td>".$lstworkspace[$idwork]."</td><td>".$chdate."</td><td>".$elem['username']."</td><td>";

											if (file_exists("./common/img/".$_SESSION['dims']['lang'][$elem['id_lang']].".gif")) {
												echo "<img src=\"./common/img/".$_SESSION['dims']['lang'][$elem['id_lang']].".gif\" alt=\"\">";
											}
											else echo "&nbsp;";

											echo "</td<td>".dims_strcut($elem['value'],500);
											echo "</td></tr>";
										}
									}
								}

								echo "</table>";
								echo $skin->close_widgetbloc();
								echo "</tr>";
							}
						}
						else {
							foreach ($fields['enabled'] as $workid) {
								if (!isset($workspaceenabled[$workid])) $workspaceenabled[$workid]=1;
								else $workspaceenabled[$workid]++;
							}
						}
					}

				// affichage des infos des champs dispos par les autres workspaces
				if (!empty($workspaceenabled)) {
					foreach ($workspaceenabled as $workid => $cpte) {
						echo "<tr><td colspan=\"2\">".$lstworkspace[$workid]." poss&egrave;de ".$cpte." champ(s)</td></tr>";
					}
				}
				?>
			</table>
		</td>
	</tr>
	<?
		}
	?>
	<tr>
		<td colspan="3">
			<table width="100%">
				<tr>
					<td align="center"><input type="submit" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"/></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
</form>


