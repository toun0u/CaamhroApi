<?php
$share 				= $this->get('share');
$sharefile_param 	= $this->get('sharefile_param');
$arrayusers 		= $this->get('users');
$arraycontacts 		= $this->get('contacts');
$tab_histo 			= $this->get('tab_histo');

?>
<form name="form_etape1" method="post" action="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'stats'))); ?>">
	<table width="100%" cellpadding="0" cellspacing="2" border="0">
		<tr>
			<td colspan="6" style="font-size:14px;font-weight:bold;text-align:center;height:30px;">
				Nom du partage : <?= $share['label']; ?>
			</td>
		</tr>
		<tr style="background-color:#EDEDED;font-size:14px;font-weight:bold;">
			<td width="25%">Nom</td>
			<td width="25%">Email</td>
			<td width="10%">Derni&egrave;re consult.</td>
			<td width="10%">Nb consult.</td>
			<td width="20%">Téléchargements possibles avant limite (<?= $sharefile_param['nbdownload']; ?>)</td>
			<td width="10%">Rappel</td>
		</tr>
		<?php
		if (!empty($arrayusers)) {
			foreach($arrayusers as $k => $user) {
				if (isset($tab_histo['users'][$user['id']])) {
					$nb_histo = $tab_histo['users'][$user['id']]['cpte'];
					$datlect=dims_timestamp2local($tab_histo['users'][$user['id']]['timestp_create']);
				}
				else {
					$nb_histo = 0;
					$datlect['date']="";
				}
				?>

				<tr>
					<td>
						<?= $user['firstname']; ?>
						<?= $user['lastname'] ?>
					</td>
					<td>
						<?= $user['email'] ?>
					</td>
					<td>
						<?= $datlect['date'] ?>
					</td>
					<td>
						<?= $nb_histo; ?>
					</td>
					<td>
						<?php if ($user['view']>=$sharefile_param['nbdownload']): ?>
							<img src="./common/modules/system/img/ico_point_red.gif">&nbsp;
						<?php else: ?>
							<img src="./common/modules/system/img/ico_point_green.gif">&nbsp;
						<?php endif; ?>
						<?= ($sharefile_param['nbdownload']-$user['view']); ?>
					</td>
					<td>
						<a title="Relancer" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'unlock_account', 'id_shareuser' => $user['shareuser_id']))); ?>">
							<img border="0" src="./common/modules/system/img/unclose_16.png">
						</a>
					</td>
				</tr>
				<?php
			}
		}
		?>

		<?php
		if (!empty($arraycontacts)) {
			foreach($arraycontacts as $k => $user) {
				if (isset($tab_histo['contacts'][$user['id']])) {
					$nb_histo = $tab_histo['contacts'][$user['id']]['cpte'];
					$datlect=dims_timestamp2local($tab_histo['contacts'][$user['id']]['timestp_create']);
				}
				else {
					$nb_histo = 0;
					$datlect['date']="";
				}
				?>
				<tr>
					<td>
						<?= $user['firstname']; ?>
						<?= $user['lastname']; ?>
					</td>
					<td>
						<?= $user['email']; ?>
					</td>
					<td>
						<?= $datlect['date']; ?>
					</td>
					<td>
						<?= $nb_histo; ?>
					</td>
					<td>
						<?php if ($user['view']>=$sharefile_param['nbdownload']): ?>
							<img src="./common/modules/system/img/ico_point_red.gif">
						<?php else : ?>
							<img src="./common/modules/system/img/ico_point_green.gif">
						<?php endif; ?>
						<?= ($sharefile_param['nbdownload']-$user['view']); ?>
					</td>
					<td>
						<a title="Relancer" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'unlock_account', 'id_shareuser' => $user['shareuser_id']))) ;?>">
							<img border="0" src="./common/modules/system/img/unclose_16.png">
						</a>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
</form>
