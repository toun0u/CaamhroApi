<?php
$db = dims::getInstance()->getDb();
global $business_periode;
require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
?>
<div>
	<div class="actions">
		<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?= $this->getLightAttribute('id_popup'); ?>');">
			<img src="/common/modules/system/desktopV2/templates/gfx/common/close_news.png" />
		</a>
	</div>
	<h2>
		Détail de <?= $this->getLibelle().' ('.$this->getNumero().') - '.$this->getTiers()->getLabel(); ?>
	</h2>
	<?php
	$others = suivi::find_by(array('contact_id'=>$this->fields['contact_id'],'tiers_id'=>$this->fields['tiers_id'])," ORDER BY id");
	$prev = $next = 0;
	$is = false;
	foreach($others as $o) {
		if($o->fields['id_suivi'] == $this->fields['id_suivi']){
			$is = true;
		}elseif($is){
			$next = $o->fields['id_suivi'];
			break;
		}else{
			$prev = $o->fields['id_suivi'];
		}
	}
	if($prev > 0) {
		?>
		<div class="suivi_precedant">
			<a href="javascript:void(0);" class="open" dims-data-value="<?= $prev; ?>"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/suivi_precedant.png" /></a>
		</div>
		<?php
	}
	if($next > 0) {
		?>
		<div class="suivi_suivant">
			<a href="javascript:void(0);" class="open" dims-data-value="<?= $next; ?>"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/suivi_suivant.png"/></a>
		</div>
		<?php
	}
	?>
	<div class="<?= ($this->getType() == 'Facture' || ($this->getType() == 'Devis' && $this->getValide()))?"col-xs-12 col-md-8":""; ?>">
		<?php
		$form = new Dims\form(array(
			'name' 			=> "new_suivi",
			'method'		=> "POST",
			'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=suivi&action=save",
			'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
			'back_name'		=> $_SESSION['cste']['_DIMS_CANCEL'],
			'back_url'		=> "javascript:void(0);\" onclick=\"javascript:dims_closeOverlayedPopup('".$this->getLightAttribute('id_popup')."');",
			'object'		=> $this,
		));

		$form->add_hidden_field(array(
			'name'		=> 'id',
			'db_field'	=> 'id_suivi',
		));

		if($this->get('id_type') <= 0){
			$lstTypes = array(0=>"");
			$types = suivi_type::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'status'=>1)," ORDER BY label ");
			foreach($types as $t) {
				$lstTypes[$t->fields['id']] = $t->fields['label'];
			}
			$form->add_select_field(array(
				'name'		=> 'suivi_id_type',
				'label' 	=> $_SESSION['cste']['_TYPE'],
				'options'	=> $lstTypes,
				'db_field'	=> "id_type",
			));
		}else{
			$form->add_simple_text(array(
				'label' 	=> $_SESSION['cste']['_TYPE'],
				'value'		=> $this->getType(),
			));
		}

		$form->add_simple_text(array(
			'label' 	=> $_SESSION['cste']['_DUTY'],
			'value'		=> $this->getExercice(),
		));

		$form->add_simple_text(array(
			'label' 	=> $_SESSION['cste']['_NUMBER'],
			'value'		=> $this->getNumero(),
		));

		$form->add_text_field(array(
			'name'						=> 'suivi_datejour',
			'label' 					=> $_SESSION['cste']['_DIMS_DATE'],
			'value'						=> $this->getDateJour(),
			'revision'					=> "date_jj/mm/yyyy",
			'additionnal_attributes'	=> 'maxlength="10" size="20"',
		));

		$form->add_text_field(array(
			'name'		=> 'suivi_libelle',
			'label' 	=> $_SESSION['cste']['_AGENDA_LABEL_LABEL'],
			'db_field'	=> "libelle",
			'mandatory'	=> true,
		));

		$form->add_text_field(array(
			'name'		=> 'suivi_remise',
			'label' 	=> $_SESSION['cste']['_DISCOUNT']." (%)",
			'db_field'	=> "remise",
		));

		switch ($this->getType()) {
			case 'Devis':
				$form->add_checkbox_field(array(
					'name'		=> 'suivi_valide',
					'label' 	=> $_SESSION['cste']['_ACCEPTED'],
					'value'		=> 1,
					'checked'	=> $this->getValide(),
				));
				if ($this->getValide()) {
					$form->add_text_field(array(
						'name'						=> 'suivi_datevalide',
						'label' 					=> "Date d'acceptation",
						'value'						=> $this->getDateValide(),
						'revision'					=> "date_jj/mm/yyyy",
						'additionnal_attributes'	=> 'maxlength="10" size="20"',
					));
				}
				break;
			case 'Facture':
				array_unshift($business_periode,ucfirst($_SESSION['cste']['NO_ELEMENT_FEMININ']));
				$form->add_select_field(array(
					'name'		=> 'suivi_periode',
					'label' 	=> "Périodicité",
					'options'	=> $business_periode,
					'db_field'	=> 'periode',
				));
				break;
		}

		$arrayTiers = array();
		if($this->fields['tiers_id'] > 0){
			$arrayTiers[] = $this->getTiers()->get('id_globalobject');
		}
		$arrayCt = array();
		if($this->fields['contact_id'] > 0){
			$arrayCt[] = $this->getTiers()->get('id_globalobject');
		}
		require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
		$matrix = new search();
		$linkedObjectsIds = $matrix->exploreMatrice(
			array($_SESSION['dims']['workspaceid']),
			null,
			null,
			null,
			$arrayTiers,
			$arrayCt
		);
		$desktop = new desktopv2();
		$lstObj = $desktop->getLinkedObjects($linkedObjectsIds);
		$lstDossiers = array(
			0 => "",
		);
		if(!empty($lstObj['dossiers'])){
			foreach ($lstObj['dossiers'] as $dossier) {
				$lstDossiers[$dossier->get('id_globalobject')] = $dossier->get('label');
			}
		}
		$form->add_select_field(array(
			'name'		=> 'suivi_dossier_id',
			'label' 	=> $_SESSION['cste']['_DOC_FOLDER'],
			'options'	=> $lstDossiers,
			'db_field'	=> 'dossier_id',
		));

		$form->add_textarea_field(array(
			'name'		=> 'suivi_description',
			'label' 	=> $_SESSION['cste']['_DIMS_COMMENTS'],
			'db_field'	=> "description",
		));
		$form->build();
		?>
	</div>
	<?php if($this->getType() == 'Facture' || ($this->getType() == 'Devis' && $this->getValide())){ ?>
		<div class="col-xs-6 col-md-4">
			<h3>Liste des versements</h3>
			<?php
			$form = new Dims\form(array(
				'name' 			=> "new_versement",
				'method'		=> "POST",
				'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=suivi&action=add_versement",
				'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
				'object'		=> $this,
			));
			echo $form->get_header();
			echo $form->hidden_field(array(
				'name'		=> 'id',
				'db_field'	=> 'id_suivi',
			));

			?>
			<label for="montant">Versement</label>
			<?php
			echo $form->text_field(array(
				'name'		=> 'montant',
				'id'		=> 'montant',
				'revision'	=> 'number',
				'mandatory'	=> true,
			));
			?>
			<div class="actions">
				<?php
				echo $form->submit_field(array(
					'value'	=> $_SESSION['cste']['_DIMS_ADD'],
				));
				if ($this->getType() == 'Facture'){
					echo $form->button_field(array(
						'value'						=> "Solder",
						'additionnal_attributes'	=> 'onclick="javascript:dims_confirmlink(\''.dims::getInstance()->getScriptEnv().'?submenu=1&mode=suivi&action=solder&id='.$this->fields['id_suivi'].'\',\''.$_SESSION['cste']['_DIMS_CONFIRM'].'\');"',
					));
				}
				?>
			</div>
			<?php
			echo $form->close_form();
			?>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td nowrap>Total Versements:&nbsp;<?= $this->getMontantTTC() - $this->getSoldeTTC(); ?>&nbsp;&euro;&nbsp;</td>
					<?
					if ($this->getSoldeTTC() > 0){
						?>
						<td nowrap align="right" class="nonsolde">&nbsp;Solde:&nbsp;<?= $this->getSoldeTTC(); ?>&nbsp;&euro;</td>
						<?
					}else{
						?>
						<td nowrap align="right" class="solde">&nbsp;Soldé</td>
						<?
					}
					?>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" style="width:100%;">
				<tr>
					<th>Date du Paiement</th>
					<th align="right">Montant</th>
					<th>&nbsp;</th>
				</tr>
				<?php
				$select = "	SELECT		*
							FROM		dims_mod_business_versement
							WHERE		suivi_id = :idsuivi
							AND			suivi_type = :typesuivi
							AND			suivi_exercice = :exercicesuivi
							ORDER BY 	date_paiement DESC";
				$params = array(
					':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
					':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
					':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
				);
				$rs = $db->query($select, $params);
				if($db->numrows($rs) > 0){
					while ($fields = $db->fetchrow($rs)) {
						$date_fr = dims_timestamp2local($fields['date_paiement']);
						?>
						<tr>
							<td><strong><?= current(dims_timestamp2local($fields['date_paiement'])); ?></strong></td>
							<td align="right"><?= business_format_price($fields['montant']); ?>&nbsp;&euro;</td>
							<td width="40" align="center">
								<a title="Supprimer ce versement" href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo dims_urlencode(dims::getInstance()->getScriptEnv()."?submenu=1&mode=suivi&action=del_versement&id=".$this->fields['id_suivi']."&versement_id={$fields['id']}"); ?>', 'Êtes-vous sûr(e) de vouloir supprimer ce versement ?');">
									<img border="0" src="/common/modules/system/desktopV2/templates/gfx/common/delete16.png" />
								</a>
							</td>
						</tr>
						<?
					}
				}else{
					?>
					<tr><td colspan="3" style="text-align:center">Aucun versement</td></tr>
					<?php
				}
				?>
			</table>
		</div>
	<?php } ?>
	<div>
		<div>
			<?php
			require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";
			$models = print_model::find_by(array('id_type'=>$this->get('id_type'))," ORDER BY label ");
			$lstModels = array(0=>"");
			foreach($models as $model) {
				$lstModels[$model->get('id')] = $model->getLabel();
			}
			if(count($lstModels) > 1){
				$form = new Dims\form(array(
					'name' 			=> "imprimer",
					'method'		=> "POST",
					'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=suivi&action=imprimer",
					'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
					'object'		=> $this,
				));
				echo $form->get_header();
				echo $form->hidden_field(array(
					'name'		=> 'id',
					'db_field'	=> 'id_suivi',
				));
				?>
				<label for="suivi_modele">Imprimer</label>
				<?php
				echo $form->select_field(array(
					'name'		=> 'suivi_modele',
					'id'		=> 'suivi_modele',
					'options'	=> $lstModels,
					'mandatory'	=> true,
				));
				echo $form->submit_field(array(
					'name'						=> 'format',
					'value'						=> 'ODT',
					'additionnal_attributes'	=> 'style="background-color:#FFFFFF;color:rgba(255,255,255,0);border:0px;height:21px;width:53px;background-image:url(\'/common/modules/forms/img/download_odt.gif\');" title="Télécharger au format ODT" alt="Télécharger au format ODT"',
				));
				echo $form->submit_field(array(
					'name'						=> 'format',
					'value'						=> 'PDF',
					'additionnal_attributes'	=> 'style="background-color:#FFFFFF;color:rgba(255,255,255,0);border:0px;height:21px;width:53px;background-image:url(\'/common/modules/forms/img/download_pdf.gif\');" title="Télécharger au format PDF" alt="Télécharger au format PDF"',
				));
				echo $form->submit_field(array(
					'name'						=> 'format',
					'value'						=> 'DOC',
					'additionnal_attributes'	=> 'style="background-color:#FFFFFF;color:rgba(255,255,255,0);border:0px;height:21px;width:53px;background-image:url(\'/common/modules/forms/img/download_doc.gif\');" title="Télécharger au format DOC" alt="Télécharger au format DOC"',
				));
				echo $form->close_form();
			}else{
				?>
				Aucun modèle disponible pour l'impression
				<?php
			}
			?>
		</div>
		<?php if ($this->getType() == 'Devis') { ?>
			<div class="actions">
				<input type="button" value="Générer une facture" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=suivi&action=facture&id=<?= $this->fields['id_suivi']; ?>';" />
			</div>
		<?php } ?>
		<h3>Détail du suivi : <?= $this->getNumero(); ?></h3>
		<div class="actions">
			<a href="javascript:void(0);" class="add-line">Ajouter une ligne</a>
		</div>
		<p style="clear:both;"></p>
		<div id="form_add_line" style="display:none;"></div>
		<div id="list_lines">
			<table cellpadding="0" cellspacing="0" style="width:100%;">
				<tr>
					<th>Position</th>
					<th>Code</th>
					<th>Libellé</th>
					<th>Description</th>
					<th align="right">Prix Unitaire</th>
					<th align="right">Qté</th>
					<th align="right">TVA (%)</th>
					<th align="right">Montant HT</th>
					<th align="right">Montant TVA</th>
					<th align="right">Montant TTC</th>
					<th>&nbsp;</th>
				</tr>
				<?php
				$select = "	SELECT		*
							FROM		dims_mod_business_suivi_detail
							WHERE		suivi_id = :idsuivi
							AND			suivi_type = :typesuivi
							AND			suivi_exercice = :exercicesuivi
							AND			id_workspace = :idworkspace
							ORDER BY 	position";
				$params = array(
					':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $this->getId()),
					':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getType()),
					':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $this->getExercice()),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				);
				$rs = $db->query($select, $params);
				if($db->numrows($rs) > 0){
					$nbElem = $db->numrows($rs);
					while ($fields = $db->fetchrow($rs)) {
						//tri des lignes par taux de tva de manière à calculer un sous total de tva par code tva appliqué

						$montantht = business_round_price($fields['pu']*$fields['qte']);
						$montanttva = business_round_price(($montantht*$fields['tauxtva'])/100);
						$montantttc = business_round_price($montantht+$montanttva);
						?>
						<tr>
							<td width="55" align="center">
								<?php if($fields['position'] > 1): ?>
									<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=suivi&action=down_detail&id=<?= $this->getIdSuivi(); ?>&idd=<?= $fields['id']; ?>"><img border="0" src="/common/img/go-up.png" /></a>
								<?php else: ?>
									<img border="0" style="width:16px;" src="/common/img/blank.gif" />
								<?php endif; ?>
								<?php if($fields['position'] < $nbElem): ?>
									<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=suivi&action=up_detail&id=<?= $this->getIdSuivi(); ?>&idd=<?= $fields['id']; ?>"><img border="0" src="/common/img/go-down.png" /></a>
								<?php else: ?>
									<img border="0" style="width:16px;" src="/common/img/blank.gif" />
								<?php endif; ?>
							</td>
							<td valign="top"><?= $fields['code']; ?></td>
							<td valign="top"><?= $fields['libelle']; ?></td>
							<td><?= nl2br($fields['description']); ?></td>
							<td valign="top" align="right" nowrap><?= business_format_price($fields['pu']); ?>&nbsp;&euro;</td>
							<td valign="top" align="right" nowrap><?= $fields['qte']; ?></td>
							<td valign="top" align="right" nowrap><?= business_format_price($fields['tauxtva']); ?></td>
							<td valign="top" align="right" nowrap><?= business_format_price($montantht); ?>&nbsp;&euro;</td>
							<td valign="top" align="right" nowrap><?= business_format_price($montanttva); ?>&nbsp;&euro;</td>
							<td valign="top" align="right" nowrap><?= business_format_price($montantttc); ?>&nbsp;&euro;</td>
							<td valign="top" width="75" align="center">
								<a href="javascript:void(0);" dims-data-value="<?= $fields['id']; ?>" class="add-line"><img border="0" src="/common/modules/system/desktopV2/templates/gfx/common/crayon16.png" /></a>
								<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=suivi&action=delete_detail&id=<?= $this->getIdSuivi(); ?>&idd=<?= $fields['id']; ?>', 'Êtes vous sûr(e) de vouloir supprimer cette ligne ?')"><img border="0" src="/common/modules/system/desktopV2/templates/gfx/common/delete16.png" /></a>
							</td>
						</tr>
						<?php
					}
					$detail_commande = $this->get_detail();
					if ($this->fields['remise'] > 0){
						?>
						<tr class="title">
							<td colspan="7" align="right" nowrap>Montant Total (sans remise)</td>
							<td align="right" nowrap><?= business_format_price($detail_commande['montant_ht']+$detail_commande['remise_ht']); ?>&nbsp;&euro;</td>
							<td align="right" nowrap><?= business_format_price($detail_commande['montant_tva']+$detail_commande['remise_tva']); ?>&nbsp;&euro;</td>
							<td align="right" nowrap><?= business_format_price($detail_commande['montant_ttc']+$detail_commande['remise_ttc']); ?>&nbsp;&euro;</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="title">
							<td colspan="7" align="right" class="remise">Remise (<? echo $this->fields['remise']; ?> %)</td>
							<td align="right" nowrap class="remise"><?= business_format_price($detail_commande['remise_ht']); ?>&nbsp;&euro;</td>
							<td align="right" nowrap class="remise"><?= business_format_price($detail_commande['remise_tva']); ?>&nbsp;&euro;</td>
							<td align="right" nowrap class="remise"><?= business_format_price($detail_commande['remise_ttc']); ?>&nbsp;&euro;</td>
							<td>&nbsp;</td>
						</tr>
						<?php
					}
					?>
					<tr class="title">
						<td colspan="7" align="right" nowrap style="font-weight:bold;">Montant Total</td>
						<td align="right" nowrap style="font-weight:bold;"><?= business_format_price($detail_commande['montant_ht']); ?>&nbsp;&euro;</td>
						<td align="right" nowrap style="font-weight:bold;"><?= business_format_price($detail_commande['montant_tva']); ?>&nbsp;&euro;</td>
						<td align="right" nowrap style="font-weight:bold;"><?= business_format_price($detail_commande['montant_ttc']); ?>&nbsp;&euro;</td>
						<td>&nbsp;</td>
					</tr>
					<?php
				}else{
					?>
					<tr><td colspan="11" style="text-align:center;">Aucune ligne renseignée</td></tr>
					<?php
				}
				?>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
$('a.add-line').click(function(){
	var idd = 0;
	if($(this).attr('dims-data-value') !== undefined){
		idd = $(this).attr('dims-data-value');
	}
	$.ajax({
		type: "POST",
		url: "<?= dims::getInstance()->getScriptEnv(); ?>",
		data: {
			'submenu': '1',
			'mode': 'suivi',
			'action': 'edit_detail',
			'id': '<?= $this->get('id_suivi'); ?>',
			'idd': idd
		},
		dataType: "html",
		success: function(data){
			$('#list_lines').hide();
			$('#form_add_line').html(data).show();
		},
	});

});
$('#popup_container a.open').click(function(){
	if($(this).attr('dims-data-value') !== undefined){
		document.getElementById('dims_popup').innerHTML="";
		dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', 'submenu=1&mode=suivi&action=show&id='+$(this).attr('dims-data-value')+'&id_popup=<?= $this->getLightAttribute('id_popup'); ?>','','p<?= $this->getLightAttribute('id_popup'); ?>');
	}
});
</script>
