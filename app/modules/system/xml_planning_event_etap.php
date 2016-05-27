<?php

//Dans le cas ou le script a �t� apell� sans etre inclue par xml_planning_modifier_action_detail.php
if(!isset($action)) {
	$action = new action();
	$action->open($_SESSION['dims']['currentaction']);
}

$sql = 'SELECT
			ee.*,
			efile.id AS id_file_etap,
			efile.id_doc AS id_file_doc,
			efile.label AS label_file,
			efile.content AS content_file
		FROM
			dims_mod_business_event_etap ee
		LEFT JOIN
			dims_mod_business_event_etap_file efile
			ON
				ee.id = efile.id_etape
		WHERE
			ee.id_action = :idaction
		ORDER BY
			position';

$res_etap = $db->query($sql, array(
	':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
));

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	$action_etap=dims_load_securvalue("action",dims_const::_DIMS_CHAR_INPUT,true,true,false);
	?>
	<script type="text/javascript">
		var uploads = new Array();
		var upload_cell, file_name;
		var count = 0;
		var checkCount = 0;
		var check_file_extentions = true;
		var sid = '<?php echo session_id() ; ?>';
		var page_elements = ["toolbar","page_status_bar"];
		var img_path = "../common/img/";
		var path = "";
		var bg_color = false;
		var status;
		var debug = false;
		var param1=<?php echo ($op == 'file_add') ? 'true' : 'false'; ?>;
		var param2=<?php echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
	</script>
	<script type="text/javascript" language="javascript" src="/js/upload/javascript/uploader.js"></script>
	<form name='filterform' action='<?php echo $scriptenv; ?>' method='post'>
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op","saveetap_position");
	?>
	<input type='hidden' name='op' value='saveetap_position' />
	<table cellpadding="2" cellspacing="1" style="width:100%;background:#FFFFFF; border-collapse: collapse;">

			 <tr>
				<td valign="top" colspan="2">
				<?php
				$js = "dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction="._DIMS_ACTION_ETAP."&action=add_etap', '', 'block_content5');";
				echo '<a href="javascript: void(0);" onclick="'.$js.'"><img border="0" src="./common/img/add.gif"/>'.$_DIMS['cste']['_DIMS_EVT_STEP_ADD_ONE'].'</a>';
				/*
				// essai de generation de document en pdf
				echo "&nbsp;<a href=\"admin-light.php?op=export_odt&type_doc=inscription\" \"><img border=\"0\" src=\"./common/img/file_types/icon_pdf_32x32.gif\"/>Inscription</a>&nbsp;";
				echo "&nbsp;<a href=\"admin-light.php?op=export_odt&type_doc=brochure\" \"><img border=\"0\" src=\"./common/img/file_types/icon_pdf_32x32.gif\"/>Brochure</a>&nbsp;";
				echo "&nbsp;<a href=\"admin-light.php?op=export_odt&type_doc=materiel\" \"><img border=\"0\" src=\"./common/img/file_types/icon_pdf_32x32.gif\"/>Mat�riel</a>&nbsp;";
				echo "&nbsp;<a href=\"admin-light.php?op=export_odt&type_doc=transport\" \"><img border=\"0\" src=\"./common/img/file_types/icon_pdf_32x32.gif\"/>Transport</a>&nbsp;";
				*/
				?>

				</td>
				<td valign="top" align="right">
					<?php
					if ($db->numrows($res_etap)==0 && $action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') {
						echo '<a href="?op=import_etap_model&type=1">';
						echo '<img src="./common/img/data_view.png" border="0" />'.$_DIMS['cste']['_DIMS_FAIR_IMPORT_TABS'];
						echo ' : A5</a><br />';

						echo '<a href="?op=import_etap_model&type=2">';
						echo '<img src="./common/img/data_view.png" border="0" />'.$_DIMS['cste']['_DIMS_FAIR_IMPORT_TABS'];
						echo ' : Pocket</a>';
					}
					?>
				</td>
			 </tr>
			 <tr>
				<td valign="top">
				<?php echo $_DIMS['cste']['_DIMS_LABEL']; ?>
				</td>
				<td>
				<?php echo $_DIMS['cste']['_POSITION']; ?>
				</td>
				<td align="center">

				</td>
			 </tr>
<?php

