<?php

require_once(DIMS_APP_PATH . "/modules/system/desktop_mailinglist_left.php");

$id_env = dims_load_securvalue('id_env',dims_const::_DIMS_CHAR_INPUT,true,true);
$sent = dims_load_securvalue('sent',dims_const::_DIMS_NUM_INPUT,true,true);

$inf_env = new list_diff_content();

if(isset($id_env) && $id_env != '') {
	$inf_env->open($id_env);

	//on verifie si l'envoi est deja lie
	$sql_env =	"SELECT			l.*
				 FROM			dims_mailing_list l
				 INNER JOIN		dims_mailing_content_list cl
				 ON				cl.id_list = l.id
				 AND			cl.id_content = :idenv ";

	$res_env = $db->query($sql_env, array(
		':idenv' => $id_env
	));

	if ($db->numrows($db->query("SELECT * FROM dims_mailing_content WHERE id = :idenv ", array(':idenv' => $id_env))) == 1)
		$exist = true ;
	else $exist = false ;

}
else {
	$inf_env->init_description();
}

$title = $_DIMS['cste']['_DIMS_LABEL_MAILING_ADD_EMAIL'];
if($exist) $title = $_DIMS['cste']['_DIRECTORY_EMAIL'].' : '.$inf_env->fields['subject'] ;

