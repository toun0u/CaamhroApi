<?php
$dims = dims::getInstance();
$db = $dims->getDb();
global $skin;
global $business_periode;

$id_popup = $this->getLightAttribute('id_popup');

if ($this->fields['tiers_id'] > 0) {
	$tiers = new tiers();
	$tiers->open($this->fields['tiers_id']);
}

?>

<div>
	<div class="actions">
		<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
			<img src="modules/system/desktopV2/templates/gfx/common/icon_close.gif" />
		</a>
	</div>
	<h2>
		Détail de <?php echo $this->getLibelle().' ('.$this->getNumero().') - '.$this->getTiers()->getIntitule(); ?>
	</h2>
	<?php
	$src = array_search($this->fields['id_suivi'], $_SESSION['desktopv2']['concepts']['liste_suivis']);

	if($src > 0) {
	?>
	<div class="suivi_precedant">
		<a href="javascript:void(0);" onclick="javascript:dims_closeOverlayedPopup('<?php echo $id_popup; ?>');openSuivi(<?php echo $_SESSION['desktopv2']['concepts']['liste_suivis'][$src-1]; ?>)"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/suivi_precedant.png"/></a>
	</div>
	<?php
	}
	if($src < count($_SESSION['desktopv2']['concepts']['liste_suivis'])-1) {
	?>
	<div class="suivi_suivant">
		<a href="javascript:void(0);" onclick="javascript:dims_closeOverlayedPopup('<?php echo $id_popup; ?>');openSuivi(<?php echo $_SESSION['desktopv2']['concepts']['liste_suivis'][$src+1]; ?>)"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/suivi_suivant.png"/></a>
	</div>
	<?php
	}
	?>
	<div id="suivi_container" class="scrollable-y">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<td>
				<FORM ACTION="<? echo $dims->getScriptEnv(); ?>" METHOD="POST" NAME="form_suivi">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("dims_op",	"desktopv2");
					$token->field("action",		"suivi_enregistrer");
					$token->field("id_suivi",	$this->getIdSuivi());
					$token->field("suivi_tiers_id");
					$token->field("suivi_contact_id");
					$token->field("suivi_datejour");
					$token->field("suivi_libelle");
					$token->field("suivi_remise");
					$token->field("suivi_valide");
					$token->field("suivi_datevalide");
					$token->field("suivi_periode");
					$token->field("suivi_dossier_id");
					$token->field("suivi_description");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<INPUT TYPE="HIDDEN" NAME="dims_op" VALUE="desktopv2" />
				<INPUT TYPE="HIDDEN" NAME="action" VALUE="suivi_enregistrer" />
				<INPUT TYPE="HIDDEN" NAME="id_suivi" VALUE="<? echo $this->getIdSuivi(); ?>" />

				<table>
					<tr>
						<TD valign="top">
							<TABLE CELLPADDING="2" CELLSPACING="1">
							<tr>
								<td align="right">Type:&nbsp;</td>
								<td align="left"><? echo $this->getType(); ?></td>
							</tr>
							<tr>
								<td align="right">Exercice:&nbsp;</td>
								<td align="left"><? echo $this->getExercice(); ?></td>
							</tr>
							<tr>
								<td align="right">Numéro:&nbsp;</td>
								<td align="left"><? echo $this->getNumero(); ?></td>
							</tr>
							<TR>
								<TD ALIGN="right">Client:&nbsp;</TD>
								<td align="left">

									<?php
									if ($this->fields['tiers_id']>0) {
										echo '<select style="width:220px;" name="suivi_tiers_id">';
										// on a un tiers selectionne
										// choix du client
										$tiers=$this->getTiers();

										$res=$this->db->query("select * from dims_mod_business_tiers order by intitule");

										while ($f=$db->fetchrow($res)) {
										if ($f['id']==$this->fields['tiers_id']) $selected='selected';
										else $selected='';
										echo '<option '.$selected.' value="'.$f['id'].'">'.$f['intitule']."</option>";
										}
										echo '</select>';

									}
									elseif ($this->fields['contact_id']>0) {
										echo '<select style="width:220px;"	name="suivi_contact_id">';
										// on a un tiers selectionne
										// choix du client
										$tiers=$this->getTiers();

										$res=$this->db->query("select * from dims_mod_business_contact");

										while ($f=$db->fetchrow($res)) {
										if ($f['id']==$this->fields['contact_id']) $selected='selected';
										else $selected='';
										echo '<option '.$selected.' value="'.$f['id'].'">'.$f['lastname'].' '.$f['firstname']."</option>";
										}
										echo '</select>';
									}

									?>
								</td>
							</TR>
							<TR>
								<TD ALIGN="right">Date:&nbsp;</TD>
								<td align="left">
									<input maxlength="10" name="suivi_datejour" id="suivi_datejour" size="20" class="text" value="<? echo $this->getDateJour(); ?>">
								</td>
							</TR>
							<tr>
								<td align="right">Libellé:&nbsp;</td>
								<td align="left">
									<input type="text" class="text" size="30" name="suivi_libelle" value="<? echo $this->getLibelle(); ?>">
								</td>
							</tr>
							<tr>
								<td align="right">Remise (%):&nbsp;</td>
								<td align="left">
									<input type="text" class="text" size="30" name="suivi_remise" value="<? echo $this->getRemise(); ?>">
								</td>
							</tr>
							<?php
							if ($this->getType() == 'Devis') {
								?>
								<tr>
									<td align="right">Accepté :&nbsp;</td>
									<td align="left">
										<?
										if ($this->getValide()) $checked='checked';
										else $checked='';
										?>
										<input type="checkbox" <? echo $checked; ?> name="suivi_valide">
									</td>
								</tr>
								<?php
								if ($this->getValide()) {
									?>
									<tr>
										<td align="right">Date d'acceptation :&nbsp;</td>
										<td align="left">
											<input type="text" class="text" size="30" name="suivi_datevalide" value="<? echo $this->getDateValide(); ?>">
										</td>
									</tr>
									<?php
								}
							}
							if ($this->fields['type'] == 'Facture') {
								?>
								<tr>
									<td align="right">Périodicité:&nbsp;</td>
									<td align="left">
										<select name="suivi_periode" class="select" style="width:250px">
											<option>aucune</option>
											<?php
											foreach($business_periode as $value => $text) {
												?><option <? if ($this->getPeriode() == $value) echo 'selected'; ?> value="<? echo $value; ?>"><? echo $text; ?></option><?
											}
											?>
										</select>
									</td>
								</tr>
								<?
							}
							?>
							<tr>
								<td align="right" valign="top">Dossier:&nbsp;</td>
								<td align="left">
								<select name="suivi_dossier_id" class="select" style="width:250px">
								<?php
								// recherche du dossier du suivi
								require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
								$matrix = new search();
								$linkedObjectsIds = $matrix->exploreMatrice(
									null,
									null,
									null,
									array($_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]),
									null,
									null,
									null,
									array($this->getGlobalObjectId()),
									null,
									null
									);


								$desktop = new desktopv2();
								// $lstObj = $desktop->getLinkedObjects($linkedObjectsIds, $_SESSION['desktop']['concept']['tags']);
								$a_go_dossiers = array_keys($linkedObjectsIds['distribution']['dossiers']);
								$go_dossier = $a_go_dossiers[0];

								// recherche de tous les dossiers du client
								require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
								$matrix = new search();
								$linkedObjectsIds = $matrix->exploreMatrice(
									null,
									null,
									null,
									array($_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]),
									null,
									null,
									null,
									array(0),
									null,
									null
									);
								$desktop = new desktopv2();
								$lstObj = $desktop->getLinkedObjects($linkedObjectsIds, $_SESSION['desktop']['concept']['tags']);

								foreach ($lstObj['dossiers'] as $dossier) {
									$selected = ($dossier->fields['id_globalobject'] == $go_dossier) ? ' selected' : '';
									echo '<option value="'.$dossier->fields['id_globalobject'].'"'.$selected.'>'.$dossier->fields['label'].'</option>';
								}

								?>
								</select>
								</td>
							</tr>
							</TABLE>
						</TD>
						<td>
							<TABLE CELLPADDING="2" CELLSPACING="1">
							<tr>
								<td align="right" valign="top">Commentaire:&nbsp;</td>
								<td align="left">
									<textarea name="suivi_description" rows="9" cols="20" class="text"><? echo $this->getDescription(); ?></textarea>
								</td>
							</tr>
							</TABLE>
						</td>
					</tr>
				</table>

				</FORM>
			</TD>

			<!-- colonne 3 -->
			<TD valign="top">
			<?
			if ($this->getType() == 'Facture' || ($this->getType() == 'Devis') && $this->getValide()) {
				echo $skin->open_simplebloc('Liste des Versements','100%');

				if ($this->getSoldeTTC() > 0) {
					?>
					<form action="<? echo $dims->getScriptEnv(); ?>" method="post">
					<?
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("dims_op",	"desktopv2");
						$token->field("action",		"ajouter_versement");
						$token->field("id_suivi",	$this->getIdSuivi());
						$token->field("montant");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
					?>
					<input type="hidden" name="dims_op" value="desktopv2" />
					<input type="hidden" name="action" value="ajouter_versement" />
					<input TYPE="hidden" NAME="id_suivi" VALUE="<? echo $this->getIdSuivi(); ?>" />

					<TABLE WIDTH="260px" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
					<tr>
						<td ALIGN="left">Versement:</td>
						<td ALIGN="left"><input type="text" name="montant" class="text" size="8"></td>
						<td><?php echo dims_create_button($_SESSION['cste']['_DIMS_ADD'], 'disk'); ?></td>
						<td ALIGN="right">
							<?php
							if ($this->getType() == 'Facture')
							echo dims_create_button('Solder', 'check', 'dims_confirmlink(\''.dims_urlencode($dims->getScriptEnv().'?dims_op=desktopv2&action=solder_suivi&id_suivi='.$this->getIdSuivi()).'\', \''.$_SESSION['cste']['_DIMS_CONFIRM'].'\')');
							?>
						</TD>
					</tr>
					</TABLE>
					</form>

					<TABLE WIDTH="260px" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
					<?
				}
				?>
				<TABLE WIDTH="260px" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
				<tr>
					<td nowrap>Total Versements:&nbsp;<? echo $this->getMontantTTC() - $this->getSoldeTTC(); ?>&nbsp;&euro;&nbsp;</td>
					<?
					if ($this->getSoldeTTC() > 0)
					{
						?>
						<td nowrap ALIGN="right" class="nonsolde">&nbsp;Solde:&nbsp;<? echo $this->getSoldeTTC(); ?>&nbsp;&euro;</td>
						<?
					}
					else
					{
						?>
						<td nowrap ALIGN="right" class="solde">&nbsp;Soldé</td>
						<?
					}
					?>
				</tr>
				</form>
				</TABLE>

				<TABLE WIDTH="260px" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
				<table cellpadding="2" cellspacing="1" width="100%">
				<?
				$color = $skin->values['bgline1'];
				?>
				<tr bgcolor="<? echo $color; ?>" class="title">
					<td>Date du Paiement</td>
					<td align="right">Montant</td>
					<td>&nbsp;</td>
				</tr>
				<?

				$select = "
					SELECT	*
					FROM	dims_mod_business_versement
					WHERE	suivi_id = :idsuivi
					AND	suivi_type = :typesuivi
					AND	suivi_exercice = :exercicesuivi
					ORDER BY date_paiement DESC";
				$rs = $db->query($select, array(
					':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
					':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
					':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
				));
				while ($fields = $db->fetchrow($rs)) {
					$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
					$date_fr = dims_timestamp2local($fields['date_paiement']);
					?>
					<tr bgcolor="<? echo $color; ?>" >
						<td><? echo "<strong>{$date_fr['date']}</strong>"; ?></td>
						<td align="right"><? echo business_format_price($fields['montant']); ?>&nbsp;&euro;</td>
						<td width="40" align="center">
							<a title="Supprimer ce versement" href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo dims_urlencode($dims->getScriptEnv()."?dims_op=desktopv2&action=supprimer_versement&id_suivi=".$this->getIdSuivi()."&versement_id={$fields['id']}"); ?>', 'Êtes-vous sûr(e) de vouloir supprimer ce versement ?');">
								<img border="0" src="./common/modules/business/img/ico_delete.gif" />
							</a>
						</td>
					</tr>
					<?
				}
				echo '</table>';

				echo $skin->close_simplebloc();
			}
			?>
			</td>
			</TR>
		</TABLE>

		<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<tr>
			<td ALIGN="left">
				<form action="<? echo $dims->getScriptEnv(); ?>" method="post" name="form_print">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("dims_op",	"desktopv2");
					$token->field("action",		"imprimer_suivi");
					$token->field("id_suivi",	$this->getIdSuivi());
					$token->field("format",		"ODT");
					$token->field("suivi_modele");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<input type="hidden" name="dims_op" value="desktopv2" />
				<input type="hidden" name="action" value="imprimer_suivi" />
				<input TYPE="hidden" NAME="id_suivi" VALUE="<? echo $this->getIdSuivi(); ?>" />
				<input TYPE="hidden" NAME="format" value="ODT" />

				<table cellpadding="2" cellspacing="0">
				<tr>
					<td>Imprimer:</td>
					<td>
					<select class="Select" name="suivi_modele">
					<?php
					//$listenum = business_getlistenum('modele_suivi',false);
					require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
					require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";
					$models = print_model::getModelsForType($this->getType());
					foreach($models as $model) {
						echo "<option value=\"{$model->getId()}\">{$model->getLabel()}</option>";
					}
					?>
					</select>
					</td>
					<td><input title="Télécharger au format ODT" type="image" value="ODT" onclick="javascript:document.form_print.format.value='ODT';" src="./common/modules/business/img/download_odt.gif" alt="imprimer (ODT)" /></td>
					<td><input title="Télécharger au format PDF" type="image" value="PDF" onclick="javascript:document.form_print.format.value='PDF';" src="./common/modules/business/img/download_pdf.gif" alt="imprimer (PDF)" /></td>
					<td><input title="Télécharger au format DOC" type="image" value="DOC" onclick="javascript:document.form_print.format.value='DOC';" src="./common/modules/business/img/download_doc.gif" alt="imprimer (DOC)" /></td>
				</tr>
				</table>
				</form>
			</td>
			<td ALIGN="RIGHT">
				<?php
				if ($this->getType() == 'Devis') {
					echo dims_create_button('Générer une facture', 'extlink', 'document.location.href=\''.$dims->getScriptEnv().'?dims_op=desktopv2&action=generer_facture&id_suivi='.$this->getIdSuivi().'\'');
				}
				echo dims_create_button('Enregistrer le suivi', 'disk', 'document.form_suivi.submit()');
				?>
			</TD>
		</tr>
		</TABLE>

		<br/>
		<?
		echo '<h2>Détail du suivi '.$this->getNumero().'</h2>';
		?>
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
		<tr>
			<td ALIGN="right">
				<?php echo dims_create_button('Ajouter une ligne', 'plus', 'newSuiviDetail('.$this->getIdSuivi().')'); ?>
			</TD>
		</tr>
		</TABLE>

		<div id="suiviDetail"></div>


		<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
		<table cellpadding="2" cellspacing="1" width="100%">
		<?
		$color = $skin->values['bgline1'];
		?>
		<tr bgcolor="<? echo $color; ?>" class="title">
			<td>Position</td>
			<td>Code</td>
			<td>Libellé</td>
			<td>Description</td>
			<td align="right">Prix Unitaire</td>
			<td align="right">Qté</td>
			<td align="right">TVA (%)</td>
			<td align="right">Montant HT</td>
			<td align="right">Montant TVA</td>
			<td align="right">Montant TTC</td>
			<td>&nbsp;</td>
		</tr>

		<?

		$detail_commande = array();
		$select = "
			SELECT	*
			FROM	dims_mod_business_suivi_detail
			WHERE	suivi_id = :idsuivi
			AND	suivi_type = :typesuivi
			AND	suivi_exercice = :exercicesuivi
			AND	id_workspace = :idworkspace
			ORDER BY position";
		$rs = $db->query($select, array(
			':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
			':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
			':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		while ($fields = $db->fetchrow($rs)) {
			//tri des lignes par taux de tva de manière à calculer un sous total de tva par code tva appliqué
			$detail_commande[$fields['tauxtva']]['articles'][] = $fields;

			$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
			$montantht = business_round_price($fields['pu']*$fields['qte']);
			$montanttva = business_round_price(($montantht*$fields['tauxtva'])/100);
			$montantttc = business_round_price($montantht+$montanttva);

			$bold1 = $bold2 = '';
			if (isset($modifier_ligne) && $modifier_ligne == $fields['id']) {
				$bold1 = '<b>';
				$bold2 = '</b>';
				?>
				<tr>
				<td colspan="12">
					<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
				</td>
				</tr>
				<?
			}
			?>
			<tr bgcolor="<? echo $color; ?>">
				<td width="55" align="center">
					<a href="javascript:alert('A faire');"><img border="0" src="./common/modules/business/img/ico_up.gif"></a>
					<a href="javascript:alert('A faire');"><img border="0" src="./common/modules/business/img/ico_down.gif"></a>
				</td>
				<td valign="top"><? echo "{$bold1}{$fields['code']}{$bold2}"; ?></td>
				<td valign="top"><? echo "{$bold1}{$fields['libelle']}{$bold1}"; ?></td>
				<td><? echo "{$bold1}".nl2br($fields['description'])."{$bold1}"; ?></td>
				<td valign="top" align="right" nowrap><? echo $bold1.business_format_price($fields['pu']); ?>&nbsp;&euro;</td>
				<td valign="top" align="right" nowrap><? echo "{$bold1}{$fields['qte']}{$bold1}"; ?></td>
				<td valign="top" align="right" nowrap><? echo $bold1.business_format_price($fields['tauxtva']).$bold2; ?></td>
				<td valign="top" align="right" nowrap><? echo $bold1.business_format_price($montantht).$bold2; ?>&nbsp;&euro;</td>
				<td valign="top" align="right" nowrap><? echo $bold1.business_format_price($montanttva).$bold2; ?>&nbsp;&euro;</td>
				<td valign="top" align="right" nowrap><? echo $bold1.business_format_price($montantttc).$bold2; ?>&nbsp;&euro;</td>
				<td valign="top" width="75" align="center">
					<a href="javascript:void(0);" onclick="javascript:editSuiviDetail(<?php echo $this->getIdSuivi(); ?>, <?php echo $fields['id']; ?>)">
						<img border="0" src="./common/modules/business/img/ico_modify.gif">
					</a>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $dims->getScriptEnv(); ?>?dims_op=desktopv2&action=supprimer_suivi_detail&id_suivi=<?php echo $this->getIdSuivi(); ?>&suivi_detail_id=<?php echo $fields['id']; ?>', 'Êtes vous sûr(e) de vouloir supprimer cette ligne ?')">
						<img border="0" src="./common/modules/business/img/ico_delete.gif">
					</a>
				</td>
			</tr>
			<?php
		}

		$detail_commande = $this->get_detail();

		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		?>

		<?php
		if ($this->fields['remise'] > 0)
		{
			?>
			<tr bgcolor="<? echo $color; ?>" class="title">
				<td colspan="7" align="right" nowrap>Montant Total (sans remise)</td>
				<td align="right" nowrap><? echo business_format_price($detail_commande['montant_ht']+$detail_commande['remise_ht']); ?>&nbsp;&euro;</td>
				<td align="right" nowrap><? echo business_format_price($detail_commande['montant_tva']+$detail_commande['remise_tva']); ?>&nbsp;&euro;</td>
				<td align="right" nowrap><? echo business_format_price($detail_commande['montant_ttc']+$detail_commande['remise_ttc']); ?>&nbsp;&euro;</td>
				<td>&nbsp;</td>
			</tr>
			<tr bgcolor="<? echo $color; ?>" class="title">
				<td colspan="7" align="right" class="remise">Remise (<? echo $this->fields['remise']; ?> %)</td>
				<td align="right" nowrap class="remise"><? echo business_format_price($detail_commande['remise_ht']); ?>&nbsp;&euro;</td>
				<td align="right" nowrap class="remise"><? echo business_format_price($detail_commande['remise_tva']); ?>&nbsp;&euro;</td>
				<td align="right" nowrap class="remise"><? echo business_format_price($detail_commande['remise_ttc']); ?>&nbsp;&euro;</td>
				<td>&nbsp;</td>
			</tr>
			<?php
		}
		?>

		<tr bgcolor="<? echo $color; ?>" class="title">
			<td colspan="7" align="right" nowrap style="font-weight:bold;">Montant Total</td>
			<td align="right" nowrap style="font-weight:bold;"><? echo business_format_price($detail_commande['montant_ht']); ?>&nbsp;&euro;</td>
			<td align="right" nowrap style="font-weight:bold;"><? echo business_format_price($detail_commande['montant_tva']); ?>&nbsp;&euro;</td>
			<td align="right" nowrap style="font-weight:bold;"><? echo business_format_price($detail_commande['montant_ttc']); ?>&nbsp;&euro;</td>
			<td>&nbsp;</td>
		</tr>
		</table>
	</div>
</div>
