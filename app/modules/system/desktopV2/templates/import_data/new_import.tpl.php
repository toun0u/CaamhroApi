<?php

?>
<div style="margin:10px;width:100%>">
	<form action="/admin.php?import_op=<? echo _OP_UPLOAD_FILE; ?>" method="post" enctype="multipart/form-data" id="import_step1">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("fichier_modele");
			$token->field("import_filesource");
			$token->field("user_import");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<table cellspacing="5" cellpadding="0">

			<?
			if(isset($global_linked) && $global_linked > 0){
				$fichiers_modele = import_fichier_modele::getFichiersModeleObject($global_linked);
				if(!empty($fichiers_modele)){
						$content_contact_import .= '<tr>
								<td align="right">
									'.$_SESSION['cste']['_DIMS_LABEL_IMPORT_FICHIER_MODEL_SELECT'].' :
								</td>
								<td>
									<select name="fichier_modele">';

						foreach ($fichiers_modele as $fic_m) {
							$content_contact_import .= '<option value="'.$fic_m->getId().'">'.$fic_m->getLibelle().'</option>';
						}


						$content_contact_import .= ' </select>
											</td>
										</tr>';
				}else{
					$content_contact_import .= $_SESSION['cste']['_DIMS_LABEL_IMPORT_NO_FICHIER_MODEL_SELECT'];
				}

			}
			?>

			<tr>
				<td align="right">
					<? echo $_SESSION['cste']['_DIMS_LABEL_IMPORTSRC']; ?>&nbsp;*:&nbsp;
				</td>
				<td>
					&nbsp;<input type="file" name="import_filesource"/>&nbsp;
				</td>
			</tr>

			<?/*
			if($dims->isAdmin() || $dims->isManager() || $_SESSION['dims']['userid'] == '151') { //exception pour michele diederich ==> Han !!! c'est moche
				?>
				<tr>
					<td align="right">
						<? echo $_SESSION['cste']['_IMPORT_USER_WHO_IMPORT']; ?>&nbsp;
					</td>
					<td>
						&nbsp;<select name="user_import">
						<option value="0">-</option>
						<?
						$workspace = new workspace();
						$workspace->open($_SESSION['dims']['workspaceid']);
						$users = $workspace->getusers();
						foreach($users as $userid => $user){
							$selectedid='';
							if ($userid==$_SESSION['dims']['userid']) $selectedid=" selected ";
							?>
							<option value="<? echo $userid; ?>"<? echo $selectedid; ?>><? echo $user['firstname'].' '.$user['lastname']; ?></option>
							<?
						}
						?>

						</select>
					</td>
				</tr>
				<?
			}*/
			?>
		</table
	</form>
</div>
<div style="text-align:center;width:100%;float:left;">
	<? echo dims_create_button($_SESSION['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();"); ?>
	<? echo dims_create_button($_SESSION['cste']['_DIMS_BACK'], "cancel", "document.location.href='".$dims->getScriptEnv()."?mode=import_data&import_op="._OP_DEFAULT_IMPORT."';"); ?>
</div>