if ($db->numrows($res_etap)>0) {
	$class="trl2";

	$tab_etap = array();
	$tab_file = array();
	$icoPaiement = true;

	if(empty($action->fields['prix']))
		$icoPaiement = false;

	while ($value=$db->fetchrow($res_etap)) {
		$tab_etap[$value['id']]['id']			= $value['id'];
		$tab_etap[$value['id']]['id_action']	= $value['id_action'];
		$tab_etap[$value['id']]['label']		= $value['label'];
		$tab_etap[$value['id']]['position']	= $value['position'];
		$tab_etap[$value['id']]['description']	= $value['description'];
		$tab_etap[$value['id']]['paiement']	= $value['paiement'];

		if($value['paiement'])
			$icoPaiement = false;

		if(isset($value['id_file_etap']) && !empty($value['id_file_etap'])) {
			$tab_file[$value['id']][$value['id_file_etap']]['id']		= $value['id_file_etap'];
			$tab_file[$value['id']][$value['id_file_etap']]['id_doc']	= $value['id_file_doc'];
			$tab_file[$value['id']][$value['id_file_etap']]['label']	= $value['label_file'];
			$tab_file[$value['id']][$value['id_file_etap']]['content']	= $value['content_file'];
		}
	}

	$cpteused = count($tab_etap);

	foreach($tab_etap as $value) {

		$js = '';

		//if ($class=="trl1") $class="trl2";
		//else $class="trl1";

		echo "<tr class=\"$class\"><td style=\"width:20%;\">".$value['label'];

		if($value['paiement']) {
			$js = 'javascript: unmakePayementEtap('.$value['id'].');';
			echo '&nbsp;<img src="./common/modules/system/img/fair-unfacture.png" onclick="'.$js.'"/>';
		}

		echo "<input type=\"hidden\" name=\"use".$value['id']."\" value=\"".$value['id']."\">";
		$token->field("use".$value['id']);
		echo "</td>";
		// on affiche la position
		echo "<td>";
		echo $value['position'];
		$js = "javascript: moveEtape('".$value['id']."','-1');";
		echo '&nbsp;<img src="./common/modules/forms/img/ico_up.gif" onclick="'.$js.'"/>';
		$js = "javascript: moveEtape('".$value['id']."','1');";
		echo '&nbsp;<img src="./common/modules/forms/img/ico_down.gif" onclick="'.$js.'"/>';
		echo "</td>";

		//Lien suppression etape
		$js = "javascript:dims_confirmlink('javascript: deleteEtap(".$value['id'].")','".$_DIMS['cste']['_DIMS_CONFIRM']."')";
		$delete = "<a href=\"javascript: void(0);\" onclick=\"".$js."\"><img src=\"./common/img/delete.gif\" align=\"middle\" border=\"0\" title=\"".$_DIMS['cste']['_DELETE']."\"></a>";

		//Lien modification etape
		$js = "javascript:dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction="._DIMS_ACTION_ETAP."&id_actionetap=".$value['id']."', '', 'test_temp');";
		$modify = "<a href=\"javascript: void(0);\" onclick=\"".$js."\"><img src=\"./common/img/edit.gif\" align=\"middle\" border=\"0\" title=\"".$_DIMS['cste']['_MODIFY']."\"></a>" ;

		//Lien ajout de document
		$js = "javascript:dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction="._DIMS_ACTION_ETAP."&id_etap=".$value['id']."', '', 'test_temp');";
		$add_file = '<a href="javascript: void(0);" onclick="'.$js.'"><img src="./common/img/attachment.png" align="middle" border="0" title="'.$_DIMS['cste']['_DIMS_EVT_DOC'].'"></a>';

		//Lien ajout de demande de doc
		$js = "javascript:dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction="._DIMS_ACTION_ETAP."&id_etap=".$value['id']."&typedoc=input', '', 'test_temp');";
		$add_input = '<a href="javascript: void(0);" onclick="'.$js.'"><img src="./common/img/ico_newfile.png" align="middle" border="0" title="Param&egrave;tres pour le retour des documents depuis le frontoffice"></a>';

		/*if($icoPaiement) {
			$js = 'javascript: makePayementEtap('.$value['id'].');';
			$makePayement = '<a href="javascript: void(0);" onclick="'.$js.'"><img src="./common/modules/system/img/fair-facture.png" align="middle" border="0"></a>';
		}
		else {
			$makePayement = '';
		}*/

		echo '<td align="center">'.$modify.'&nbsp;&nbsp;'.$delete.'&nbsp;&nbsp;'.$add_file.'&nbsp;&nbsp;'.$add_input.'&nbsp;&nbsp;'.$makePayement.'</td>';
		echo '</tr>';

		echo '<tr><td><b>Description</b></td><td colspan="2" style="padding: 5px;padding-left: 15px;">';
		if ($value['description']=="") {
			echo "Aucune description";
		}
		else {
			echo $value['description'];
		}
		echo '</td></tr>';
		if(isset($tab_file[$value['id']]) && is_array($tab_file[$value['id']]) && (count($tab_file[$value['id']]) > 0)) {
			echo '<tr><td><b>Documents propos�s</b></td><td colspan="2" text-align="left"><ul>';

			$doc_etape = new docfile;

			foreach($tab_file[$value['id']] as $file) {
				if($file['id_doc'] > 0) {
					$doc_etape->open($file['id_doc']);

					echo '<li><a href="'.$doc_etape->getwebpath().'" target="_blank">';
					echo $doc_etape->fields['name'];
					$js = "javascript:dims_confirmlink('javascript: deleteFile(".$file['id'].")','".$_DIMS['cste']['_DIMS_CONFIRM']."')";
					echo "</a><a href=\"javascript: void(0)\" onclick=\"".$js."\"><img src=\"./common/img/delete.png\" align=\"middle\" border=\"0\"></a>";
					echo '</li>';
				}
			}

			echo '</ul></td></tr>';

			echo '<tr><td><b>Documents � retourner</b></td><td colspan="2"><ul>';

			$doc_etape = new docfile;

			foreach($tab_file[$value['id']] as $file) {
				if($file['id_doc'] == 0) {
					echo '<li>';
					echo $file['label'];
					echo ' / ';
					echo $file['content'];

					//Lien modif de demande de doc
					$js = "javascript:dims_xmlhttprequest_todiv('admin.php', 'op=refresh_etap&subaction="._DIMS_ACTION_ETAP."&id_etap=".$value['id']."&typedoc=input&iddoc=".$file['id']."', '', 'test_temp');";

					echo "<a href=\"javascript: void(0)\" onclick=\"".$js."\"><img src=\"./common/img/edit.gif\" align=\"middle\" border=\"0\"></a>";

					$js = "javascript:dims_confirmlink('javascript: deleteFile(".$file['id'].")','".$_DIMS['cste']['_DIMS_CONFIRM']."')";

					echo "<a href=\"javascript: void(0)\" onclick=\"".$js."\"><img src=\"./common/img/delete.png\" align=\"middle\" border=\"0\"></a>";

					echo '</li>';
				}
			}

			echo '</ul></td></tr>';
		}
	}
}
$tokenHTML = $token->generate();
echo $tokenHTML;
?>
	</table>
	</form>
