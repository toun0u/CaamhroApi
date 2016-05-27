<!-- Requests -->
<?
// boucle sur les requests

$newreg=$inf_news->getNewRegistration();
$nbrq=sizeof($newreg);

?>
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['REQUESTS']; ?> (<?php echo $nbrq; ?>)</span>
</div>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>
				<th class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
				</th>
				<th class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
				</th>
				<th class="title_table_news" style="width: 150px">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_DATE_REGISTRATION']; ?>
				</th>
				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
					</tbody>
<?php

$class="";
foreach ($newreg as $insc) {
	$class = ($class == "") ? 'ligne_2_news' : '';
?>
				<tr class="<?php echo $class;?>">
						<td>
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_avatar.png" border="0" style="float: left; padding-right: 5px;"/>
								<span style="line-height: 20px;"><? echo $insc['nom']." ".$insc['prenom'];?></span>
						</td>
						<td>
								<? echo $insc['email'];?>
						</td>
						<td>
								<?
								$date_inscr = dims_timestamp2local($insc['date_inscription']);
								echo $date_inscr['date'];
								?>
						</td>
						<td class="case_news_center">
								<a href="<?php echo $dims->getScriptEnv(); ?>?news_op=<? echo dims_const_desktopv2::_NEWSLETTER_ATTACH_REGISTRATION;?>&id_dmd=<? echo $insc['id']; ?>"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_validation.png" border="0" style="padding-right: 5px;"/></a>
								<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_ATTACH_REGISTRATION_DELETE; ?>&id_dmd=<? echo $insc['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']?>');">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" alt="<?php  echo $_SESSION['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']; ?>" />
								</a>
						</td>
				</tr>



<!-- Mailing lists attached -->
<?php
}
?>
</table>
</div>
<?php
$mailinglist=$inf_news->getMailinglist();
$nbml=sizeof($mailinglist);
?>
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['MAILING_LISTS_ATTACHED']; ?> (<?php echo $nbml;?>)</span>
</div>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>
				<th class="title_table_news">
					<?php echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?>
				</th>
				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['NB_EMAILS']; ?>
				</th>
				<th class="title_table_news" style="width: 300px">
					<?php echo $_SESSION['cste']['_DIMS_COMMENTS']; ?>
				</th>
				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
<?php
$class="";
foreach ($mailinglist as $idml =>$ml) {
	$class = ($class == "") ? 'ligne_2_news' : '';
	?>
	<tr class="<?php echo $class;?>">
		<td>
				<?php echo $ml['label'];?>
		</td>
		<td class="case_news_droite">
				<?php echo $ml['id_nb_ct'];?>
		</td>
		<td>
				<?php echo $ml['comment'];?>
		</td>
		<td class="case_news_center">
			<a href="<?php echo $dims->getScriptEnv()."?news_op=".dims_const_desktopv2::_FICHE_NEWSLETTER; ?>&id_link=<? echo $ml['id_link']; ?>&from=to_insc"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/open_record16.png" border="0" style="padding-right: 5px;"/></a>

			<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_ACTION_SUPP_LIST; ?>&id_link=<? echo $ml['id_link']; ?>&from=to_insc','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close_news.png" border="0" /></a>
		</td>
	</tr>

	<?php
}

?>

		</tbody>
	</table>
</div>


<?php

if(!isset($_SESSION['dims']['current_nameregistration'])) $_SESSION['dims']['current_nameregistration'] = '';

$filtername = dims_load_securvalue('filter_name_registration',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['dims']['current_nameregistration'], $_SESSION['dims']['current_nameregistration']);
$sqlfiltername='';
$sqlfiltername2='';

if ($filtername!='') {
   $sqlfiltername= " and (lastname like '%". $filtername."%' or firstname like '%". $filtername."%' or email like '%". $filtername."%')";
	$sqlfiltername2= " and (email like '%". $filtername."%')";

}

$iconame="trie_noire";
$icodate="trie_noire";
$icoemail="trie_noire";

$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);

$sql='';
$sql2='';

if(isset($upname) && $upname == 1 ) {
	$sql .= " ORDER BY		nom DESC, prenom DESC";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
	$iconame="trie_rouge";
}
elseif(isset($upname) && $upname == -1) {
	$sql .= " ORDER BY		nom ASC, prenom ASC";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
	$iconame="trie_rouge_inv";
}
elseif(isset($upname) && $upname == 2) {
	$sql .= " ORDER BY		email DESC ";
	$sql2 .= " ORDER BY		email DESC ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
	$icoemail="trie_rouge";
}
elseif(isset($upname) && $upname == -2) {
	$sql .= " ORDER BY		email ASC ";
	$sql2 .= " ORDER BY		email ASC ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
	$icoemail="trie_rouge_inv";
}
elseif(isset($upname) && $upname == 3) {
	$sql .= " ORDER BY		date_inscription DESC ";
	$sql2 .= " ORDER BY		date_creation DESC ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
	$icodate="trie_rouge";
}
elseif(isset($upname) && $upname == -3) {
	$sql .= " ORDER BY		date_inscription ASC ";
	$sql2 .= " ORDER BY		date_creation ASC ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
	$icodate="trie_rouge_inv";
}
else {
	$sql .= " ORDER BY    date_inscription DESC";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}


