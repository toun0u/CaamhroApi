<form name="form_etape1" method="post" action="<? echo dims_urlencode("/admin.php?op_model=addNewModelFile"); ?>" method="post" enctype="multipart/form-data">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("import_title");
		$token->field("file_import");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<div class="dims_form" style="float:left; width:80%;padding-top:20px;">
		<div style="padding:2px;">
			<span style="width:10%;display:block;float:left;">
				<? echo '<img src="'.$_SESSION['dims']['template_path'].'/media/properties32.png">'; ?>
			</span>
			<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
				<? echo $_SESSION['cste']['_DIMS_LABEL_STEP']." 1 : ".$_SESSION['cste']['_DIMS_LABEL_FILE']; ?>
			</span>
		</div>
		<div style="padding:2px;clear:both;float:left;width:100%;">
			<p>
				<label><? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label>
				<input class="text" type="text" onkeyup="javascript:importFileCheck();" style="width:350px;" id="import_title" name="import_title" value="<? echo $_SESSION['dims']['import']['import_label']; ?>" tabindex="1" />
			</p>
			<p>
				<label><? echo $_SESSION['cste']['_IMPORT_DOWNLOAD_FILE']; ?></label>
				<input type="file" name="file_import" id="file_import" class="text" tabindex="1" onchange="javascript:importFileCheck();">
			</p>

			<?
			$error =  dims_load_securvalue('error', dims_const::_DIMS_NUM_INPUT,true,false);
			if ($error>0) {
				echo "<p><label><img src=\"./common/img/warning.png\"></label>";
				switch ($error) {
					case _ASSUR_STATUT_FILE_NOT_CORRECT: // extension non correcte
						echo $_SESSION['cste']['_IMPORT_ERROR_FILE_NOT_CORRECT'];
						break;
				}
				echo "</p>";
			}
			?>
		</div>
		<div id="import_button" style="padding:2px;clear:both;float:left;width:100%;display:none;">
			<span style="width:50%;display:block;float:left;">&nbsp;</span>
			<span style="width:50%;display:block;float:left;">
				<a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_etape1.submit();">
					<img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<? echo $_SESSION['cste']['_DIMS_NEXT']; ?>">
				</a>
			</span>
		</div>
	</div>
</form>
<script language="JavaScript" type="text/JavaScript">

	function importFileCheck() {

		if ($('#import_title').val()!="" && $('#file_import').val()!="" ) $('#import_button').css('display','block');
		else $('#import_button').css('display', 'none');
	}
	$("#import_title").focus();
	importFileCheck();
</script>