<span style="float:right">
            <?php
            echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_ADD_ARTICLE'],'./common/img/add.gif','javascript:document.location.href=\''.$scriptenv.'?news_op='.dims_const_desktopv2::_NEWSLETTER_ARTICLE_NEW.'\''); ?>
        </span>
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['LIST_OF_PREPARED_SENDINGS']; ?></span>

</div>
<?php
$newsprepar=$inf_news->getNewsletters();

?>

<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>
				<th class="case_news_center" style="width: 50px">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trombone_gris16.png" border="0" />
				</th>
				<th class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?>
				</th>
				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?>
				</th>

				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
                    </tbody>
<?php
/*
 <th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_LANG']; ?>
				</th>
 <td class="case_news_center">
					[FR]
				</td>
 */
$class='';

foreach ($newsprepar['notsent'] as $news) {

    $class = ($class == "") ? 'ligne_2_news' : '';
?>
                <tr class="<?php echo $class;?>">
				<td class="case_news_center">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trombone_rouge16.png" border="0" />
				</td>
				<td>
					<?php echo $news['label']; ?>
				</td>
				<td>
                                    <?
                                    $date_news = dims_timestamp2local($news['date_create']);
                                    echo $date_news['date'];
                                ?>
				</td>
				<td class="case_news_center">
                                    <a href="/admin.php?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_ARTICLE_EDIT;?>&id_news=<?php echo $id_news;?>&id_env=<?php echo $news['id'];?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_edit_petit.png" border="0" style="float:left" /></a>
                                    <a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_ARTICLE_DELETE; ?>&id_news=<?php echo $id_news;?>&id_env=<? echo $news['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']?>');"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" style="float:left" />
                                    <a href="/admin.php?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_TEST_SENDING;?>&id_news=<?php echo $id_news;?>&id_env=<?php echo $news['id'];?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_validation.png" border="0" style="float:left" /></a>
                                    <a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_SEND_ARTICLE; ?>&id_news=<?php echo $id_news;?>&id_env=<? echo $news['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_envoyer.png" border="0" style="float:left" /></a>
				</td>
			</tr>
<?php
}
?>
	</table>
</div>

<!-- List of newsletters already sent -->

<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['LIST_OF_NEWSLETTERS_ALREADY_SENT']; ?></span>
</div>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>
				<th class="case_news_center" style="width: 50px">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trombone_gris16.png" border="0" />
				</th>
				<th class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?>
				</th>

				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_SEND_DATE']; ?>
				</th>
                                <th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?>
				</th>
                                <th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
                </tbody>
<?php
$class='';

foreach ($newsprepar['sent'] as $news) {

    $class = ($class == "") ? 'ligne_2_news' : '';
?>
    <tr class="<?php echo $class;?>">
        <td class="case_news_center">
                <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trombone_rouge16.png" border="0" />
        </td>
        <td>
                <?php echo $news['label']; ?>
        </td>
        <td>
        <?
            $date_news = dims_timestamp2local($news['date_envoi']);
            echo $date_news['date'];
        ?>
        </td>
        <td class="case_news_center">
        <?
            $date_news = dims_timestamp2local($news['date_create']);
            echo $date_news['date'];
        ?>
        </td>

        <td class="case_news_center">
                <a href="/admin.php?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_ARTICLE_EDIT;?>&id_news=<?php echo $id_news;?>&id_env=<?php echo $news['id'];?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_edit_petit.png" border="0" style="float:left" /></a>
                <a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_ARTICLE_DELETE; ?>&id_news=<?php echo $id_news;?>&id_env=<? echo $news['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']?>');"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" style="float:left" />
        </td>
    </tr>
<?php
}
?>
	</table>
</div>