<?php

$id_actionetap	= 0;
$id_etap		= 0;

// gestion de l'id de l'action etape
if (!isset($_SESSION['dims']['currentactionetap'])) $_SESSION['dims']['currentactionetap']=0;
if (!isset($_SESSION['dims']['currentetapfile'])) $_SESSION['dims']['currentetapfile']=0;

$id_actionetap	= dims_load_securvalue('id_actionetap',dims_const::_DIMS_NUM_INPUT,true,false,true);
$id_etap		= dims_load_securvalue('id_etap',dims_const::_DIMS_NUM_INPUT,true,true,true);

$actionetap = new action_etap();
if ($id_actionetap>0) $actionetap->open($id_actionetap);
if ($id_actionetap>0 || $action_etap=="add_etap") {
	if ( $action_etap=="add_etap") {
		$title = $_DIMS['cste']['_DIMS_EVT_STEP_ADD_ONE'];
		$actionetap->init_description();
	}
	else {
		$title = "Modifier l'&eacute;tape : ".$actionetap->fields['label'];
	}
   ?>
   <div style="position:absolute;z-index:9;width:600px;height:350px;left:250px;top:150px;display:block;" id="mod_etap">
	<? echo $skin->open_simplebloc($title); ?>
	<form onsubmit="javascript: return false;">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("actionetap_label");
			$token->field("actionetap_type_etape");
			$token->field("actionetap_description");
			$token->field("actionetap_date_fin");
			$token->field("actionetap_condition");
			$token->field("actionetap_condition_content");
			$token->field("actionetap_condition_label_yes");
			$token->field("actionetap_condition_label_no");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<table cellpadding="2" cellspacing="1" width="100%">
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>
				</td>
				<td >
					<input type="text" id="actionetap_label" name="actionetap_label" value="<?php echo $actionetap->fields['label'];?>" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_TYPE']; ?>
				</td>
				<td>
					<select name="actionetap_type_etape" id="actionetap_type_etape">
						<option value="0" <? if(empty($actionetap->fields['type_etape'])) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_FORM_TASK_PRIORITY_0']; ?></option>
						<option value="1" <? if($actionetap->fields['type_etape'] == 1) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_OBJECT_RESUME']; ?></option>
						<option value="2" <? if($actionetap->fields['type_etape'] == 2) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_FAIRS_ACCUSE_RECEPTION']; ?></option>
						<option value="4" <? if($actionetap->fields['type_etape'] == 4) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_FIELD_FORMFIELD']; ?></option>
						<option value="5" <? if($actionetap->fields['type_etape'] == 5) echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_FAIRS_PAIEMENT']; ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
				</td>
				<td>
					<textarea class="text" style="width:300px;height:80px;" id="actionetap_description" name="actionetap_description"><?php echo ($actionetap->fields['description']); ?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_INFOS_END_DATE']; ?>
				</td>
				<td>
					<?php
						if(empty($actionetap->fields['date_fin'])) {
							$date_fin = '';
						}
						else {
							$date = dims_timestamp2local($actionetap->fields['date_fin']);
							$date_fin = $date['date'];
						}
					?>
					<input type="text" id="actionetap_date_fin" name="actionetap_date_fin" value="<?php echo $date_fin;?>" />
					<a href="#" onclick="javascript:dims_calendar_open('actionetap_date_fin', event,'updateDate()');">
						<img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0">
					</a>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_FAIR_ETAP_CONDITION']; ?>
				</td>
				<td>
					<?php
						$check = '';

						if($actionetap->fields['condition'] == 1) {
							$check = 'checked="checked"';
						}
					?>
					<input type="checkbox" name="actionetap_condition" id="actionetap_condition" <?php echo $check; ?>/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_FAIR_ETAP_CONDITION_LABEL']; ?>
				</td>
				<td>
					<input type="text" id="actionetap_condition_content" name="actionetap_condition_content" value="<?php echo $actionetap->fields['condition_content'];?>" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_FAIR_ETAP_CONDITION_LABEL_YES']; ?>
				</td>
				<td>
					<textarea id="actionetap_condition_label_yes" name="actionetap_condition_label_yes"><?php echo $actionetap->fields['condition_label_yes'];?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_FAIR_ETAP_CONDITION_LABEL_NO']; ?>
				</td>
				<td>
					<textarea id="actionetap_condition_label_no" name="actionetap_condition_label_no"><?php echo $actionetap->fields['condition_label_no'];?></textarea>
				</td>
			</tr>
			<tr>
				<td align="right" colspan="2">
					<?php

					echo dims_create_button($_DIMS['cste']['_DIMS_VALID'],"./common/img/checkdo.png","javascript: addEtap(".$id_actionetap.");", '', 'float:right;');
					//RMQ: on fait un reload pour qu'une eventuelle modif ne soit pas enregistr�e
					echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"./common/img/delete.png","document.location.reload();", '', 'float:left;');

					?>
				</td>
			</tr>
		</table>
	</form>
	<script language="JavaScript" type="text/JavaScript">
		//document.getElementById("actionetap_label").focus();
	</script>
	<? echo $skin->close_simplebloc(); ?>
   </div>
	 <?php
}

