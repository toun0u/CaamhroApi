<?
$article_object = new article_object();
$article_object->open($this->getLightAttribute('returnId'));
?>
<div style="clear:both;padding:10px;margin-left:30px;">
	<form method="POST" action="<?= module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_EDIT_BREVE."&id=".$article_object->fields['id']."&id_obj=".$this->fields['id']; ?>" name="chang_lang">
		<label>
			<?= $_SESSION['cste']['_LIST_LANGUAGES']; ?> :
		</label>
		<select id="change_lang" name="lg">
			<?
			$l = new wce_lang();
			$lstLangDipo = $this->getListArticleLangVersionWCE();
			foreach($l->getAll(true) as $langue){
				if(isset($lstLangDipo[$langue->fields['id']]) || $this->isNew()){
					$et = "";
					$opt = "ref=\"exist\"";
				}else{
					$et = " *";
					$opt = "ref=\"\"";
				}
				?>
				<option <? echo $opt; ?> <? if($this->fields['id_lang'] == $langue->fields['id']) echo 'selected=true'; ?> value="<? echo $langue->fields['id']; ?>">
					<? echo $langue->getLabel().$et; ?>
				</option>
				<?
			}
			?>
		</select>
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			$('select#change_lang').change(function(){
				if($('option:selected',$(this)).attr('ref') == 'exist'){
					document.chang_lang.submit();
				}else{
					if(confirm('<? echo $_SESSION['cste']['_CONFIRM_INITIALIZE_NEW_LANGUAGE']; ?>'))
						document.chang_lang.submit();
					else
						$(this).val(<? echo $this->fields['id_lang']; ?>);
				}
			});
		});
	</script>
