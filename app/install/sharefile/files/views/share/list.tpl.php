<?php
$tab_histo = $this->get('tab_histo');
$tab_share = $this->get('tab_share');
?>
<table style="font-size:12px;" width="100%" cellpadding="0" cellspacing="5" border="0" class="filtre_partage">
	<tr style="background-color:#EDEDED;font-size:12px;font-weight:bold;">
		<td>Nom de partage</td>
		<td>Date d'envoi</td>
		<td>Date de fin</td>
		<td>Nb fichiers</td>
		<td>Nb consult.</td>
		<td align="center">Stats</td>
		<td align="center">Voir</td>
		<td align="center">Dupliquer</td>
		<td align="center">Sup.</td>
	</tr>
	<?php
	if (!empty($tab_share)) {
		$ind=1;
		foreach($tab_share as $id_share => $share) {
			$nb_histo = 0;
			if (isset($tab_histo[$id_share]['cpte'])) $nb_histo = count($tab_histo[$id_share]['cpte']);

			// construction de la liste
			$datenvoi=dims_timestamp2local($share['timestp_create']);
			$datenvoi=$datenvoi['date'];

			if ($share['timestp_finished']==0)
				$datefin="-";
			else {
				$datefin=dims_timestamp2local($share['timestp_finished']);
				$datefin=$datefin['date'];
			}

			$nbconsult=0;

			if (isset($tab_histo[$share['id']])) $nbconsult=$tab_histo[$share['id']];
			$ind=($ind==1) ? $ind=2 : $ind=1;

			?>
			<tr class="trl<?= $ind; ?>">
				<td>
					<a title="<?= $share['description']; ?>" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'view', 'id_share' => $id_share))); ?>">
						<?= $share['label']; ?>
					</a>
				</td>
				<td><?= $datenvoi; ?></td>
				<td><?= $datefin; ?></td>
				<td><?= $share['cptefile']; ?></td>
				<td><?= $nbconsult; ?></td>
				<td align="center">
					<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'stats', 'id_share' => $id_share))); ?>" alt="Statistiques">
						<img title="Statistiques" src="/modules/sharefile/img/icon_stats.png" style="border:0px;">
					</a>
				</td>
				<td align="center">
					<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'view', 'id_share' => $id_share))); ?>" alt="Voir les fichiers">
						<img title="Voir les fichiers" src="/modules/sharefile/img/voir.png" style="border:0px;">
					</a>
				</td>
				<td align="center">
					<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'duplicate', 'id_share' => $id_share))); ?>" alt="Dupliquer l'envoi">
						<img title="Dupliquer l'envoi" src="/modules/sharefile/img/icon_dupliquer.png" style="border:0px;">
					</a>
				</td>
				<td align="center">
					<a href="javascript:dims_confirmlink('<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'delete', 'id_share' => $id_share))); ?>','Souhaitez vous supprimer le partage \'<? echo addslashes($share['label']); ?>\' ?');">
						<img title="Supprimer" style="border:0;" src="/modules/sharefile/img/poubelle.png">
					</a>
				</td>

			</tr>
			<?php
		}
	}
	else {
			?>
			<tr style="background-color: #B0DB78;">
				<td colspan="9" style="text-align:center;color:white;font-weight:bold;">
					Pas d'envoi en cours
				</td>
			</tr>
			<?php
	}
	?>
</table>