$tab_dmd = $inf_news->getAllregistration($sql,$sql2,$sqlfiltername,$sqlfiltername2);

// check for choice
if(!isset($_SESSION['dims']['current_choiceregistration'])) $_SESSION['dims']['current_choiceregistration'] = 0;

$choice_reg = dims_load_securvalue('choice_req',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['dims']['current_choiceregistration'], $_SESSION['dims']['current_choiceregistration']);

$checkregs=array();
for ($i=0;$i<=3;$i++) {
	if ($i==$choice_reg) $checkregs[$i]='checked';
	else $checkregs[$i]='';
}

?>
<!-- Recipients lists -->
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['RECIPIENTS_LISTS']; ?> (<?php echo $subscriptions[$choice_reg];?>)</span>
</div>
<form>
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("choice_reg");
	$token->field("filter_name_registration");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<div class="filtre_header_news">
	<span class="title_filtre_header_news"><?php echo $_SESSION['cste']['FROM']; ?> :</span>
		<div class="filtre_all">
			<input name="choice_reg" onchange="javascript:document.location.href='/admin.php?news_record_op=<? echo dims_const_desktopv2::_NEWS_RECIPIENTS;?>&choice_req=0';" type="radio" <?php echo $checkregs[0]; ?>><span><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></span>
		</div>
		<div class="filtre_all">
			<input name="choice_reg" onchange="javascript:document.location.href='/admin.php?news_record_op=<? echo dims_const_desktopv2::_NEWS_RECIPIENTS;?>&choice_req=1';" type="radio" <?php echo $checkregs[1]; ?>><span><?php echo $_SESSION['cste']['_DIMS_LABEL_INSCRIPTION']; ?></span>
		</div>
		<div class="filtre_all">
			<input name="choice_reg" onchange="javascript:document.location.href='/admin.php?news_record_op=<? echo dims_const_desktopv2::_NEWS_RECIPIENTS;?>&choice_req=2';" type="radio" <?php echo $checkregs[2]; ?>><span><?php echo $_SESSION['cste']['_DIMS_LABEL_MAILINGLIST']; ?></span>
		</div>
		<div class="filtre_all">
			<input name="choice_reg" onchange="javascript:document.location.href='/admin.php?news_record_op=<? echo dims_const_desktopv2::_NEWS_RECIPIENTS;?>&choice_req=3';" type="radio" <?php echo $checkregs[3]; ?>><span><?php echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></span>
		</div>
	<div class="search_contact_news"><span class="title_filtre_header_news"><?php echo $_SESSION['cste']['SEARCH_A_CONTACT']; ?></span>
			<input type="text" name="filter_name_registration" value="<? echo $filtername; ?>"><input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_FILTER']; ?>"> <span style="float:left;line-height: 17px;margin-right:5px"><?php echo $_SESSION['cste']['_DIMS_OR']; ?></span> <a class="reset_filtre" href="admin.php?news_op=<?php echo dims_const_desktopv2::_NEWS_RECIPIENTS_RESET_FILTER; ?>"><?php echo $_SESSION['cste']['_DIMS_RESET']; ?></a></div>
</div>
</form>
<?php

$linklabel='<a href="'.$scriptenv.'?news_record_op='.dims_const_desktopv2::_NEWS_RECIPIENTS.'&upname='.$opt_trip.'">';
$linkdate='<a href="'.$scriptenv.'?news_record_op='.dims_const_desktopv2::_NEWS_RECIPIENTS.'&upname='.$opt_tric.'">';
$linkemail='<a href="'.$scriptenv.'?news_record_op='.dims_const_desktopv2::_NEWS_RECIPIENTS.'&upname='.$opt_trit.'">';

?>
<div class="table_news_group">
	<table cellspacing="0" cellpadding="5" >
		<tbody>
			<tr>
				<th class="title_table_news" style="width: 50px">
					&nbsp
				</th>
				<th class="title_table_news">
					<?php echo $linklabel.$_SESSION['cste']['_DIMS_LABEL_NAME']."</a>"; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo $iconame;?>.png" border="0" style="float:right" />
				</th>
								<th class="title_table_news" style="width: 150px">
					<?php echo $linkemail.$_SESSION['cste']['_DIMS_LABEL_EMAIL']."</a>"; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo $icoemail;?>.png" border="0" style="float:right" />
				</th>
				<th class="title_table_news" style="width: 150px">
					<?php echo $linkdate.$_SESSION['cste']['_DIMS_LABEL_DATE_REGISTRATION']."</a>"; ?><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo $icodate;?>.png" border="0" style="float:right" />
				</th>
				<th class="title_table_news" style="width: 150px">
					<?php echo $_SESSION['cste']['UNSUSCRIBE_DATE']; ?>
				</th>
				<th class="title_table_news" style="width: 100px">
					<?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
				</th>
			</tr>
				</tbody>
