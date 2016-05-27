<div class="zone_title_newsletters">
	<div class="title_newsletters">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_newsletter_management.png" border="0" /><h1><?php echo $_SESSION['cste']['NEWSLETTERS_MANAGEMENT']; ?></h1>
	</div>
</div>
<!-- Groups of Newsletters !-->
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['GROUPS_OF_NEWSLETTERS']; ?></span>
</div>

<?php

//recuperation des donnees
$params = array();
$sql = 'SELECT		distinct n.*,
					count(distinct c.id) as nb_article,
					s.id_contact as id_inscr,
					i.id as id_dmd,
					u.lastname,
					u.firstname
		FROM		dims_mod_newsletter n
		LEFT JOIN	dims_user as u
		ON			u.id=n.id_user_responsible
		LEFT JOIN	dims_mod_newsletter_content c
		ON			c.id_newsletter = n.id
		LEFT JOIN	dims_mod_newsletter_subscribed s
		ON			s.id_newsletter = n.id
		AND			s.etat = 1
		LEFT JOIN	dims_mod_newsletter_inscription i
		ON			i.id_newsletter = n.id
		WHERE		n.id_workspace in ('.$db->getParamsFromArray(explode(',', $listworkspace_nl ), 'idworkspace', $params).')
		GROUP by	n.id
		ORDER BY	n.label ASC';


$res = $db->query($sql, $params);

$tab_news = array();
while($tab_res = $db->fetchrow($res)){
	//on compte le nombre d'inscrits issus des mailing lists backoffice


	if(isset($tab_res['id_dmd']) && $tab_res['id_dmd'] != '') {
		$tab_news[$tab_res['id']]['nb_dmd'][$tab_res['id_dmd']] = $tab_res['id_dmd'];
	}

	$tab_news[$tab_res['id']]['news'] = $tab_res;
}
echo '<table width="100%" cellpadding="0" cellspacing="0">';
echo	'<tr>
			<td colspan="4" style="padding:5px 0;">';
echo dims_create_button($_DIMS['cste']['_DIMS_ADD'], 'plus', 'javascript:document.location.href=\'admin.php?news_op='.dims_const_desktopv2::_NEWS_ADD_NEWSLETTER_MODEL.'\'');
echo		'</td>
		</tr></table>';
?>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
					<tr>
							<th class="title_table_news">
									<?php echo $_SESSION['cste']['TITLE_OF_THE_GROUP']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_rouge.png" border="0" style="float:right" />
							</th>
							<th class="title_table_news">
									<?php echo $_SESSION['cste']['ENABLED']; ?>
							</th>
							<th class="title_table_news">
									<?php echo $_SESSION['cste']['_DIMS_LABEL_RESPONSIBLE']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_noire.png" border="0" style="float:right" />
							</th>

							<th class="title_table_news">
									<?php echo $_SESSION['cste']['REQUESTS']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_noire.png" border="0" style="float:right" />
							</th>
							<th class="title_table_news">
									<?php echo $_SESSION['cste']['NB_SENDINGS']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_noire.png" border="0" style="float:right" />
							</th>
							<th class="title_table_news">
									<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
							</th>
					</tr>
<?

