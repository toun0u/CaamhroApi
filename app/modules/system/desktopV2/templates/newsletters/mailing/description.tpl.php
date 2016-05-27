<?

?>
<form name="newsletter_rub" id="newsletter_rub" method="POST" action="admin.php?news_op=<? echo dims_const_desktopv2::_NEWSLETTER_SAVE_LIST_EMAIL; ?>">
    <?php
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        if(isset($id_mail) && $id_mail != '') $token->field("id_mail", $id_mail);
        $token->field("fck_list_comment");
        $token->field("list_label");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
        if(isset($id_mail) && $id_mail != '') {
    ?>
        <input type="hidden" name="id_mail" value="<?php echo $id_mail; ?>"/>
    <?php
}
?>
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?></span>
</div>
<div class="description_cadre">
	<?
        $content = '';
        if(isset($id_mail) && $id_mail != '' && $label != '') $labl= $label;
        else $labl="";
	?>
	<input type="text" id="list_label" name="list_label" value="<?php echo $labl; ?>"/>
</div>

<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></span>
</div>

<div class="description_cadre">
	<p>
        <?
        $content = '';
        if(isset($id_mail) && $id_mail != '' && $list->fields['comment'] != '') $content= $list->fields['comment'];
        else $content="";
        dims_fckeditor("list_comment",$content,"800","350");
        ?>
        </p>
</div>

<?
echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_rub.submit();','','float:right;padding-right:10px;');
?>
</form>