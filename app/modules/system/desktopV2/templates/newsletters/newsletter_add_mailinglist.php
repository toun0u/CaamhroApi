<?php

//en cas de modif on recupere une id_mailing
$id_mail = dims_load_securvalue('id_mail', dims_const::_DIMS_NUM_INPUT, true, true);
$search_val = dims_load_securvalue('search_val', dims_const::_DIMS_CHAR_INPUT, true,true);

$label = '';
$comment = '';

if(isset($id_mail) && $id_mail != '') {
///////// DEBUT MODIF MAILINGLIST //////
	$list = new mailing();
	$list->open($id_mail);

	//informations utilisees pour le formulaire
	$label = $list->fields['label'];
	$comment = $list->fields['comment'];

	//on insere un formulaire pour (1) le rattachement aux news ou (2) l'import Outlook ou (3) un rattachement direct sur un email
	//(1)




	//on passe l'id dans le post pour la sauvegarde des modifs
	$input_hidden = '<input type="hidden" name="id_mail" value="'.$id_mail.'"/>';

	//largeur des colonnes
	$td_width = 'width="50%"';
	$colspan = 'colspan="2"';

	$title = $_DIMS['cste']['_DIMS_LABEL_MODIF_MAILINGLIST'].' : '.$label;

/////// FIN MODIF MAILINGLIST ////////
}
else {
	$title = $_DIMS['cste']['_DIMS_NEWSLETTER_ADD_LIST_MAILING'];
	$input_hidden = '';
	$form_modif = '';
	$td_width = 'width="100%"';
	$colspan = '';
}
?>
<div class="zone_title_newsletters">
    <div class="title_newsletters">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_newsletter_management.png" border="0" /><h1><?php echo $_SESSION['cste']['NEWSLETTERS_MANAGEMENT']; ?></h1>
	</div>
	<div class="come_back">
		<a href="/admin.php?news_op=<? echo dims_const_desktopv2::_NEWSLETTERS_DESKTOP;?>&mode=newsletters"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/come_back.png" border="0" /><span><?php echo $_SESSION['cste']['COME_BACK_TO_THE_NEWSLETTERS_MANAGEMENT']; ?></span></a>
	</div>
</div>
<div class="title_groups_news">
	<span><?php echo $title; ?></span>
</div>

<?php
if( !isset ( $_SESSION['dims']['newsletters']['record']['opml'] ) ) $_SESSION['dims']['newsletters']['record']['opml'] = dims_const_desktopv2::_NEWS_DESCRIPTION;
$news_record_opml = dims_load_securvalue('news_record_opml',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['newsletters']['record']['opml'],$_SESSION['dims']['newsletters']['record']['opml']);
?>
<div class="lien_news_header">
    <a <?php echo ($news_record_opml==dims_const_desktopv2::_NEWS_DESCRIPTION) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_opml='.dims_const_desktopv2::_NEWS_DESCRIPTION;?>&id_mail=<?php echo $id_mail;?>"><?php echo $_SESSION['cste']['DESCRIPTION']; ?></a>
    <a <?php echo ($news_record_opml==dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_DETAIL) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_opml='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_DETAIL;?>&id_mail=<?php echo $id_mail;?>"><?php echo strtoupper($_SESSION['cste']['_DIMS_LABEL_MAILING_EMAIL_LIST']); ?></a>
    <a <?php echo ($news_record_opml==dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_NEWSLINKED) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_opml='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_NEWSLINKED;?>&id_mail=<?php echo $id_mail;?>"><?php echo strtoupper($_SESSION['cste']['_DIMS_LABEL_NEWSLETTER']); ?></a>
</div>

<?php
switch($news_record_opml){
    default:
    case dims_const_desktopv2::_NEWS_DESCRIPTION:
            require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/mailing/description.tpl.php';
            break;
    case dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_DETAIL:
            require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/mailing/details.tpl.php';
            break;
    case dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_NEWSLINKED:
            require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/mailing/newsletters.tpl.php';
            break;
}
?>