echo '<div style="width:65%;float:right;margin-right:20px;">';
echo $skin->open_simplebloc($title);
?>
	<form name="newsletter_env" id="newsletter_env" method="POST" action="admin.php?dims_mainmenu=<? echo dims_const::_DIMS_MENU_HOME; ?>&dims_desktop=block&dims_action=public&action=save_sending" enctype="multipart/form-data">
	<?php
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_env",	$id_env);
		$token->field("env_subject");
		$token->field("env_template");
		$token->field("list_att");
		$token->field("email_list");
		$token->field("add_email");
		$token->field("fck_env_content");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
		if($exist) {
	?>
		<input type="hidden" name="id_env" value="<?php echo $id_env; ?>"/>
	<?php
		}
	?>
	<table width="100%" cellpadding="0" cellspacing="5">
		<tr>
			<td colspan="2" align="left" style="font-size:15px;">
			</td>
		</tr>
		<tr>
			<td align="right" width="20%">
				<?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?>&nbsp;*&nbsp;
			</td>
			<td>
				<input type="text" id="env_subject" name="env_subject"
					<? if(isset($id_env) && $id_env != '')
							echo 'value="'.$inf_env->fields['subject'].'"';
						elseif(isset($_SESSION['dims']['mailing']['content']))
							echo 'style="background-color: #FE9292;"';
					?> />
			</td>
		</tr>
		<tr>
			<td align="right" width="20%">
				<?php echo $_DIMS['cste']['_FORMS_MODEL']; ?>&nbsp;
			</td>
			<td>
				<select class="select" name="env_template">
						<option value="">
				<?
				$sql="	select		*
						from		dims_workspace_template where id_workspace= :workspaceid ";

				$res=$db->query($sql, array(
					':workspaceid' => $_SESSION['dims']['workspaceid']
				));

				// collecte la liste des templates disponibles
				$availabletpl = dims_getavailabletemplates();

				while ($f=$db->fetchrow($res)) {
					if (in_array($f['template'],$availabletpl)) {
						$select=($inf_env->fields['template']==$f['template']) ? "selected='selected'" : '';

						echo "<option ".$select.">".$f['template']."</option>";
					}
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right">
				<?php echo $_DIMS['cste']['_CONTENT']; ?>&nbsp;
			</td>
			<td>
				<?
				if(isset($id_env) && $id_env != '') $content= $inf_env->fields['content'];
				elseif(isset($_SESSION['dims']['mailing']['content'])){
					$content = $_SESSION['dims']['mailing']['content'] ;
					unset($_SESSION['dims']['mailing']['content']);
				}else $content="";
				dims_fckeditor("env_content",$content,"800","350");
				?>
			</td>
		</tr>
		<?php
			// fichier joint ( un seul fichier peux être joint)
				if($exist) {
					$id_module = $_SESSION['dims']['moduleid'];
					$id_object = dims_const::_SYSTEM_OBJECT_LIST_DIFF;
					$id_record = $inf_env->fields['id'];
					$doc = '';
					require_once DIMS_APP_PATH.'include/functions/files.php';
					$lstfiles = dims_getFiles($dims,$id_module,$id_object,$id_record);
					if(isset($lstfiles) && $lstfiles != '') {
						foreach($lstfiles as $key => $file) {
							if($doc != '') $doc .= '<br/>';
							$doc .= '<a href='.$file['downloadlink'].' title="'.$file['name'].' - Voir le document.">'.$file['name'].'</a>';
							$doc .= "<a href=\"javascript:dims_confirmlink('".dims_urlencode("$scriptenv?dims_op=doc_file_delete&docfile_id=".$file['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\"></a>";
							$_SESSION['dims']['uploadfile']['url'] = $scripenv.'?action=add_sending&id_env='.$id_env;
						}
					}
		 ?>
		<tr>
			<td align="right">
				<?php echo $_DIMS['cste']['_DIMS_LABEL_PIECE_JOINTE']; ?>&nbsp;
			</td>
			<td align="left">
				<?php
					if($doc == '') {
						echo dims_createAddFileLink($id_module,$id_object,$id_record,'float:left;');
					}
					else {
						echo $doc;
					}
				?>
			</td>
		</tr>
		<?php
		// ajout mailing list
					if($db->numrows($res_env) > 0) {
						$res_li = $db->query("SELECT		ml.*
											  FROM			dims_mailing_list ml
											  WHERE			ml.id_user = :userid
											  AND			ml.id_workspace = :workspaceid
											  AND			ml.id NOT IN (SELECT	mcl.id_list
																		  FROM		dims_mailing_content_list mcl
																		  WHERE		mcl.id_content = :idenv )"
											,array(
												':userid'		=> $_SESSION['dims']['userid'],
												':workspaceid'	=> $_SESSION['dims']['workspaceid'],
												':idenv'		=> $id_env
											));
					}else{
						$res_li = $db->query("SELECT		*
											  FROM			dims_mailing_list
											  WHERE			id_user = :userid
											  AND			id_workspace = :workspaceid "
											,array(
												':userid'		=> $_SESSION['dims']['userid'],
												':workspaceid'	=> $_SESSION['dims']['workspaceid']
											));
					}

					if($db->numrows($res_li) > 0) {
		?>
		<tr>
			<td align="right" valign="top">
				<?php echo $_DIMS['cste']['_DIMS_LABEL_MAILING_TO_ATTACH']; ?> :
			</td>
			<td align="left">
				<select id="list_att" name="list_att" style="float: left;">
					<option>--</option>
					<?php
						while($tab_r = $db->fetchrow($res_li)) {
							echo '<option value="'.$tab_r['id'].'">'.$tab_r['label'].'</option>';
						}
					?>
				</select>
				<?
				// bouton ajout mailing list
						echo  dims_create_button($_DIMS['cste']['_DIMS_NEWSLETTER_ADD_LIST_MAILING'], './common/img/add_user.png', 'javascript:document.newsletter_env.submit();', '', 'float:left;');

				?>
			</td>
		</tr>
		<?php
					}

			// affichage mailing list déjà liées
					if($db->numrows($res_env) > 0) {
		?>
		<tr>
			<td align="right" valign="top">
				<?php	echo $_DIMS['cste']['_DIMS_NEWSLETTER_YOUR_MAILING_LIST']; ?> :
			</td>
			<td align="left">
				<?php
						$list_diff = '';
						while($tab_l = $db->fetchrow($res_env)) {
							$list_diff .= '<a href="admin.php?&action=add_mailinglist&id_mail='.$tab_l['id'].'">'.$tab_l['label'].'</a>
												<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\''.$scriptenv.'?action=delete_link&id_env='.$id_env.'&id_list='.$tab_l['id'].'\', \''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');"><img src="./common/img/delete.png"/></a>, ';
						}
						$list_diff = substr($list_diff, 0, -2);
						echo $list_diff;
				?>
			</td>
		</tr>
			<?
					}
			?>
		<tr>
			<td align="right" valign="top">
				<?php
				// import de contacts : fichiers & email
					echo $_DIMS['cste']['_LABEL_ADMIN_IMPORT_CT'];
				?> :
			</td>
			<td align="left">
				<div style="float:left; width:50%;>"
					<div style="float:left; width:100%;">
						<input style="float:left;" type="file" name="email_list" id="email_list">&nbsp;
					<?php
					// bouton ajout fichier
						echo  dims_create_button($_DIMS['cste']['_IMPORT_DOWNLOAD_FILE'], './common/img/data_view.png', 'javascript:document.newsletter_env.submit();', '', 'float:left;');
						if (isset($_SESSION['dims']['mailing']['file'])){
							unset($_SESSION['dims']['mailing']['file']);
							echo '<br><div style="color: red; float:left; width:100%">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT_DANGER'].'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_TMP_IMPORT'].'</div>';
						}
						else
							echo '<br><div style="float:left; width:100%">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_IMPORT_DANGER'].'&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_TMP_IMPORT'].'</div>';
						echo '</div>';

					// bouton ajout email
						echo '<div style="float:left; width:100%;margin-top:10px;">';
						$st = "";
						$em = "";
						if (isset($_SESSION['dims']['mailing']['erreur_mail'])){
							$em = $_SESSION['dims']['mailing']['erreur_mail'];
							unset ($_SESSION['dims']['mailing']['erreur_mail']);
							$st = " background-color: #FE9292;";
						}
						echo	'<input style="float:left;'.$st.'" type="text" name="add_email" id="add_email" value="'.$em.'">&nbsp;';
						echo	dims_create_button($_DIMS['cste']['_DIMS_LABEL_MAILING_ADD_EMAIL'], './common/img/add_user.png', 'javascript:document.newsletter_env.submit();', '', 'float:left;');
						echo '</div>';
					?>
				</div>
					<?
						if(isset($_SESSION['dims']['tmp_mailing'][$id_env])) {
							// bouton supprimer tous les emails importés
							echo '<div style="float:left; width:50%;">';
							echo	'<div style="float:right; margin-right:30px;">';
							echo		$_DIMS['cste']['_DELETE'].'&nbsp;:&nbsp;
										<a href="'.$scriptenv.'?action=delete_mail_list&id_env='.$id_env.'&id_mail=0">
											<img src="./common/img/delete.gif"/>
										</a>' ;
							echo	'</div>';
							echo '</div>';

							$st ="";
							if (count($_SESSION['dims']['tmp_mailing'][$id_env]) > 12)
								echo '<div style="height:240px; width:50%;overflow:auto;float:left;">';
							else
								echo '<div style="width:50%;overflow:auto;float:left;">';
					?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<?php
							$cl = "trl2";
							foreach($_SESSION['dims']['tmp_mailing'][$id_env] as $id_mail => $mail) {
								($cl == 'trl1') ? $cl = "trl2" : $cl = "trl1";
								$id_email = $id_mail +1 ;
								echo '<tr class="'.$cl.'">
										<td style="height:20px;">&nbsp;'
											.$mail.'
										</td>
										<td style="width:30px;">
											<a href="'.$scriptenv.'?action=delete_mail_list&id_env='.$id_env.'&id_mail='.$id_email.'"><img src="./common/img/delete.png"/></a>
										</td>
									</tr>';
									//on pourrait ajouter un lien pour la suppression ... pas le temps aujourd'hui ...
							}
					?>
				</table>
				</div>
				<?
						}
				}
				?>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<p style="text-align:center;margin:0 auto;width:400px;margin-top:20px;">
			   <?php
				if ($exist) {

					$res_v = $db->query("SELECT		id_list
										 FROM		dims_mailing_content_list
										 WHERE		id_content = :idcontent
										 LIMIT		0,1", array(
								':idcontent' => $id_env
							));
					$link_env = '';
					if($db->numrows($res_v) == 1) {
						echo  dims_create_button($_DIMS['cste']['_DIMS_SEND'], './common/img/mail_sent.png', 'javascript:dims_confirmlink(\''.$scriptenv.'?action=send_mail&id_env='.$id_env.'\',\''.$_DIMS['cste']['_DIMS_CONFIRM'].'\');', '', 'float:right;');
					}

					echo  dims_create_button($_DIMS['cste']['_PREVIEW'], './common/img/view.png', 'javascript:displayPreviewNewsletter('.$id_env.');', '', 'float:right;');
					echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_env.submit();', '', 'float:right;');
				}else {
					echo  dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'], './common/img/go-next.png', 'javascript:document.newsletter_env.submit();', '', 'float:right;');
				}
			   ?>
				</p>
			</td>
		</tr>
	</table>
	</form>
<?php
echo $skin->close_simplebloc();
echo '</div>';

echo '<script language="JavaScript" type="text/JavaScript">
		window.onload=function(){
	document.getElementById("env_subject").focus();
}
</script>';
?>