if(count($tab_news) > 0) {
	$news_default = 0;
	$class='';
	foreach($tab_news as $id_nw => $news) {

		if(isset($_SESSION['dims']['default_newsletter']) && $_SESSION['dims']['default_newsletter'] == 0) {
			$_SESSION['dims']['default_newsletter'] = $id_nw;
			$_SESSION['dims']['current_newsletter'] = $id_nw;
			$id_news = $id_nw;
		}

		if($class == "") $class = "ligne_2_news";
		else $class = "";

		if($id_nw == $_SESSION['dims']['current_newsletter']) $style = 'style="background-color:#BCBCBC;"';
		else $style = "";

		if (!isset($news['nb_dmd'])) $news['nb_dmd']=array();
		echo '<tr class="'.$class.'" '.$style.'>';
		echo	'<td><a href="admin.php?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER.'&id_news='.$id_nw.'">'.$news['news']['label'].'</a></td>';
		echo	'<td class="case_news_center">';
		// affichage du status
		if ($news['news']['etat']==1)
			echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/puce_verte_news.png" border="0" />';
		else
			echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/puce_rouge_news.png" border="0" />';

		echo "</td>";

		echo	'<td class="case_news_center">'.$news['news']['firstname']." ".$news['news']['lastname'].'</td>';
		echo	'<td class="case_news_center">'.count($news['nb_dmd']).' '.$_DIMS['cste']['_DIMS_NEWSLETTER_INSC_REQUEST'].'</td>';
		echo	'<td class="case_news_center">'.$news['news']['nb_article']."</td>";
		echo	'<td class="case_news_center">';

		echo	"<a href=\"/admin.php?news_op=".dims_const_desktopv2::_FICHE_NEWSLETTER."&id_news=".$id_nw."\"><img src=\""._DESKTOP_TPL_PATH."/gfx/common/open_record16.png\" border=\"0\" style=\"padding-right: 5px;\"/></a>";

		echo	'<a href="javascript:void(0);" onclick="dims_confirmlink(\''.dims_urlencode("admin.php?news_op=".dims_const_desktopv2::_NEWS_DELETE_NEWSLETTER."&id_news=".$id_nw).'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close_news.png"/></a></td>';
		echo '</td></tr>';
	}
}
else {
	echo		'<td colspan="6">'.$_DIMS['cste']['_DIMS_LABEL_NO_NEWSLETTER'].'</td>';
}

?>

		</tbody>
	</table>
</div>
<?php
$inf_news = new newsletter();
$mailinglist=$inf_news->getAllMailinglist($listworkspace_nl);
$nbml=sizeof($mailinglist);


$mailingbygroup=$inf_news->getAllGroupMailinglist($listworkspace_nl);
?>
<!-- Mailing Lists !-->
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['_DIMS_LABEL_MAILINGLIST']. " (".$nbml.")"; ?></span>
</div>
<?php
echo '<table width="100%" cellpadding="0" cellspacing="0">';
echo	'<tr>
			<td colspan="4" style="padding:5px 0;">';
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADD_MODEL'], 'plus', 'javascript:document.location.href=\'admin.php?news_op='.dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST_ADD.'\'');
echo		'</td>
		</tr></table>';
?>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>
				<td class="title_table_news">
					<?php echo $_SESSION['cste']['TITLE_OF_THE_GROUP']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_rouge.png" border="0" style="float:right" />
				</td>
				<td class="title_table_news">
					<?php echo $_SESSION['cste']['NB_CONTACT_EMAILS']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_noire.png" border="0" style="float:right" />
				</td>
				<td class="title_table_news">
					<?php echo $_SESSION['cste']['LINKED_GROUP(S)_OF_NEWSLETTERS']; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/trie_noire.png" border="0" style="float:right" />
				</td>
				<td class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</td>
			</tr>
<?php
$class='';

foreach ($mailinglist as $ml) {

	$class = ($class == "") ? 'ligne_2_news' : '';
?>
	<tr class="<?php echo $class;?>">
			<td>
				<a href="/admin.php?news_op=<?php echo dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST; ?>&id_mail=<?php echo $ml['id']; ?>">
				<?php echo $ml['label']; ?>
				</a>
			</td>
			<td class="case_news_droite">
					<?php echo $ml['id_nb_ct']; ?>
			</td>
			<td>
				<?php
				if (isset($mailingbygroup[$ml['id']])) {
					foreach ($mailingbygroup[$ml['id']] as $label) {
						echo '<li class="puce_li_news">'.$label.'</li>';
					}

				}
				?>
			</td>
			<td class="case_news_center">
					<a href="/admin.php?news_op=<?php echo dims_const_desktopv2::_NEWSLETTERS_MAILINGLIST; ?>&id_mail=<?php echo $ml['id']; ?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/open_record16.png" border="0" style="padding-right: 5px;"/></a>

					<?php
					echo	'<a href="javascript:void(0);" onclick="dims_confirmlink(\''.dims_urlencode("admin.php?news_op=".dims_const_desktopv2::_NEWSLETTER_DELETE_MAILING_LIST."&id_mail=".$ml['id']).'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="'._DESKTOP_TPL_PATH.'/gfx/common/close_news.png"/></a></td>';
					?>
			</td>
	</tr>

<?php
}
?>
	</table>
</div>
