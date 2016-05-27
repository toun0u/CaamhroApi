<?php
ob_start();
$nav = 'Mon Panier &raquo; Enregistrer';

$type = _CATA_PANIER_TYPE_LIST_CLASSIQUE;
$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true, false, $type);

$err = dims_load_securvalue('err', dims_const::_DIMS_CHAR_INPUT, true, true, false);

if($type == _CATA_PANIER_TYPE_LIST_CLASSIQUE) {
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
            <td colspan="3">
                <?php echo _DESC_PANIERSTYPES; ?>
            </td>
        </tr>
        <?php
        if(!empty($err)) {
            if($err == 'nolabel') {
                ?>
                <tr>
                    <td colspan="3">
                        <div class="error">Vous devez donner un libellé à votre panier type</div>
                    </td>
                </tr>
                <?php
            }
            if($err == 'nocart') {
                ?>
                <tr>
                    <td colspan="3">
                        <div class="error">Vous devez sélectionner votre panier type</div>
                    </td>
                </tr>
                <?php
            }
        }
        ?>

        <form name="nompanier" method="post">
        <input type="hidden" name="op" value="enregistrer_panier_fin" />
        <input type="hidden" name="panier_type" value="<?php echo _CATA_PANIER_TYPE_LIST_CLASSIQUE; ?>" />
        <input type="hidden" name="action" value="create_new" />
        <tr>
            <td><? echo _LABEL_SAVENEWPANIERTYPE; ?> :</td>
            <td><input type="text" name="panier_libelle" width="30" class="WebText"></td>
            <td><? echo catalogue_makebutton('Valider',"document.nompanier.submit()",'*'); ?></td>
        </tr>
        </form>

        <?php
        $rs = $db->query("SELECT * FROM dims_mod_cata_panierstypes WHERE id_user = {$_SESSION['dims']['userid']} AND type = "._CATA_PANIER_TYPE_LIST_CLASSIQUE." ORDER BY libelle");
        if ($db->numrows($rs)) {
            ?>
            <form name="panier" method="post">
            <input type="hidden" name="op" value="enregistrer_panier_fin" />
            <input type="hidden" name="panier_type" value="<?php echo _CATA_PANIER_TYPE_LIST_CLASSIQUE; ?>" />
            <input type="hidden" name="action" value="add_to_existing" />
            <tr>
                <td><?php echo _LABEL_SAVEADDPANIERTYPE; ?> :</td>
                <td>
                    <select name="panier_id" width="30" class="WebText">
                        <option value=""></option>
                        <?php
                        while ($row = $db->fetchrow($rs)) {
                            ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['libelle']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td><?php echo catalogue_makebutton('Valider', "document.panier.submit()", '*'); ?></td>
            </tr>
            </form>
            <?
        }
        ?>
        </table>
    </td>
</tr>
</table>

<?php
}
elseif($type == _CATA_PANIER_TYPE_LIST_SCOLAIRE) {
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
            <td colspan="3">
                <?php echo _DESC_LISTSCOLAIRE; ?>
            </td>
        </tr>
        <?php
        if(!empty($err)) {
            if($err == 'nolabel') {
                ?>
                <tr>
                    <td colspan="3">
                        <div class="error">Vous devez donner un libellé à votre liste scolaire</div>
                    </td>
                </tr>
                <?php
            }
            if($err == 'nocart') {
                ?>
                <tr>
                    <td colspan="3">
                        <div class="error">Vous devez sélectionner votre liste scolaire</div>
                    </td>
                </tr>
                <?php
            }
        }
        ?>

        <form name="nompanier" method="post">
        <input type="hidden" name="op" value="enregistrer_panier_fin" />
        <input type="hidden" name="panier_type" value="<?php echo _CATA_PANIER_TYPE_LIST_SCOLAIRE; ?>" />
        <input type="hidden" name="action" value="create_new" />
        <tr>
            <td><? echo _LABEL_SAVENEWSCHOOLLIST; ?> :</td>
            <td><input type="text" name="panier_libelle" width="30" class="WebText"></td>
            <td><? echo catalogue_makebutton('Valider',"document.nompanier.submit()",'*'); ?></td>
        </tr>
        </form>

        <?php
        $rs = $db->query("SELECT * FROM dims_mod_cata_panierstypes WHERE id_user = {$_SESSION['dims']['userid']} AND type = "._CATA_PANIER_TYPE_LIST_SCOLAIRE." ORDER BY libelle");
        if ($db->numrows($rs)) {
            ?>
            <form name="panier" method="post">
            <input type="hidden" name="op" value="enregistrer_panier_fin" />
            <input type="hidden" name="panier_type" value="<?php echo _CATA_PANIER_TYPE_LIST_SCOLAIRE; ?>" />
            <input type="hidden" name="action" value="add_to_existing" />
            <tr>
                <td><?php echo _LABEL_SAVEADDSCHOOLLIST; ?> :</td>
                <td>
                    <select name="panier_id" width="30" class="WebText">
                        <option value=""></option>
                        <?php
                        while ($row = $db->fetchrow($rs)) {
                            ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['libelle']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td><?php echo catalogue_makebutton('Valider', "document.panier.submit()", '*'); ?></td>
            </tr>
            </form>
            <?
        }
        ?>
        </table>
    </td>
</tr>
</table>

<?php
}
$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

$page['TITLE'] = 'Ajouter un panier type';
$page['META_DESCRIPTION'] = 'Ajouter un panier type';
$page['META_KEYWORDS'] = 'Panier type, ajouter, créer';
$page['CONTENT'] = '';

ob_end_clean();
