<div class="bloc_last_participations">
    <div class="title_bloc_last_participations"><h1>Last participations</h1></div>
    <div class="bloc_zone_search_last_participations">
        <div class="bloc_searchform_last_participations">
            <form action="#" method="post" name="formsearch" id="bloc_formsearch_last_participations">
                <?
                    // Sécurisation du formulaire par token
                    require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                    $token = new FormToken\TokenField;
                    $token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
                    $token->field("button_search_y");
                    $token->field("editbox_search");
                    $tokenHTML = $token->generate();
                    echo $tokenHTML;
                ?>
                <span>
                    <input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
                    <input type="text" name="editbox_search" id="bloc_editbox_search_last_participations" class="bloc_editbox_search" maxlength="80" value="Looking for an event or an activity ?" onfocus="Javascript:this.value='';" onblur="Javascript:if (this.value=='')this.value='Looking for an event or an activity ?';">
                    <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left">
                </span>
            </form>
        </div>
    </div>
    <div class="cadre_bloc_last_participations">
        {include file="concepts/bloc_last_participations/fiche_bloc_last_participations.tpl"}
    </div>
    <div class="cadre_bloc_last_participations_bas">
        <table class="bloc_last_participations_bas">
            <tbody>
                <tr>
                    <td style="color:#df1d31">
                        <span>See all the 76 events / activities...</span>
                    </td>
                    <td class="add_participation">
                        <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" style="float:left;"><span>New participation</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
