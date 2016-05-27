<?php
//$id_news = dims_load_securvalue('id_news', dims_const::_DIMS_NUM_INPUT, true, true);

$inf_news = new newsletter();
$inf_news->open($id_news);

// responable
$idusr=(!empty($inf_news->fields['id_user_responsible'])) ? $inf_news->fields['id_user_responsible'] : $inf_news->fields['id_user_create'];

$user = new user();
$user->open($idusr);

?>
<div class="zone_title_newsletters">
    <div class="title_newsletters">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_newsletter_management.png" border="0" /><h1><?php echo $_SESSION['cste']['NEWSLETTERS_MANAGEMENT']; ?></h1>
	</div>
	<div class="come_back">
		<a href="/admin.php?news_op=<? echo dims_const_desktopv2::_NEWSLETTERS_DESKTOP;?>&mode=newsletters"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/come_back.png" border="0" /><span><?php echo $_SESSION['cste']['COME_BACK_TO_THE_NEWSLETTERS_MANAGEMENT']; ?></span></a>
	</div>
</div>
<div class="news_header_gauche">
	<div class="news_header_picture">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_bric.png" border="0" />
	</div>
	<div class="title_news_header"><? echo $inf_news->fields['label']; ?></div>
	<div class="created_news_header">
		<span class="createdby"><?php echo $_SESSION['cste']['CREATED_BY']; ?> :</span><span>
			<?= $user->get('firstname')." ".$user->get('lastname'); ?>
		</span>
	</div>
	<div class="created_news_header">
		<span class="createdby"><?php echo $_SESSION['cste']['CREATED_THE']; ?> :</span><span>
            <?
            $datecreate=$inf_news->fields['timestp_create'];
            $date=dims_timestamp2local($datecreate);
            echo $date['date'];
            ?>
        </span>
	</div>
</div>
<?

// calcul du nombre de subscription
// nbre de demande, nb de sendings

$tabresult=$inf_news->getNbRegistration();

// on traite maintenant les tags
$listAttachTags=$inf_news->attachContactsByTag();

$tailletag=sizeof($listAttachTags);
$subscriptions=array();
$subscriptions[0]=$tabresult['nbsub']+$tabresult['nbinml']+$tailletag;
$subscriptions[1]=$tabresult['nbsub'];
$subscriptions[2]=$tabresult['nbinml'];
$subscriptions[3]=$tailletag;

$request=$tabresult['nbinscr'];

$newsprepar=$inf_news->getNewsletters();

$nbtag=$inf_news->getNbTag();


?>
<div class="news_header_droite">
	<div class="news_header_puce">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/puce_grde_verte.png" border="0" />
	</div>
	<div class="nbsub_requets_header">
		<span class="nbsub_requets"><?php echo $_SESSION['cste']['NB_SUBSCRIPTIONS']; ?> :</span><span><? echo $subscriptions[0]; ?></span>
	</div>
	<div class="nbsub_requets_header">
		<span class="nbsub_requets"><?php echo $_SESSION['cste']['REQUESTS']; ?> :</span><span><? echo $request; ?></span>
	</div>
	<div class="nbsub_requets_header">
		<span class="nbsub_requets"><?php echo $_SESSION['cste']['NB_SENDINGS']; ?> :</span><span><?= count($newsprepar['sent']); ?></span>
	</div>
        <div class="nbsub_requets_header">
		<span class="nbsub_requets"><?php echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?> :</span><span><? echo $nbtag; ?></span>
	</div>
</div>
<?php
if( !isset ( $_SESSION['dims']['newsletters']['record']['op'] ) ) $_SESSION['dims']['newsletters']['record']['op'] = dims_const_desktopv2::_NEWS_DESCRIPTION;
$news_record_op = dims_load_securvalue('news_record_op',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['newsletters']['record']['op'],$_SESSION['dims']['newsletters']['record']['op']);

?>
<div class="lien_news_header">
	<a <?php echo ($news_record_op==dims_const_desktopv2::_NEWS_DESCRIPTION) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_op='.dims_const_desktopv2::_NEWS_DESCRIPTION;?>&id_news=<?php echo $id_news;?>"><?php echo $_SESSION['cste']['DESCRIPTION']; ?></a>
	<a <?php echo ($news_record_op==dims_const_desktopv2::_NEWS_RECIPIENTS) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_op='.dims_const_desktopv2::_NEWS_RECIPIENTS;?>&id_news=<?php echo $id_news;?>"><?php echo $_SESSION['cste']['RECIPIENTS']; ?></a>
	<a <?php echo ($news_record_op==dims_const_desktopv2::_NEWS_SENDINGS) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_op='.dims_const_desktopv2::_NEWS_SENDINGS;?>&id_news=<?php echo $id_news;?>"><?php echo $_SESSION['cste']['SENDINGS']; ?></a>
        <a <?php echo ($news_record_op==dims_const_desktopv2::_NEWS_TAGS) ? 'class="selected"' : '';?> href="<?php echo $dims->getScriptEnv().'?news_record_op='.dims_const_desktopv2::_NEWS_TAGS;?>&id_news=<?php echo $id_news;?>"><?php echo strtoupper($_SESSION['cste']['_DIMS_LABEL_TAGS']); ?></a>
</div>
<?php
switch($news_record_op){
	default:
	case dims_const_desktopv2::_NEWS_DESCRIPTION:
		require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/record/description.tpl.php';
		break;
	case dims_const_desktopv2::_NEWS_RECIPIENTS:
		require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/record/recipients.tpl.php';
		break;
	case dims_const_desktopv2::_NEWS_SENDINGS:
		require_once  _DESKTOP_TPL_LOCAL_PATH.'/newsletters/record/sendings.tpl.php';
		break;
        case dims_const_desktopv2::_NEWS_TAGS:
                require_once _DESKTOP_TPL_LOCAL_PATH . '/newsletters/record/tags.tpl.php';
                break;
}
?>
