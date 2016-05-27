<form name="newsletter_rub" id="newsletter_rub" method="POST" action="admin.php?news_op=<? echo dims_const_desktopv2::_NEWS_SAVE_TAG; ?>">
    <?php
        // Sécurisation du formulaire par token
        require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
        $token = new FormToken\TokenField;
        $token->field("id_news", $id_news);
        $token->field("add_tag");
        $tokenHTML = $token->generate();
        echo $tokenHTML;
        if(isset($id_news) && $id_news != '') {
    ?>
        <input type="hidden" name="id_news" value="<?php echo $id_news; ?>"/>
    <?php
}

$listattachedTags=$inf_news->getSelectedTags();

?>
<table style="width:50%">
    <tr>
        <td align="right">
            <?php echo $_DIMS['cste']['_DIMS_ADD']." tag"; ?>&nbsp;
        </td>
        <td>
            <select name="add_tag">
            <?php
            // on fait la liste des tags
            $list=$inf_news->getAllTags();

            foreach ($list as $tag) {
                if (!isset($listattachedTags[$tag['id']]))
                    echo "<option value=\"".$tag['id']."\">".$tag['tag']."</option>";
            }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="2">
        <?
            echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_rub.submit();','','float:right;padding-right:10px;');
            ?>
        </td>
    </tr>
</table>
</form>


<?php


$class='';



?>

<div class="title_groups_news" style="clear:both;">
	<span><?php echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></span>
</div>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>

				<th class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?>
				</th>

                                <th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
                </tbody>
<?php
$class='';

foreach ($listattachedTags as $id => $tag) {
    $class = ($class == "") ? 'ligne_2_news' : '';
?>
    <tr class="<?php echo $class;?>">

        <td>
                <?php echo $tag['tag']; ?>
        </td>
        <td class="case_news_center">
                <a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_TAG_DELETE; ?>&id_news=<?php echo $id_news;?>&id_attach=<? echo $tag['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_LABEL_CONFIRM_DELETE']?>');"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" style="float:left" />
        </td>
    </tr>
<?php
}
?>
	</table>
</div>
