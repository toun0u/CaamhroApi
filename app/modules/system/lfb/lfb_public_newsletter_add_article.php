<?php

//$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_env = dims_load_securvalue('id_env',dims_const::_DIMS_CHAR_INPUT,true,true);
$sent = dims_load_securvalue('sent',dims_const::_DIMS_NUM_INPUT,true,true);

$inf_news = new newsletter();
$inf_news->open($id_news);

if(isset($id_env) && $id_env != '') {
    $inf_env = new news_article();
    $inf_env->open($id_env);
}

//else {
//	$inf_env = new news_article();
//	 $inf_env->init_description();
//	 $inf_env->fields['label']= $inf_news->fields['label']." ".$_DIMS['cste']['_RSS_LABELTAB_ADD'];
//	 $inf_env->fields['id_newsletter']=$id_news;
//	 $inf_env->fields['content']=$inf_news->fields['descriptif'];
//
//	 $inf_env->save();
//	$id_env=$inf_env->fields['id'];
//}

//$title = $_DIMS['cste']['_DIMS_NEWSLETTER_MAILLINKED']."\"".$inf_news->fields['label']."\"";

//echo $skin->open_simplebloc($title);
?>
<div style="width:100%;float:left;">
    <form name="newsletter_env" id="newsletter_env" method="POST" action="admin.php?dims_mainmenu=<? echo dims_const::_DIMS_MENU_NEWSLETTER; ?>&cat=0&dims_desktop=block&dims_action=public&action=<? echo _NEWSLETTER_ACTION_SAVE_ENV; ?>">
        <input type="hidden" name="id_news" value="<?php echo $id_news; ?>"/>
    <?php
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("id_news",    $id_news);
        $token->field("id_env",     $id_env);
        $token->field("env_label");
        $token->field("env_id_lang");
        $token->field("fck_env_content");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
        if(isset($id_env) && $id_env != '') {
    ?>
        <input type="hidden" name="id_env" value="<?php echo $id_env; ?>"/>
    <?php
        }
    ?>
    <table width="100%" cellpadding="0" cellspacing="5" style="border:#3B567E 1px solid;">
        <tr>
            <td colspan="2" align="left" style="font-size:15px;">
            </td>
        </tr>
        <tr>
            <td align="left" width="10%">
                <?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?>&nbsp;
            </td>
            <td>
                <input type="text" id="env_label" name="env_label" <? if(isset($id_env) && $id_env != '') echo 'value="'.$inf_env->fields['label'].'"'; else echo 'value="'.$inf_news->fields['label'].'"';?> />
            </td>
        </tr>
		 <tr>
            <td align="left" >
                <?php echo $_DIMS['cste']['_DIMS_LABEL_LANG']; ?>&nbsp;
            </td>
            <td>
				<select class="select" name="env_id_lang">
					<?
					if(isset($id_env) && $id_env != '')  $langsel=$inf_env->fields['id_lang'];
					else $langsel=1;
					$res=$db->query("select * from dims_lang");
					if ($db->numrows($res)>0) {
						while ($f=$db->fetchrow($res)) {
							$sel=($langsel == $f['id']) ? 'selected' : '';
							echo "<option value=\"".$f['id']."\" $sel>".$f['label']."</option>";
						}
					}
					?>
				</select>
            </td>
        </tr>
        <tr>
            <td align="left">
                <?php echo $_DIMS['cste']['_CONTENT']; ?>&nbsp;
            </td>
            <td>
				<?
				if(isset($id_env) && $id_env != '') $content= $inf_env->fields['content'];
				elseif(isset($id_news) && $id_news != '') $content= $inf_news->fields['descriptif'];
				else $content="";
				dims_fckeditor("env_content",$content,"800","350");
				?>
            </td>
        </tr>
        <?php
               if(isset($id_env) && $id_env != '') {
                    $id_module = $_SESSION['dims']['moduleid'];
                    $id_object = dims_const::_SYSTEM_OBJECT_NEWSLETTER;
                    $id_record = $inf_env->fields['id'];
                    $doc = '';
                    require_once DIMS_APP_PATH.'include/functions/files.php';

                    $lstfiles = dims_getFiles($dims,$id_module,$id_object,$id_record);
                    dims_print_r($lstfiles);
                    if(isset($lstfiles) && $lstfiles != '') {
                        foreach($lstfiles as $key => $file) {
                            if($doc != '') $doc .= '<br/>';
                            $doc .= '<a href='.$file['downloadlink'].' title="'.$file['name'].' - Voir le document.">'.$file['name'].'</a>';
                            $doc .= "<a href=\"javascript:dims_confirmlink('".dims_urlencode("$scriptenv?dims_op=doc_file_delete&docfile_id=".$file['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\"></a>";
                        }
                    }
         ?>
        <tr>
            <td align="left">
                <?php if($doc != '') echo $_DIMS['cste']['_DIMS_LABEL_PIECE_JOINTE']; ?>&nbsp;
            </td>
            <td align="left">
                <?php
                    if($doc == '') {
						//echo $id_module." ".$id_object." ".$id_record;
                        echo dims_createAddFileLink($id_module,$id_object,$id_record,'float:left;');
                    }
                    else {
                        echo $doc;
                    }
				?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td align="center" colspan="2">
               <?php
                    echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_env.submit();', '', 'float:right;');
                    echo  dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\''.$scriptenv.'?subaction='._DIMS_NEWSLETTER_NEWSLETTER.'\';', '', 'float:right;');
               ?>

            </td>
        </tr>
    </table>
    </form>
</div>
<?php
//echo $skin->close_simplebloc();
?>