</div>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" name="save_object" enctype="multipart/form-data">
		<input type="hidden" name="action" value="<? echo module_wce::_DYN_OBJ_SAVE_BREVE; ?>" />
		<input type="hidden" name="id_obj" value="<? echo $this->fields['id']; ?>" />
		<input type="hidden" name="id" value="<? echo $this->getLightAttribute('returnId'); ?>" />
		<input type="hidden" name="id_lang" value="<? echo $this->fields['id_lang']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['title']; ?>" name="obj_title" />
						</td>
						<td class="label_field" style="width: 60%;text-align: left;padding-left:10px;">
							<label>
								<? echo $_SESSION['cste']['DESCRIPTION_COURTE']; ?>
							</label>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_LINK_TO_ARTICLE']; ?>
							</label>
						</td>
						<td>
							<?
							$article_title="";
							if ($this->fields['id_article_link'] != '' && $this->fields['id_article_link'] > 0) {
								$art_temp = new wce_article();
								$art_temp->open($this->fields['id_article_link']);
								if (isset($art_temp->fields['title'])) {
									$article_title=$art_temp->fields['title'];
								}
							}
							?>
							<input type="hidden" id="wce_article_id_article_link" name="obj_id_article_link" value="<? echo $this->fields['id_article_link']; ?>">
							<input type="text" style="width:auto;" readonly class="text" id="linkedpage_displayed" value="<? echo $article_title; ?>">
							<input type="button" class="button" style="width:auto;" value="<? echo $_SESSION['cste']['_FORM_SELECTION']; ?>" onclick="javascript:dims_showpopup('',300,event,'click','dims_popup');dims_xmlhttprequest_todiv('admin.php','dims_op=selectlinkarticle',false,'dims_popup');" />
							<input type="button" class="button" style="width:auto;" value="<? echo $_SESSION['cste']['_DIRECTORY_LEGEND_DELETE']; ?>" onclick="javascript:dims_getelem('wce_article_id_article_link').value='';dims_getelem('linkedpage_displayed').value='';" />
						</td>
						<td style="padding-left:10px;" rowspan="<? echo (6+$article_object->fields['pubfin_dependant']);?>">
							<textarea id="obj_description" name="fck_obj_description"><? echo $this->fields['description']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_INFORMATION_SOURCE']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['source']; ?>" name="obj_source" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_LABEL_REDIRECT_URL']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['url']; ?>" name="obj_url" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_POSTED_ON']; ?>
							</label>
						</td>
						<td>
							<?
							$pubdate = '';
							if($this->fields['timestp_published'] != 0){
								$date_dims = dims_timestamp2local($this->fields['timestp_published']);
								$pubdate = $date_dims['date'];
							}
							?>
							<input class="datepicker" type="text" name="obj_timestp_published" value="<? echo $pubdate; ?>" />
						</td>
					</tr>
					<?
					if($article_object->fields['pubfin_dependant']){
						?>
						<tr>
							<td class="label_field">
								<label>
									<? echo $_SESSION['cste']['_TAKEN_OFFLINE_THE']; ?>
								</label>
							</td>
							<td>
								<?
								$unpubdate = '';
								if($this->fields['timestp_unpublished'] != 0){
									$date_dims = dims_timestamp2local($this->fields['timestp_unpublished']);
									$unpubdate = $date_dims['date'];
								}
								?>
								<input class="datepicker" type="text" name="obj_timestp_unpublished" value="<? echo $unpubdate; ?>" />
							</td>
						</tr>
						<?
					}
					?>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_PICTO']; ?>
							</label>
						</td>
						<td>
							<input type="file" name="photo" />
							<?
							$path=realpath('.').'/data/articles/'.$this->fields['picto'];
							if ($this->fields['picto']!='' && file_exists($path)) {
								// TODO : mettre le bon lien pour supprimer le picto
								?>
								<img src="data/articles/<? echo $this->fields['picto']; ?>" />
								<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<?= module_wce::get_url(module_wce::_SUB_DYN).'&action=breve_deletepicto&articleid='.$this->getId().'&id_return='.$this->getLightAttribute('returnId');?>','<? echo $_DIMS['cste']['_DIMS_CONFIRM']; ?>');">
									<img src="img/delete.png" alt="<? echo $_DIMS['cste']['_DELETE']; ?>" />
								</a>
								<?
							}
							?>
						</td>
					</tr>

					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['ALERT_LEVEL']; ?>
							</label>
						</td>
						<td>
							<input type="radio" name="obj_alert_level" id="low_gravity" value="1" <?php if(!isset($this->fields['alert_level']) || $this->fields['alert_level'] == 1) echo 'checked="checkded"'; ?>/>
							<label for="low_gravity" class="level_alert low_gravity" title="<?= $_SESSION['cste']['LOW_LEVEL']; ?>"></label>
							<input type="radio" name="obj_alert_level" id="middle_gravity" value="2" <?php if(isset($this->fields['alert_level']) && $this->fields['alert_level'] == 2) echo 'checked="checkded"'; ?>/>
							<label for="middle_gravity" class="level_alert middle_gravity" title="<?= $_SESSION['cste']['MIDDLE_LEVEL']; ?>"></label>
							<input type="radio" name="obj_alert_level" id="high_gravity" value="3" <?php if(isset($this->fields['alert_level']) && $this->fields['alert_level'] == 3) echo 'checked="checkded"'; ?>/>
							<label for="high_gravity" class="level_alert high_gravity" title="<?= $_SESSION['cste']['HIGH_LEVEL']; ?>"></label>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>
							</label>
						</td>
						<td>
							<input style="width:70px;" type="text" value="<? echo $this->fields['color']; ?>" name="obj_color" id="obj_color" />
							<a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('obj_color', event);">
								<img src="./common/img/colorpicker/colorpicker.png" align="top" border="0">
							</a>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DYNAMIC_OBJECTS']; ?>
							</label>
						</td>
						<td>
							<table cellpadding="0" cellspacing="0" style="width: 100%;">
								<?
								$db = dims::getInstance()->getDb();
								$sel = "SELECT 	*
										FROM 	".article_object::TABLE_NAME."
										WHERE 	id_module = :id_module";
								$res = $db->query($sel,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
								$lstObj = $this->getObjectCorresp(false);
								while($r = $db->fetchrow($res)){
									?>
									<tr>
										<td>
											<?
											switch($r['type']){
												case WCE_OBJECT_TYPE_NEWS:
													echo $_SESSION['cste']['_DIMS_LABEL_NEWS'];
													break;
												case WCE_OBJECT_TYPE_UNE:
													echo $_SESSION['cste']['_DIMS_LABEL_TOP_NEWS'];
													break;
												case WCE_OBJECT_TYPE_NEWSLETTER:
													echo $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER'];
													break;
												case WCE_OBJECT_TYPE_SONDAGE:
													echo substr($_SESSION['cste']['SMILE_SURVEYS'],0,-1);
													break;
												case WCE_OBJECT_TYPE_ALL_BREVES:
													echo "Toutes les br&egrave;ves";
													break;
												case WCE_OBJECT_TYPE_ALERTES:
													echo "Alertes";
													break;
												default:
													echo $_SESSION['cste']['_DIMS_LABEL_UNDEFINED'];
													break;
											}
											?>
										</td>
										<td>
											<? echo $r['label']; ?>
										</td>
										<td>
											<input type="checkbox" name="lst_affect[]" value="<? echo $r['id']; ?>" <? echo (isset($lstObj[$r['id']]) || ($this->fields['id'] <= 0 && ($r['type'] == WCE_OBJECT_TYPE_ALL_BREVES || $r['id'] == $this->getLightAttribute('returnId'))))?"checked=true":""; ?> />
										</td>
									</tr>
									<?
								}
								?>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$this->getLightAttribute('returnId'); ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php
// récupération du template
$sql = "SELECT 	*
		FROM 	dims_workspace_template
		WHERE 	id_workspace = :id_workspace
		AND 	is_default = 1
		LIMIT 	1";
$db = dims::getInstance()->getDb();
$res = $db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
$template_name = "default";
if($r = $db->fetchrow($res))
	$template_name = $r['template'];
?>
<script type="text/javascript" src="/common/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var instance=CKEDITOR.replace('obj_description',
			{
				customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
				stylesSet:'default:/common/templates/frontoffice/<?= $template_name; ?>/ckstyles.js',
				contentsCss:'/common/templates/frontoffice/<?= $template_name; ?>/ckeditorarea.css'
			});
	});
</script>
