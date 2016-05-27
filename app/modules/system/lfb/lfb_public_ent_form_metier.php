<form action="" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",		"save_ent");
	$token->field("action",	_BUSINESS_TAB_CONTACTSTIERS);
	$token->field("search_ent");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_ent">
<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>">
<table width="100%">
				<tr>
					<td width="45%" style="vertical-align:top;">
					<?
				//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CONT_DESCM'],'100%');
					echo '<div id="vertical_container2">';
					// construction des champs dynamiques pour la fiche
					// 04/04/2009
					//echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;background:#FFFFFF;\">
					//<tr bgcolor=\"".$skin->values['colsec']."\"><td colspan=\"2\"></td></tr>";

					// construction de la recherche des champs sur le type d'objet
					$categcour=0;
					$replies=array();

					$sql =	"	SELECT		mf.*,mc.label as categlabel
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mod_business_meta_categ as mc
								ON			mf.id_metacateg=mc.id
								AND			mf.id_object = :idobject
								AND			mc.admin=0
								ORDER BY	mc.position, mf.position
								";

					$rs_fields=$db->query($sql, array(
						':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
					));
					$color="";
					$workspaceenabled=array();
					while ($fields = $db->fetchrow($rs_fields)) {
						// test si utilise ou non
						if ($categcour!=$fields['id_metacateg']) {
							if ($categcour>0) {
								// affichage des infos des champs dispos par les autres workspaces
								if (!empty($workspaceenabled)) {
									foreach ($workspaceenabled as $workid => $cpte) {
										echo "<tr><td colspan=\"2\">".$lstworkspace[$workid]." poss&egrave;de ".$cpte." champ(s)</td></tr>";
									}
								}
								$workspaceenabled=array();
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

						if ($rubgen[$fields['id_metacateg']]['list'][$fields['id']]['use']>0) {
							$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
							echo "<tr>";
							if ($fields['option_needed']) $oblig=" *";
							else $oblig="";
							echo "<td width=\"25%\" valign=\"top\" align=\"right\" style=\"padding:4px;padding-top:".$fields['interline']."px;font-size:1em;\">".$fields['name'].$oblig."&nbsp;</td>";
							echo "<td width=\"75%\" style=\"padding:4px;padding-top:".$fields['interline']."px;\">";

							// construction du reply eventuel
							if (isset($contact->fields['field'.$fields['fieldname']]) ) {
								$replies[$fields['id']] = explode('||',$contact->fields['field'.$fields['fieldname']]);
							}
							include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

							echo "</td></tr>";
						}
						else {
							foreach ($rubgen[$fields['id_metacateg']]['list'][$fields['id']]['enabled'] as $workid) {
								if (!isset($workspaceenabled[$workid])) $workspaceenabled[$workid]=1;
								else $workspaceenabled[$workid]++;
							}
						}
					}
					// on ferme le dernier block
					if ($categcour>0) {
						// affichage des infos des champs dispos par les autres workspaces
						if (!empty($workspaceenabled)) {
							foreach ($workspaceenabled as $workid => $cpte) {
								echo "<tr><td colspan=\"2\">".$lstworkspace[$workid]." poss&egrave;de ".$cpte." champ(s)</td></tr>";
							}
						}

						echo "</table></div></div></div>";
					}
					?>
				</div>
				<? //echo $skin->close_simplebloc(); ?>
					</td>
				</tr>
				<?
					//dans le cas o� on connait d�j� l'entreprise rattach�e on n'affiche pas la zone
					if($contact_id == "") {
				?>
				<tr>
					<td>
					<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CONT_ENTRAT'],'100%'); ?>
					<table width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr>
							<td align="right" width="30%">
								<? echo $_DIMS['cste']['_SEARCH_ENT']; ?> :
							</td>
							<td align="left">
								<input type="text" value="" onkeyup="javascript:upKeysearch();" id="search_ent" name="search_ent"/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div id="dispres_searchent" style="display:none;width:100%">

								</div>
							</td>
						</tr>
					</table>
					<? echo $skin->close_simplebloc(); ?>
					</td>
				</tr>
				<? } ?>
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
</form>
<script type="text/javascript">
	var bottomAccordion = new accordion('vertical_container2');

	var verticalAccordions = $$('.accordion_toggle');
	verticalAccordions.each(function(accordion){
		$(accordion.next(0)).setStyle({height: '0px'});
	});
	bottomAccordion.activate($$('#vertical_container2 .accordion_toggle')[0]);
</script>
