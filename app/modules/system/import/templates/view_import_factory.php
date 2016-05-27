<?php

require_once DIMS_APP_PATH . '/modules/system/import/global.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

class view_import_factory {
		public static function new_import($global_linked = 0) {
			global $dims;
			$db = dims::getInstance()->getDb();
			global $_DIMS;

			$content_contact_import = '<div style="margin:10px;width:100%>">
					<form action="/admin.php?import_op='._OP_UPLOAD_FILE.'" method="post" enctype="multipart/form-data" id="import_step1">';
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$content_contact_import .= '<table cellspacing="5" cellpadding="0">';

			if($global_linked > 0){
				$fichiers_modele = import_fichier_modele::getFichiersModeleObject($global_linked);
				if(!empty($fichiers_modele)){
						$content_contact_import .= '<tr>
								<td align="right">
									'.$_SESSION['cste']['_DIMS_LABEL_IMPORT_FICHIER_MODEL_SELECT'].' :
								</td>
								<td>
									<select name="fichier_modele">';
						$token->field("fichier_modele");

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

			$content_contact_import .= '<tr>
								<td align="right">
										'.$_DIMS['cste']['_DIMS_LABEL_IMPORTSRC'].'&nbsp;*:&nbsp;
								</td>
								<td>
									&nbsp;<input type="file" name="import_filesource"/>&nbsp;
									<!--<a style="text-decoration:none;" onclick="javascript:displayImportExample(event,\'ct\');">
											Example
									</a>-->
								</td>
							</tr>';
			$token->field("import_filesource");

			if($dims->isAdmin() || $dims->isManager() || $_SESSION['dims']['userid'] == '151') { //exception pour michele diederich
				$content_contact_import .= '<tr>
											<td align="right">
													'.$_SESSION['cste']['_IMPORT_USER_WHO_IMPORT'].'&nbsp;
											</td>
											<td>&nbsp;<select name="user_import">';
				$token->field("user_import");
				$workspace = new workspace();
				$workspace->open($_SESSION['dims']['workspaceid']);
				$users = $workspace->getusers();
				$content_contact_import .= '<option value="0">-</option>';
				foreach($users as $userid => $user){
					$selectedid='';
					if ($userid==$_SESSION['dims']['userid']) $selectedid=" selected ";
					$content_contact_import .= '<option value="'.$userid.'"'.$selectedid.'>'.$user['firstname'].' '.$user['lastname'].'</option>';
				}

				$content_contact_import .= '</select>
													</td>
											</tr>';

				/*
				// import entreprise
				$content_contact_import .= '<tr><td align="right">'.$_SESSION['cste']['_IMPORT_ENT_WHO_IMPORT'].'&nbsp;
												</td><td>&nbsp;<select name="ent_import">';

				$res=$db->query("select * from dims_mod_business_tiers order by intitule");
				$content_contact_import .= '<option value="0">-</option>';
				if ($db->numrows($res)>0) {
					while ($ent=$db->fetchrow($res)) {
							$content_contact_import .= '<option value="'.$ent['id'].'">'.$ent['intitule'].'</option>';
					}
				}*/
				$content_contact_import .= '</select></td></tr>';
			}

			// gestion des tags
			/*
			$_SESSION['dims']['tag_temp']=array();
			$content_contact_import .= '<tr>
							<td align="right" valign="top">
									<div style="margin-top:5px;">'.$_DIMS['cste']['_DIMS_LABEL_TAGS'].'&nbsp;</div>
							</td>
							<td><span style="float:left;text-align:left;width:45%;margin-left:5px;" id="tagblockdisplay">';
			$content_contact_import .= dims_getBlockTag($dims, $_DIMS, $_SESSION['dims']['moduleid'], _SYSTEM_OBJECT_CONTACT, 0);
			$content_contact_import .='</span></td></tr>';
			*/
			$content_contact_import .= '	</table';
			$tokenHTML = $token->generate();
			$content_contact_import .=  $tokenHTML;
			$content_contact_import .= '</form>
									</div>
									<div style="text-align:center;width:100%;float:left;">'.
										dims_create_button($_SESSION['cste']['_SYSTEM_LABELTAB_USERIMPORT'], "./common/img/go-down.png", "dims_getelem('import_step1').submit();").''.
										dims_create_button($_SESSION['cste']['_DIMS_BACK'], "cancel", "document.location.href='".$dims->getScriptEnv()."?mode=import_data&import_op="._OP_DEFAULT_IMPORT."';").'
									</div>';

			echo $content_contact_import;
		}

		public static function buildAccueil(){
			global $_DIMS;
			?>
			<div>
				<h1>
					<img src="./common/img/icon_gclients.png" />
					<? echo $_SESSION['cste']['_DIMS_LABEL_IMPORT_MANAGEMENT']; ?>
				</h1>
			</div>
			<?
			// History
			echo '<div style="float:left;width:32%;text-align:center;margin:2px auto;">
					<img src="'.$_SESSION['dims']['template_path'].'/media/history32.png">
				<br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?action=get_history").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_HISTORY'].'"/>
			</div>';
			// new file import
			echo '<div style="float:left;width:33%;text-align:center;margin:2px auto;">
					<img src="./common/img/doc_add.png">
				<br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?import_op="._OP_NEW_IMPORT).'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_IMPORT_DOWNLOAD_FILE'].'"/>
			</div>';
			// manage model
			echo '<div style="float:left;width:33%;text-align:center;margin:2px auto;">
					<img src="./common'.$_SESSION['dims']['template_path'].'/media/add_table32.png">
				<br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?import_op="._OP_MODULE_IMPORT).'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_MODELS_MGT'].'"/>
			</div>';
		}


		public static function buildImportListFiles() {
			echo '<div style="float:left;clear:both;width:99%;margin:2px auto;" id="listeErrors">';

			global $_DIMS;
			global $skin;
			// construction du tableau de présentation des champs
			$data =array();
			$elements=array();

			// collecte des données de la base
			$import = new dims_import();
			$fields = $import->getImportsFromUser($_SESSION['dims']['userid']);

			// headers
			$data['headers'][]=$_DIMS['cste']['_DIMS_LABEL_COMPANY'];
			$data['headers'][]=$_DIMS['cste']['_DIMS_LABEL_TEMPLATE'];
			$data['headers'][]=$_DIMS['cste']['_DIMS_DATE'];
			$data['headers'][]=$_DIMS['cste']['_LABEL_ADMIN_NBLINES'];
			$data['headers'][]=$_DIMS['cste']['_INFOS_STATE'];
			$data['headers'][]=$_DIMS['cste']['_DIMS_ACTIONS'];

			// construction des données à afficher
			foreach ($fields as $f) {
				$elem=array();

				$elem[0]=$f['intitule'];
				$elem[1]=$f['libellemodelfile'];
				$elem[2]=dims_timestamp2cleanprint($f['timestp_create'], 1);
				$elem[3]=$f['nbelements'];

				switch ($f['status']) {
					case _IMPORT_STATUT_FILE_NOT_CORRECT:
						$elem[4]='<img src ="/modules/system/img/ico_point_red.gif">&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_INCORRECT_FILE'];
						$elem[5]='';
						break;
					default:
					case _IMPORT_STATUT_FILE_IMPORTED:
						$elem[3] = ($f['nbelements']-$f['nb_restants']).'/'.$f['nbelements'];
						$elem[4]='<img src ="/modules/system/img/ico_point_grey.gif">&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_FILE_IMPORTED'];
						$elem[5]='<a href="/admin.php?action=loadDataFromTableTemp&id_import='.$f['id'].'"><img src="./common/img/go-next.png"></a>';
						break;
					case _IMPORT_STATUT_IMPORT_IN_PROGRESS:
						$elem[3] = ($f['nbelements']-$f['nb_restants']).'/'.$f['nbelements'];
						$elem[4]='<img src ="/modules/system/img/ico_point_orange.gif">&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_IN_PROGRESS'];
						$elem[5]='<img onclick="javascript:showErrorImport('.$f['id'].');" src="./common/img/event.png" style="cursor:pointer;" /><img onclick="javascript:restartImport('.$f['id'].',\'Etes-vous sur ?\');" src="/common/modules/assurance/img/icon_invert.png" style="cursor:pointer"/>';
						break;
					case _IMPORT_STATUT_DATE_IMPORTED:
						$elem[4]='<img src ="/modules/system/img/ico_point_green.gif">&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_IMPORT_FINISHED'];
						$elem[5]='';
						break;
				}

			   //nbelements
			   $elements[]=$elem;
			}

			//elements of table
			$data['data']['elements']=$elements;
			echo $skin->displayArray($data);
			echo '</div>';
				?>
				<script type="text/javascript">
						$(document).ready(function(){$("div#listeErrors table.display tr:first th:eq(2)").trigger('click.DT'); $("div#listeErrors table.display tr:first th:eq(2)").trigger('click.DT');});
				</script>
				<?
		}

}

?>
