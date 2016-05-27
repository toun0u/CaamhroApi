<?
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


$sel_template='';
$sel_template .= '<select name="news_template">';

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$users = $workspace->getusers();

$sel_template .= '<option value="">--</option>';

$sql="	select	*
	from	dims_workspace_template where id_workspace=:idworkspace";

$res=$db->query($sql, array(
	':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspace->getId()),
));

// collecte la liste des templates disponibles
$availabletpl = dims_getavailabletemplates();

while ($f=$db->fetchrow($res)) {
	if (in_array($f['template'],$availabletpl)) {
		$sel = ($f['template'] == $inf_news->fields['template']) ? 'selected' : '';
		$sel_template .= "<option $sel>".$f['template']."</option>";
	}
}
$sel_template .= '</select>';
?>
<form name="newsletter_rub" id="newsletter_rub" method="POST" action="admin.php?news_op=<? echo dims_const_desktopv2::_NEWS_SAVE_NEWSLETTER_MODEL; ?>">
	<?php
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_news", $id_news);
		$token->field("news_id_user_responsible");
		$token->field("news_template");
		$token->field("news_etat");
		$token->field("fck_news_address");
		$token->field("fck_news_descriptif");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		if(isset($id_news) && $id_news != '') {
	?>
		<input type="hidden" name="id_news" value="<?php echo $id_news; ?>"/>
	<?php
}
?>
<div class="title_groups_news">
	<span><?php echo $_SESSION['cste']['DESCRIPTION___SYNOPSIS']; ?></span>
</div>

<div class="description_cadre">
	<p>
		<?
		$content = '';
		if(isset($id_news) && $id_news != '' && $inf_news->fields['descriptif'] != '') $content= $inf_news->fields['descriptif'];
		else $content="";
		dims_fckeditor("news_descriptif",$content,"800","350");
		?>
		</p>
</div>
<table style="width:70%">
	<tr>
		<td align="right">
			<?php echo $_SESSION['cste']['_FORMS_MODEL']; ?>&nbsp;
		</td>
		<td>
			<?php
			echo $sel_template;
			?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?php echo $_SESSION['cste']['_DIMS_LABEL_MAILINGLIST_ADDRESS']; ?>&nbsp;
		</td>
		<td>
			<input style="width:400px;" type="text" id="fck_news_address" name="fck_news_address" <? if(isset($id_news) && $id_news != '') echo 'value="'.html_entity_decode($inf_news->fields['address']).'"'; ?> />
		</td>
	</tr>
	<tr>
		<td align="right">
			<?php echo $_SESSION['cste']['_DIMS_LABEL_RESPONSIBLE']; ?>&nbsp;
		</td>
		<td>
			<?php echo $sel_resp; ?>
		</td>
	</tr>
	<tr>
		<td align="right" style="width:35%">
			<?php echo $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER_ACTIVE']; ?>&nbsp;
		</td>
		<td align="left">
			<?php echo $_SESSION['cste']['_DIMS_YES']; ?><input type="radio" id="news_etat" name="news_etat" value="1" <?php if(isset($id_news) && $id_news != '' && $inf_news->fields['etat'] == 1) echo 'checked="checked"'; ?>/>
			<?php echo $_SESSION['cste']['_DIMS_NO']; ?><input type="radio" id="news_etat" name="news_etat" value="0" <?php if(isset($id_news) && $id_news != '' && $inf_news->fields['etat'] == 0) echo 'checked="checked"'; ?>/>
		</td>
	</tr>
</table>
<?
echo  dims_create_button($_SESSION['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_rub.submit();','','float:right;padding-right:10px;');
?>
</form>
