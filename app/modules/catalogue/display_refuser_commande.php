<?php
	ob_start();
if ($_SESSION['dims']['connected']) {

	$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);

	$nav = "<a href=\"$scriptenv&op=commandes\" class=\"WebNavTitle\">". _LABEL_COMMANDESENCOURS ."</a>";

	$commandes = array();
	$rs_cmd = $db->query('
		SELECT cmd.id, cmd.libelle, cmd.numcmd, cmd.total_ht
		FROM dims_mod_vpc_cmd as cmd
		LEFT JOIN dims_mod_vpc_cmd_detail as cmd_det
		ON cmd_det.id_cmd = cmd.id
		WHERE cmd.id = '.$id_cmd);
	$row = $db->fetchrow($rs_cmd); {
		$commandes[] = $row;
		if (!empty($id_cmd) && $id_cmd == $row['id']) $nav .= "&nbsp;»&nbsp;<a href=\"$scriptenv&op=commandes&id_cmd=$id_cmd\" class=\"WebNavTitle\">{$row['libelle']} ($id_cmd)</a>&nbsp;»&nbsp;Refuser";
	}
	?>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr bgcolor="#eeeeee">
	  <td colspan="3" class="WebNavTitle">&nbsp;<? echo $nav; ?></td>
	</tr>
	<tr bgcolor="#dddddd" height="1"><td colspan="3"></td></tr>
	<tr>
		<td>
			<form name="refus_form" action="<? echo $dims->getScriptEnv(); ?>" method="Post">
			<input type="Hidden" name="op" value="refuser_commande_fin">
			<input type="Hidden" name="id_cmd" value="<? echo $row['id']; ?>">

			<table cellpadding="6" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr><td colspan="2" height="1" bgcolor="#dddddd"></td></tr>
							<tr>
								<td colspan="2" bgcolor="#f8f8f8"><b>Motif du refus :</b></td>
							</tr>
							<tr><td colspan="2" height="1" bgcolor="#dddddd"></td></tr>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<textarea class="WebText" name="motif" cols="50" rows="3"></textarea>
											</td>
											<td width="10"></td>
											<td><?=catalogue_makegfxbutton('Refuser', '<img src="./common/modules/catalogue/img/refuser.gif">', 'document.refus_form.submit();', "*", false, 'negative');?></td>
											<td><?=catalogue_makegfxbutton('Annuler', '<img src="./common/modules/catalogue/img/retour.gif">', "document.location.href='".$dims->getScriptEnv()."?op=commandes&id_cmd=$id_cmd#$id_cmd';", "*");?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</form>
			</table>
		</td>
	</tr>
	</table>

<div id="raccourci">
    <table width="100%" cellpadding="20" cellspacing="0">
        <tr>
            <td id="espace_raccourci">
                <a href="/index.php?op=saisierapide"><img border="0" alt="Saisie rapide" src="/modules/catalogue/img/saisie_rapide.png" /><p>&nbsp;Saisie rapide</p></a>
            </td>
            <td id="espace_raccourci">
                <a href="/index.php?op=panier"><img border="0" alt="Panier" src="/modules/catalogue/img/panier.png" /><p>&nbsp;Panier</p></a>
            </td>
            <?php if ($oCatalogue->getParams('panier_type')): ?>
            <td id="espace_raccourci">
                <a href="/index.php?op=panierstype"><img border="0" alt="Paniers types" src="/modules/catalogue/img/paniers_types.png" /><p>&nbsp;Paniers types</p></a>
            </td>
            <?php endif ?>
            <td id="espace_raccourci">
                <a href="/index.php?op=commandes"><img border="0" alt="Commandes" src="/modules/catalogue/img/commandes.png" /><p>&nbsp;Commandes en cours</p></a>
            </td>
            <td id="espace_raccourci">
                <a href="/index.php?op=historique"><img border="0" alt="Historique" src="/modules/catalogue/img/historique.png" /><p>&nbsp;Historique</p></a>
            </td>
            <td id="espace_raccourci">
                <a href="/index.php?op=factures"><img border="0" alt="Factures" src="/modules/catalogue/img/factures.png" /><p>&nbsp;Factures</p></a>
            </td>
            <td id="espace_raccourci">
                <a href="/index.php?op=infospersos"><img border="0" alt="Infos persos" src="/modules/catalogue/img/infosperso.png" /><p>&nbsp;Infos persos</p></a>
            </td>
            <td id="espace_raccourci">
                <a href="/index.php?op=promotions"><img border="0" alt="Promotions" src="/modules/catalogue/img/promotions.png" /><p>&nbsp;Promotions</p></a>
            </td>
        </tr>
    </table>
</div>

	<?php
	$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

	$page['TITLE'] = 'Vos commandes';
    $page['META_DESCRIPTION'] = 'Visualiser vos commandes';
    $page['META_KEYWORDS'] = 'commandes, produits, articles';
	$page['CONTENT'] = '';

	ob_end_clean();
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
