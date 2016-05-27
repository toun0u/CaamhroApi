<?
$article_timestp_published = $article_timestp_unpublished = $lastupdate_timestp = "";
if(!$this->new){
	$ldate = ($this->fields['timestp_published']) ? dims_timestamp2local($this->fields['timestp_published']) : array('date' => '');
	$article_timestp_published = $ldate['date'];

	$ldate = ($this->fields['timestp_unpublished']) ? dims_timestamp2local($this->fields['timestp_unpublished']) : array('date' => '');
	$article_timestp_unpublished = $ldate['date'];

	$ldate = ($this->fields['lastupdate_timestp']) ? dims_timestamp2local($this->fields['lastupdate_timestp']) : array('date' => '', 'time' => '');
	$lastupdate_timestp = "{$ldate['date']} {$ldate['time']}";
}
?>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub']); ?>" name="save_prop_art" enctype="multipart/form-data">
		<input type="hidden" name="sub" value="<? echo module_wce::_SITE_PROPERTIES; ?>" />
		<input type="hidden" name="action" value="<? echo module_wce::_PROPERTIES_SAVE_ART; ?>" />
		<input type="hidden" name="articleid" value="<? echo $this->fields['id']; ?>" />
		<input type="hidden" name="headingid" value="<? echo $this->fields['id_heading']; ?>" />
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
							<input type="text" value="<? echo $this->fields['title']; ?>" name="art_title" />
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_POSTED_ON']; ?>
							</label>
						</td>
						<td>
							<input class="datepicker" style="width:100px;" type="text" name="art_timestp_published" id="art_timestp_published" value="<? echo $article_timestp_published; ?>" tabindex="14" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_POSITION']; ?>
							</label>
						</td>
						<td>
							<?
							$db = dims::getInstance()->getDb();
							$sel = "SELECT		COUNT(id) as maxi
									FROM		".wce_article::TABLE_NAME."
									WHERE		id_heading= :id_heading
									AND			id_module = :id_module
									GROUP BY	id_lang";
							$res = $db->query($sel,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
														':id_heading'=>array('value'=>$this->fields['id_heading'],'type'=>PDO::PARAM_INT)));
							$maxi = 1;
							if($r = $db->fetchrow($res)){
								$maxi = $r['maxi'];
							}
							?>
							<select name="art_position">
								<?
								for($i=1;$i<=$maxi;$i++){
									?>
									<option <? echo ($i == $this->fields['position'])?"selected=true":""; ?> value="<? echo $i; ?>"><? echo $i; ?></option>
									<?
								}
								?>
							</select>
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_TAKEN_OFFLINE_THE']; ?>
							</label>
						</td>
						<td>
							<input class="datepicker" style="width:100px;" type="text" name="art_timestp_unpublished" id="art_timestp_unpublished" value="<? echo $article_timestp_unpublished; ?>" tabindex="14" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_AUTHOR']; ?>
							</label>
						</td>
						<td>
							<input type="text" name="art_author" value="<? echo $this->fields['author']; ?>" />
						</td>
						<td class="label_field" rowspan="8" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
							</label>
						</td>
						<td rowspan="8">
							<textarea style="height: 100px;width:300px;" id="art_description" name="fck_art_description"><?= $this->fields['description']; ?></textarea>
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
							$title = $lk = "";
							if ($this->fields['id_article_link'] != '' && $this->fields['id_article_link'] > 0){
								$art = new wce_article();
								$art->open($this->fields['id_article_link']);
								$title = $art->fields['title'];
								$lk = "&articleid=".$this->fields['id_article_link'];
							}
							?>
							<input type="hidden" id="wce_article_id_article_link" name="art_id_article_link" value="<? echo $this->fields['id_article_link']; ?>" />
							<input type="text" style="width:auto;" readonly class="text" id="linkedpage_displayed" value="<? echo $title; ?>" />
							<input type="button" style="width:auto;" class="button" value="<? echo $_SESSION['cste']['_FORM_SELECTION']; ?>" onclick="javascript:dims_showpopup('',300,event,'click','dims_popup');dims_xmlhttprequest_todiv('admin-light.php','dims_op=selectlinkarticle&input=wce_article_id_article_link&display=linkedpage_displayed<? echo $lk; ?>',false,'dims_popup');"/>&nbsp;
							<input type="button" style="width:auto;" class="button" value="<? echo $_SESSION['cste']['_DIRECTORY_LEGEND_DELETE']; ?>" onclick="javascript:dims_getelem('wce_article_id_article_link').value='';dims_getelem('linkedpage_displayed').value='';" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_LABEL_REDIRECT_URL']; ?>
							</label>
						</td>
						<td>
							<input type="text" name="art_url" value="<? echo $this->fields['url']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_LABEL_NEW_WINDOW']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="art_url_window" value="1" <? echo ($this->fields['url_window'])?"checked=true":""; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_VISIBLE_IN_THE_MENU']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="art_visible" value="1" <?= ($this->fields['visible']) ? "checked='checked'" : ""; ?> />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_PRESENT_IN_THE_SITEMAP']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="art_is_sitemap" value="1" <? echo ($this->fields['is_sitemap'])?"checked=true":""; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_EDITORIAL']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="art_edito" value="1" <? echo ($this->fields['edito'])?"checked=true":""; ?>" />
						</td>


					</tr>

					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['CONTENT_MODEL']; ?>
							</label>
						</td>
						<td>
							<select name="art_model">
								<option <? echo ($this->fields['model'] == "" ) ? 'selected=true' : ''; ?> value="">
									<? echo $_SESSION['cste']['_DIMS_LABEL_NONE']; ?>
								</option>
								<?
								$wce_models = wce_getmodels();
								asort($wce_models['pages_publiques']);
								asort($wce_models['workspace']);
								foreach($wce_models["pages_publiques"] as $key => $model) {
									?>
									<option <? echo ($this->fields['model'] == $model ) ? 'selected=true' : ''; ?> value="<? echo $model; ?>">
										<? echo $model; ?>
									</option>
									<?
								}
								if(is_array($wce_models["workspace"])) {
									foreach($wce_models["workspace"] as $key => $model) {
										?>
										<option <? echo ($this->fields['model'] == $model) ? 'selected=true' : ''; ?> value="<? echo $model; ?>">
											<? echo $model; ?>
										</option>
										<?
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>
							</label>
						</td>
						<td>
							<input style="width:70px;" type="text" value="<? echo $this->fields['color']; ?>" name="art_color" id="art_color" />
							<a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('art_color', event);">
								<img src="./common/img/colorpicker/colorpicker.png" align="top" border="0">
							</a>
						</td>
					</tr>
					<!--tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_LANG']; ?>
							</label>
						</td>
						<td>
							<select name="art_id_lang">
								<?
								$res_lg = $db->query("SELECT	id, label
													 FROM		dims_lang");
								while($tab_l = $db->fetchrow($res_lg)) {
									?>
									<option <? echo ($tab_l['id'] == $this->fields['id_lang'])?"selected=true":""; ?> value="<? echo $tab_l['id']; ?>">
										<? echo $tab_l['label']; ?>
									</option>
									<?
								}
								?>
							</select>
						</td>
					</tr-->
					<tr>
						<td class="label_field" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_PICTO']; ?>
							</label>
						</td>
						<td style="vertical-align: top;">
							<input type="file" id="photo" name="photo" class="text" /><br />
							<?
							$path=realpath('.').'/data/articles/'.$this->fields['picto'];
							if ($this->fields['picto']!='' && file_exists($path)) {
								?>
								<img src="<? echo '/data/articles/'.$this->fields['picto']; ?>" />
								<!--<a href="javascript:dims_confirmlink('".dims_urlencode($scriptenv."?op=article_deletepicto&articleid=".$article->fields['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\">
									<img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\">
								</a>-->
								<?
							}
							?>
						</td>
						<td class="label_field" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_DYNAMIC_OBJECTS']; ?>
							</label>
						</td>
						<td>
							<?
							$arrayobj = $this->getObjectCorresp(false);
							// on liste les objets actus disponibles
							require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object.php";
							$res=$db->query("SELECT		*
											FROM		".article_object::TABLE_NAME."
											WHERE		id_module=:id_module",
											array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));

							if ($db->numrows($res)>0) {
								?>
								<table width="100%">
									<tr>
										<td><? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?></td>
										<td><? echo $_SESSION['cste']['_DISPLAY']; ?></td>
										<td>Sel.</td>
									</tr>
								<?
								$color="";
								while ($ob=$db->fetchrow($res)) {
									$color=($color=='trl2') ? 'trl1' : 'trl2';

									?>
									<tr class="<? echo $color; ?>">
										<td>
											<? echo $ob['label']; ?>
										</td>
										<td>
											<? echo ($ob['mode']==0)?$_SESSION['cste']['_DIMS_LABEL_STANDARD']:"Dynamique"; ?>
										</td>
										<td>
											<input <? echo (isset($arrayobj[$ob['id']]))?"checked=true":""; ?> type="checkbox" name="obj_affect[]" value="<? echo $ob['id']; ?>" />
										</td>
									</tr>
									<?
								}
								?>
								</table>
								<?
							}
							?>
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
						<a href="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$this->fields['id_heading']."&articleid=".$this->fields['id']; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
					<? if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) { ?>
						<div>
							<? echo $_SESSION['cste']['_DIMS_OR']; ?>
							<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEL_ART."&articleid=".$this->fields['id']; ?>','<? echo str_replace("'","\'",$_SESSION['cste']['_CONFIRM_DELETE_ARTICLE']); ?>');">
								<? echo $_SESSION['cste']['_DELETE']; ?>
							</a>
						</div>
					<? } ?>
				</div>
			</div>
		</div>
	</form>
</div>
<?php
// récupération du template
$headings = wce_getheadings($_SESSION['dims']['moduleid']);
$template_name = (!empty($headings['list'][$this->fields['id_heading']]['template'])) ? $headings['list'][$this->fields['id_heading']]['template'] : 'default';
 ?>
<script type="text/javascript" src="/common/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	function wce_showheading(hid,str) {
		elt = document.getElementById(hid+'_plus');
		if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
		else  if (elt.innerHTML.indexOf('minusbottom')	!= -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
		else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
		else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');


		if (elt = document.getElementById(hid)) {
			if (elt.style.display == 'none') {
				if (elt.innerHTML.length < 20) dims_xmlhttprequest_todiv('<? echo dims::getInstance()->getScriptEnv(); ?>','op=xml_detail_heading&hid='+hid+'&str='+str,'',hid);
				document.getElementById(hid).style.display='block';
			}
			else {
				document.getElementById(hid).style.display='none';
			}
		}
	}
	$(document).ready(function(){
		var instance=CKEDITOR.replace('art_description',
			{
				customConfig : '/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js',
				stylesSet:'default:/common/templates/frontoffice/<?= $template_name; ?>/ckstyles.js',
				contentsCss:'/common/templates/frontoffice/<?= $template_name; ?>/ckeditorarea.css'
			});
	});
</script>
