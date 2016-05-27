<?php
$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
$sent = dims_load_securvalue('sent',dims_const::_DIMS_NUM_INPUT,true,true);
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);
$title='';
//si on a pas d'id_news on est dans le cas d'un ajout
//si on a l'id_news, on est dans le cas d'une modif
if(isset($id_news) && $id_news != '') {
    $title = $_DIMS['cste']['_DIMS_LABEL_MODIF_NEWSLETTER'];

    $inf_news = new newsletter();
    $inf_news->open($id_news);
}
elseif($sent == 1) {
    $title = $_DIMS['cste']['_DIMS_LABEL_ADD_NEWSLETTER'];
}
else {
	$title = $_DIMS['cste']['_DIMS_LABEL_ADD_MODEL'];
}

$inf_sent = '';
if($sent == 1) {
	$inf_sent = $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_SENT'];
}
elseif($sent == 2) {
    $inf_sent = $_DIMS['cste']['_DIMS_LABEL_TEST_NEWSLETTER'];
}

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

    echo $skin->open_simplebloc($title);
?>
<div style="float:left;width:100%;">
    <form name="newsletter_rub" id="newsletter_rub" method="POST" action="admin.php?dims_mainmenu=<? echo dims_const::_DIMS_MENU_NEWSLETTER; ?>&cat=0&dims_desktop=block&dims_action=public&action=<? echo _NEWSLETTER_ACTION_SAVE; ?>">
    <?php
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("id_news",    $id_news);
        $token->field("fck_news_label");
        $token->field("news_etat");
        $token->field("news_id_user_responsible");
        $token->field("fck_news_descriptif");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
        if(isset($id_news) && $id_news != '') {
    ?>
        <input type="hidden" name="id_news" value="<?php echo $id_news; ?>"/>
    <?php
        }
    ?>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" style="padding-left:25px;padding-top:25px;">
                    <table width="100%" cellpadding="0" cellspacing="5">
                        <tr>
                            <td colspan="2" style="color:#FF0000;font-weight:bold;">
                                <?php echo $inf_sent; ?>&nbsp;
                            </td>
                        </tr>
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
                <td>
                    <?
                        if(isset($id_news) && $id_news != '') {
                    ?>
                    <table width="100%" cellpadding="0" cellspacing="5">
                        <tr>
                            <td align="left">
                                <?php echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_INSC.'&id_news='.$id_news.'">'.$_DIMS['cste']['_DIMS_NEWSLETTER_LIST_INSC'].'</a>'; ?>&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td align="left">
                                <?php echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_VIEW_DMDINSC.'&id_news='.$id_news.'">'.$_DIMS['cste']['_DIMS_NEWSLETTER_LIST_DMDINSC'].'</a>'; ?>&nbsp;
                            </td>
                        </tr>
                    </table>
                    <? } ?>
                </td>
            </tr>
            <tr>
                <td>
                   <?php
                        echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_rub.submit();','','float:right;');
                   ?>
                </td>
                <td>
                   <?php
                        echo  dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public\';', '', 'float:right;');
                   ?>
                </td>
            </tr>
