<?php
$etape = $this->get('step');
$bgclass="share_tdselected";
// traitement des etapes
?>
<link rel="stylesheet" href="./common/modules/sharefile/include/styles.css" type="text/css"/>
<div style="width:100%;margin:0 auto;border:0px;">
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="<?= $bgclass; ?>" style="width:5%;">
			<?php if($etape>1): ?>
				<img src="/common/modules/sharefile/img/puce_etape1.png">
			<?php else: ?>
				<img src="/common/modules/sharefile/img/puce_etape1.png">
			<?php endif; ?>
		</td>
		<td class="<?= $bgclass; ?>" style="width:15%">
			<?php if ($etape>1): ?>
				<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'first_step'))); ?>">
					<img src="/common/modules/sharefile/img/icon_dossier_etape.png" style="border:0px;">
				</a>
			<?php else: ?>
				<img src="/common/modules/sharefile/img/icon_dossier_etape.png" style="border:0px;">
			<?php endif; ?>
		</td>
		<?php if ($etape>=2): ?>
			<td class="$bgclass" style="width:5%">
			<?php if ($etape==2): ?>
				<img src="/common/modules/sharefile/img/puce_etape2.png">
			<?php else: ?>
				<img src="/common/modules/sharefile/img/puce_etape2.png">
			<?php endif; ?>
			</td>
			<td class="$bgclass" style="width:15%">
				<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'second_step'))); ?>">
					<img src="/common/modules/sharefile/img/icon_utilisateur_etape.png" style="border:0px;">
				</a>
			</td>

		<?php else: ?>
			<td style="width:30%">&nbsp;</td>
		<?php endif; ?>

		<?php if ($etape==3) : ?>
			<td class="$bgclass" style="width:5%">
				<img src="/common/modules/sharefile/img/puce_etape3.png">
			</td>
			<td class="$bgclass" style="width:15%">
				<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'third_step'))); ?>">
					<img src="/common/modules/sharefile/img/icon_upload.png" style="border:0px;">
				</a>
			</td>
		<?php else: ?>
			<td style="width:30%">&nbsp;</td>
		<?php endif; ?>
	</tr>
</table>
</div>
