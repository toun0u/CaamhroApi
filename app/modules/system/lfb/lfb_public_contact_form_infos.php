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
			WHERE		mc.id=2
			ORDER BY	mc.position, mf.position
			";
//echo $sql;
$rubgen=array();
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
		WHERE		mf.id_metacateg = 2";
//echo $sql;
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
$sql= " select		cf.*,
					w.label as labelworkspace,
					u.firstname,u.lastname
		from		dims_mod_business_contact_field as cf
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=cf.id_metafield
		AND			mf.id_metacateg=2
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
	$token->field("op",		"save_contact");
	$token->field("action",	_BUSINESS_TAB_CONTACTSTIERS);
	$token->field("ct_lux");
	$token->field("ct_professional");
	$token->field("ct_vip");
?>
<input type="hidden" name="op" value="save_contact"/>
<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>"/>
<table width="100%">
	<tr>
		<td align="right" width="45%"><? echo _DIMS_LABEL_CONT_LUX; ?></td>
		<td align="left" width="25%">
			<select id="ct_lux" name="ct_lux">
				<option value="1" <? if($contact->fields['lux'] == $_DIMS['cste']['_DIMS_YES']) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_YES']; ?></option>
				<option value="0" <? if($contact->fields['lux'] == $_DIMS['cste']['_DIMS_NO']) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_NO']; ?></option>
			</select>
		</td>
		<td align="left">

		</td>
	</tr>
	<tr>
		<td align="right"><? echo _DIMS_LABEL_CONT_SECTACT; ?> : </td>
		<td align="left">
			<select id="ct_professional" name="ct_professional">
				<option <? if($contact->fields['professional'] == "services") echo 'selected="selected"'; ?> value="services">Services</option>
				<option <? if($contact->fields['professional'] == "banque") echo 'selected="selected"'; ?> value="banque">Banque</option>
				<option <? if($contact->fields['professional'] == "informatique") echo 'selected="selected"'; ?> value="informatique">Informatique</option>
			</select>
		</td>
		<td align="left">

		</td>
	</tr>
	<tr>
		<td align="right"><? echo _DIMS_LABEL_CONT_VIP ?> : </td>
		<td align="left">
			<select id="ct_vip" name="ct_vip">
				<option value="<? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_N'];?>" <? if($contact->fields['vip'] == $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_N']) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_N']; ?></option>
				<option value="<? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_V'];?>" <? if($contact->fields['vip'] == $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_V']) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_V']; ?></option>
				<option value="<? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_T'];?>" <? if($contact->fields['vip'] == $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_T']) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_CONT_VIP_T']; ?></option>
			</select>
		</td>
		<td align="left">

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