<?php
if (($choice_reg==0 || $choice_reg==1) && isset($tab_dmd['ct'])) {
	foreach($tab_dmd['ct'] as $id_ct => $inscription) {

	$date_inscr = dims_timestamp2local($inscription['date_inscription']);
	$class = ($class == "") ? 'ligne_2_news' : '';

	?>
	<tr class="<?php echo $class; ?>">
		<td class="case_news_center">
			<?php
			if ($inscription['date_desinscription']=='') {
			?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/puce_verte_news.png" border="0" />
			<?php
			}
			else {
			?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/sleep.png" border="0" />
			<?php
			}
			?>
		</td>
		<td>
			<?php echo strtoupper($inscription['lastname'])." ".$inscription['firstname'];?>
		</td>
		<td>
			<?php echo $inscription['email']; ?>
		</td>
		<td>
			<?php echo $date_inscr['date']; ?>
		</td>
		<td>
			<?php
			if ($inscription['date_desinscription']!='') {
				$date_desinscr = dims_timestamp2local($inscription['date_desinscription']);
				echo $date_desinscr['date'];
			}
			?>
		</td>
		<td><?php
		if ($inscription['date_desinscription']!='') {
			?>
			<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_RECREATE_INSC; ?>&id_contact=<?php echo $inscription['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');">
				<img alt="<?php echo $_SESSION['cste']['_DIMS_VALID_REGISTER']; ?>" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_attach.png" border="0" />
			</a>
			<?php
		}
		else {
		?>
			<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_DELETE_INSC; ?>&id_contact=<?php echo $inscription['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');">
				<img alt="<?php echo $_SESSION['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']; ?>" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" />
			</a>
		<?
		}
		?>
		</td>
	</tr>
<?php
	}
}
// on traite maintenant les mailingslistes
if (($choice_reg==0 || $choice_reg==2) && isset($tab_dmd['ml'])) {
	foreach($tab_dmd['ml'] as $id_ct => $inscription) {

	$date_inscr = dims_timestamp2local($inscription['date_creation']);
	$class = ($class == "") ? 'ligne_2_news' : '';

	?>
	<tr class="<?php echo $class; ?>">
		<td class="case_news_center">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/puce_verte_news.png" border="0" />
		</td>
		<td>

		</td>
		<td>
			<?php echo $inscription['email']; ?>
		</td>
		<td>
			<?php echo $date_inscr['date']; ?>
		</td>
		<td>

		</td>
		<td>
			<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_DELETE_INSC; ?>&mailing_ct=1&id_mailing=<?echo $inscription['id_mailing']; ?>&id_contact=<?php echo $inscription['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');">
				<img alt="<?php echo $_SESSION['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']; ?>" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" />
			</a>
		</td>
	</tr>
<?php
	}
}

// on traite maintenant les tags
$listAttachTags=$inf_news->attachContactsByTag('',$filtername);

// on traite maintenant les mailingslistes
if ($choice_reg==0 || $choice_reg==3) {
	foreach($listAttachTags as $id_ct => $inscription) {

	$date_inscr = dims_timestamp2local($inscription['timestp_modify']);
	$class = ($class == "") ? 'ligne_2_news' : '';
	$email='';

	if ($inscription['email']<>'')
		$email= $inscription['email'];
	elseif ($inscription['emai2']<>'')
		$email= $inscription['email2'];

	if ($email=='') $puce='icon_attention.png';
	else $puce='puce_verte_news.png';
	?>
	<tr class="<?php echo $class; ?>">
		<td class="case_news_center">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? echo $puce;?>" border="0" />
		</td>
		<td>
			<?php
			echo $inscription['lastname']." ".$inscription['firstname'];
			?>
		</td>
		<td>
			<?php
			echo $email;
			?>
		</td>
		<td>
			<?php echo $date_inscr['date']; ?>
		</td>
		<td>

		</td>
		<td>
			<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?php echo $scriptenv; ?>?news_op=<?php echo dims_const_desktopv2::_NEWSLETTER_DELETE_INSC; ?>&id_contact=<?php echo $inscription['id']; ?>','<?php echo $_DIMS['cste']['_DIMS_CONFIRM']?>');">
				<img alt="<?php echo $_SESSION['cste']['_DIMS_LABEL_REFUSED_REGISTRATION']; ?>" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" border="0" />
			</a>
		</td>
	</tr>
<?php
	}
}
?>
	</table>
</div>
