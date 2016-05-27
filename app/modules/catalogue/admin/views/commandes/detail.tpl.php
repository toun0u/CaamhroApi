<?php
$view = view::getInstance();
$cde = $view->get('commande');
?>
<table class="tableau">
	<tr>
		<td>
			<?= dims_constant::getVal('REF'); ?>
		</td>
		<td>
			<?= dims_constant::getVal('_DESIGNATION'); ?>
		</td>
		<td>
			<?= dims_constant::getVal('_QTY'); ?>
		</td>
		<td>
			<?= dims_constant::getVal('PU_HT'); ?>
		</td>
<!-- 		<td>
			<?= dims_constant::getVal('_DISCOUNT')." (&#37;)"; ?>
		</td>
 -->		<td>
			TVA (&#37;)
		</td>
		<td>
			<?= dims_constant::getVal('_TOTAL_DUTY_FREE_AMOUNT'); ?>
		</td>
	</tr>
	<?php
	$lstLignes = $this->get('lignes');
	if (count($lstLignes)) {
		if ($cde->fields['hors_cata']) {
			foreach ($lstLignes as $ligne) {
				?>
				<tr>
					<td>
						<?= $ligne->fields['reference']; ?>
					</td>
					<td>
						<?= $ligne->fields['designation']; ?>
					</td>
					<td>
						<?= $ligne->fields['qte']; ?>
					</td>
					<td>
						<?= $ligne->fields['pu']; ?>
					</td>
<!-- 					<td>
						-
					</td>
 -->					<td>
						-
					</td>
					<td>
						<?= $ligne->fields['pu'] * $ligne->fields['qte']; ?>
					</td>
				</tr>
				<?
			}
		} else { // commande hors catalogue
			foreach ($lstLignes as $ligne) {
				?>
				<tr>
					<td>
						<?= $ligne->fields['ref']; ?>
					</td>
					<td>
						<?= $ligne->fields['label']; ?>
					</td>
					<td style="text-align:right;">
						<?= $ligne->fields['qte']; ?>
					</td>
					<td style="text-align:right;">
						<?= money_format('%n', $ligne->fields['pu_remise']); ?>
					</td>
<!-- 					<td style="text-align:right;">
						<?= ($ligne->fields['remise']=='0%' || $ligne->fields['remise'] == 0) ? "-" : number_format(floatval($ligne->fields['remise']), 2, ',', ' '); ?>
					</td>
 -->					<td style="text-align:right;">
						<?= number_format($ligne->fields['tx_tva'], 2, ',', ' '); ?>
					</td>
					<td style="text-align:right;">
						<?= money_format('%n',$ligne->fields['pu_remise'] * $ligne->fields['qte']); ?>
					</td>
				</tr>
				<?
			}
		}
	} else {
		?>
		<tr>
			<td colspan="7" style="text-align:center;">
				<?= dims_constant::getVal('NO_RESULT'); ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
