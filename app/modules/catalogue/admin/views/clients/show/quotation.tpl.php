<?php
$client     = $this->get('client');
$quotations = $this->get('quotations');
?>
<h3>
	<?= dims_constant::getVal('QUOTATION'); ?>
</h3>
<table class="tableau">
	<tr>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_STATE'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_DIMS_DATE'); ?></td>
		<td class="w60 title_tableau"><?= dims_constant::getVal('_DIMS_LABEL'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_DISCOUNT'); ?></td>
		<td class="w10 title_tableau"><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></td>
	</tr>
	<?php
	if(!empty($quotations)) {
		foreach($quotations as $quotation) {
			$localdate = array('date' => '', 'time' => '');
			if($quotation->get('date_cree') > 0) {
				$localdate = dims_timestamp2local($quotation->get('date_cree'));
			}
			?>
			<tr>
				<td>
					<img src="<?= cata_facture::getstatepicture($quotation->fields['state']); ?>" alt="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" title="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" />
				</td>
				<td><?= (!empty($localdate['date']) ? $localdate['date'] : '<em>n/a</em>'); ?></td>
				<td><?= $quotation->fields['libelle']; ?></td>
				<td><?= $quotation->fields['discount']; ?>&nbsp;%</td>
				<td>
					<a href="<?= get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->getId())); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>">
						<img src="<?=  $this->getTemplateWebPath('gfx/ouvrir16.png'); ?>" />
					</a>
				</td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="5">
				<?= dims_constant::getVal('NO_QUOTATION'); ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