<?php
    //affichage des articles rattaches
    if(isset($id_news) && $id_news != '') {

    $sql_a = '  SELECT      c.*,
                            c.id as id_env
                FROM        dims_mod_newsletter_content c
                WHERE       id_newsletter = :idnewsletter
                ';

    if(isset($upname) && $upname == 1 ) {
        $sql_a .= " ORDER BY		c.label DESC";
        $opt_trip = -1;
        $opt_trit = -2;
        $opt_tric = -3;
    }
    elseif(isset($upname) && $upname == -1) {
        $sql_a .= " ORDER BY		c.label ASC";
        $opt_trip = 1;
        $opt_trit = -2;
        $opt_tric = -3;
    }
    elseif(isset($upname) && $upname == 2) {
        $sql_a .= " ORDER BY		c.date_create DESC ";
        $opt_trip = -1;
        $opt_trit = -2;
        $opt_tric = -3;
    }
    elseif(isset($upname) && $upname == -2) {
        $sql_a .= " ORDER BY		c.date_create ASC ";
        $opt_trip = -1;
        $opt_trit = 2;
        $opt_tric = -3;
    }
    elseif(isset($upname) && $upname == 3) {
        $sql_a .= " ORDER BY		c.date_envoi DESC ";
        $opt_trip = -1;
        $opt_trit = -2;
        $opt_tric = -3;
    }
    elseif(isset($upname) && $upname == -3) {
        $sql_a .= " ORDER BY		c.date_envoi ASC ";
        $opt_trip = -1;
        $opt_trit = -2;
        $opt_tric = 3;
    }
    else {
        $sql_a .= " ORDER BY    c.date_create";
        $opt_trip = -1;
        $opt_trit = -2;
        $opt_tric = -3;
    }

    $res_a = $db->query($sql_a, array(
        ':idnewsletter' => $id_news
    ));

    $tab_env = array();
    while($tab_res = $db->fetchrow($res_a)) {
        $tab_env[$tab_res['id_env']] = $tab_res;
    }

?>
            <tr>
                <td colspan="2" width="100%">
                    <?php
                        echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_LIST_ARTICLE']);
                    ?>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="right" width="100%">
                                <?php if($db->numrows($res_a) > 0) echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_ADD_ARTICLE'],'./common/img/add.gif','javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ADD_ARTICLE.'&id_news='.$id_news.'\''); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <?php
                                    if($db->numrows($res_a) > 0) {
                                        $class = "trl1";
                                        echo '  <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr class="trl1">
                                                        <td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'&upname='.$opt_trip.'">'.$_DIMS['cste']['_DIMS_LABEL_TITLE'].'</a>
                                                        </td>
                                                        <td>'.$_DIMS['cste']['_DIMS_LABEL_LINKED_DOCS_EVT'].'
                                                        </td>
                                                        <td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'&upname='.$opt_trit.'">'.$_DIMS['cste']['_DIMS_LABEL_ENT_DATEC'].'</a>
                                                        </td>
                                                        <td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ACTION_ADD.'&id_news='.$id_news.'&upname='.$opt_tric.'">'.$_DIMS['cste']['_DIMS_LABEL_SEND_DATE'].'</a>
                                                        </td>
                                                        <td>'.$_DIMS['cste']['_DIMS_ACTIONS'].'
                                                        </td>
                                                    </tr>
                                                ';
                                        $id_module = $_SESSION['dims']['moduleid'];
                                        $id_object = dims_const::_SYSTEM_OBJECT_NEWSLETTER;
                                        foreach($tab_env as $id_env => $tab) {
                                            if($class == "trl1") $class = "trl2";
                                            else $class = "trl1";

                                            $date_cre = dims_timestamp2local($tab['date_create']);
                                            if(isset($tab['date_envoi']) && $tab['date_envoi'] != '') $date_env = dims_timestamp2local($tab['date_envoi']);
                                            else $date_env['date'] = '';

                                            $id_record = $id_env;

                                            $doc = '';
                                            require_once DIMS_APP_PATH.'include/functions/files.php';
                                            $lstfiles = dims_getFiles($dims,$id_module,$id_object,$id_record);
                                            if(isset($lstfiles) && $lstfiles != '') {
                                                foreach($lstfiles as $key => $file) {
                                                    if($doc != '') $doc .= '<br/>';
                                                    $doc .= '<a href='.$file['downloadlink'].' title="'.$file['name'].' - Voir le document.">'.$file['name'].'</a>';
                                                }
                                            }

                                            echo '  <tr class="'.$class.'">
                                                        <td><a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ADD_ARTICLE.'&id_news='.$id_news.'&id_env='.$id_env.'">'.$tab['label'].'</a>
                                                        </td>
                                                        <td>'.$doc.'
                                                        </td>
                                                        <td>'.$date_cre['date'].'
                                                        </td>
                                                        <td>'.$date_env['date'].'
                                                        </td>
                                                        <td>
                                                            <a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ADD_ARTICLE.'&id_news='.$id_news.'&id_env='.$id_env.'"><img src="./common/img/edit.gif" title="More details"/></a> /
                                                            <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SUPPR_ARTICLE.'&id_news='.$id_news.'&id_env='.$id_env.'').'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')"><img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DELETE'].'"/></a> /
                                                            <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action=test_sending&id_news='.$id_news.'&id_env='.$id_env.'').'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')"><img src="./common/img/publish.png" title="Make a test"/></a> /
                                                            <a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.dims_urlencode('admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_SEND_ARTICLE.'&id_news='.$id_news.'&id_env='.$id_env.'').'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\')"><img src="./common/img/mail_tovalid.png" title="Send newsletter"/></a>
                                                        </td>
                                                    </tr>';

                                        }
                                        echo '</table>';
                                    }
                                    else {
                                        echo $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_NO_ARTICLE'];
                                    }
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                <?php echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_ADD_ARTICLE'],'./common/img/add.gif','javascript:document.location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_NEWSLETTER.'&cat=0&dims_desktop=block&dims_action=public&action='._NEWSLETTER_ADD_ARTICLE.'&id_news='.$id_news.'\''); ?>
                                </td>
                            </tr>
                        </table>
                    <?
                        echo $skin->close_simplebloc();
                    ?>
                </td>
            </tr>
<?php
    }
?>
        </table>
    </form>
</div>
<?php
    echo $skin->close_simplebloc();
?>
