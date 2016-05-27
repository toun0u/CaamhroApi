<?php
require_once 'modules/system/intervention/global.php';
/**
 * Description of view_case
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class view_case {

	public static function buildViewCase(dims_case $case = null,$id_popup = 0) {
		global $dims;
		$lst = $case->searchGbLink();
		ksort($lst);
		?>
		<script type="text/javascript">
			var i = 0;
		</script>
		<div id="bank_account_detail">
			<div class="actions">
				<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
					<img src="modules/notaire/templates/backoffice/img/icon_close.gif" />
				</a>
			</div>
			<h2>
				<? echo $_SESSION['cste']['_DOC_FOLDER']; ?> : <? echo $case->getLabel(); ?>
			</h2>
			<div class="displayCase" style="border-bottom:1px solid #D1D1D1;width:100%;">
				<span style="width:250px;">
					<span style="clear:both;width:240px;font-weight:bold;padding-bottom:4px;margin-left:10px;">
						Propri&eacute;t&eacute;s du dossier
					</span>
					<div style="margin-left:20px;">
						<? echo $_SESSION['cste']['_INFOS_START_DATE']; ?> : <? if ($case->getDatestart() > 0){ $d = dims_timestamp2local($case->getDatestart()); echo $d['date']; } ?>
					</div>
					<div style="margin-left:20px;margin-bottom:10px;">
						<? echo $_SESSION['cste']['_INFOS_END_DATE']; ?> : <? if ($case->getDateend() > 0){ $d = dims_timestamp2local($case->getDateend()); echo $d['date']; }else echo $_SESSION['cste']['_DIMS_LABEL_UNDEFINED']; ?>
					</div>
				</span>
				<?
								if (!isset($lst[dims_const::_SYSTEM_OBJECT_DOCFILE])){
				?>
				<span style="width:400px;vertical-align:top;">
					<span style="width:340px;text-align:right;cursor:pointer;" onclick="javascript:$('div#addFileCase').fadeToggle('fast');if (i == 0){ createFileInput(); i++;}">
						<img src="modules/notaire/img/icon_plus.png" />
						<? echo $_SESSION['cste']['_DIMS_LABEL_ADD_FILE']; ?>
					</span>
					<div id="addFileCase" style="display:none;margin-left:50px;margin-bottom:10px;">
						<form method="POST" id="docfile_add" name="docfile_add" action="<? echo $dims->getScriptEnv().'?dims_op=case_manager&case_op='._OP_ADD_FILE_CASE; ?>" enctype="multipart/form-data">
							<?
								// Sécurisation du formulaire par token
								require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
								$token = new FormToken\TokenField;
								$token->field("idCase",	$case->getId());
								$token->field("addfile");
								$token->field("uploadForm");
								$tokenHTML = $token->generate();
								echo $tokenHTML;
							?>
							<input type="hidden" name="idCase" value="<? echo $case->getId(); ?>" />
							<input type="button" class="flatbutton" style="width:200px;" name="addfile" onclick="javascript:createFileInput();" value="<?php echo $_SESSION['cste']['_DOC_LABEL_ADD_OTHER_FILE']; ?>">

							<div id="ScrollBox" style="overflow:auto;">
								<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
								<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;height:10px;width:auto;" src=""></iframe>
							</div>
							<span id="btn_upload" style="margin-left60px;"><input type="button" class="flatbutton" onclick="javascript:upload();" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></span>
						</form>
					</div>
				</span>
				<?
								}
				?>
			</div>
			<?
				$idGbContact = 0;
				foreach($lst as $type => $objects){
					switch($type){
						case dims_const::_SYSTEM_OBJECT_CONTACT:
							foreach ($objects as $ctid){
								$assure = new contact();
								$assure->openWithGB($ctid);
								$idGbContact = $assure->fields['id_globalobject'];
								?>
								<div class="displayCase" style="border-bottom:1px solid #D1D1D1;width:100%;">
									<span style="clear:both;width:90%;font-weight:bold;padding-bottom:4px;margin-left:10px;">
										Informations du contact
									</span>
									<table cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px;">
										<tr style="vertical-align:top;">
											<td>
												<img style="margin-left:10px;margin-right:10px;" src="modules/notaire/img/icon_avatar.png">
											</td>
											<td style="width:200px;">
												<span style="width:190px;margin-left:10px;">
													<? echo $assure->fields['lastname'].'&nbsp;'.$assure->fields['firstname']; ?>
												</span>
												<span style="width:190px;margin-left:10px;">
													<? echo $_SESSION['cste']['_PHONE'].' : '.$assure->fields['phone']; ?>
												</span>
												<span style="width:190px;margin-left:10px;">
													<? echo $_SESSION['cste']['_MOBILE'].' : '.$assure->fields['mobile']; ?>
												</span>
												<span style="width:190px;margin-left:10px;">
													<? echo $assure->fields['email']; ?>
												</span>
											</td>
											<td style="width:230px;">
												<span style="width:230px;">
													<? echo $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?> : <? $date = dims_timestamp2local($assure->fields['date_create']); echo $date['date']; ?>
												</span>
												<span style="width:230px;">
													<? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?> : <?
															if ($contact->fields['id_user_create'] > 0){
																	$user = new user();
																	$user->open($assure->fields['id_user_create']);
																	echo $user->fields['lastname'].'&nbsp;'.$user->fields['firstname'];
																}else
																	echo $_SESSION['cste']['_LABEL_IMPORT'];
															?>
												</span>
											</td>
										</tr>
									</table>
								</div>
								<?
							}
							break;
						case dims_const::_SYSTEM_OBJECT_TIERS:
							foreach ($objects as $ctid){
								$assure = new tiers();
								$assure->openWithGB($ctid);
								$idGbContact = $assure->fields['id_globalobject'];
								?>
								<div class="displayCase" style="border-bottom:1px solid #D1D1D1;width:100%;">
									<span style="clear:both;width:90%;font-weight:bold;padding-bottom:4px;margin-left:10px;">
										Informations du contact
									</span>
									<table cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px;">
										<tr style="vertical-align:top;">
											<td>
												<img style="margin-left:10px;margin-right:10px;height:64px;" src="img/tiers_card128.png">
											</td>
											<td style="width:200px;">
												<span style="width:190px;margin-left:10px;">
													<? echo $assure->fields['intitule']; ?>
												</span>
												<span style="width:190px;margin-left:10px;">
													<? echo $_SESSION['cste']['_PHONE'].' : '.$assure->fields['telephone']; ?>
												</span>
												<span style="width:190px;margin-left:10px;">
													<? echo $_SESSION['cste']['_MOBILE'].' : '.$assure->fields['telmobile']; ?>
												</span>
												<span style="width:190px;margin-left:10px;">
													<? echo $assure->fields['mel']; ?>
												</span>
											</td>
											<td style="width:230px;">
												<span style="width:230px;">
													<? echo $_SESSION['cste']['_DIMS_LABEL_ENT_DATEC']; ?> : <? $date = dims_timestamp2local($assure->fields['date_creation']); echo $date['date']; ?>
												</span>
												<span style="width:230px;">
													<? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?> : <?
																if ($contact->fields['id_user'] > 0){
																	$user = new user();
																	$user->open($assure->fields['id_user']);
																	echo $user->fields['lastname'].'&nbsp;'.$user->fields['firstname'];
																}else
																	echo $_SESSION['cste']['_LABEL_IMPORT'];
															?>
												</span>
											</td>
										</tr>
									</table>
								</div>
								<?
							}
							break;
						case dims_const::_SYSTEM_OBJECT_DOCFILE:
							global $skin;
							?>
							<div class="displayCase" style="border-bottom:1px solid #D1D1D1;width:100%;">
								<div style="margin-bottom:10px;">
									<span style="font-weight:bold;width:47%;margin-left:10px;">
										<img src="img/pdf.png" style="margin-right:5px;" />
										Documents disponibles
									</span>
									<span style="width:49%;text-align:right;margin-right:10px;">
										<span style="width:150px;cursor:pointer;" onclick="javascript:$('div#addFileCase').fadeToggle('fast');if (i == 0){ createFileInput(); i++;}">
											<img src="modules/notaire/img/icon_plus.png" />
											<? echo $_SESSION['cste']['_DIMS_LABEL_ADD_FILE']; ?>
										</span>
										<span id="selectVignettes" onclick="javascript:$('div#vignetteDocCase').show('fast');$('div#listDocCase').hide('fast');$('span#selectListe').css('color','#222222');$('span#selectVignettes').css('color','red');$('span#selectListe > img').attr('src','modules/notaire/img/afficher_M.png');$('span#selectVignettes > img').attr('src','modules/notaire/img/afficher_L_red.png');" style="margin-left:5px;width:75px;color:red;cursor:pointer;">
											<img style="height:16px;" src="modules/notaire/img/afficher_L_red.png" />
											Vignettes
										</span>
										<span id="selectListe" onclick="javascript:$('div#vignetteDocCase').hide('fast');$('div#listDocCase').show('fast');$('span#selectListe').css('color','red');$('span#selectVignettes').css('color','#222222');$('span#selectListe > img').attr('src','modules/notaire/img/afficher_M_red.png');$('span#selectVignettes > img').attr('src','modules/notaire/img/afficher_L.png');" style="width:75px;cursor:pointer;">
											<img style="height:16px;" src="modules/notaire/img/afficher_M.png" />
											<? echo $_SESSION['cste']['_DIMS_LIST']; ?>
										</span>
									</span>
								</div>
								<div id="addFileCase" style="display:none;margin-left:50px;margin-bottom:10px;">
									<form method="POST" id="docfile_add" name="docfile_add" action="<? echo $dims->getScriptEnv().'?dims_op=case_manager&case_op='._OP_ADD_FILE_CASE; ?>" enctype="multipart/form-data">
										<?
											// Sécurisation du formulaire par token
											require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
											$token = new FormToken\TokenField;
											$token->field("idCase",	$case->getId());
											$token->field("addfile");
											$token->field("uploadForm");
											$token->field($file->fields['md5id']);
											$tokenHTML = $token->generate();
											echo $tokenHTML;
										?>
										<input type="hidden" name="idCase" value="<? echo $case->getId(); ?>" />
										<input type="button" class="flatbutton" style="width:200px;" name="addfile" onclick="javascript:createFileInput();" value="<?php echo $_SESSION['cste']['_DOC_LABEL_ADD_OTHER_FILE']; ?>">

										<div id="ScrollBox" style="overflow:auto;">
											<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
											<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;height:10px;width:auto;" src=""></iframe>
										</div>
										<span id="btn_upload" style="margin-left60px;"><input type="button" class="flatbutton" onclick="javascript:upload();" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></span>
									</form>
								</div>
								<div id="vignetteDocCase">
							<?
								$data =array();
								$elements=array();

								$data['headers'][0] = $_SESSION['cste']['_AUTHOR'];
								$data['headers'][1] = $_SESSION['cste']['_DIMS_LABEL_NAME'];
								$data['headers'][2] = $_SESSION['cste']['_TYPE'];
								$data['headers'][3] = $_SESSION['cste']['_SIZE'];
								$data['headers'][4] = $_SESSION['cste']['_DIMS_DATE'];
								$data['headers'][5] = $_SESSION['cste']['_LABEL_ACTION'];
								foreach ($objects as $idfile){
									$file = new docfile();
									$file->openWithGB($idfile);
									$elem = array();
									$user = new user();
									$user->open($file->fields['id_user']);
									$create_date = dims_timestamp2local($file->fields['timestp_create']);
									$elem[0] = $user->fields['firstname'].' '.$user->fields['lastname'];
									$elem[1] = $file->fields['name'];
									$elem[2] = $file->fields['extension'];
									$elem[3] = $file->fields['size'];
									$elem[4] = $create_date['date'];
									$elem[5] = '
										<div style="float:right;">
											<a style="text-decoration:none;" href="?dims_op=doc_file_download&docfile_md5id='.$file->fields['md5id'].'">
												<img src="./common/img/go-down.png" />
											</a>
											<a style="text-decoration:none;" href="Javascript: void(0);" onclick="Javascript: previsuDoc(\''.$file->fields['md5id'].'\');">
												<img src="./common/img/view.png" />
											</a>
											<a style="text-decoration:none;" href="Javascript: dims_confirmlink(\'?dims_op=doc_file_delete&docfile_md5id='.$file->fields['md5id'].'\',\''.$_SESSION['cste']['_DIMS_LABEL_CONFIRM_ACTION'].'\');">
												<img src="./common/modules/notaire/templates/backoffice/img/icon_del.png" />
											</a>
										</div>
										';

									$elements[] = $elem;
									?>
									<span class="vignettesFile" style="width:150px;height:125px;cursor:pointer;margin-left:10px;text-align:center;vertical-align:top;" name="<? echo $file->fields['md5id']; ?>">
										<span style="width:150px;">
											Le <? $d = dims_timestamp2local($file->fields['timestp_create']); echo $d['date'].' &agrave; '.substr($d['time'],0,-3); ?>
										</span>
										<img src="<? echo $file->getFileIcon(64); ?>" />
										<span style="width:150px;color:red;">
											<? echo $file->fields['name']; ?>
										</span>
									</span>
									<?
								}
								$data['data']['elements'] = $elements;
							?>
								</div>
								<div id="listDocCase" style="display:none;width:780px;margin-bottom:10px;">
									<?php echo $skin->displayArray($data); ?>
								</div>
							</div>
							<script type="text/javascript">
								$.contextMenu({
									selector: 'div#vignetteDocCase span.vignettesFile',
									trigger: "left",
									items: {
										download: {name: "<? echo utf8_encode(html_entity_decode($_SESSION['cste']['_DIMS_DOWNLOAD'])); ?>", callback: function(){
																					document.location.href="<? echo $dims->getScriptEnv(); ?>?dims_op=doc_file_download&docfile_md5id="+$(this).attr('name');
																					}, icon: "download"},
										view: {name: "<? echo $_SESSION['cste']['_DIMS_LABEL_VIEW']; ?>", callback: function(){
																					previsuDoc($(this).attr('name'));
																					}, icon: "view"},
										del: {name: "<? echo $_SESSION['cste']['_DELETE']; ?>", callback: function(){
																					dims_confirmlink('?dims_op=doc_file_delete&docfile_md5id='+$(this).attr('name'),'<? echo $_SESSION['cste']['_DIMS_LABEL_CONFIRM_ACTION']; ?>');
																					}, icon: "delete"},
										sep1: "---------",
										quit: {name: "<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>", callback: $.noop, icon: "quit"}
									}
								});
							</script>
							<?
							break;
						case dims_const::_SYSTEM_OBJECT_CATEGORY:
							require_once DIMS_APP_PATH.'/modules/system/class_category.php';
							$categ = new category();
							$categ->openWithGB(current($objects));
							?>
							<div class="displayCase" style="border-bottom:1px solid #D1D1D1;width:100%;min-height:180px;">
								<span style="font-weight:bold;margin-left:10px;margin-top:10px;width:auto;">
									<img src="img/tags-icon.png" style="margin-right:5px;" />
									Cat&eacute;gorisation :
								</span>
								<span style="color:red;min-width:665px;margin-bottom:10px;">
								<?
									echo $categ->getAriane();
								?>
								</span>
								<div>
									<?
									require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
									$db = dims::getInstance()->getDb();
									$module = new module();
									$module->open($_SESSION['dims']['moduleid']);
									$sel = "SELECT	id_category
											FROM	dims_category_object
											WHERE	object_id_module_type = :idmoduletype
											AND		id_object = :idobject ";
									$res = $db->query($sel, array(':idmoduletype' => $module->fields['id_module_type'], ':idobject' => dims_const::_SYSTEM_OBJECT_CASE) );
									require_once DIMS_APP_PATH.'modules/system/class_category.php';
									$lstCat = array();
									$lvl = 0;
									while($r = $db->fetchrow($res)){
										$cat = new category();
										$cat->open($r['id_category']);
										$cat->initDescendance();
										$lstCat[$r['id_category']] = $cat->getArborescence();
										if ($lstCat[$r['id_category']]['nbLvl'] > $lvl)
											$lvl = $lstCat[$r['id_category']]['nbLvl'];
									}
									require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');
									$browser = new dims_browser($lvl+1,$lstCat,'listeCategCaseSave');
									$browser->displayBrowser(DIMS_APP_PATH . '/modules/system/class_category.tpl.php',$categ->getBrowserAriane());
									?>
								</div>
							</div>
							<?
							break;
					}
				}
			?>
		</div>
		<?php echo controller_op_intervention::op_intervention(_OP_SHOW_INTERVENTION, array('from'=>'case', 'id' => $idGbContact,'idCase'=>$case->getId())); ?>
		<script type="text/javascript">
			$("div.displayCase:last").css("border-bottom","none");
		</script>
		<?
		echo dims_create_button($_SESSION['cste']['_DIMS_CLOSE'],'close','Javascript: dims_closeOverlayedPopup('.$id_popup.');','','float:right;margin:10px;');
	}
}

?>
