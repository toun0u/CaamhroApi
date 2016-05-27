<?php
$db = dims::getInstance()->getDb();
global $desktop;
$tmpPhotoComp = DIMS_TMP_PATH . '/uploadsjquery/'.session_id();
?>

<div class="new_company">
	<div class="zone_new_company">
		<span class="title_new_company">
			<?
			if ($this->fields['id'] != '' && $this->fields['id'] > 0) {
				echo 'Edit Company';
					echo "<input type=\"hidden\" name=\"id_tiers\" value=\"".$this->fields['id']."\">";
			}else
				echo 'New Company';
			?>
		</span>
		<table cellspacing="10" cellpadding="0" style="width:65%">
			<tbody>
				<tr>
					<td>
						<span style="float:right;"><? echo $_SESSION['cste']['_DIMS_LABEL_ENT_RS']; ?></span>
					</td>
					<td>
						<input value="<? echo $this->fields['intitule']; ?>" type="text" id="company_intitule" name="company_intitule" style="width: 240px;" class="name" />
					</td>
					<td rowspan="3">
						<div class="cadre_add_picture" id="cadre_new_company">
								<input type="file" id="opp_company_photo" />
								<input type="hidden" id="photo_path_company" name="photo_path_company" value="" />
								<!--<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />-->
						</div>
						<span style="float: right; width: 100%;font-size:10px;font-weight:normal;"><i>60x60px</i></span>
					</td>
				</tr>
				<!--<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?>
					</td>
					<td>
						<select id="new_company_search_country" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_START_TYPE_COUNTRY']; ?>" style="width: 240px;" name="country" class="country">
							<option value=""></option>
							<?
							$sel = "SELECT	*
								FROM	dims_country";
							$res = $db->query($sel);
							while ($r = $db->fetchrow($res))
								if ($r['id'] == $this->fields['id_country'])
									echo '<option value="'.$r['id'].'" selected=true>'.$r['printable_name'].'</option>';
								else
									echo '<option value="'.$r['id'].'">'.$r['printable_name'].'</option>';
							?>
						</select>
						<span style="float: right; width: 100%;font-size:10px;font-weight:normal;"><i><?php echo $_DIMS['cste']['_DIMS_START_TYPE_COUNTRY']; ?></i></span>
					</td>

				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
					</td>
					<td class="new_company_search_city" id="new_company_search_city">
						<select data-placeholder="<?php echo $_DIMS['cste']['_DIMS_START_TYPE_CITY']; ?>" style="width: 240px;" name="city" class="new_company_city" id="new_company_city">
							<option value=""></option>
							<?
							if ($this->fields['id_country']>0) {
								$sel = "SELECT	*
									FROM	dims_city where id_country=:idcountry";

								$res = $db->query($sel, array(
									':idcountry' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_country']),
								));

								while ($r = $db->fetchrow($res))
									if (strtoupper($r['label']) == strtoupper($this->fields['ville']))
										echo '<option value="'.$r['id'].'" selected=true>'.$r['label'].'</option>';
									else
										echo '<option value="'.$r['id'].'">'.$r['label'].'</option>';
							}
							?>

						</select>
						<span style="float: right; width: 100%;font-size:10px;font-weight:normal;"><i><?php echo $_DIMS['cste']['_DIMS_START_TYPE_CITY']; ?></i></span>
					</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
					</td>
					<td>
						<input value="<? echo $this->fields['adresse']; ?>" type="text" id="company_adresse" name="company_adresse" style="width: 240px;" class="name" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
					</td>
					<td>
						<input value="<? echo $this->fields['codepostal']; ?>" type="text" id="company_codepostal" name="company_codepostal" style="width: 240px;" class="name" />
					</td>
					<td>&nbsp;</td>
				</tr>-->
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['PHONE_NUMBER']; ?>
					</td>
					<td>
						<input value="<? echo $this->fields['telephone']; ?>" type="text" id="company_telephone" name="company_telephone" style="width: 240px;" class="name" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_FAX']; ?>
					</td>
					<td>
						<input value="<? echo $this->fields['telecopie']; ?>" type="text" id="company_telecopie" name="company_telecopie" style="width: 240px;" class="name" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
					</td>
					<td>
						<input value="<? echo $this->fields['mel']; ?>" type="text" id="company_mel" name="company_mel" style="width: 240px;" class="name" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_ENT_WSITE']; ?>
					</td>
					<td>
						<input value="<? echo $this->fields['site_web']; ?>" type="text" id="company_site_web" name="company_site_web" style="width: 240px;" class="name" />
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="text">
						<? echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?>
					</td>
					<td class="styles_for_tags">
						<select data-placeholder="<?php echo $_DIMS['cste']['_DIMS_START_TYPE_TAG_NAME']; ?>" multiple="" style="width: 400px;" name="tags_company" class="tags_company">
							<option value=""></option>
							<?
							$lstCateg = tag_category::getForObject(tiers::MY_GLOBALOBJECT_CODE);
							foreach($lstCateg as $cat){
								$lstTag = $cat->getTagLink();
								if(count($lstTag)){
									?>
									<optgroup label="<?= $cat->get('label'); ?>">
									<?php
									foreach($lstTag as $tag){
										?>
										<option value="<?= $tag->get('id'); ?>"><?= $tag->get('tag'); ?></option>
										<?
									}
									?>
									</optgroup>
									<?php
								}
							}
							$lstTag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'id_category'=>0, 'type'=>tag::TYPE_DEFAULT),' ORDER BY tag ');
							if(count($lstTag)){
								?>
								<optgroup label="<?= $_SESSION['cste']['_UNCATEGORIZED']; ?>">
								<?php
								foreach($lstTag as $tag){
									?>
									<option value="<?= $tag->get('id'); ?>"><?= $tag->get('tag'); ?></option>
									<?
								}
								?>
								</optgroup>
								<?php
							}
							?>
						</select>
						<span style="float: right; width: 100%;font-size:10px;font-weight:normal;"><i><?php echo $_DIMS['cste']['_DIMS_START_TYPE_TAG_NAME']; ?></i></span>
					</td>
					<td>&nbsp;</td>

				</tr>
				<tr>
					<td colspan="2" align="center">
						<input onclick="javascript:saveCompanyActivity();" type="button" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
						<span> or </span>
						<input type="button" value="Cancel" onclick="javascript:hide_new_company_form();" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	function remplaceImageUploaded(path, id){
		$('#'+id+'Uploader').css('display', 'none');
		$("div.cadre_add_picture[id='cadre_new_company']").append("<img onclick=\"javascript:newFileLoader('"+id+"');\" src=\"<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png\" style=\"position:absolute;margin-top:-14px;margin-left:68px;cursor:pointer;\"/> <img src=\""+path+"\" style=\"width:60px;height:60px;margin-top:5px;margin-left:5px;\" />");
		$("input#photo_path_company").val(path);
	}

	$(document).ready(function(){

		<?php
		if ($this->fields['id'] == '' || $this->fields['id'] == 0){
			?>
			$('input#company_intitule').val($('input#editbox_search_company').val());
			<?
		}
		?>
		$("select.tags_company")
			.chosen({no_results_text: "<div onclick=\"javascript:addNewTag('styles_for_tags');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"})
			/*.change(function(){
				selTag('tags_company',$(this).val());
			})*/;
		$("select.new_company_city").chosen({allow_single_deselect:true, no_results_text: "<div onclick=\"javascript:addNewCity('new_company_search_city','new_company_search_country');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"});
		$("select.country").chosen({no_results_text: "No results matched", allow_single_deselect:true});
		$("select.country")
			.chosen({allow_single_deselect:true, no_results_text: "No results matched"})
			.change(function(){
				refreshCityOfCountry($(this).val(),'new_company_city');
			});
	});
</script>
