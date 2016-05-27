<script type="text/javascript" src="./common/js/dims_autocomplete.js"></script>
<form name="form_new_tiers" class="ajaxForm" action="<?php echo '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=save_tiers';?>" method="POST" enctype="multipart/form-data">
	<div class="sub_bloc">
		<div class="sub_bloc_form">
			<table>
				<tr>
					<td class="label_field">
						<label for="tiers_intitule"><?php echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?></label><span class="required">*</span>
					</td>
					<td class="value_field" colspan="3">
						<input type="text" name="tiers_intitule" id="tiers_intitule" value="<?= $this->get('intitule'); ?>" autocomplete="off" />
						<input type="hidden" name="id_tiers" id="id_tiers" value="<?= $this->get('id'); ?>" />
						<div id="ac_tiers" class="ac_container" style="display:none;">
							<ul id="ul_ac_tiers">
							</ul>
						</div>
					</td>
				</tr>
				<tr><td></td><td colspan="3"><div class="mess_error" id="def_tiers_intitule"></div></td></tr>
				<tr>
					<td class="label_field">
						<label for="photo"><?php echo $_SESSION['cste']['_DIMS_LABEL_PHOTO']; ?></label>
					</td>
					<td class="value_field" colspan="3">
						<input type="file" name="photo" id="photo" rev="ext:jpg,jpeg,png,gif"/>
					</td>
				</tr>
				<tr><td></td><td colspan="3"><div class="mess_error" id="def_photo"></div></td></tr>
				<tr>
					<td class="label_field">
						<label for="tiers_mel"><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?></label>
					</td>
					<td class="value_field">
						<input type="text" name="tiers_mel" id="tiers_mel" value="<?= $this->get('mel'); ?>" />
					</td>
					<td class="label_field">
						<label for="tiers_site_web"><?php echo $_SESSION['cste']['_DIMS_LABEL_ENT_WSITE']; ?></label>
					</td>
					<td class="value_field">
						<input type="text" name="tiers_site_web" id="tiers_site_web" value="<?= $this->get('site_web'); ?>" />
					</td>
				</tr>
				<tr><td></td><td colspan="3"><div class="mess_error" id="def_tiers_mel"></div></td></tr>
				<tr>
					<td class="label_field">
						<label for="tiers_telephone"><?php echo $_SESSION['cste']['PHONE_NUMBER']; ?></label>
					</td>
					<td class="value_field">
						<input type="text" name="tiers_telephone" id="tiers_telephone" value="<?= $this->get('telephone'); ?>" />
					</td>
					<td class="label_field">
						<label for="tiers_telecopie"><?php echo $_SESSION['cste']['_DIMS_LABEL_FAX']; ?></label>
					</td>
					<td class="value_field">
						<input type="text" name="tiers_telecopie" id="tiers_telecopie" value="<?= $this->get('telecopie'); ?>"/>
					</td>
				</tr>
				<tr>
					<td class="label_field">
						<label for="tags[]"><?php echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></label>
					</td>
					<td class="value_field tag_for_tiers_opp" colspan="3">
						<select multiple="" style="width: 400px;" name="tags[]" class="tags" data-placeholder="">
							<option value=""></option>
							<?
							$lstUsed = array();
							$myTags = $this->getMyTags();
							foreach($myTags as $t)
								$lstUsed[] = $t->get('id');
							$lstCateg = tag_category::getForObject(tiers::MY_GLOBALOBJECT_CODE);
							foreach($lstCateg as $cat){
								$lstTag = $cat->getTagLink();
								if(count($lstTag)){
									?>
									<optgroup label="<?= $cat->get('label'); ?>">
										<?php
										foreach($lstTag as $tag){
											if(in_array($tag->get('id'), $lstUsed)){
												?>
												<option selected="true" value="<? echo $tag->fields['id']; ?>"><? echo $tag->fields['tag']; ?></option>
												<?
											}else{
												?>
												<option value="<? echo $tag->fields['id']; ?>"><? echo $tag->fields['tag']; ?></option>
												<?
											}
										}
										?>
									</optgroup>
									<?php
								}
							}
							$lstTag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'id_category'=>0),' ORDER BY tag ');
							?>
							<optgroup label="<?= $_SESSION['cste']['_UNCATEGORIZED']; ?>">
								<?php
								foreach($lstTag as $tag){
									if(in_array($tag->get('id'), $lstUsed)){
										?>
										<option selected="true" value="<? echo $tag->fields['id']; ?>"><? echo $tag->fields['tag']; ?></option>
										<?
									}else{
										?>
										<option value="<? echo $tag->fields['id']; ?>"><? echo $tag->fields['tag']; ?></option>
										<?
									}
								}
								?>
							</optgroup>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="sub_form">
		<div class="form_buttons">
			<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
			<?php if($this->get('id_tiers') != '' && $this->get('id_tiers') > 0){

			}else{ ?>
				<div><input type="submit" name="services" value="<?php echo $_SESSION['cste']['_ADD_SERVICE']; ?>"><?= $_SESSION['cste']['_DIMS_OR']; ?></div>
			<?php } ?>
			<div><input type="submit" name="continue" value="<?php echo $_SESSION['cste']['SAVE_COMPANY']. ' '.$_SESSION['cste']['AND_CONTINUE']; ?>"><?= $_SESSION['cste']['_DIMS_OR']; ?></div>
			<div><input type="button" onclick="javascript:document.location.href='/admin.php?submenu=<?= _DESKTOP_V2_DESKTOP; ?>&mode=new_contact&action=step3';" value="<?php echo $_SESSION['cste']['_IMPORT_GO_NEXT_STEP']; ?>"></div>
			<?
			$back_op = $this->getLightAttribute("back_op");
			if(isset($back_op)){
				?>
				<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<?php echo $back_op;?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
				<?php
			}
			?>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$("td.tag_for_tiers_opp select.tags").chosen({no_results_text: "<div onclick=\"javascript:addNewTag('tag_for_tiers_opp');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"});
		$('#tiers_intitule').dims_autocomplete( { dims_op: 'desktopv2', action: 'search_tiers' }, 2, 500, '#id_tiers', '#ac_tiers', '#ul_ac_tiers', '<li>${label}</li>', '', null );
		$("#id_tiers").change(function(){
			var idTiers = $(this).val();
			$.ajax({
				type: "GET",
		        url: "/admin.php",
		        data: {
		            'dims_op': 'desktopv2',
		            'action' : 'get_tiers_data',
		            'value' : idTiers
		        },
		        dataType: "json",
		        async: false,
		        success: function(data){
					if(data != null && data != 'undefined' && data != '' ){
						$('#tiers_mel').val(data['mel']);
						$('#tiers_telephone').val(data['telephone']);
						$('#tiers_telecopie').val(data['telecopie']);
						if($('img.existing_img',$("#photo").parent()).length){
							$('img.existing_img',$("#photo").parent()).remove();
						}
						if(data['photo'] != ''){
							$("#photo").after('<img class="existing_img" src="'+data['photo']+'" border="0" />');
						}
					}
		        }
			});
		});
	});
</script>