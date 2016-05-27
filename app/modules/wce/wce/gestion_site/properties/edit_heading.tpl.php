<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub']); ?>" name="save_prop_head" enctype="multipart/form-data">
		<input type="hidden" name="sub" value="<? echo module_wce::_SITE_PROPERTIES; ?>" />
		<input type="hidden" name="action" value="<? echo module_wce::_PROPERTIES_SAVE_HEAD; ?>" />
		<input type="hidden" name="headingid" value="<? echo $this->fields['id']; ?>" />
		<input type="hidden" name="lang" value="<? echo $this->fields['id_lang']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['label']; ?>" name="head_label" />
						</td>
						<td class="label_field" rowspan="3" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
							</label>
						</td>
						<td rowspan="3">
							<textarea style="height:58px;" class="text" name="head_description"><? echo $this->fields['description']; ?></textarea>
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
							$respos=$db->query("SELECT	MAX(position) as maxi
											   FROM		dims_mod_wce_heading
											   WHERE	id_heading=:id_heading
											   AND		id_module = :id_module",
											   array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT),
													':id_heading'=>array('value'=>$this->fields['id_heading'],'type'=>PDO::PARAM_INT)));
							if ($db->numrows($respos)>0) {
								$fresu=$db->fetchrow($respos);
								$maxi=$fresu['maxi'];
							}else
								$maxi=1;
							?>
							<select name="head_position">
								<?
								for($i=1;$i<=$maxi;$i++){
									?>
									<option value="<? echo $i; ?>" <? echo ($this->fields['position'] == $i)?"selected=true":""; ?>>
										<? echo $i; ?>
									</option>
									<?
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_VISIBLE']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" value="1" <? echo ($this->fields['visible'])?"checked=true":""; ?> name="head_visible" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_VISIBLE_IF_CONNECTED']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" value="1" <? echo ($this->fields['visible_if_connected'])?"checked=true":""; ?> name="head_visible_if_connected" />
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_WCE_URLREWRITE']; ?>
							</label>
						</td>
						<td>
							<input type="text" name="head_urlrewrite" value="<? echo $this->fields['urlrewrite']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_PRESENT_IN_THE_SITEMAP']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" value="1" <? echo ($this->fields['is_sitemap'])?"checked=true":""; ?> name="head_is_sitemap" />
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>
							</label>
						</td>
						<td>
							<input type="text" style="width:100px;" name="head_color" id="wce_heading_color" value="<? echo $this->fields['color']; ?>" />
							<a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('wce_heading_color', event);">
								<img src="./common/img/colorpicker/colorpicker.png" align="top" border="0">
							</a>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" value="1" <? echo ($this->fields['private'])?"checked=true":""; ?> name="head_private" />
						</td>
						<td class="label_field" colspan="2">
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_TEMPLATE']; ?>
							</label>
						</td>
						<td>
							<select name="head_template">
								<option value=""></option>
								<?
								$sql = "SELECT	*
										FROM	dims_workspace_template
										WHERE	id_workspace=:id_workspace";
								$res=$db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
								while ($f=$db->fetchrow($res)) {
									?>
									<option value="<? echo $f['template']; ?>" <? echo ($f['template'] == $this->fields['template'])?"selected=true":""; ?>>
										<? echo $f['template']; ?>
									</option>
									<?
								}
								?>
							</select>
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_FREE_FIELD']; ?> 1
							</label>
						</td>
						<td>
							<input type="text" name="head_free1"  value="<? echo $this->fields['free1']; ?>" />
						</td>
					</tr>
					<?php
					if($this->get('id_heading') == 0){
						?>
						<tr>
							<td class="label_field">
								<label>
									<?= dims_constant::getVal('TEMPLATE_FREE_ROOT'); ?>
								</label>
							</td>
							<td>
								<input type="text" name="head_freetemplate" value="<?= $this->fields['freetemplate']; ?>" />
							</td>
							<td></td><td></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_REDIRECT_TO_ARTICLE']; ?>
							</label>
						</td>
						<td>
							<?
							$tmpArt = $lk = "";
							if ($this->fields['linkedpage'] != '' && $this->fields['linkedpage'] > 0){
								$art = new wce_article();
								$art->open($this->fields['linkedpage']);
								if(isset($art->fields['title'])){
									$tmpArt = $art->fields['title'];
									$lk = "&articleid=".$this->fields['linkedpage'];
								}
							}
							?>
							<input type="hidden" id="wce_heading_linkedpage" name="head_linkedpage" value="<? echo $this->fields['linkedpage']; ?>">
							<input type="text" style="width:auto;" readonly class="text" id="linkedpage_displayed" value="<? echo $tmpArt; ?>">
							<input type="button" style="width:auto;" value="<? echo $_SESSION['cste']['_FORM_SELECTION']; ?>" onclick="javascript:dims_showpopup('',600,event,'click','dims_popup');dims_xmlhttprequest_todiv('admin-light.php','dims_op=selectlinkarticle&input=wce_heading_linkedpage&display=linkedpage_displayed<? echo $lk; ?>',false,'dims_popup');" />
							<input type="button" style="width:auto;" value="<? echo $_SESSION['cste']['_DIRECTORY_LEGEND_DELETE']; ?>" onclick="javascript:dims_getelem('wce_heading_linkedpage').value='';dims_getelem('linkedpage_displayed').value='';" />
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_FREE_FIELD']; ?> 2
							</label>
						</td>
						<td>
							<input type="text" name="head_free2"  value="<? echo $this->fields['free2']; ?>" />
						</td>
					</tr>

					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_REDIRECT_TO_TOPIC']; ?>
							</label>
						</td>
						<td>
							<?
							$tmpArt = $lk = "";
							if ($this->fields['linkedheading'] != '' && $this->fields['linkedheading'] > 0){
								$art = new wce_heading();
								$art->open($this->fields['linkedheading']);
								$tmpArt = $art->fields['label'];
								$lk = "&headingid=".$this->fields['linkedheading'];
							}
							?>
							<input type="hidden" id="wce_heading_linkedheading" name="head_linkedheading" value="<? echo $this->fields['linkedheading']; ?>">
							<input type="text" style="width:auto;" readonly class="text" id="linkedheading_displayed" value="<? echo $tmpArt; ?>">
							<input type="button" style="width:auto;" value="<? echo $_SESSION['cste']['_FORM_SELECTION']; ?>" onclick="javascript:dims_showpopup('',600,event,'click','dims_popup');dims_xmlhttprequest_todiv('admin-light.php','dims_op=selectredirectheading&input=wce_heading_linkedheading&display=linkedheading_displayed<? echo $lk; ?>',false,'dims_popup');" />
							<input type="button" style="width:auto;" value="<? echo $_SESSION['cste']['_DIRECTORY_LEGEND_DELETE']; ?>" onclick="javascript:dims_getelem('wce_heading_linkedheading').value='';dims_getelem('linkedheading_displayed').value='';" />
						</td>
						<td class="label_field" rowspan="3" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_PICTO']; ?>
							</label>
						</td>
						<td rowspan="3" style="vertical-align: top;">
							<input type="file" id="photo" name="photo" />
							<?
							$path=realpath('.').'/data/headings/'.$this->fields['picto'];
							if ($this->fields['picto']!='' && file_exists($path)) {
								?>
								<img src="/data/headings/<? echo $this->fields['picto']; ?>" />
								<!--<a href=\"javascript:dims_confirmlink('".dims_urlencode("/admin.php?op=heading_deletepicto&headingid=".$heading->fields['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\">
									<img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\">
								</a>"; -->
								<?
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_LABEL_REDIRECT_URL']; ?>
							</label>
						</td>
						<td>
							<input type="text" name="head_url"	value="<? echo $this->fields['url']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_LABEL_NEW_WINDOW']; ?>
							</label>
						</td>
						<td>
							<input type="checkbox" name="head_url_window" value="1" <? echo ($this->fields['url_window'])?"checked=true":""; ?> />
						</td>
					</tr>
					<tr>
						<td class="label_field" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_DYNAMIC_OBJECTS']; ?>
							</label>
						</td>
						<td colspan="3">
							<?
							$arrayobj=array();
							if ($this->fields['id']>0) {
								$res=$db->query("SELECT		*
												FROM		dims_mod_wce_object_corresp
												WHERE		id_heading=:id_heading",
												array(':id_heading'=>array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT)));

								if ($db->numrows($res)>0) {
									while ($ob=$db->fetchrow($res)) {
										$arrayobj[$ob['id_object']]=$ob['id_object'];
									}
								}
							}
							// on liste les objets actus disponibles
							$res=$db->query("SELECT		*
											FROM		dims_mod_wce_object
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
											<input <? echo (isset($arrayobj[$ob['id']]))?"checked=true":""; ?> type="checkbox" name="obj_affect<? echo $ob['id']; ?>" />
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
						<a href="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF."&headingid=".$this->fields['id']; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
					<? if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) { ?>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('<? echo module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEL_ROOT."&headingid=".$this->fields['id']; ?>','<? echo str_replace("'","\'",$_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']); ?>');">
							<? echo $_SESSION['cste']['_DELETE']; ?>
						</a>
					</div>
					<? } ?>
				</div>
			</div>
		</div>
	</form>
</div>
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
	function propertiesArticleWce(id_heading,id_article) {
		dims_showcenteredpopup("",700,30,'dims_popup');
		if( arguments[0] == null) id_article=0;
		dims_xmlhttprequest_todiv('admin.php','dims_op=properties_article&id_article='+id_article+"&id_heading="+id_heading,'','dims_popup');
	}
</script>
