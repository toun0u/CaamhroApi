<?php

/**
 * Description of dims_view_historique_factory
 *
 * @author Aurélien Tisserand
 * @copyright Wave Software / Netlor 2011
 *
 */
class dims_view_historique_factory {

    public static function build_view_history(array $tab_history_action){
        if(!(get_called_class() instanceof dims_view_historique_factory)){
            get_called_class()->build_view_history($tab_history_action);
        }
    }

    /**
     * TODO Enlever Dims_action_informaton
     * @global type $_DIMS
     * @param array $liste_commentaires
     */
    public static function build_view_comment(array $liste_commentaires){
        global $_DIMS ; //TODO A ENLEVER
        if(!empty($liste_commentaires)){
            foreach ($liste_commentaires as $commentaire) {
                if($commentaire instanceof dims_historique){
                    ?>
                    <table style="width: 350px; margin-left: 40px;border-top: solid 1px; border-color: #DAEF48">
                        <tr>
                            <td>
                                <div>
                                    <?echo $commentaire->getUserFirstname();?>
                                    <?echo " ";?>
                                    <?echo $commentaire->getUserLastname();?>
                                    <?echo " ".$_DIMS['cste']['_DIMS_ACTION_HAS_WRITTEN']." : ";?>
                                </div>
                                <div style="margin-top:5px; text-align: center;">
                                    <?echo $commentaire->getActionCommentaire();?>
                                </div>
                                <div style="margin-top:5px;">
                                    <?echo dims_nicetime($commentaire->getActionTimestpModify());?>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <?
                }
            }
        }
    }

    public static function build_view_add_comment($id_action){
        global $_DIMS ;
        if($id_action != ""){
            $url_return = dims::getInstance()->getLastVisitedUrl();
            ?>
            <div id="popup_user" style="width:400px; height: 100%;">
                <div class="bann">
                    <div style="margin-left:20px;float:left;"><?echo "Ajouter un commentaire" ; ?></div>
                    <img style="float:right; margin-right: 2px; margin-top:2px;cursor:pointer;" onclick="javascript:dims_hidepopup();" src="./common/modules/immo/gfx/close.png" />
                </div>
                <div style="width:100%;">
                    <form action="" method="post" name="form_action">
                        <?
                            // Sécurisation du formulaire par token
                            require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
                            $token = new FormToken\TokenField;
                            $token->field("dims_op",    "action_save");
                            $token->field("url_return", $url_return);
                            $token->field("action_content");
                            $tokenHTML = $token->generate();
                            echo $tokenHTML;
                        ?>
                        <input type="hidden" name="dims_op" value="action_save"/>
                        <input type="hidden" name="url_return" value="<?echo $url_return?>"/>
                        <div style="padding:2px 4px;">
                            <textarea class="text" style="width:99%;" tabindex="1" rows="5" id="action_content" name="action_content"></textarea>
                        </div>
                        <div style="padding:2px 4px;text-align:right;">
                            <input type="button" onclick="javascript:dims_hidepopup();" value="<?php echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" class="flatbutton"/>
                            <input type="submit" class="flatbutton" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
                        </div>
                    </form>
                </div>
            </div>
            <?
        }
    }
}



?>
