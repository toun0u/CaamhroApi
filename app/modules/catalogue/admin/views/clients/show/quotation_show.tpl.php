<?php
$client             = $this->get('client');
$quotation          = $this->get('quotation');
$quotelines         = $this->get('quotelines');
$articles           = $this->get('articles');
$quotelineedition   = $this->get('quotelineedition');
$caneditlines       = $this->get('caneditlines');
$case               = $this->get('case');

$docmodels          = $this->get('docmodels');
$lstModels = array();
foreach($docmodels as $model) {
	$lstModels[$model->get('id')] = $model->getLabel();
}

$localdate          = array('date' => '', 'time' => '');
if($quotation->get('date_cree') > 0) {
	$localdate = dims_timestamp2local($quotation->get('date_cree'));
}
$quotelinesform = new Dims\form();
?>
<div class="form_object_block">
	<h3>
		<?= dims_constant::getVal('QUOTATION'); ?> - <?= $quotation->fields['libelle']; ?>
	</h3>
	<div class="sub_bloc">
		<div class="sub_bloc_form">
			<div class="grid2 pa1 line">
				<div class="mod">
					<table>
						<tr>
							<td class="w20 txtright"><?= dims_constant::getVal('PRINT_OUT'); ?> :</td>
							<td class="value_field">
								<?php
								if(!empty($lstModels)) {
									$printoutform = new Dims\form(array(
										'name'          => "printout",
										'method'        => "POST",
										'action'        => get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'printout')),
										'submit_value'  => dims_constant::getVal('_DIMS_SAVE'),
										'object'        => $quotation,
									));
									echo $printoutform->get_header();
									echo $printoutform->hidden_field(array(
										'name'      => 'idquotation',
										'db_field'  => 'id',
									));
									echo $printoutform->select_field(array(
										'name'      => 'suivi_modele',
										'id'        => 'suivi_modele',
										'options'   => $lstModels,
										'mandatory' => true,
									));
									echo $printoutform->submit_field(array(
										'name'                      => 'format',
										'value'                     => 'ODT',
										'additionnal_attributes'    => 'style="background-color:#FFFFFF;color:rgba(255,255,255,0);border:0px;height:21px;width:53px;background-image:url(\'/common/modules/forms/img/download_odt.gif\');" title="Télécharger au format ODT" alt="Télécharger au format ODT"',
									));
									echo $printoutform->submit_field(array(
										'name'                      => 'format',
										'value'                     => 'PDF',
										'additionnal_attributes'    => 'style="background-color:#FFFFFF;color:rgba(255,255,255,0);border:0px;height:21px;width:53px;background-image:url(\'/common/modules/forms/img/download_pdf.gif\');" title="Télécharger au format PDF" alt="Télécharger au format PDF"',
									));
									echo $printoutform->submit_field(array(
										'name'                      => 'format',
										'value'                     => 'DOC',
										'additionnal_attributes'    => 'style="background-color:#FFFFFF;color:rgba(255,255,255,0);border:0px;height:21px;width:53px;background-image:url(\'/common/modules/forms/img/download_doc.gif\');" title="Télécharger au format DOC" alt="Télécharger au format DOC"',
									));
									echo $printoutform->close_form();
								} else {
									echo dims_constant::getVal('NO_MODEL_AVAILABLE_FOR_PRINTING');
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="w20 txtright"><?= dims_constant::getVal('_STATE'); ?> :</td>
							<td class="value_field">
								<img src="<?= cata_facture::getstatepicture($quotation->fields['state']); ?>" alt="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" title="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" />
							</td>
						</tr>
						<?php
						if(!$case->isNew()) {
							?>
							<tr>
								<td class="w20 txtright"><?= dims_constant::getVal('DIMS_CASE'); ?> :</td>
								<td class="value_field">
									<a href="<?= get_path('dossier', 'show', array('dims_mainmenu' => 'gescom', 'id' => $case->getId())); ?>">
										<?= $case->fields['label']; ?>
									</a>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td class="w20 txtright"><?= dims_constant::getVal('_DIMS_DATE'); ?> :</td>
							<td class="value_field"><?= $localdate['date']; ?></td>
						</tr>
						<tr>
							<td class="w20 txtright"><?= dims_constant::getVal('_DISCOUNT'); ?>&nbsp;:</td>
							<td class="value_field"><?= $quotation->get('discount'); ?>&nbsp;%</td>
						</tr>
						<tr>
							<td class="w20 txtright"><?= dims_constant::getVal('REMARK'); ?> :</td>
							<td class="value_field"><?= nl2br($quotation->get('commentaire')); ?></td>
						</tr>
					</table>
				</div>
				<div class="mod txtright">
					<a class="link_img" href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'editheader', 'quotationid' => $quotation->getId())); ?>">
						<img src="<?= $this->getTemplateWebPath('gfx/edit16.png'); ?>" />
						<span><?= dims_constant::getVal('EDIT_HEADER'); ?></span>
					</a>
					<br />
					<a class="link_img" href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'duplicate', 'quotationid' => $quotation->getId())); ?>">
						<img src="<?= $this->getTemplateWebPath('gfx/dupliquer20.png'); ?>" />
						<span><?= dims_constant::getVal('DUPLICATE_QUOTATION'); ?></span>
					</a>
					<?php
					if($quotation->fields['state'] != cata_facture::STATE_VALIDATED) {
						?>
						<br />
						<a class="link_img" href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'validate', 'quotationid' => $quotation->getId())); ?>">
							<img src="<?= $this->getTemplateWebPath('gfx/publier20.png'); ?>" />
							<span><?= dims_constant::getVal('_DIMS_VALID'); ?></span>
						</a>
						<?php
					}
					if($quotation->fields['state'] != cata_facture::STATE_REFUSED) {
						?>
						<br />
						<a class="link_img" href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'refuse', 'quotationid' => $quotation->getId())); ?>">
							<img src="<?= $this->getTemplateWebPath('gfx/depublier20.png'); ?>" />
							<span><?= dims_constant::getVal('REJECT'); ?></span>
						</a>
						<?php
					}
					?>
					<?php
					if(!$quotation->fields['deleted']) {
						?>
						<br />
						<a class="link_img" href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'delete', 'quotationid' => $quotation->getId())); ?>', '<?= dims_constant::getVal('_DIMS_CONFIRM'); ?>');">
							<img src="<?= $this->getTemplateWebPath('gfx/supprimer16.png'); ?>" />
							<span><?= dims_constant::getVal('_DELETE'); ?></span>
						</a>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<form name="documentlines" id="documentlines" class="form_object_block" method="POST" action="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'savequoteline', 'quotationid' => $quotation->getId())); ?>">
	<?= $quotelinesform->hidden_field(array(
		'name'  => 'idquotation',
		'value' => $quotation->getId(),
	)); ?>
	<?php
	if(!$quotelineedition->isNew()) {
		echo $quotelinesform->hidden_field(array(
			'name'  => 'idquoteline',
			'value' => $quotelineedition->getId(),
		));
	}
	?>
	<h3>
		<?= dims_constant::getVal('DOCUMENT_LINES'); ?>
		<?php
		if(!$quotelineedition->isNew()) {
			?>
				- <?= dims_constant::getVal('SHORT_EDITION'); ?> : <?= $quotelineedition->fields['label']; ?>
			<?php
		}
		?>
	</h3>
	<div class="sub_bloc">
		<div class="sub_bloc_form">
			<div class="grid2 pa1 line">
				<div class="mod">
						<?= $quotelinesform->text_field(array(
							'name'                      => 'searchreference',
							'additionnal_attributes'    =>
								'placeholder="'.dims_constant::getVal('ARTICLE_REF').'" '.
								'autocomplete="off"',
							'value'                     => (!$quotelineedition->isNew() ? $quotelineedition->fields['ref'] : ''),
							'mandatory'                 => true,
						)); ?>
						<div id="def_searchreference" class="mess_error"></div>
						<?= $quotelinesform->hidden_field(array(
							'name'  => 'idarticle',
							'value' => $quotelineedition->fields['id_article'],
						)); ?>
						<div id="ac_references" class="ac_container" style="display:none;">
							<ul id="ul_ac_references">
							</ul>
						</div>
						<?= $quotelinesform->text_field(array(
							'name'                      => 'quantity',
							'additionnal_attributes'    => 'placeholder="'.dims_constant::getVal('SHORT_QUANTITY').'"',
							'value'                     => $quotelineedition->fields['qte'],
							'mandatory'                 => true,
						)); ?>
						<?= $quotelinesform->text_field(array(
							'name'                      => 'puht',
							'additionnal_attributes'    =>
								'placeholder="'.dims_constant::getVal('PU_HT').'" '.
								((!$caneditlines) ? 'readonly="readonly"' : ''),
							'value' => $quotelineedition->fields['pu_ht'],
						)); ?>
						<?= $quotelinesform->text_field(array(
							'name'                      => 'discount',
							'additionnal_attributes'    =>
								'placeholder="'.dims_constant::getVal('_DISCOUNT').'" '.
								((!$caneditlines) ? 'readonly="readonly"' : ''),
							'value' => $quotelineedition->fields['remise'],
						)); ?>
						<?= $quotelinesform->text_field(array(
							'name'                      => 'tva',
							'additionnal_attributes'    =>
								'placeholder="'.dims_constant::getVal('RATE_TVA').'" '.
								((!$caneditlines) ? 'readonly="readonly"' : ''),
							'value' => $quotelineedition->fields['tx_tva'],
						)); ?>
						<div id="def_quantity" class="mess_error"></div>
				</div>
				<div class="mod">
					<?= $quotelinesform->text_field(array(
						'name'                      => 'libelle',
						'additionnal_attributes'    =>
							'placeholder="'.dims_constant::getVal('_DIMS_LABEL_LABEL').'" '.
							((!$caneditlines) ? 'readonly="readonly"' : ''),
						'value' => $quotelineedition->fields['label'],
					)); ?>
					<?= $quotelinesform->textarea_field(array(
						'name'                      => 'description',
						'additionnal_attributes'    => 'placeholder="'.dims_constant::getVal('_DIMS_LABEL_DESCRIPTIF').'"',
						'value' => $quotelineedition->fields['description'],
					)); ?>
				</div>
			</div>
			<div id="global_message" class="mess_error"></div>
			<div class="quotationaddline txtright">
				<?= $quotelinesform->submit_field(array(
					'name'  => 'submit',
					'value' => dims_constant::getVal('_DIMS_SAVE'),
				)); ?>
				<?= dims_constant::getVal('_DIMS_OR'); ?>
				<a href="">
					<?= dims_constant::getVal('REINITIALISER'); ?>
				</a>
			</div>
			<div class="quotationaddline">
			</div>
		</div>
		<div class="sub_bloc_form">
			<?php
			if(empty($quotelines)) {
				echo dims_constant::getVal('NO_LINES_HAVE_BEEN_CREATED_YET');
			} else {
				?>
				<table class="tableau">
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><?= dims_constant::getVal('ARTICLE_REF'); ?></td>
						<td><?= dims_constant::getVal('_DIMS_LABEL_LABEL'); ?></td>
						<td class="w50 title_tableau"><?= dims_constant::getVal('_DIMS_LABEL_DESCRIPTIF'); ?></td>
						<td><?= dims_constant::getVal('PU_HT'); ?></td>
						<td><?= dims_constant::getVal('SHORT_QUANTITY'); ?></td>
						<td><?= dims_constant::getVal('_DISCOUNT'); ?></td>
						<td><?= dims_constant::getVal('_DUTY_FREE_AMOUNT'); ?></td>
						<td><?= dims_constant::getVal('RATE_TVA'); ?></td>
						<td>&nbsp;</td>
					</tr>
					<?php
					foreach($quotelines as $quoteline) {
						$photo = '';
						if(!empty($articles[$quoteline->fields['ref']])) {
							$article    = $articles[$quoteline->fields['ref']];
							$photo      = $article->getWebPhoto();
						}
						?>
						<tr>
							<td>
								<a href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'quoteline_down', 'quotationid' => $quotation->getId(), 'quotelineid' => $quoteline->getId())); ?>">
									<img src="<?= $this->getTemplateWebPath('gfx/haut16_s.png'); ?>" />
								</a>
								<a href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'quoteline_up', 'quotationid' => $quotation->getId(), 'quotelineid' => $quoteline->getId())); ?>">
									<img src="<?= $this->getTemplateWebPath('gfx/bas16_s.png'); ?>" />
								</a>
							</td>
							<td><?= (!empty($photo)) ? '<img src="'.$photo.'" />' : ''; ?></td>
							<td><?= $quoteline->fields['ref']; ?></td>
							<td><?= $quoteline->fields['label']; ?></td>
							<td><?= $quoteline->fields['description']; ?></td>
							<td><?= number_format(round($quoteline->fields['pu_ht'], 2), 2, ',', ' '); ?></td>
							<td><?= $quoteline->fields['qte']; ?></td>
							<td><?= $quoteline->fields['remise']; ?>&nbsp;%</td>
							<td><?= number_format(round($quoteline->gettotalht(), 2), 2, ',', ' '); ?></td>
							<td><?= $quoteline->fields['tx_tva']; ?></td>
							<td>
								<a href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'editquoteline', 'quotationid' => $quotation->getId(), 'quotelineid' => $quoteline->getId())); ?>">
									<img src="<?= $this->getTemplateWebPath('gfx/edit16.png'); ?>" />
								</a>
								<a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'deletequoteline', 'quotationid' => $quotation->getId(), 'quotelineid' => $quoteline->getId())); ?>', '<?= dims_constant::getVal('_DIMS_CONFIRM'); ?>');">
									<img src="<?= $this->getTemplateWebPath('gfx/supprimer16.png'); ?>" />
								</a>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<div class="totals">
					<div>
						<div class="label"><?= dims_constant::getVal('_TOTAL_DUTY_FREE_AMOUNT'); ?> :</div>
						<div>
							<?= number_format(round($quotation->fields['total_ht'], 2), 2, ',', ' '); ?>&nbsp;&euro;
						</div>
					</div>
					<div>
						<div class="label"><?= dims_constant::getVal('_TOTAL_VAT'); ?> :</div>
						<div>
							<?= number_format(round($quotation->fields['total_tva'], 2), 2, ',', ' '); ?>&nbsp;&euro;
						</div>
					</div>
					<div>
						<div class="label"><?= dims_constant::getVal('_DISCOUNT'); ?> (<?= $quotation->fields['discount']; ?>&nbsp;%) :</div>
						<div>
							<?= number_format(round($quotation->fields['total_ht'] * floatval($quotation->fields['discount']) / 100, 2), 2, ',', ' '); ?>&nbsp;&euro;
						</div>
					</div>
					<div>
						<div class="label"><?= dims_constant::getVal('_SUBTOTAL_HT'); ?> :</div>
						<div>
						<strong>
							<?= number_format(round($quotation->fields['total_ht'] * (1 - (floatval($quotation->fields['discount']) / 100)), 2), 2, ',', ' '); ?>&nbsp;&euro;
						</strong>
						</div>
					</div>
					<div>
						<div class="label"><?= dims_constant::getVal('SUBTOTAL_VAT'); ?> :</div>
						<div>
							<?= number_format(round($quotation->fields['total_tva'] * (1 - (floatval($quotation->fields['discount']) / 100)), 2), 2, ',', ' '); ?>&nbsp;&euro;
						</div>
					</div>
					<div>
						<div class="label"><?= dims_constant::getVal('CATA_TOTAL_TTC'); ?> :</div>
						<div>
							<strong>
								<?= number_format(round($quotation->fields['total_ttc'], 2), 2, ',', ' '); ?>&nbsp;&euro;
							</strong>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</form>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$('#searchreference') .keypress(function(event){
			if ( event.which == 13 ) {
				event.preventDefault();
			}
		})
		.dims_autocomplete(
			{ c: 'articles', a: 'ac_articles' },
			2,
			600,
			'#idarticle',
			'#ac_references',
			'#ul_ac_references',
			'<li>${reference} - ${label}</li>',
			'<?= addslashes(dims_constant::getVal('NO_REFERENCE')); ?>',
			function() {
				idarticle = $('#idarticle').val();

				if(idarticle.length > 0) {
					jQuery.ajax({
						type: "POST",
						url: 'admin.php',
						data : {
							c:  'articles',
							a:  'json_article',
							id: idarticle,
						},
						dataType: "json",
						success: function(data){
							$('#libelle').val(data.label);
							$('#puht').val(data.puht);
							$('#discount').val('<?= $quotation->fields['discount']; ?>');
							$('#tva').val(data.tauxtva);
							$('#description').val(data.description);
						}
					});
				}
			}
		);
		$("#documentlines").dims_validForm({
			messages: {
				defaultError:   '<?= addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']); ?>',
			},
			displayMessages: true,
			refId: 'def',
			globalId: 'global_message',
		});
	});
</script>