/** Ajout de fichier **/
if($id_etap > 0){
	//Ouverture de l'etape pour infos
	$actionetap->open($id_etap);

	//R�cup�ration du type de "doc"
	$typedoc = '';
	$typedoc = dims_load_securvalue('typedoc', dims_const::_DIMS_CHAR_INPUT, true,true);

	switch ($typedoc) {
		//Cas classique : Un document de r�f�rence, un document a renvoyer
		default:

			?>
			<div style="position:absolute;z-index:9;width:600px;height:350px;left:250px;top:150px;display:block;" id="div_pj">
			<? echo $skin->open_simplebloc(); ?>
			<link type="text/css" rel="stylesheet" href="./common/modules/doc/include/styles.css" media="screen" />
			<div class="doc_fileform">
			<?php
			require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");

			// on supprime ce qu'il peut y avoir en temporary
			$sid = session_id();
			$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
			$session_dir = $temp_dir.$sid;
			$upload_size_file = $session_dir."/upload_size";
			$upload_finished_file = $session_dir."/upload_finished";

			if (file_exists($upload_size_file)) unlink($upload_size_file);
			if (file_exists($upload_finished_file)) unlink($upload_finished_file);

			$docfile = new docfile();
			$docfile->init_description();
			?>
			<form id="docfile_add" name="docfile_add" action="javascript: addFile();" method="post" enctype="multipart/form-data" onsubmit="javascript: addFile();">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("op",			"save_eventfile");
				$token->field("id_etap",	$id_etap);
				$token->field("uploadForm");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="op" value="save_eventfile">
			<input type="hidden" id="id_etap_file" name="id_etap" value="<?php echo $id_etap; ?>">

			<div class="doc_fileform_main">
				<div style="padding:2px;">
					<?php
					echo dims_create_button($_DIMS['cste']['_DOC_LABEL_ADD_OTHER_FILE'],'./common/img/add.gif',"javascript:createFileInput();")
					?>
					<div id="ScrollBox" style="widht:100%;overflow:auto;">
						<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
						<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;" src=""></iframe>
					</div>
					<span id="btn_upload" style="display:block;float:left;width:50%">
						   <?php
							   echo dims_create_button($_DIMS['cste']['_DIMS_SEND'],'./common/img/go-up.png',"javascript:upload();" , '', 'float:right;');

							   echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"./common/img/delete.png","javascript:dims_switchdisplay('div_pj');", '', 'float:left;');
						   ?>
					</span>
				</div>
			</div>
			<div id="sharefile_button" style="padding-top:20px;clear:both;float:left;width:100%;">
				 <?php
				 /*<span style="width:50%;display:block;float:left;text-align:right;"><a style="text-decoration:none;padding-right:50px;" href="<? echo dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=2"); ?>"><img style="border:0px;" src="./common/modules/sharefile/img/back.png" alt="<? echo $_DIMS['cste']['_DIMS_PREVIOUS']; ?>"></a></span>*/
				 ?>
			</div>
			</form>
			<? echo $skin->close_simplebloc(); ?>
			</div>
			<?php
			global $dims;
			$rootpath=$dims->getProtocol().$http_host;
			echo "<script type=\"text/javascript\">status = document.getElementById(\"status\");setVariables(\"$rootpath\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']."\",\"".$_DIMS['cste']['_DOC_MSG_COPY_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']."\");createFileInput(path);</script>";

			break;

		case 'input':

			$idDoc = 0;
			$idDoc = dims_load_securvalue('iddoc', dims_const::_DIMS_CHAR_INPUT, true,true);

			if(empty($_POST)) {

				$etapFile = new etap_file();
				$etapFile->open($idDoc)
				?>
				<div style="position:absolute;z-index:9;width:600px;height:350px;left:250px;top:150px;display:block;">
				<? echo $skin->open_simplebloc('Param&egrave;tres pour le retour des documents'); ?>
				<div class="doc_fileform">
					<form method="post" name="docfile_add">
						<?
							// Sécurisation du formulaire par token
							require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
							$token = new FormToken\TokenField;
							$token->field("typedoc",	"input");
							$token->field("id_etap",	$id_etap);
							$token->field("iddoc",$idDoc);
							$token->field("input_label",);
							$token->field("input_content");
							$tokenHTML = $token->generate();
							echo $tokenHTML;
						?>
						<!--<input type="hidden" name="op" value="save_eventfile_noref">-->
						<input type="hidden" name="typedoc" value="input">
						<input type="hidden" name="id_etap" value="<?php echo $id_etap; ?>">
						<input type="hidden" name="iddoc" value="<?php echo $idDoc; ?>">

						<?php echo $_DIMS['cste']['_DIMS_LABEL']; ?><br />
						<input type="text" name="input_label" value="<?php echo $etapFile->fields['label']; ?>" /><br />
						<?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']; ?><br />
						<textarea name="input_content"><?php echo $etapFile->fields['content']; ?></textarea><br />
						<?php
							echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_ADD'],'./common/img/checkdo.png',"javascript:docfile_add.submit();", '', 'float:right;');
							//addFileNoRef(".$id_etap.");");
							echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"./common/img/delete.png","document.location.reload();", '', 'float:left;');
						?>
					</form>
				</div>
				<? echo $skin->close_simplebloc(); ?>
				</div>
				<?php
			}
			else {
				$etap_file = new etap_file();

				if($idDoc == 0) {
					$etap_file->init_description();
				}
				else {
					$etap_file->open($idDoc);
				}

				$etap_file->setvalues($_POST, 'input_');

				$etap_file->fields['label'] = ($etap_file->fields['label']);
				$etap_file->fields['content'] = ($etap_file->fields['content']);

				$etap_file->fields['id_action']=$_SESSION['dims']['currentaction'];
				$etap_file->fields['id_etape']=$id_etap;
				$etap_file->fields['id_doc']=0;

				$etap_file->save();

				dims_redirect("admin.php?op=xml_planning_modifier_action&id=".$_SESSION['dims']['currentaction'], true);
			}
			break;
	}
}
?>
<div id="test_temp">
</div>
