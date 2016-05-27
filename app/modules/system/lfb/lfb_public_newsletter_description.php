<?php

//creation de la liste des personnes pouvant etre rattachees
$sel_resp = '';
$sel = '';//utilise pour pre-selection dans le cas d'une modif
$sel_resp .= '<select name="news_id_user_responsible">';

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$users = $workspace->getusers();

$sel_resp .= '<option value="0">--</option>';

foreach($users as $userid => $user){

    if(isset($id_news) && $id_news != '' && $userid == $inf_news->fields['id_user_responsible']) {
        $sel .= 'selected="selected"';
    }
    else {
        $sel ='';
    }
    $sel_resp .= '<option value="'.$userid.'" '.$sel.'>'.$user['firstname'].' '.$user['lastname'].'</option>';
}
$sel_resp .= '</select>';

?>
<div style="width:100%;float:left;">
<form name="newsletter_rub" id="newsletter_rub" method="POST" action="admin.php?dims_mainmenu=<? echo dims_const::_DIMS_MENU_NEWSLETTER; ?>&cat=0&dims_desktop=block&dims_action=public&action=<? echo _NEWSLETTER_ACTION_SAVE; ?>">
    <?php
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("id_news",$id_news);
        $token->field("fck_news_label");
        $token->field("fck_news_descriptif");
        $token->field("news_etat");
        $token->field("news_id_user_responsible");
        $tokenHTML = $token->generate();
        echo $tokenHTML;>
        if(isset($id_news) && $id_news != '') {
    ?>
        <input type="hidden" name="id_news" value="<?php echo $id_news; ?>"/>
    <?php
        }
    ?>
        <table width="100%" cellpadding="0" cellspacing="0" style="border:#3B567E 1px solid;">
            <tr>
                <td width="50%" style="padding-top:25px;">
                    <table width="100%" cellpadding="0" cellspacing="1">
                        <tr>
                            <td align="right">
                                <?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?>&nbsp;
                            </td>
                            <td>
                                <input type="text" id="fck_news_label" name="fck_news_label" <? if(isset($id_news) && $id_news != '') echo 'value="'.html_entity_decode($inf_news->fields['label']).'"'; ?> />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTIF']; ?>&nbsp;
                            </td>
                            <td>
                                <?
                                $content = '';
                                if(isset($id_news) && $id_news != '' && $inf_news->fields['descriptif'] != '') $content= $inf_news->fields['descriptif'];
                                else $content="";
                                dims_fckeditor("news_descriptif",$content,"800","350");
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td align="right">
                                <?php echo $_DIMS['cste']['_DIMS_LABEL_RESPONSIBLE']; ?>&nbsp;
                            </td>
                            <td>
                                <?php echo $sel_resp; ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <?php echo $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_ACTIVE']; ?>&nbsp;
                            </td>
                            <td align="left">
                                <?php echo $_DIMS['cste']['_DIMS_YES']; ?><input type="radio" id="news_etat" name="news_etat" value="1" <?php if(isset($id_news) && $id_news != '' && $inf_news->fields['etat'] == 1) echo 'checked="checked"'; ?>/>
                                <?php echo $_DIMS['cste']['_DIMS_NO']; ?><input type="radio" id="news_etat" name="news_etat" value="0" <?php if(isset($id_news) && $id_news != '' && $inf_news->fields['etat'] == 0) echo 'checked="checked"'; ?>/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                   <?php
                        echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_rub.submit();','','float:right;padding-right:10px;');
                   ?>
                </td>

            </tr>
        </table>
    </form>
</div>
