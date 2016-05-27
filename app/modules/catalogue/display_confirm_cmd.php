<?php
ob_start();

$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);
$msg = dims_load_securvalue('msg', dims_const::_DIMS_NUM_INPUT, true, false);

if (!empty($id_cmd) && is_numeric($id_cmd)) {
	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
	$commande = new commande();
	$commande->open($id_cmd);
}

if (!empty($msg) && $commande->fields['exceptionnelle'] && $commande->fields['hors_cata']) {
	$msg = 8;
}

if (!isset($msg) || $msg == "") $msg = 3;

switch ($msg) {
	case 1:
		$lemessage = str_replace("<SERVICE_RESP>","{$_SESSION['catalogue']['service_firstname']} {$_SESSION['catalogue']['service_lastname']}",$msgs_confirm[$msg]);
		break;
	case 2:
		$lemessage = str_replace("<PURCHASE_RESP>","{$_SESSION['catalogue']['achat_firstname']} {$_SESSION['catalogue']['achat_lastname']}",$msgs_confirm[$msg]);
		break;
	default:
		$lemessage = $msgs_confirm[$msg];
		break;
}
?>

<table width="100%" cellpadding="0" cellspacing="0">
	<tr bgcolor="#dddddd" height="1"><td></td></tr>
	<tr bgcolor="#eeeeee">
		<td width="100%" align="center">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center">
					<table>
					<tr>
						<td class="WebNavTitle" rowspan="2"><img src="./common/modules/catalogue/img/front_24.png" alt="Etape 1" border="0"></td>
						<td class="WebNavTitle">Etape 1</td>
					</tr>
					<tr>
						<td>&nbsp;R&eacute;capitulatif</td>
					</tr>
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td class="WebNavTitle" rowspan="2"><img src="./common/modules/catalogue/img/front_24.png" alt="Etape 2" border="0"></td>
						<td class="WebNavTitle"><a class="WebNavTitle">Etape 2</a></td>
					</tr>
					<tr>
						<td>&nbsp;Livraison / Facturation</td>
					</tr>
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td class="WebNavTitle" rowspan="2"><img src="./common/modules/catalogue/img/front_24.png" alt="Etape 3" border="0"></td>
						<td class="WebNavTitle">Etape 3</td>
					</tr>
					<tr>
						<td>&nbsp;Commentaire</td>
					</tr>
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td class="WebNavTitle" rowspan="2"><img src="./common/modules/catalogue/img/front_24.png" alt="Etape 4" border="0"></td>
						<td class="WebNavTitle">Etape 4</td>
					</tr>
					<?php if (!(isset($_SESSION['catalogue']['params']['noetape4']) && isset($_SESSION['catalogue']['params']['paiem']))): ?>
					<tr>
						<td>&nbsp;Mode de paiement</td>
					</tr>
					</table>
				</td>
				<td align="center">
					<table>
					<tr>
						<td class="WebNavTitle" rowspan="2"><img src="./common/modules/catalogue/img/front_24.png" alt="Etape 5" border="0"></td>
						<td class="WebNavTitle">Etape 5</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td>&nbsp;Confirmation</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="#dddddd" height="1"><td></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<?php if (isset($_SESSION['catalogue']['params']['noetape4']) && isset($_SESSION['catalogue']['params']['paiem'])): ?>
				<td class="WebNavTitle">&nbsp;&nbsp;Etape 4 : Confirmation de votre commande</td>
				<?php else: ?>
				<td class="WebNavTitle">&nbsp;&nbsp;Etape 5 : Confirmation de votre commande</td>
				<?php endif; ?>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="772" align="center" valign="top">
			<table cellpadding="6" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align="center">
									<div class="blocJaune">
										<font style='font-size:16px;font-weight:bold'><br><?=$lemessage;?><br><br></font>
									</div>
								</td>
							</tr>
							<tr>
								<td align="center">
									<form name="form" action="/index.php" method="Post">
									<input type="hidden" name="op" value="imprimer_commande">
									<input type="hidden" name="id_cmd" value="<?=$id_cmd;?>">

									<br/>
									<table>
									<tr>
										<td colspan="2"><?=catalogue_makegfxbutton('Retour aux commandes en attente de validation', '<img src="'.$template_path.'/gfx/continuer.png">', "document.location.href='/index.php?op=commandes';", '*');?></td>
									</tr>
									<tr>
										<?php if ( !($commande->fields['exceptionnelle'] && $commande->fields['hors_cata']) ): ?>
											<td><?=catalogue_makegfxbutton('Imprimer la commande','<img src="'.$template_path.'/gfx/pdf.gif">',"document.form.submit();",'*');?></td>
										<?php endif; ?>

										<?php if ($_SESSION['session_adminlevel'] != _DIMS_ID_LEVEL_SERVICERESP): ?>
											<?php if ($commande->fields['id']): ?>
												<?php if ($commande->fields['etat'] == 'validee'): ?>
													<td><?=catalogue_makegfxbutton('Visualiser la commande','<img src="./common/modules/catalogue/img/loupe_16.png">',"document.location.href='/index.php?op=historique&date=".substr($commande->fields['date_validation'], 0, 6)."&id_cmd=".$commande->fields['id']."';",'*');?></td>
												<?php else: ?>
													<td><?=catalogue_makegfxbutton('Visualiser la commande','<img src="./common/modules/catalogue/img/loupe_16.png">',"document.location.href='/index.php?op=commandes&id_cmd=".$commande->fields['id']."';",'*');?></td>
												<?php endif; ?>
											<?php endif; ?>
										<?php endif; ?>

									</tr>
									</table>

									</form>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php
$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

$page['TITLE'] = 'Commander';
$page['META_DESCRIPTION'] = 'Finaliser votre commande';
$page['META_KEYWORDS'] = 'commande, rapide';
$page['CONTENT'] = '';

ob_end_clean();
