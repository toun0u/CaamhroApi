<?php
if (!isset($ajout)) $ajout=false;
// construction des cat�gories et champs dynamiques
$categcour=0;
// 1ere requete pour les champs attaches aux rubriques g�n�riques
$sql =	"	SELECT		mf.*,mc.label as categlabel, mc.id as id_cat
			FROM		dims_mod_business_meta_field as mf
			INNER JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			AND			mf.id_object = :idobject
			AND			mc.admin=1
			WHERE		mc.id=3
			ORDER BY	mc.position, mf.position
			";

$rubgen=array();
$rubgen_cpteworkspace=0;
$lstworkspace=$workspace->getAllWorkspace();

// construciton de la liste des workspaces
if (!isset($lstworkspace[$f['id_workspace']])) $lstworkspace[$f['id_workspace']]=$f['labelworkspace'];

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
		WHERE		mf.id_metacateg = 3";

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

		//on veut sacoir s'il y a des workspace pour le partage
		if(!empty($f['id_workspace'])) {
			$rubgen_cpteworkspace++;
		}
	}
}


// construction des valeurs saisies dans les champs en ne tenant compte que des derni�res modifications
$ctfields=array();
$sql= " select		cf.*,
					w.label as labelworkspace,
					u.firstname,u.lastname
		from		dims_mod_business_contact_field as cf
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=cf.id_metafield
		AND			mf.id_metacateg=3
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
//dims_print_r($ctfields);
?>
<form action="" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",		"save_contact");
	$token->field("action",	_BUSINESS_TAB_CONTACTSTIERS);
	$token->field("ct_civilite");
	$token->field("ct_firstname");
	$token->field("ct_lastname");
	$token->field("ct_sexe");
	$token->field("ct_nationalite");
	$token->field("ct_country");
?>
<input type="hidden" name="op" value="save_contact"/>
<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>"/>
<table width="100%">
	<tr>
		<td align="right" width="40%"><? echo _DIMS_LABEL_TITLE; ?> : </td>
		<td align="left" width="30%">
			<select id="ct_civilite" name="ct_civilite">
				<option value="Mr"	  <? if($contact->fields['civilite'] == "Mr")	 echo "selected=\"selected\""; ?>>Mr</option>
				<option value="Mme"   <? if($contact->fields['civilite'] == "Mme")	 echo "selected=\"selected\""; ?> >Mme</option>
				<option value="Melle" <? if($contact->fields['civilite'] == "Melle") echo "selected=\"selected\""; ?>>Melle</option>
				<option value="Dr"	  <? if($contact->fields['civilite'] == "Dr")	 echo "selected=\"selected\""; ?>>Dr</option>
				<option value="Cheik" <? if($contact->fields['civilite'] == "Cheik") echo "selected=\"selected\""; ?>>Cheik</option>
			</select>
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
		<td align="right"><? echo _DIMS_LABEL_NAME; ?> : </td>
		<td align="left">
			<input type="text" id="ct_nom" size="35" name="ct_lastname" value="<? echo $contact->fields['lastname']; ?>"/>
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
		<td align="right"><? echo _DIMS_LABEL_CONT_FNAME; ?> : </td>
		<td align="left">
			<input type="text" id="ct_prenom" size="35" name="ct_firstname" value="<? echo $contact->fields['firstname']; ?>"/>
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
		<td align="right"><? echo _DIMS_LABEL_CONT_SEXE; ?> : </td>
		<td align="left">
			<select id="ct_sexe" name="ct_sexe">
				<option value="<? echo _DIMS_LABEL_CONT_SEXE_F; ?>" <? if($contact->fields['sexe'] == _DIMS_LABEL_CONT_SEXE_F) echo "selected=\"selected\""; ?>><? echo _DIMS_LABEL_CONT_SEXE_F; ?></option>
				<option value="<? echo _DIMS_LABEL_CONT_SEXE_M; ?>" <? if($contact->fields['sexe'] == _DIMS_LABEL_CONT_SEXE_M) echo "selected=\"selected\""; ?>><? echo _DIMS_LABEL_CONT_SEXE_M; ?></option>
			</select>
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
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_NATIONALITY']; ?> : </td>
		<td align="left">
			<input type="text" id="ct_nationalite" size="35" name="ct_nationalite" value="<? echo $contact->fields['nationalite']; ?>"/>
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
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY']; ?> : </td>
		<td align="left">
			<select id="ct_country" name="ct_country">
				<option value="" <? if($contact->fields['country'] == "") echo "selected=\"selected\""; ?>>--</option>
				<option value="<? echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY_A']; ?>" <? if($contact->fields['country'] == $_DIMS['cste']['_DIMS_LABEL_COUNTRY_A']) echo "selected=\"selected\""; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY_A']; ?></option>
				<option value="<? echo $_DIMS['cste']['_DIMS_PAYS_BE']; ?>" <? if($contact->fields['country'] == $_DIMS['cste']['_DIMS_PAYS_BE']) echo "selected=\"selected\""; ?>><? echo $_DIMS['cste']['_DIMS_PAYS_BE']; ?></option>
				<option value="<? echo $_DIMS['cste']['_DIMS_PAYS_FR']; ?>" <? if($contact->fields['country'] == $_DIMS['cste']['_DIMS_PAYS_FR']) echo "selected=\"selected\""; ?>><? echo $_DIMS['cste']['_DIMS_PAYS_FR']; ?></option>
				<option value="<? echo $_DIMS['cste']['_DIMS_PAYS_LU']; ?>" <? if($contact->fields['country'] == $_DIMS['cste']['_DIMS_PAYS_LU']) echo "selected=\"selected\""; ?>><? echo $_DIMS['cste']['_DIMS_PAYS_LU']; ?></option>
			</select>
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
			<table width="100%">
				<?
				$workspaceenabled=array();
				// construction des champs dynamiques pour cette rubrique generique

					echo '<tr><td><b><u>'.$_DIMS['cste']['_MOBILE'].'</u> : </b></td></tr>';
					foreach ($rubcour['list'] as $fields) {
						// test si utilise ou non
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


