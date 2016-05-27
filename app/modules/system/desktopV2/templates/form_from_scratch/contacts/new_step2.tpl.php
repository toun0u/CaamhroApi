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
		<div class="sub_bloc"  style="clear:both;float:left;margin-top:10px;">
			<h3>
				<? echo ucfirst(strtolower($_SESSION['cste']['_NEW_COMPANY'])); ?>
			</h3>
			<div class="sub_bloc_form" style="float:left;width:99%;clear:both;">
				<div id="content_addresses">
					<?php
					$dataTiers = current($this->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR'));
					$tiers = new tiers();
					if(empty($dataTiers))
						$tiers->init_description();
					else{
						$tiers->open($dataTiers['id']);
						$tiers->setLightAttribute('function',$dataTiers['function']);
					}
					$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/new_tiers.tpl.php');
					?>
				</div>
			</div>
		</div>

	</div>
</div>
