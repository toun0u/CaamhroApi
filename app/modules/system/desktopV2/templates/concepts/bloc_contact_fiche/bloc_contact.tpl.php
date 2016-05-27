<div class="bloc_contact">
    <div class="title_bloc_contact"><h1>Contacts / Companies</h1></div>
    <div class="bloc_zone_search_contact">
        <div class="bloc_searchform_contact">
            <form action="#" method="post" name="formsearch" id="bloc_formsearch_contact">
                <?
                    // Sécurisation du formulaire par token
                    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                    $token = new FormToken\TokenField;
                    $token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
                    $token->field("button_search_y");
                    $token->field("edibox_search");
                    $tokenHTML = $token->generate();
                    echo $tokenHTML;
                ?>
                <span>
                    <input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
                    <input type="text" name="editbox_search" class="editbox_search" id="editbox_search_contact" maxlength="80" value="<?php echo $_SESSION['cste']['LOOKING_FOR_A_CONTACT_OR_A_COMPANY']; ?> ?" onfocus="Javascript:this.value='';" onblur="Javascript:if (this.value=='')this.value='<?php echo $_SESSION['cste']['LOOKING_FOR_A_CONTACT_OR_A_COMPANY']; ?>?';">
                    <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left">
                </span>
            </form>
        </div>
    </div>
    <div class="bloc_filtre_contact">
        <table cellspacing="10" cellpadding="0">
            <tbody>
                <tr>
                    <td style="color:#df1d31">
                        <span><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></span>
                    </td>
                    <td>
                        <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png" style="float:left"><span>Companies</span>
                    </td>
                    <td>
                        <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human_picto.png" style="float:left"><span>Contacts</span>
                    </td>
                    <td class="filter">
                        <span><?php echo $_SESSION['cste']['_FORMS_FILTER']; ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="cadre_bloc_contact_fiche">
        {include file="concepts/bloc_contact_fiche/bloc_contact_fiche.tpl"}
    </div>
    <div class="cadre_bloc_contact_bas">
        <table class="bloc_contact_bas" cellspacing="10" cellpadding="0">
            <tbody>
                <tr>
                    <td style="color:#df1d31">
                        <span>133 <?php echo $_SESSION['cste']['RELATIONSHIPS']; ?></span>
                    </td>
                    <td class="add_contact">
                        <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" style="float:left;"><span><?php echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT']; ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
