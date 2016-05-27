<div class="container_admin global_content_record">
	<div class="form_object_block" id="edit_address_<?= $this->get('id'); ?>">
		<div class="sub_bloc">
			<h3>
				<?= ucfirst(strtolower($_SESSION['cste']['_DIMS_LABEL_ADDRESS'])); ?>
				<a href="javascript:void(0);" onclick="javascript:editAddress(<?= $this->get('id'); ?>, this);">
					<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/editer20.png" alt="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" />
				</a>
			</h3>
			<div class="sub_bloc_form">
				<div style="display:block;float:left;width:70%;">
					<table>
						<tr>
							<td class="label_field">
								<label for="contact_civilite"><?= $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?></label>
							</td>
							<td class="value_field">
								<?= $this->get('address'); ?>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label for="contact_civilite"><?= $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?> 2</label>
							</td>
							<td class="value_field">
								<?= $this->get('address2'); ?>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label for="contact_civilite"><?= $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?> 3</label>
							</td>
							<td class="value_field">
								<?= $this->get('address3'); ?>
							</td>
						</tr>
						<tr>
							<td class="label_field" style="width:10%;">
								<label for="contact_civilite"><?= $_SESSION['cste']['_DIMS_LABEL_CP']; ?></label>
							</td>
							<td class="value_field" style="width:40%;">
								<?= $this->get('postalcode'); ?>
							</td>
							<td class="label_field" style="width:10%;">
								<label for="contact_civilite"><?= $_SESSION['cste']['_DIMS_LABEL_CITY']; ?></label>
							</td>
							<td class="value_field" style="width:40%;">
								<?php
								$city = $this->getCity();
								echo $city->get('label');
								?>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label for="contact_civilite"><?= $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?></label>
							</td>
							<td class="value_field" colspan="3">
								<?php
								$country = $this->getCountry();
								echo $country->get('printable_name');
								?>
							</td>
						</tr>
						<?php
						$id_ct = $this->getLightAttribute('id_ct');
						if($id_ct != '' && $id_ct > 0){
							$contact = new contact();
							$contact->open($id_ct);
							if(!$contact->isNew() && $contact->get('id_workspace') == $_SESSION['dims']['workspaceid']){
								$lk = $this->getLinkCt($contact->get('id_globalobject'));
								if(!empty($lk)){
									?>
									<tr>
										<td class="label_field">
											<label for="contact_civilite"><?= $_SESSION['cste']['_PHONE']; ?></label>
										</td>
										<td class="value_field">
											<?= $lk->get('phone'); ?>
										</td>
										<td class="label_field">
											<label for="contact_civilite"><?= $_SESSION['cste']['_DIRECTORY_EMAIL']; ?></label>
										</td>
										<td class="value_field">
											<?= $lk->get('email'); ?>
										</td>
									</tr>
									<?php
								}
							}
						}
						?>
					</table>
				</div>
				<div style="display:block;float:left;width:29%;">
					<div style="border:1px solid #DEDEDE;padding:8px;">
						<h5 style="margin:0px;">
							<?= $_SESSION['cste']['_DIMS_LABEL_FICHE_ATTACHED']; ?>
						</h5>
						<?php
						$lstAttach = $this->getLightAttribute('attached');
						if(!empty($lstAttach)){
							foreach($lstAttach as $idtype => $values){
								$type = new address_type();
								$type->open($idtype);
								?>
								<h6 style="margin:0px;margin-left:5px;"><?= $type->getLabel(); ?></h6>
								<ul style="margin-left:10px;" class="list-attached">
									<?php
									foreach($values as $go){
										$goo = new dims_globalobject();
										$goo->open($go);
										switch ($goo->get('id_object')) {
											case contact::MY_GLOBALOBJECT_CODE:
												$ct = contact::find_by(array('id_globalobject'=>$go,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
												if(!empty($ct)){
													echo '<li dims-data-value="'.$go.'" class="human">'.$ct->get('firstname')." ".$ct->get('lastname').'</li>';
												}
												break;
											case tiers::MY_GLOBALOBJECT_CODE:
												$tiers = tiers::find_by(array('id_globalobject'=>$go,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
												if(!empty($tiers)){
													echo '<li dims-data-value="'.$go.'" class="entreprise">'.$tiers->get('intitule').'</li>';
												}
												break;
										}
									}
									?>
								</ul>
								<?php
							}
						}
						?>
					</div>
				</div>
				<p style="clear:both;"></p>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	if(typeof(window['editAddress']) == "undefined"){
		window['editAddress'] = function editAddress(id,elem){
			var div = $(elem).parents('div.container_admin.global_content_record:first');
			$.ajax({
				type: "POST",
		        url: "<?= dims::getInstance()->getScriptEnv(); ?>",
		        data: {
		            'submenu': '1',
		            'mode': 'address',
		            'action' : 'view_edit',
		            'id' : id,
		            'id_ct': '<?= $this->getLightAttribute('id_ct'); ?>',
		        },
		        dataType: "html",
		        async: false,
		        success: function(data){
					div.replaceWith(data);
		        },
		        error: function(data){}
			});
		}
	}
</script>
