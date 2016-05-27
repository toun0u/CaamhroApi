<?php

$id_dmd = dims_load_securvalue('id_dmd', dims_const::_DIMS_NUM_INPUT, true, true, true);

$inscription = new newsletter_inscription();

$inscription->open($id_dmd);

//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'].' : '.$newsletter->fields['label'].' '.$inscription->fields['nom'].' '.$inscription->fields['prenom']);
echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\''.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd\'');

if(!$inscription->new) {
    $class = "trl1";
    ?>
<p style="clear:both;">
    <?php echo $_SESSION['cste']['_DIMS_NEWSLETTER_TEXT_ATTACH']; ?>
</p>
<table width="25%" cellpadding="0" cellspacing="0" style="float: left; margin-right: 20px;">
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['nom']))
                echo $inscription->fields['nom'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_FIRSTNAME']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['prenom']))
                echo $inscription->fields['prenom'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_PHONE']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['tel']))
                echo $inscription->fields['tel'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['email']))
                echo $inscription->fields['email'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['adresse']))
                echo $inscription->fields['adresse'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['ville']))
                echo $inscription->fields['ville'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['cp']))
                echo $inscription->fields['cp'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['pays']))
                echo $inscription->fields['pays'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['entreprise']))
                echo $inscription->fields['entreprise'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>
        </td>
        <td>
            <?php
            if(!empty($inscription->fields['fonction']))
                echo $inscription->fields['fonction'];
            else
                echo 'n/a';
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_DATE_REGISTRATION']; ?>
        </td>
        <td>
            <?php
                $date_insc = dims_timestamp2local($inscription->fields['date_inscription']);

                echo $date_insc['date'];
            ?>
        </td>
    </tr>
</table>
<form name="attach_inscr" method="post" action="<?php echo $scriptenv; ?>?subaction=<?php echo _DIMS_NEWSLETTER_INSCR; ?>&list_insc=attach_contact">
    <?
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("id_dmd",$id_dmd);
        $token->field("id_contact");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
    ?>
    <input type="hidden" name="id_dmd" value="<?php echo $id_dmd; ?>" />
    <table cellpadding="0" cellspacing="0" width="60%">
        <tr>
            <td colspan="3">
                <?php echo $_SESSION['cste']['_DIMS_LINK_CONTACT'] ?>
            </td>
        </tr>
        <?php
        ?>
        <tr class="trl2">
            <td>
                <?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
            </td>
            <td>
                <?php echo $_SESSION['cste']['_FIRSTNAME']; ?>
            </td>
            <td>
            </td>
        </tr>
        <?php
        $sql = 'SELECT id, lastname, firstname FROM dims_mod_business_contact';

        $ress = $db->query($sql);

        $class = 'trl1';
        while($result = $db->fetchrow($ress)) {

            $lev_nom = levenshtein(strtoupper($result['lastname']), strtoupper($inscription->fields['nom']));
            $coef_nom = $lev_nom - (ceil(strlen($inscription->fields['nom'])/4));

            $lev_pre = levenshtein(strtoupper($result['firstname']), strtoupper($inscription->fields['prenom']));
            $coef_pre = $lev_pre - (ceil(strlen($inscription->fields['prenom'])/4));

            $coef_tot = $coef_nom + $coef_pre;


			$lev_nom2 = levenshtein(strtoupper($result['firstname']), strtoupper($inscription->fields['nom']));
            $coef_nom2 = $lev_nom2 - (ceil(strlen($inscription->fields['nom'])/4));

            $lev_pre2 = levenshtein(strtoupper($result['lastname']), strtoupper($inscription->fields['prenom']));
            $coef_pre2 = $lev_pre2 - (ceil(strlen($inscription->fields['prenom'])/4));

            $coef_tot2 = $coef_nom2 + $coef_pre2;

            if(($coef_nom<=1 && $coef_tot < 2) || ($coef_nom2<=1 && $coef_tot2 < 2)) {
                ?>
                <tr class="<?php echo $class; ?>">
                    <td>
                        <?php echo $result['lastname']; ?>
                    </td>
                    <td>
                        <?php echo $result['firstname']; ?>
                    </td>
                    <td>
                        <input type="radio" name="id_contact" value="<?php echo $result['id']; ?>" />
                    </td>
                </tr>
                <?php
                $class = ($class == 'trl1')? 'trl2' : 'trl1';
            }
        }
        ?>
        <tr class="<?php echo $class; ?>">
            <td colspan="2">
                <?php echo $_SESSION['cste']['_DIMS_NEWSLETTER_NEW_CONTACT'] ?>
            </td>
            <td>
                <input type="radio" name="id_contact" value="-1" />
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <?php
                echo dims_create_button($_DIMS['cste']['_DIMS_VALID'], './common/img/checkdo.png', 'javascript: attach_inscr.submit();');
                ?>
            </td>
        </tr>
    </table>
</form>


    <?php
}
else {
    ?>
<table width="100%" cellpadding="0" cellspacing="0" style="clear:both;">
    <tr>
        <td>
            <?php echo $_SESSION['cste']['_DIMS_LABEL_NO_REGISTRATION']; ?>
        </td>
    </tr>
</table>
    <?php
}
?>
<p style="clear:both">
    <?php
    echo dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\''.$scriptenv.'?subaction='._DIMS_NEWSLETTER_INSCR.'&list_insc=list_dmd\'');
    ?>
</p>
<?php
//echo $skin->close_simplebloc();

?>
