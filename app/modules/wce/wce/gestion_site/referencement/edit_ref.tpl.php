<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub']); ?>" name="save_prop_art" enctype="multipart/form-data">
		<input type="hidden" name="sub" value="<? echo module_wce::_SITE_REF; ?>" />
		<input type="hidden" name="action" value="<? echo module_wce::_GEST_REF_SAVE; ?>" />
		<input type="hidden" name="articleid" value="<? echo $this->fields['id']; ?>" />
		<input type="hidden" name="headingid" value="<? echo $this->fields['id_heading']; ?>" />
		<input type="hidden" name="lang" value="<? echo $this->fields['id_lang']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td class="label_field" rowspan="3" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION_META']; ?>
							</label>
						</td>
						<td rowspan="3">
							<textarea style="width:370px;height: 80px;" name="art_meta_description" id="art_meta_description"><? echo $this->fields['meta_description']; ?></textarea>
						</td>
						<td colspan="2" style="text-align: center;vertical-align: middle;">
							<?
							$db =dims::getInstance()->getDb();
							$sql="  SELECT 		dims_keywords.word,dims_keywords_index.count
									FROM 		dims_keywords_index
									INNER JOIN 	dims_keywords
									ON 			dims_keywords.id = dims_keywords_index.id_keyword
									AND 		id_record = :id_record
									AND 		id_object = :id_object
									AND 		id_module = :id_module
									AND 		dims_keywords_index.length>2
									ORDER BY 	COUNT DESC
									LIMIT 		0,10";

							$reskey=$db->query($sql,array(
								':id_module'=>array('value'=>$this->fields['id_module'],'type'=>PDO::PARAM_INT),
								':id_record'=>array('value'=>$this->fields['id'],'type'=>PDO::PARAM_INT),
								':id_object'=>array('value'=>_WCE_OBJECT_ARTICLE,'type'=>PDO::PARAM_INT)
							));
							if ($db->numrows($reskey)>0) {
								while ($key=$db->fetchrow($reskey)) {
									echo $key['word']." (".$key['count'].") -";
								}
							}else{
								?>
								<p style="color:#808080;">
									<? echo $_DIMS['cste']['_DIMS_LABEL_NOINDEX']; ?>
								</p>
								<?
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_FREQUENCY_CHANGE']; ?>
							</label>
						</td>
						<td>
							<input type="text" name="art_changefreq" value="<? echo $this->fields['changefreq']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_FORM_TASK_PRIORITY']; ?>
							</label>
						</td>
						<td>
							<input type="text" name="art_priority" value="<? echo $this->fields['priority']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field" style="vertical-align: top;" rowspan="2">
							<label>
								<? echo $_SESSION['cste']['_WCE_KEYWORDS_META']; ?>
							</label>
						</td>
						<td rowspan="2">
							<textarea style="width:370px;height: 80px;" name="art_meta_keywords" id="art_meta_keywords"><? echo $this->fields['meta_keywords']; ?></textarea>
						</td>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_WCE_URLREWRITE']; ?>
							</label>
						</td>
						<td>
							<?
							$domainlist=$this->getDomainList();
							$contentDom = "";
							foreach ($domainlist as $d) {
								$contentDom .= $d['domain'];
								if (sizeof($domainlist)>0) $contentDom .= "<br />";
							}
							?>
							<table cellpadding="0" cellspaccing="0">
								<tr>
									<td>http://</td>
									<td>
									<? echo $contentDom; ?>
									</td>
									<td>
										<input style="width:80%;" type="text" name="art_urlrewrite" value="<? echo $this->fields['urlrewrite']; ?>" />.html
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_WCE_URLREWRITE_OLD']; ?>
							</label>
						</td>
						<td>
							<table cellpadding="0" cellspaccing="0">
								<tr>
									<td>http://</td>
									<td>
									<? echo $contentDom; ?>
									</td>
									<td>
										<input style="width:80%;" type="text" name="art_urlrewriteold" value="<? echo $this->fields['urlrewriteold']; ?>" />.html
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="label_field" style="vertical-align: top;">
							<label>
								<? echo $_SESSION['cste']['_WCE_SCRIPT_BOTTOM']; ?>
							</label>
						</td>
						<td>
							<textarea style="width:370px;height: 80px;" name="art_script_bottom"><? echo $this->fields['script_bottom']; ?></textarea>
						</td>
						<td colspan="2"></td>
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
						<a href="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_REF."&action=".module_wce::_GEST_REF_DEF."&headingid=".$this->fields['id_heading']."&articleid=".$this->fields['id']; ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>