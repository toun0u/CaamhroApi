<div>
	<div class="zone_title">
		<h1><?php echo $_SESSION['cste']['_IMPORT_TAB_NEW_COMPANY'];?></h1>
	</div>
	<div class="form_object_block">
		<?php
			global $dims;
			if(!empty($_SESSION['dims']['form_scratch']['companies']['success']) && $_SESSION['dims']['form_scratch']['companies']['success']){
				?>
				<div class="success">
					<?= $_SESSION['cste']['COMPANY_CREATED_WITH_SUCCESS']; ?>
				</div>
				<?php
				unset($_SESSION['dims']['form_scratch']['companies']['success']);//permet de dégager le message si on fait F5
			}
		?>

		<form name="form_client" id="form_client" action="<?php echo $dims->getScriptEnv().'?mode=new_company&action=save'; ?>" method="POST" enctype="multipart/form-data">
				<?php
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;

					$glob_message = $this->getLightAttribute("global_error");
					if(empty($glob_message)){
						?>
						<div class="global_message error_message" style="display: none;"></div>
						<?php
					}
					else{
						if(is_array($glob_message)) {
							?>
							<ul class="global_message error_message">
								<?php
								foreach($glob_message as $error) {
									?>
									<li>
										<?php echo $glob_message;?>
									</li>
									<?php
								}
								?>
							</ul>
							<?php
						}
						else {
							?>
							<div class="global_message error_message"><?php echo $glob_message;?></div>
							<?php
						}
					}
				?>

				<div class="sub_bloc">
					<h3>
						<? echo ucfirst(strtolower($_SESSION['cste']['_DIMS_LABEL_PARENT_COMPANY'])); ?>
					</h3>
					<div class="sub_bloc_form">
						<table>
							<tr>
								<td class="label_field">
									<label for="tiers_id_tiers"><?php echo $_SESSION['cste']['_DIMS_SELECT_A_COMPANY']; ?></label>
								</td>
								<td class="value_field">
									<select name="tiers_id_tiers" id="tiers_id_tiers" style="width: 400px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_A_COMPANY']; ?>">
										<option value=""></option>
										<?php
										$token->field("tiers_id_tiers");
										$a_tiers = tiers::getAllTiers();
										foreach ($a_tiers as $tiers) {
											echo '<option value="'.$tiers->getId().'">'.$tiers->getIntitule().'</option>';
										}
										?>
									</select>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="sub_bloc">
					<h3>
						<? echo ucfirst(strtolower($_SESSION['cste']['_DIMS_PERS_COORD'])); ?>
					</h3>
					<div class="sub_bloc_form">
						<?php
						foreach($this->getAllLightAttributes() as $attr => $val){
							?>
							<input type="hidden" name="<?php echo $attr; ?>" value="<?php echo $val;?>" />
							<?php
								$token->field($attr, $attr);
						}
						if(!$this->isNew()){
							?>
							<input type="hidden" name="id_globalobject" value="<?php echo $this->fields['id_globalobject'];?>" />
							<?php
								$token->field("id_globalobject", $this->fields['id_globalobject']);
						}
						?>
						<table>
							<tr>
								<td class="label_field">
									<label for="tiers_intitule"><?php echo $_SESSION['cste']['_BUSINESS_FIELD_NAME']; ?></label><span class="required">*</span>
								</td>
								<td class="value_field" colspan="3">
									<input type="text" name="tiers_intitule" id="tiers_intitule" value="<?php echo (!isset($this->fields["intitule"]))?'':$this->fields["intitule"];?>" rel="requis"/>
									<?
										$token->field("tiers_intitule");
									?>
								</td>
							</tr>
							<tr><td></td><td colspan="3"><div class="mess_error" id="def_tiers_intitule"></div></td></tr>

							<tr>
								<td class="label_field">
									<label for="photo"><?php echo $_SESSION['cste']['_DIMS_LABEL_PHOTO']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<input type="file" name="photo" id="photo" rev="ext:jpg,jpeg,png,gif"/>
									<?php
									$token->field("photo");
									if(! $this->isNew()){
										$web_path = $this->getPhotoWebPath(24);
										if($web_path != '' && file_exists($this->getPhotoPath(24))){
											?>
												<img class="existing_img" src="<?php echo $web_path; ?>" border="0" />
											<?php
										}
									}
									?>
								</td>
							</tr>
							<tr><td></td><td colspan="3"><div class="mess_error" id="def_photo"></div></td></tr>
							<tr>
								<td class="label_field">
									<label for="tiers_mel"><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<input type="text" name="tiers_mel" id="tiers_mel" value="<?php echo (!isset($this->fields["mel"]))?'':$this->fields["mel"];?>"/>
									<?
										$token->field("tiers_mel");
									?>
								</td>
							</tr>
							<tr><td></td><td colspan="3"><div class="mess_error" id="def_tiers_mel"></div></td></tr>
							<tr>
								<td class="label_field">
									<label for="tiers_telephone"><?php echo $_SESSION['cste']['PHONE_NUMBER']; ?></label>
								</td>
								<td class="value_field">
									<input type="text" name="tiers_telephone" id="tiers_telephone" value="<?php echo (!isset($this->fields["telephone"]))?'':$this->fields["telephone"];?>" />
									<?
										$token->field("tiers_telephone");
									?>
								</td>
								<td class="label_field">
									<label for="tiers_telecopie"><?php echo $_SESSION['cste']['_DIMS_LABEL_FAX']; ?></label>
								</td>
								<td class="value_field">
									<input type="text" name="tiers_telecopie" id="tiers_telecopie" value="<?php echo (!isset($this->fields["telecopie"]))?'':$this->fields["telecopie"];?>"/>
									<?
										$token->field("tiers_telecopie");
									?>
								</td>
							</tr>
							<tr>
								<td class="label_field">
									<label for="tiers_adresse"><?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<input type="text" name="tiers_adresse" id="tiers_adresse" value="<?php echo (!isset($this->fields["adresse"]))?'':$this->fields["adresse"];?>"/>
									<?
										$token->field("tiers_adresse");
									?>
								</td>
							</tr>
							<tr>
								<td class="label_field">
									<label for="tiers_codepostal"><?php echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?></label>
								</td>
								<td class="value_field">
									<input type="text" name="tiers_codepostal" id="tiers_codepostal" value="<?php echo (!isset($this->fields["codepostal"]))?'':$this->fields["codepostal"];?>"/>
									<?
										$token->field("tiers_codepostal");
									?>
								</td>
								<td></td><td></td>
							</tr>
							<tr>
								<td class="label_field">
									<label for="tiers_id_country"><?php echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?></label>
								</td>
								<td class="value_field">
									<select name="tiers_id_country" id="tiers_id_country" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_COUNTRY']; ?>">
										<?
											$token->field("tiers_id_country");
										?>
										<option value=""></option>
										<?php
										require_once DIMS_APP_PATH.'modules/system/class_country.php';
										$a_countries = country::getAllCountries();
										$sel_Country = null;
										if (sizeof($a_countries)) {
											foreach ($a_countries as $country) {
												$sel = '';
												if (isset($this->fields['id_country']) && $country->fields['id'] == $this->fields['id_country']){
													$sel = "selected=true";
													$sel_Country = $country;
												} else if (stripslashes($country->fields['printable_name']) == 'France'){
													$sel = "selected=true";
													$sel_Country = $country;
												}
												echo '<option value="'.$country->fields['id'].'"'.$sel.'>'.stripslashes($country->fields['printable_name']).'</option>';
											}
										}
										?>
									</select>
								</td>
								<td class="label_field">
									<label for="tiers_id_city"><?php echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?></label>
								</td>
								<td class="value_field" id="opportunity_rech_add_city" >
									<select id="tiers_id_city" type="text" name="tiers_id_city" <?php echo ($sel_Country != null && $sel_Country->fields['id'] > 0) ? '' : 'disabled="disabled"'; ?> style="width:100%" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_CITY']; ?>">
										<?
											$token->field("tiers_id_city");
										?>
										<option value=""></option>
										<?
										if ($sel_Country != null && $sel_Country->fields['id'] > 0){
											$citys = $sel_Country->getAllCity();
											foreach($citys as $city){
												if (isset($this->fields['id_city']) && $this->fields['id_city'] == $city->fields['id'])
													echo '<option value="'.$city->fields['id'].'" selected=true>'.$city->fields['label'].'</option>';
												else
													echo '<option value="'.$city->fields['id'].'">'.$city->fields['label'].'</option>';
											}
										}
										?>
									</select>
								</td>
							</tr>
						</table>
					</div>
				</div>
			<div class="sub_form">
					<div class="form_buttons">
						<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>
						<div><input type="submit" value="<?php echo $_SESSION['cste']['SAVE_COMPANY']; ?>"> <?php if( $this->isNew() ) echo $_SESSION['cste']['_DIMS_OR']; ?></div>
						<?php
						if( $this->isNew() ){
							?>
							<div><input type="submit" name="continue" value="<?php echo $_SESSION['cste']['SAVE_COMPANY']. ' '.$_SESSION['cste']['AND_CONTINUE']; ?>"></div>
							<?php
						}
						$back_op = $this->getLightAttribute("back_op");
						if(isset($back_op)){
							?>
							<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<?php echo $back_op;?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
							<?php
						}
						?>
					</div>
				</div>
			<?
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("select#tiers_id_tiers").chosen({
			allow_single_deselect:true,
		});

		$("#form_client").dims_validForm({messages: {	defaultError: 	'<? echo addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']); ?>',
														formatMail: 	'<? echo addslashes($_SESSION['cste']['WRONG_EMAIL_FORMAT']); ?>',
														globalMessage: 	'<? echo addslashes($_SESSION['cste']['PLEASE_VERIFY_FIELDS']); ?>',
														login: 			'<? echo addslashes($_SESSION['cste']['LOGIN_ALREADY_USED']); ?>',
													},
												    displayMessages: true,
												    refId: 'def',
												    globalId: 'global_message',
												    extended_controls: {tiers_intitule: verifyCompanyTitle }
										});
		$("select#tiers_id_city").chosen(
				{
					allow_single_deselect:true,
					no_results_text: "<div class=\"button_add_city\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LA']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"
				}
		);
		$("select#tiers_id_country")
			.chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"})
			.change(function(){
				if($(this).val() != '') {
					$('#tiers_id_city').removeAttr('disabled');
				}
				else {
					$('#tiers_id_city').attr('disabled','disabled');
				}
				refreshCityOfCountry($(this).val(),'tiers_id_city');
		});

		$('div.button_add_city').live('click',function(){
			$(this).die('click');
			addNewCity('opportunity_rech_add_city','tiers_id_country');
		});

		function verifyCompanyTitle(){
			var text = '';
		    var pb = false;
			$.ajax({
		        type: "GET",
		        url: "/admin.php",
		        data: {
		            'dims_op': 'desktopv2',
		            'action' : 'verify_company_title',
		            'value' : $('#tiers_intitule').val()
		        },
		        dataType: "json",
		        async: false,
		        success: function(data){
					if(data != null && data != 'undefined' && data != '' ){
						if (!$('#create_new_company').is(':checked')) {
							pb = true;
							text = '<?php echo addslashes($_SESSION['cste']['MAYBE_THIS_COMPANY_EXISTS']);?> : <ul>' ;
							for( var i=0 ; i < data.length ; i++){
								text += '<li><a href="'+data[i].link+'" target="_blank">'+data[i].intitule+'</a></li>';
							}
							text += '</ul>';

							// ajout de la checkbox
							text += '<input type="checkbox" id="create_new_company" name="create_new_company" value="1" /> <label for="create_new_company"><?php echo $_DIMS['cste']['CREATE_NEW_COMPANY']; ?></label>';
							<?
								$token->field("create_new_company");
							?>
						}
					}
		        },
		        error: function(data){}
		    });
		    return {error: pb, message: text }
		}
	});
</script>
