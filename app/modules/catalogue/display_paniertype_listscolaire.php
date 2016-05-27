<?php
if ($_SESSION['dims']['connected']) {
    ob_start();
    $nav = 'Mon Panier &raquo; Liste scolaire';

    $id_paniertype = dims_load_securvalue('id_paniertype', dims_const::_DIMS_NUM_INPUT, true, true, true);

    $paniertype = new paniertype();
    $paniertype->open($id_paniertype);
    ?>

// include DIMS_APP_PATH.'/modules/catalogue/include/class_paniertype.php';
$paniertype = new paniertype();
$paniertype->open($id_paniertype);

    ?>

    <table width="100%" cellpadding="0" cellspacing="0">
    <tr bgcolor="#eeeeee">
        <td class="WebNavTitle">&nbsp;<?php echo $nav; ?></td>
    </tr>
    <tr bgcolor="#dddddd" height="1"><td></td></tr>
    <tr>
        <td>
            <table cellpadding="6" cellspacing="0">
            <tr>
                <td colspan="2">
                    <?php echo _DESC_PANIERSTYPES_LISTSCOLAIRE; ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong><?php echo $paniertype->fields['code_liste']; ?></strong>
                </td>
            </tr>
            <tr>
                <td>
					<a href="?op=home">
						<img src="<?= $template_web_path; ?>gfx/bt_retour_catalogue.png" />
					</a>
				</td>
                <td>
					<a href="?op=panierstype">
						<img src="<?= $template_web_path; ?>gfx/bt_retour_panier_types.png" />
					</a>
				</td>
            </tr>
            </table>
        </td>
    </tr>
    </table>

    <?php
    $smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

    $page['TITLE'] = 'Liste scolaire';
    $page['META_DESCRIPTION'] = 'Votre liste scolaire';
    $page['META_KEYWORDS'] = 'Panier type, liste scolaire, ajouter, créer';
    $page['CONTENT'] = '';

    ob_end_clean();
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
