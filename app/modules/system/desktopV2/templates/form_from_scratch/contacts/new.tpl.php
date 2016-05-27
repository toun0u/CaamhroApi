<div>
	<div class="zone_title">
		<h1><?php echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT'];?></h1>
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

		<form name="form_client" id="form_client" action="<?php echo $dims->getScriptEnv().'?mode=new_contact&action=save'; ?>" method="POST" enctype="multipart/form-data">
			<?php
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;

			if(!$this->isNew()){
				?>
				<input type="hidden" value="<?= $this->get('id_globalobject'); ?>" name="id_globalobject" />
				<?php
				$token->field('id_globalobject', $this->get('id_globalobject'));
			}

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
					<? echo ucfirst(strtolower($_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT'])); ?>
				</h3>
				<div class="sub_bloc_form">
					<table>
						<tr>
							<td class="label_field">
								<label for="contact_civilite"><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field" colspan="3">
								<?php
								$civilite = (isset($this->fields['civilite']) ) ? $this->fields['civilite'] : '';
								?>
								<select name="contact_civilite" id="contact_civilite" rel="requis">
									<?
										$token->field("contact_civilite");
									?>
									<option value="dims_nan" <?php if( empty($civilite) ) echo 'selected="selected"'; ?>></option>
									<option value="M." <?php if( $civilite == 'M.' ) echo 'selected="selected"'; ?>>M.</option>
									<option value="Mme" <?php if( $civilite == 'Mme' ) echo 'selected="selected"'; ?>>Mme</option>
									<option value="Melle" <?php if( $civilite == 'Melle' ) echo 'selected="selected"'; ?>>Melle</option>
								</select>
							</td>
						</tr>
						<tr><td></td><td colspan="3"><div class="mess_error" id="def_contact_civilite"></div></td></tr>
						<tr>
							<td class="label_field">
								<label for="contact_firstname"><?php echo $_SESSION['cste']['_DIMS_LABEL_FIRSTNAME']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field">
								<input type="text" name="contact_firstname" id="contact_firstname" value="<?php echo (!isset($this->fields["firstname"]))?'':$this->fields["firstname"];?>" rel="requis"/>
								<?
									$token->field("contact_firstname");
								?>
							</td>
							<td class="label_field">
								<label for="contact_lastname"><?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?></label><span class="required">*</span>
							</td>
							<td class="value_field">
								<input type="text" name="contact_lastname" id="contact_lastname" value="<?php echo (!isset($this->fields["lastname"]))?'':$this->fields["lastname"];?>" rel="requis"/>
								<?
									$token->field("contact_lastname");
								?>
							</td>
						</tr>
						<tr><td></td><td><div class="mess_error" id="def_contact_firstname"></div></td><td></td><td><div class="mess_error" id="def_contact_lastname"></div></td></tr>
						<tr>
							<td class="label_field">
								<label for="photo"><?php echo $_SESSION['cste']['_DIMS_LABEL_PHOTO']; ?></label>
							</td>
							<td class="value_field" colspan="3">
								<input type="file" name="photo" id="photo" rev="ext:jpg,jpeg,png,gif"/>
								<?
									$token->field("photo");
								?>
								<?php
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
								<label for="contact_email"><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?></label>
							</td>
							<td class="value_field" colspan="3">
								<input type="text" name="contact_email" id="contact_email" value="<?php echo (!isset($this->fields["email"]))?'':$this->fields["email"];?>"/>
								<?
									$token->field("contact_email");
								?>
							</td>
						</tr>
						<tr><td></td><td colspan="3"><div class="mess_error" id="def_contact_email"></div></td></tr>
						<tr>
							<td class="label_field">
								<label for="contact_phone"><?php echo $_SESSION['cste']['PHONE_NUMBER']; ?></label>
							</td>
							<td class="value_field">
								<input type="text" name="contact_phone" id="contact_phone" value="<?php echo (!isset($this->fields["phone"]))?'':$this->fields["phone"];?>" />
								<?
									$token->field("contact_phone");
								?>
							</td>
							<td class="label_field">
								<label for="contact_fax"><?php echo $_SESSION['cste']['_DIMS_LABEL_FAX']; ?></label>
							</td>
							<td class="value_field">
								<input type="text" name="contact_fax" id="contact_fax" value="<?php echo (!isset($this->fields["fax"]))?'':$this->fields["fax"];?>"/>
								<?
									$token->field("contact_fax");
								?>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label for="tags[]"><?php echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></label>
							</td>
							<td class="value_field tag_for_contact_opp" colspan="3">
								<select multiple="" style="width: 400px;" name="tags[]" class="tags" data-placeholder="">
									<option value=""></option>
									<?
									$token->field('tags');

									$lstUsed = array();
									$myTags = $this->getMyTags();
									foreach($myTags as $t)
										$lstUsed[] = $t->get('id');
									$lstCateg = tag_category::getForObject(contact::MY_GLOBALOBJECT_CODE);
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

						<?php
						// construction des champs dynamiques
						foreach ($this->getDynamicFields() as $elem) {

							if ($elem['namefield']!='civilite' && $elem['namefield']!='address' && $elem['namefield']!='lastname' &&
							 $elem['namefield']!='firstname' && $elem['namefield']!='postalcode' && $elem['namefield']!='city') {

							}
						}

						?>
					</table>
				</div>
			</div>

			<div class="sub_form">
				<div class="form_buttons">
					<div><span class="mandatory_fields">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span></div>

					<?php
					//if( $this->isNew() ){
						?>
						<div><input type="submit" name="continue" value="<?php echo $_SESSION['cste']['_DIMS_LABEL_SAVE_CONTACT']. ' '.$_SESSION['cste']['AND_CONTINUE']; ?>"></div>
						<?php
					//}
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
	function dimsAjaxLoading(contentid,loadUrl,focuselement) {
		$("#"+contentid)
			.html(ajax_load)
			.load(loadUrl, {language: "php", version: 5}, function(responseText){
				if (focuselement!='') {
					$("#"+focuselement).focus();
				}
				$('.ajaxForm').submit(function (event) {
					event.preventDefault();
					$.ajax({
						type: $(this).attr('method'),
						url: $(this).attr('action'),
						data: $(this).serialize()
					});
				});
			});
	}

	function verifyContactName(){
		var text = '';
	    var pb = false;
		$.ajax({
	        type: "GET",
	        url: "/admin.php",
	        data: {
	            'dims_op': 'desktopv2',
	            'action' : 'verify_contact_name',
	            'value' : $('#contact_firstname').val()+' '+$('#contact_lastname').val()
	        },
	        dataType: "json",
	        async: false,
	        success: function(data){
				if(data != null && data != 'undefined' && data != '' ){
					if (!$('#create_new_contact').is(':checked')) {
						pb = true;
						text = '<?php echo addslashes($_SESSION['cste']['MAYBE_THIS_CONTACT_EXISTS']);?> : <ul>' ;
						for( var i=0 ; i < data.length ; i++){
							text += '<li><a href="'+data[i].link+'" target="_blank">'+data[i].firstname+' '+data[i].lastname+'</a></li>';
						}
						text += '</ul>';

						// ajout de la checkbox
						text += '<input type="checkbox" id="create_new_contact" name="create_new_contact" value="1" /> <label for="create_new_contact"><?php echo $_DIMS['cste']['CREATE_NEW_CONTACT']; ?></label>';
					}
				}
	        },
	        error: function(data){}
	    });
	    return {error: pb, message: text }
	}

	$(document).ready(function(){
		$("td.tag_for_contact_opp select.tags").chosen({no_results_text: "<div onclick=\"javascript:addNewTag('tag_for_contact_opp');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"});

		$('.ajaxForm').submit(function (event) {
			event.preventDefault();
			$.ajax({
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: $(this).serialize()
			});
		});

		$("#form_client").dims_validForm({
			messages: {	defaultError: 	'<? echo addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']); ?>',
					formatMail: 	'<? echo addslashes($_SESSION['cste']['WRONG_EMAIL_FORMAT']); ?>',
					globalMessage: 	'<? echo addslashes($_SESSION['cste']['PLEASE_VERIFY_FIELDS']); ?>',
					login: 			'<? echo addslashes($_SESSION['cste']['LOGIN_ALREADY_USED']); ?>',
				},
			    displayMessages: true,
			    refId: 'def',
			    globalId: 'global_message',
			    extended_controls: {contact_lastname: verifyContactName }
		});
	});
</script>
