<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	$action_etap=dims_load_securvalue("action2",dims_const::_DIMS_CHAR_INPUT,true,true,false);

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
	<script type="text/javascript" language="javascript" src="/common/js/upload/javascript/uploader.js"></script>
	<form name='filterform' action='<?php echo $scriptenv; ?>' method='post'>
	<input type='hidden' name='op' value='saveetap_position' />
	<table cellpadding="2" cellspacing="1" style="width:100%;background:#FFFFFF; border-collapse: collapse;">
			 <tr>
				<td valign="top" colspan="3">
				<?php
				echo "<a href=\"admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=modeletap_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL."&action2=add_etap\" onclick=\"".$js."\"><img border=\"0\" src=\"./common/img/add.gif\"/>".$_DIMS['cste']['_DIMS_EVT_STEP_ADD_ONE']."</a>";

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
				<?php echo $_DIMS['cste']['_DIMS_MODIFY'];?>/<?php echo $_DIMS['cste']['_DELETE'];?>
				</td>
			 </tr>
<?php

$sql = 'SELECT
			ee.*,
			efile.id AS id_file_etap,
			efile.id_doc AS id_file_doc
		FROM
			dims_mod_business_event_etap ee
		LEFT JOIN
			dims_mod_business_event_etap_file efile
			ON
				ee.id = efile.id_etape
		WHERE
			ee.id_model = :idmodel
		ORDER BY
			position';

$res=$db->query($sql, array(':idmodel' => $_SESSION['dims']['tmp_event_model']) );

if ($db->numrows($res)>0) {
	$class="trl1";

	$tab_etap = array();
	$tab_file = array();

	while ($value=$db->fetchrow($res)) {
		$tab_etap[$value['id']]['id']			= $value['id'];
		$tab_etap[$value['id']]['id_action']	= $value['id_action'];
		$tab_etap[$value['id']]['label']		= $value['label'];
		$tab_etap[$value['id']]['position']		= $value['position'];
		$tab_etap[$value['id']]['description']	= $value['description'];

		if(isset($value['id_file_etap']) && !empty($value['id_file_etap'])) {
			$tab_file[$value['id']][$value['id_file_etap']]['id']		= $value['id_file_etap'];
			$tab_file[$value['id']][$value['id_file_etap']]['id_doc']	= $value['id_file_doc'];
		}
	}

	$cpteused = count($tab_etap);

	foreach($tab_etap as $value) {
		$js = '';
		if ($class=="trl1") $class="trl2";
		else $class="trl1";

		echo "<tr class=\"$class\"><td>".$value['label']."</td>";
		echo "<input type=\"hidden\" name=\"use".$value['id']."\" value=\"".$value['id']."\">";
		// on affiche la position
		echo "<td>";
		echo $value['position'];
		$js = "javascript: moveEtape('".$value['id']."','-1');";
		echo '&nbsp;<img src="./common/modules/forms/img/ico_up.gif" onclick="'.$js.'"/>';
		$js = "javascript: moveEtape('".$value['id']."','1');";
		echo '&nbsp;<img src="./common/modules/forms/img/ico_down.gif" onclick="'.$js.'"/>';
		echo "</td>";

		$js = "javascript:dims_confirmlink('javascript: deleteEtap(".$value['id'].")','".$_DIMS['cste']['_DIMS_CONFIRM']."')";
		$delete = "<a href=\"javascript: void(0);\" onclick=\"".$js."\"><img src=\"./common/img/delete.png\" align=\"middle\" border=\"0\"></a>";
		$modify = "<a href=\"admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=modeletap_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL."&id_actionetap=".$value['id']."\"><img src=\"./common/img/edit.gif\" align=\"middle\" border=\"0\"></a>";
		$add_file = "<a href=\"admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=modeletap_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL."&id_actionetap=".$value['id']."\"><img src=\"./common/img/ico_newfile.png\" align=\"middle\" border=\"0\"></a>";


		echo '<td align="center">'.$modify.'&nbsp;&nbsp;'.$delete.'&nbsp;&nbsp;'.$add_file.'</td>';
		echo '</tr>';

		if(isset($tab_file[$value['id']]) && is_array($tab_file[$value['id']]) && (count($tab_file[$value['id']]) > 0)) {
			echo '<tr class="'.$class.'"><td colspan="3"><ul>';

			$doc_etape = new docfile;

			foreach($tab_file[$value['id']] as $file) {
				$doc_etape->open($file['id_doc']);

				echo '<li><a href="'.$doc_etape->getwebpath().'" target="_blank">';

				echo $doc_etape->fields['name'];
				$js = "javascript:dims_confirmlink('javascript: deleteFile(".$file['id'].")','".$_DIMS['cste']['_DIMS_CONFIRM']."')";
				echo "</a><a href=\"javascript: void(0)\" onclick=\"".$js."\"><img src=\"./common/img/delete.gif\" align=\"middle\" border=\"0\"></a>";
				echo '</li>';
			}

			echo '</ul></td></tr>';
		}
	}
}

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
$id_etap		= dims_load_securvalue('id_etap',dims_const::_DIMS_NUM_INPUT,true,false,true);

$actionetap = new action_etap();
if ($id_actionetap>0) {
	$actionetap->open($id_actionetap);
	$_SESSION['dims']['currentactionetap']=$id_actionetap;
}
if ($id_actionetap>0 || $action_etap=="add_etap") {
	if ( $action_etap=="add_etap") {
		$actionetap->init_description();
	}
   ?>
	<form name="form_event_model" action="<? echo "admin.php?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=save_model_etap_events&ssubmenu="._DIMS_ADMIN_EVENTS_MODEL; ?>" method="POST">
		<table cellpadding="2" cellspacing="1" width="80%">
			<tr>
				<td valign="top">
					<?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>
				</td>
				<td >
					<input type="text" id="actionetap_label" name="actionetap_label" value="<?php echo $actionetap->fields['label'];?>">
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
				<td align="right" colspan="2">
					<INPUT TYPE="Submit" CLASS="button" VALUE="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
				</td>
			</tr>
		</table>
	</form>
	<script language="JavaScript" type="text/JavaScript">
		//document.getElementById("actionetap_label").focus();
	</script>
	 <?php
}

/** Ajout de fichier **/
if($id_etap > 0){
	//Ouverture de l'etape pour infos
	$actionetap->open($id_etap);
	?>

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
	<input type="hidden" name="op" value="save_eventfile">
	<input type="hidden" id="id_etap_file" name="id_etap" value="<?php echo $id_etap; ?>">

	<div class="doc_fileform_main">
		<div class="dims_form" style="padding:2px;">
			<?php
			echo dims_create_button($_DIMS['cste']['_DOC_LABEL_ADD_OTHER_FILE'],'./common/img/add.gif',"javascript:createFileInput();")
			?>
			<div id="ScrollBox" style="overflow:auto;">
				<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
				<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;" src=""></iframe>
			</div>
			<span id="btn_upload" style="width:50%;display:block;float:left;">
				   <?php
					   echo dims_create_button($_DIMS['cste']['_DIMS_SEND'],'./common/img/go-up.png',"javascript:upload();")
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
	<?php
	global $dims;
	$rootpath=$dims->getProtocol().$http_host;
	echo "<script type=\"text/javascript\">status = document.getElementById(\"status\");setVariables(\"$rootpath\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']."\",\"".$_DIMS['cste']['_DOC_MSG_COPY_FILE']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']."\",\"".$_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']."\");createFileInput(path);</script>";

}
?>
