<div>
	<div class="zone_title">
		<h1><?php echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT'];?></h1>
	</div>
	<div class="form_object_block">
		<?php
		global $dims;
		if(!empty($_SESSION['dims']['form_scratch']['contacts']['success']) && $_SESSION['dims']['form_scratch']['contacts']['success']){
			?>
			<div class="success">
				<?= $_SESSION['cste']['CONTACT_CREATED_WITH_SUCCESS']; ?>
			</div>
			<?php
			unset($_SESSION['dims']['form_scratch']['contacts']['success']);//permet de dégager le message si on fait F5
		}
		?>

		<?php
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
			<div class="sub_bloc_form" style="float:left;width:99%">
					<div style="display:block;float:left;width:20%;">
						<?php
						global $_DIMS;
						$file = $this->getPhotoPath(60);//real_path
						if(file_exists($file)){
							?>
							<img class="picture" src="<?php echo $this->getPhotoWebPath(60); ?>">
							<?php
						}
						else{
							?>
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/contact_default_search.png">
							<?php
						}


						?>
					</div>
					<div style="display:block;float:left;width:80%;">
						<table>
							<tr>
								<td class="label_field">
									<label for="contact_civilite"><?php echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></label>
								</td>
								<td class="value_field" colspan="3" style="width:70%">
									<?php
									$civilite = (isset($this->fields['civilite']) ) ? $this->fields['civilite'] : '';
									echo $civilite;
									?>

								</td>
							</tr>
							<tr>
								<td class="label_field">
									<label for="contact_firstname"><?php echo $_SESSION['cste']['_DIMS_LABEL_FIRSTNAME']; ?></label>
								</td>
								<td class="value_field" style="width:35%">
									<?php echo $this->fields["firstname"]; ?>
								</td>
								<td class="label_field">
									<label for="contact_lastname"><?php echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?></label>
								</td>
								<td class="value_field" style="width:35%">
									<?php echo $this->fields["lastname"]; ?>
								</td>
							</tr>
							<tr>
								<td class="label_field">
									<label for="contact_email"><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?></label>
								</td>
								<td class="value_field" colspan="3">
									<?php echo $this->fields["email"]; ?>
								</td>
							</tr>

							<tr>
								<td class="label_field">
									<label for="contact_phone"><?php echo $_SESSION['cste']['PHONE_NUMBER']; ?></label>
								</td>
								<td class="value_field">
									<?php echo (!isset($this->fields["phone"]))?'':$this->fields["phone"];?>
								</td>
								<td class="label_field">
									<label for="contact_fax"><?php echo $_SESSION['cste']['_DIMS_LABEL_FAX']; ?></label>
								</td>
								<td class="value_field">
									<?php echo (!isset($this->fields["fax"]))?'':$this->fields["fax"];?>
								</td>
							</tr>
						</table>
					</div>
			</div>
		</div>
		<?php
		if(isset($_SESSION['dims']['newcontact']['id_tiers']) && $_SESSION['dims']['newcontact']['id_tiers'] > 0){
			$tiers = new tiers();
			$tiers->open($_SESSION['dims']['newcontact']['id_tiers']);
			if(!$tiers->isNew()){
				?>
				<div class="sub_bloc">
					<h3>
						<? echo ucfirst(strtolower($_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'])); ?>
					</h3>
					<div class="sub_bloc_form" style="float:left;width:99%">
						<div style="display:block;float:left;width:20%;">
							<?php
							global $_DIMS;
							$file = $tiers->getPhotoPath(60);//real_path
							if(file_exists($file)){
								?>
								<img class="picture" src="<?php echo $tiers->getPhotoWebPath(60); ?>">
								<?php
							}
							else{
								?>
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_default_search.png">
								<?php
							}


							?>
						</div>
						<div style="display:block;float:left;width:80%;">
							<table>
								<tr>
									<td class="label_field">
										<label for="contact_civilite"><?php echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?></label>
									</td>
									<td class="value_field" colspan="3" style="width:70%">
										<?= $tiers->get('intitule'); ?>
									</td>
								</tr>
								<tr>
									<td class="label_field">
										<label for="contact_firstname"><?php echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?></label>
									</td>
									<td class="value_field" style="width:35%">
										<?= $tiers->get("mel"); ?>
									</td>
									<td class="label_field">
										<label for="contact_firstname"><?php echo $_SESSION['cste']['_DIMS_LABEL_ENT_WSITE']; ?></label>
									</td>
									<td class="value_field" style="width:35%">
										<?= $tiers->get("site_web"); ?>
									</td>
								</tr>
								<tr>
									<td class="label_field">
										<label for="contact_lastname"><?php echo $_SESSION['cste']['PHONE_NUMBER']; ?></label>
									</td>
									<td class="value_field" style="width:35%">
										<?= $tiers->get("telephone"); ?>
									</td>
									<td class="label_field">
										<label for="contact_lastname"><?php echo $_SESSION['cste']['_DIMS_LABEL_FAX']; ?></label>
									</td>
									<td class="value_field" style="width:35%">
										<?= $tiers->get("telecopie"); ?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<?php
			}
		}
		?>
		<div class="sub_bloc"  style="clear:both;float:left;margin-top:10px;">
			<h3>
				<? echo ucfirst(strtolower($_SESSION['cste']['_DIMS_PERS_COORD'])); ?>
			</h3>
			<div class="sub_bloc_form" style="float:left;width:99%;clear:both;">
				<div id="content_addresses"></div>

			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
	var ajax_load = "<img src='/common/img/loading16.gif' alt='loading...' />";


	window['saveLittleAddress'] = function saveLittleAddress(id_adr) {
		var form = $('#form_address'+id_adr);
		$(form).submit();

		setTimeout(function() {
			dimsAjaxLoading("content_addresses","<? echo '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=list_addresses';?>");
		},1000);
	}

	function submitNewTypeAddr(elem){
		var value = $('td#add_type input:first',$(elem).parents('table:first')).val();
		$.ajax({
			type: "POST",
			url: './admin',
			data: {
				dims_op: 'desktopv2',
				action: 'add_new_type_addr',
				val: value
			},
			async: false,
			dataType: "json",
			success: function(data){
				var options = "";
				for(var i=0; i<data.length; i++){
					if(data[i]['selected'])
						options = options+'<option value="'+data[i]['go']+'" selected=true>'+data[i]['label']+'</option>';
					else
						options = options+'<option value="'+data[i]['go']+'">'+data[i]['label']+'</option>';
				}
				$('select#type_address',$(elem).parents('table:first')).html(options).trigger("liszt:updated");
				$('td#add_type input:first',$(elem).parents('table:first')).val('');
				$('td#add_type',$(elem).parents('table:first')).hide();
			}
		});
	}

	function addAddress() {

		$("#add_address_content")
			.html(ajax_load)
			.load("<? echo '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=add_address';?>", {language: "php", version: 5}, function(responseText){

				$("#adr_address").focus();

				$('.ajaxForm').on('submit',function (event) {
					event.preventDefault();
					var form = $(this);
					$.ajax({
						type: form.attr('method'),
						url: form.attr('action'),
						data: form.serialize()
					});
				});

				$("select#adr_id_city").chosen({
					allow_single_deselect:true,
					no_results_text: "<div class=\"button_add_city\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LA']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"
				});
				$("select#adr_id_country")
					.chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"})
					.change(function(){
						if($(this).val() != '') {
							$('#adr_id_city').removeAttr('disabled');
						}
						else {
							$('#adr_id_city').attr('disabled','disabled');
						}
						refreshCityOfCountry($(this).val(),'adr_id_city');
				});
				$("select#type_address").chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"});

				$('div.button_add_city').live('click',function(){
					$(this).die('click');
					addNewCity('opportunity_rech_add_city','adr_id_country');
				});

				$("#form_address0").dims_validForm({
					messages: {	defaultError: 	'<? echo addslashes($_SESSION['cste']['THIS_FIELD_IS_MANDATORY']); ?>',
							formatMail: 	'<? echo addslashes($_SESSION['cste']['WRONG_EMAIL_FORMAT']); ?>',
							globalMessage: 	'<? echo addslashes($_SESSION['cste']['PLEASE_VERIFY_FIELDS']); ?>',
							login: 		'<? echo addslashes($_SESSION['cste']['LOGIN_ALREADY_USED']); ?>',
						},
					    displayMessages: true,
					    refId: 'def',
					    globalId: 'global_message'
				});
			});


	}

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

	$(document).ready(function(){
		dimsAjaxLoading("content_addresses","<? echo '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=list_addresses';?>");
		$('.ajaxForm').submit(function (event) {
			event.preventDefault();
			$.ajax({
				type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: $(this).serialize()
			});
		});

	});
</script>
