<script language="javascript">
	var timersearch;

	function upKeysearch() {
		clearTimeout(timersearch);
		timersearch = setTimeout('execSearch()', 800);
	}

	function execSearch() {
		clearTimeout(timersearch);
		var nomsearch = dims_getelem('search_pers').value;
		var divtoaffich = dims_getelem('dispres_searchpers');

		if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "op=search_perstoadd&action=<? echo _BUSINESS_TAB_CONTACTSTIERS;?>&pers_name="+nomsearch, "", "dispres_searchpers");
			divtoaffich.style.display = "block";
		}

	}

</script>
<?

//dans le cas o� on est dans l'ajout (action = _BUSINESS_TAB_CONTACTSTIERS), on affiche pas les infobulles
//$ajout = 0;
$url_action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
$id_cont = dims_load_securvalue('id_cont', dims_const::_DIMS_CHAR_INPUT, true, true); //l'id contact n'est rempli que si on vient de valider le formulaire d'un nouveau contact dans le cas o� l'on cr�e une entreprise, va servir � savoir si on affiche ou non la zone contact rattach�
//echo "url_action : ".$url_action;
if($url_action != "" || $url_action != _BUSINESS_TAB_CONTACTSTIERS) {
	$ajout = 0;
}
else {
	$ajout = 1;
}

// construction des cat�gories et champs dynamiques
$categcour=0;
// 1ere requete pour les champs attaches aux rubriques g�n�riques
$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat
			FROM		dims_mod_business_meta_field as mf
			RIGHT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			AND			mf.id_object = ".dims_const::_SYSTEM_OBJECT_TIERS."
			AND			mc.admin=1
			ORDER BY	mc.position, mf.position
			";

$rubgen=array();
$rs_fields=$db->query($sql);
$color="";
while ($fields = $db->fetchrow($rs_fields)) {
	if (!isset($rubgen[$fields['id_cat']]))  {
		$rubgen[$fields['id_cat']]=array();
		$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
		if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
	}

	// on ajoute maintenant les champs dans la liste
	if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][]=$fields;
}

//dims_print_r($rubgen);

?>
<form action="" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",				"save_ent");
	$token->field("action",			_BUSINESS_TAB_CONTACTSTIERS);
	$token->field("ent_intitule");
	$token->field("ent_dirigeant");
	$token->field("ent_presentation");
	$token->field("ent_adresse");
	$token->field("ent_codepostal");
	$token->field("ent_ville");
	$token->field("ent_pays");
	$token->field("ent_telephone");
	$token->field("ent_telecopie");
	$token->field("ent_site_web");
	$token->field("ent_ent_activiteprincipale");
	$token->field("ent_effectif");
	$token->field("ent_date_creation");
	$token->field("ent_capital");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_ent">
