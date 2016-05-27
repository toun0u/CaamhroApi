<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_PARAM); ?>" name="save_meta" enctype="multipart/form-data">
	<input type="hidden" name="action" value="<? echo module_wce::_PARAM_INFOS_SAVE_REF; ?>" />
	<div class="title_h3">
		<h3><? echo $_SESSION['cste']['_WCE_PAGE_REFER']; ?> - <? echo $_SESSION['cste']['_DIMS_LABEL_META']; ?></h3>
	</div>
	<div class="form_object_block">

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
							<input type="text" value="<? echo $this->fields['title']; ?>" name="work_title" />
						</td>
					</tr>
					<tr class="table_ligne1">
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['meta_description']; ?>" name="work_meta_description" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_DIMS_LABEL_KEYWORDS']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['meta_keywords']; ?>" name="work_meta_keywords" />
						</td>
					</tr>
					<tr class="table_ligne1">
						<td class="label_field">
							<label>
								<? echo $_SESSION['cste']['_AUTHOR']; ?>
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['meta_author']; ?>" name="work_meta_author" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								Copyright
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['meta_copyright']; ?>" name="work_meta_copyright" />
						</td>
					</tr>
					<tr class="table_ligne1">
						<td class="label_field">
							<label>
								Robots
							</label>
						</td>
						<td>
							<input type="text" value="<? echo $this->fields['meta_robots']; ?>" name="work_meta_robots" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								Favicon
							</label>
						</td>
						<td>
							<?
							if (($fav = $this->getFrontFavicon()) != ''){
								?>
								<img style="float: left;margin-top: 3px; height:16px;" src="<? echo $fav; ?>" />
								<?
							}
							?>
							<input type="file" name="favicon" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								Twitter
							</label>
						</td>
						<td style="padding-right:12px;">
							<span style="margin-top:4px;position: fixed;">@</span>
							<input style="margin-left:12px;" type="text" value="<? echo $this->fields['twitter']; ?>" name="work_twitter" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								Facebook
							</label>
						</td>
						<td style="padding-right: 85px;">
							<span style="margin-top:4px;position: fixed;">facebook.com/</span>
							<input style="margin-left:85px;" type="text" value="<? echo $this->fields['facebook']; ?>" name="work_facebook" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								Google+
							</label>
						</td>
						<td style="padding-right:98px;">
							<span style="margin-top:4px;position: fixed;">plus.google.com/</span>
							<input style="margin-left:98px;" type="text" value="<? echo $this->fields['google_plus']; ?>" name="work_google_plus" />
						</td>
					</tr>
					<tr>
						<td class="label_field">
							<label>
								YouTube
							</label>
						</td>
						<td style="padding-right:110px;">
							<span style="margin-top:4px;position: fixed;">youtube.com/user/</span>
							<input style="margin-left:110px;" type="text" value="<? echo $this->fields['youtube']; ?>" name="work_youtube" />
						</td>
					</tr>
				</table>
			</div>

		</div>

</div>

<div class="title_h3">
	<h3><? echo $_SESSION['cste']['CONTENT_MODEL']; ?> - <? echo $_SESSION['cste']['_BUSINESS_FIELD_DEFAULTVALUE']; ?></h3>
</div>
<div class="form_object_block">

	<div class="sub_bloc">
		<div class="sub_bloc_form">
			<table>
				<tr>
					<td class="label_field">
						<label>
							<? echo $_SESSION['cste']['CONTENT_MODEL']; ?>
						</label>
					</td>
					<td>
						<?php
						$wce_models = wce_getmodels();
						?>
						<select name="work_page_default_template" id="work_page_default_template" class="select">

						<option <? echo ($this->fields['page_default_template'] == "" ) ? 'selected=true' : ''; ?> value=""><? echo "aucun"; ?></option>
						<?
						foreach($wce_models["pages_publiques"] as $key => $model) {
							?>
							<option <? echo ($this->fields['page_default_template'] == $model ) ? 'selected=true' : ''; ?> value="<? echo $model; ?>"><? echo $model; ?></option>
							<?
						}
						if(is_array($wce_models["workspace"])) {
							foreach($wce_models["workspace"] as $key => $model) {
								?>
								<option <? echo ($this->fields['model'] == $model) ? 'selected=true' : ''; ?> value="<? echo $model; ?>"><? echo $model; ?></option>
								<?
							}
						}
						?>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<div class="sub_form">
	<div class="form_buttons">
		<div>
			<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
		</div>
		<div>
			<? echo $_SESSION['cste']['_DIMS_OR']; ?>
			<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF; ?>">
				<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
			</a>
		</div>
	</div>
</div>
</form>