<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>">
<table style="width:100%">
<tr>
	<td width="45%" style="vertical-align:top;">
		<table width="100%">

				<tr>
					<td width="45%" style="vertical-align:top;">
					<?	//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_profil.php'); ?>
					</td>
				</tr>
				<?
					$categcour=0;
					$replies=array();

					$sql =	"
								SELECT		mf.*,mc.label as categlabel
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mod_business_meta_categ as mc
								ON			mf.id_metacateg=mc.id
								AND			mf.id_object = ".dims_const::_SYSTEM_OBJECT_TIERS."
								AND			mc.admin=0
								ORDER BY	mc.position, mf.position
								";


					$rs_fields=$db->query($sql);
					$nb_cmet = $db->numrows($rs_fields);

				if($nb_cmet > 0) {
				?>
				<tr>
				<td>
					<? //echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_COMMENTS'],'100%'); ?>
						<!--<table width="100%">
							<tr>
								<td align="left"><textarea cols="40" style="width:95%" name="ct_comments" rows="4"></textarea></td>
							</tr>
						</table>-->
					<?
					//echo $skin->close_simplebloc();
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CONT_DESCM'],'100%');
					echo '<div id="vertical_container2">';
					// construction des champs dynamiques pour la fiche
					// 04/04/2009
					//echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;background:#FFFFFF;\">
					//<tr bgcolor=\"".$skin->values['colsec']."\"><td colspan=\"2\"></td></tr>";

					// construction de la recherche des champs sur le type d'objet

					$color="";
					while ($fields = $db->fetchrow($rs_fields)) {
						$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
						if ($categcour!=$fields['id_metacateg']) {
							if ($categcour>0) {
								echo "</table></div></div>";
							}

							$categcour=$fields['id_metacateg'];
							$categlabel=$fields['categlabel'];
							?>
							<h3 class="accordion_toggle">
								<table style="width:100%;">
									<tr>
										<td align="left" width="30%">&nbsp;</td>
										<td align="left" width="30%">
											<table style="width:100%;" cellpadding="0" cellspacing="0">
												<tr>
													<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
													<td class="midb20">
													<? echo $categlabel; ?>
													</td>
													<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
												</tr>
											</table>
										</td>
										<td style="width:30%;text-align:right">&nbsp;</td>
									</tr>
								</table>
							</h3>

							<div class="accordion_content" style="background-color:transparent;">
								<div id="met<? echo $categcour;?>" style="display:block;">
									<table cellpadding="0" cellspacing="0" style="width:100%;">

							<?
						}
						echo "<tr>";
						if ($fields['option_needed']) $oblig=" *";
						else $oblig="";
						echo "<td width=\"25%\" valign=\"top\" align=\"right\" style=\"padding:4px;padding-top:".$fields['interline']."px;font-size:1em;\">".$fields['name'].$oblig."&nbsp;</td>";
						echo "<td width=\"75%\" style=\"padding:4px;padding-top:".$fields['interline']."px;\">";

						// construction du reply eventuel
						if (isset($ent->fields['field'.$fields['fieldname']]) ) {
							$replies[$fields['id']] = explode('||',$ent->fields['field'.$fields['fieldname']]);
						}
						include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

						echo "</td></tr>";
					}
					// on ferme le dernier block
					if ($categcour>0)echo "</table></div></div></div>";
					?>

				</div>
				<? echo $skin->close_simplebloc(); ?>
				</td></tr>
				<?
				}
				?>
		</table>
	</td>
	<td>
	<?
	reset($rubgen);
	$rubcour = current($rubgen);

	echo $skin->open_simplebloc($_DIMS['cste']['_PROFIL'],'100%'); ?>
	<div id="vertical_container">
		<h3 class="accordion_toggle">
			<table style="width:100%;">
				<tr>
					<td align="left" width="30%">&nbsp;</td>
					<td align="left" width="30%">
						<table style="width:100%;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20">
								<? echo $rubcour['label']; ?>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
					</td>
					<td  style="width:30%;text-align:right">&nbsp;</td>
				</tr>
			</table>
		</h3>
		<div class="accordion_content" style="background-color:transparent;">
			<table width="100%">
				<tr>
					<td align="right" width="30%"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?> : </td>
					<td align="left" width="25%">
						<input type="text" id="ent_intitule" size="35" name="ent_intitule" value="<? echo $ent->fields['intitule']; ?>"/>
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
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_DIR']; ?> : </td>
					<td align="left">
						<input type="text" id="ent_dirigeant" size="35" name="ent_dirigeant" value="<? echo $ent->fields['dirigeant']; ?>"/>
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
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_PRES']; ?> : </td>
					<td align="left">
						<textarea id="ent_presentation" cols="33" rows="5" name="ent_presentation"><? echo $ent->fields['presentation']; ?></textarea>
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
					if (isset($rubcour['list']) && !empty($rubcour['list'])) {
				?>
				<tr>
					<td colspan="3" style="border:1px dotted #40567E;">
						<table>
							<?
							// construction des champs dynamiques pour cette rubrique generique

								echo '<tr><td><b><u>'.$_DIMS['cste']['_FORM_CT_INF_MET'].'</u> : </b></td></tr>';
								foreach ($rubcour['list'] as $fields) {
									if ($fields['option_needed']) $oblig=" *";
									else $oblig="";

									echo "<tr><td align=\"right\" width=\"30%\">".$fields['name'].$oblig."</td>";
									echo "<td width=\"20%\">";

									// construction du reply eventuel
									if (isset($ent->fields['field'.$fields['fieldname']]) ) {
										$replies[$fields['id']] = explode('||',$ent->fields['field'.$fields['fieldname']]);
									}

									// appel du rendu
									include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

									echo "</td><td></td></tr>";
								}

							?>
						</table>
					</td>
				</tr>
				<?
					}
				?>
			</table>
		</div>
		<?
		// on passe � la rubrique gene suivante
		next($rubgen);
		$rubcour = current($rubgen);
		?>
		<h3 class="accordion_toggle">
			<table style="width:100%;">
				<tr>
					<td align="left" width="30%">&nbsp;</td>
					<td align="left" width="30%">
						<table style="width:100%;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20">
								<? echo $rubcour['label']; ?>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
					</td>
					<td  style="width:30%;text-align:right">&nbsp;</td>
				</tr>
			</table>
		</h3>
		<div class="accordion_content" style="background-color:transparent;">
			<table width="100%">
				<tr>
					<td align="right" width="30%"><? echo $_DIMS['cste']['_DIMS_LABEL_ADDRESS']; ?> : </td>
					<td align="left" width="20%">
						<input type="text" id="ent_adresse" size="35" name="ent_adresse" value="<? echo $ent->fields['adresse']; ?>"/>
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
						<input type="text" id="ent_codepostal" size="35" name="ent_codepostal" value="<? echo $ent->fields['codepostal']; ?>"/>
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
						<input type="text" id="ent_ville" size="35" name="ent_ville" value="<? echo $ent->fields['ville']; ?>"/>
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
						<input type="text" id="ent_pays" size="35" name="ent_pays" value="<? echo $ent->fields['pays']; ?>"/>
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
					<td align="right"><? echo $_DIMS['cste']['_PHONE']; ?> : </td>
					<td align="left">
						<input type="text" id="ent_telephone" size="35" name="ent_telephone" value="<? echo $ent->fields['telephone']; ?>"/>
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
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_FAX']; ?> : </td>
					<td align="left">
						<input type="text" id="ent_telecopie" size="35" name="ent_telecopie" value="<? echo $ent->fields['telecopie']; ?>"/>
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
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_WSITE']; ?> : </td>
					<td align="left">
						<input type="text" id="ent_site_web" size="35" name="ent_site_web" value="<? echo $ent->fields['site_web']; ?>"/>
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
					if (isset($rubcour['list']) && !empty($rubcour['list'])) {
				?>
				<tr>
					<td colspan="3" style="border:1px dotted #40567E;">
						<table>
							<?
							// construction des champs dynamiques pour cette rubrique generique

								echo '<tr><td><b><u>'.$_DIMS['cste']['_FORM_CT_INF_MET'].'</u> : </b></td></tr>';
								foreach ($rubcour['list'] as $fields) {
									if ($fields['option_needed']) $oblig=" *";
									else $oblig="";

									echo "<tr><td align=\"right\" width=\"30%\">".$fields['name'].$oblig."</td>";
									echo "<td width=\"20%\">";

									// construction du reply eventuel
									if (isset($ent->fields['field'.$fields['fieldname']]) ) {
										$replies[$fields['id']] = explode('||',$ent->fields['field'.$fields['fieldname']]);
									}

									// appel du rendu
									include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

									echo "</td><td></td></tr>";
								}

							?>
						</table>
					</td>
				</tr>
				<?
					}
				?>
			</table>
		</div>
		<?
		// on passe � la rubrique gene suivante
		next($rubgen);
		$rubcour = current($rubgen);
		?>
		<h3 class="accordion_toggle">
			<table style="width:100%;">
				<tr>
					<td align="left" width="30%">&nbsp;</td>
					<td align="left" width="30%">
						<table style="width:100%;" cellpadding="0" cellspacing="0">
							<tr>
								<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
								<td class="midb20">
								<? echo $rubcour['label']; ?>
								</td>
								<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							</tr>
						</table>
					</td>
					<td  style="width:30%;text-align:right">&nbsp;</td>
				</tr>
			</table>
		</h3>
		<div class="accordion_content" style="background-color:transparent;">
			<table width="100%">
				<tr>
					<td align="right" width="30%"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_SECTACT']; ?></td>
					<td align="left" width="20%">
						<select id="ent_ent_activiteprincipale" name="ent_ent_activiteprincipale"><!-- c'est normal qu'il y ait ent_ent_  -->
							<option value="ONG" <? if($ent->fields['ent_activiteprincipale'] == "ONG") echo 'selected="selected"'; ?>>ONG</option>
							<option value="Association" <? if($ent->fields['ent_activiteprincipale'] == "Association") echo 'selected="selected"'; ?>>Association</option>
							<option value="Administration" <? if($ent->fields['ent_activiteprincipale'] == "Administration") echo 'selected="selected"'; ?>>Administration</option>
							<option value="Gouvernement" <? if($ent->fields['ent_activiteprincipale'] == "Gouvernement") echo 'selected="selected"'; ?>>Gouvernement</option>
							<option value="Groupement" <? if($ent->fields['ent_activiteprincipale'] == "Groupement") echo 'selected="selected"'; ?>>Groupement</option>
						</select>
					</td>
					<td align="left">
					<? if($ajout) { ?>
						<div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_vulux');"><img src="./common/img/properties.png"/></a>
						<div id="inf_vulux" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
							<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_vulux\');', ''); ?>
							<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
							<? echo $skin->close_infobloc(); ?>
						</div>
						</div>
					<? } ?>
					</td>
				</tr>
				<tr>
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_EFFECTIF']; ?> : </td>
					<td align="left">
						<input type="text" id="ent_ent_effectif" size="35" name="ent_ent_effectif" value="<? echo $ent->fields['ent_effectif']; ?>"/>
					</td>
					<td align="left">
					<? if($ajout) { ?>
						<div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_sect_act');"><img src="./common/img/properties.png"/></a>
						<div id="inf_sect_act" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
							<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_sect_act\');', ''); ?>
							<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
							<? echo $skin->close_infobloc(); ?>
						</div>
						</div>
					<? } ?>
					</td>
				</tr>
				<tr>
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_DATEC'] ?> : </td>
					<td align="left">
						<input type="text" id="ent_date_creation" size="35" name="ent_date_creation" value="<? echo $ent->fields['date_creation']; ?>"/>
					</td>
					<td align="left">
					<? if($ajout) { ?>
						<div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_vip');"><img src="./common/img/properties.png"/></a>
						<div id="inf_vip" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
							<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_vip\');', ''); ?>
							<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
							<? echo $skin->close_infobloc(); ?>
						</div>
						</div>
					<? } ?>
					</td>
				</tr>
				<tr>
					<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_CAPITAL'] ?> : </td>
					<td align="left">
						<input type="text" id="ent_ent_capital" size="35" name="ent_ent_capital" value="<? echo $ent->fields['ent_capital']; ?>"/>
					</td>
					<td align="left">
					<? if($ajout) { ?>
						<div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_vip');"><img src="./common/img/properties.png"/></a>
						<div id="inf_vip" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
							<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_vip\');', ''); ?>
							<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
							<? echo $skin->close_infobloc(); ?>
						</div>
						</div>
					<? } ?>
					</td>
				</tr>
				<?
					if (isset($rubcour['list']) && !empty($rubcour['list'])) {
				?>
				<tr>
					<td colspan="3" style="border:1px dotted #40567E;">
						<table>
							<?
							// construction des champs dynamiques pour cette rubrique generique

								echo '<tr><td><b><u>'.$_DIMS['cste']['_FORM_CT_INF_MET'].'</u> : </b></td></tr>';
								foreach ($rubcour['list'] as $fields) {
									if ($fields['option_needed']) $oblig=" *";
									else $oblig="";

									echo "<tr><td align=\"right\" width=\"30%\">".$fields['name'].$oblig."</td>";
									echo "<td width=\"20%\">";

									// construction du reply eventuel
									if (isset($ent->fields['field'.$fields['fieldname']]) ) {
										$replies[$fields['id']] = explode('||',$ent->fields['field'.$fields['fieldname']]);
									}

									// appel du rendu
									include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

									echo "</td><td></td></tr>";
								}

							?>
						</table>
					</td>
				</tr>
				<?
					}
				?>
			</table>
		</div>
	<? echo $skin->close_simplebloc(); ?>
	</td>

</tr>
<tr>
	<td colspan="2">
		<table width="100%">
			<tr>
				<td align="center"><input type="submit" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"/></td>
			</tr>
		</table>
	</td>
</tr>
</table>
</form>
<script type="text/javascript">
	var bottomAccordion = new accordion('vertical_container2');

	var verticalAccordions = $$('.accordion_toggle');
	verticalAccordions.each(function(accordion){
		$(accordion.next(0)).setStyle({height: '0px'});
	});
	bottomAccordion.activate($$('#vertical_container2 .accordion_toggle')[0]);
</script>
