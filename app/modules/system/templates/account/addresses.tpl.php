<?php
/* dims_user form */
$errors = $this->getLightAttribute('errors');
$success = $this->getLightAttribute('success');

$subscribedmailinglists = $this->getLightAttribute('subscribedmailinglists');
$subscribedoptions = $this->getLightAttribute('subscribedoptions');

$a_countries = country::getAllCountries();

$typeAdd = address_type::all("WHERE is_active=1 AND id_workspace = :idwork", array(':idwork'=>$_SESSION['dims']['workspaceid']));
$lstTypes = array('dims_nan'=>'');
foreach($typeAdd as $add){
	$lstTypes[$add->get('id')] = $add->getLabel();
}

$addresses = $this->getLightAttribute('addresses');

if(!empty($errors)) {
	?>
	<div class="alert alert-danger">
		<?php
		foreach($errors as $error) {
			?>
			<?= $error; ?><br />
			<?php
		}
		?>
	</div>
	<?php
}

if($success) {
	?>
	<div class="alert alert-success">
		<?= dims_constant::getVal('THE_MODIFICATIONS_HAVE_BEEN_DONE_SUCCESSFULLY'); ?>
	</div>
	<?php
}
?>
<ul class ="nav nav-tabs">
	<li>
		<a href="?action=informations">
			<?= dims_constant::getVal('_INFOS_LABEL'); ?>
		</a>
	</li>
	<li class="active">
		<a href="?action=addresses">
			<?= dims_constant::getVal('_ADDRESSES'); ?>
		</a>
	</li>
	<?php
	if(!empty($subscribedmailinglists)) {
		?>
		<li>
			<a href="?action=mailinglists">
				<?= dims_constant::getVal('_DIMS_LABEL_MAILINGLIST'); ?>
			</a>
		</li>
		<?php
	}
	?>
</ul>
<div class="cointainer">
	<div class="row">
		<div class="col-md-6">
			<?php
			foreach($addresses as $address) {
				$city = new city();
				if(!empty($address->fields['id_city'])) {
					$city->open($address->fields['id_city']);
				} else {
					$city->init_description();
				}

				$country = new country();
				if(!empty($address->fields['id_country'])) {
					$country->open($address->fields['id_country']);
				} else {
					$country->init_description();
				}

				$link = $address->getLinkCt($this->getContact()->get('id_globalobject'));
				?>
				<div>
					<address>
						<strong><?= $lstTypes[$link->get('id_type')]; ?></strong><br />
						<?= $address->fields['address']; ?>
						<?= $address->fields['address2']; ?>
						<?= $address->fields['address3']; ?><br />
						<?= $city->fields['label']; ?> <?= $address->fields['postalcode']; ?><br />
						<?= $country->fields['name']; ?><br /><br />
						<a href="Javascript: void(0);" onclick="Javascript: $(this).closest('div').children('address').fadeToggle(); $(this).closest('div').children('form').fadeToggle();">
							Editer
						</a> /
						<a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('?action=deladdress&idaddress=<?= $link->getId(); ?>', '<?= dims_constant::getVal('_DIMS_CONFIRM'); ?>');">
							Supprimer
						</a>
					</address>
					<form method="post" action="?action=saveaddresses" role="form" class="cointainer" style="display: none;">
						<h3><?= dims_constant::getVal('_ADDRESSES'); ?></h3>
						<input type="hidden" name="idaddress" value="<?= $address->getId(); ?>" />
						<div class="form-group">
							<label for="id_type">
								<?= dims_constant::getVal('_TYPE'); ?>
							</label>
							<select class="form-control" name="id_type" id="id_type">
								<?php
								foreach($lstTypes as $idtype => $type) {
									$sel = '';
									if($idtype == $link->get('id_type')) {
										$sel = 'selected="selected" ';
									}
									?>
									<option value="<?= $idtype; ?>" <?= $sel; ?>><?= $type; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="addr_address">
								<?= dims_constant::getVal('_DIMS_LABEL_ADDRESS'); ?>
							</label>
							<input class="form-control" type="text" name="addr_address" id="addr_address" value="<?= $address->fields['address']; ?>" />
							<input class="form-control" type="text" name="addr_address2" id="addr_address2" value="<?= $address->fields['address2']; ?>" />
							<input class="form-control" type="text" name="addr_address3" id="addr_address3" value="<?= $address->fields['address3']; ?>" />
						</div>
						<div class="row">
							<div class="form-group col-xs-6">
								<label for="addr_postalcode">
									<?= dims_constant::getVal('_DIMS_LABEL_CP'); ?>
								</label>
								<input class="form-control postalcode" type="text" name="addr_postalcode" id="addr_postalcode" value="<?= $address->fields['postalcode']; ?>" />
							</div>
							<div class="form-group col-xs-6">
								<label for="addr_id_city">
									<?= dims_constant::getVal('_DIMS_LABEL_CITY'); ?>
								</label>
								<select class="form-control city" name="addr_id_city" id="addr_id_city">
									<option value="0"></option>
									<option value="<?= $city->getId(); ?>" selected="selected"><?= $city->get('label'); ?></option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-xs-6">
								<label for="addr_bp">
									BP / CEDEX / ...
								</label>
								<input class="form-control" type="text" name="addr_bp" id="addr_bp" value="<?= $address->fields['bp']; ?>" />
							</div>
							<div class="form-group col-xs-6">
								<label for="addr_id_country">
									<?= dims_constant::getVal('_DIMS_LABEL_COUNTRY'); ?>
								</label>
								<select class="form-control" name="addr_id_country" id="addr_id_country">
									<option></option>
									<?php
									foreach($a_countries as $cc) {
										$sel = '';
										if($cc->getId() == $country->getId()) {
											$sel = 'selected="selected" ';
										}
										?>
										<option value="<?= $cc->getId(); ?>" <?= $sel; ?>><?= $cc->get('printable_name'); ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-xs-6">
								<label for="addrlink_phone">
									<?= dims_constant::getVal('_PHONE'); ?>
								</label>
								<input class="form-control" type="text" name="addrlink_phone" id="addrlink_phone" value="<?= $link->fields['phone']; ?>" />
							</div>
							<div class="form-group col-xs-6">
								<label for="addrlink_fax">
									<?= dims_constant::getVal('_DIMS_LABEL_FAX'); ?>
								</label>
								<input class="form-control" type="text" name="addrlink_fax" id="addrlink_fax" value="<?= $link->fields['fax']; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="addrlink_email">
								<?= dims_constant::getVal('_DIMS_LABEL_EMAIL'); ?>
							</label>
							<input class="form-control" type="text" name="addrlink_email" id="addrlink_email" value="<?= $link->fields['email']; ?>" />
						</div>
						<div class="col-md-12">
							<button type="submit" class="btn btn-default"><?= dims_constant::getVal('_DIMS_VALID'); ?></button>
						</div>
					</form>
				</div>
				<hr />
				<?php
			}
			?>
		</div>

		<div class="col-md-6">
			<form method="post" action="?action=saveaddresses" role="form" class="cointainer">
				<h3><?= dims_constant::getVal('ADD_ADDRESS'); ?></h3>
				<input type="hidden" name="idaddress" value="0" />
				<div class="form-group">
					<label for="id_type">
						<?= dims_constant::getVal('_TYPE'); ?>
					</label>
					<select class="form-control" name="id_type" id="id_type">
						<?php
						foreach($lstTypes as $idtype => $type) {
							?>
							<option value="<?= $idtype; ?>"><?= $type; ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<div class="form-group">
					<label for="addr_address">
						<?= dims_constant::getVal('_DIMS_LABEL_ADDRESS'); ?>
					</label>
					<input class="form-control" type="text" name="addr_address" id="addr_address" />
					<input class="form-control" type="text" name="addr_address2" id="addr_address2" />
					<input class="form-control" type="text" name="addr_address3" id="addr_address3" />
				</div>
				<div class="row">
					<div class="form-group col-xs-6">
						<label for="addr_postalcode">
							<?= dims_constant::getVal('_DIMS_LABEL_CP'); ?>
						</label>
						<input class="form-control postalcode" type="text" name="addr_postalcode" id="addr_postalcode" />
					</div>
					<div class="form-group col-xs-6">
						<label for="addr_id_city">
							<?= dims_constant::getVal('_DIMS_LABEL_CITY'); ?>
						</label>
						<select class="form-control city" name="addr_id_city" id="addr_id_city">
							<option value="0"></option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-6">
						<label for="addr_bp">
							BP / CEDEX / ...
						</label>
						<input class="form-control" type="text" name="addr_bp" id="addr_bp" />
					</div>
					<div class="form-group col-xs-6">
						<label for="addr_id_country">
							<?= dims_constant::getVal('_DIMS_LABEL_COUNTRY'); ?>
						</label>
						<select class="form-control" name="addr_id_country" id="addr_id_country">
							<option></option>
							<?php
							foreach($a_countries as $cc) {
								?>
								<option value="<?= $cc->getId(); ?>"><?= $cc->get('printable_name'); ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-6">
						<label for="addrlink_phone">
							<?= dims_constant::getVal('_PHONE'); ?>
						</label>
						<input class="form-control" type="text" name="addrlink_phone" id="addrlink_phone" />
					</div>
					<div class="form-group col-xs-6">
						<label for="addrlink_fax">
							<?= dims_constant::getVal('_DIMS_LABEL_FAX'); ?>
						</label>
						<input class="form-control" type="text" name="addrlink_fax" id="addrlink_fax" />
					</div>
				</div>
				<div class="form-group">
					<label for="addrlink_email">
						<?= dims_constant::getVal('_DIMS_LABEL_EMAIL'); ?>
					</label>
					<input class="form-control" type="text" name="addrlink_email" id="addrlink_email" />
				</div>
				<div class="col-md-12">
					<button type="submit" class="btn btn-default"><?= dims_constant::getVal('_DIMS_VALID'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" language="Javascript">
	$(document).ready(function() {
		$('.postalcode').change(function(evt) {
			$.getJSON('?action=searchcity&postalcode='+$(evt.target).val(), function(data) {
				$(evt.target).parents('form').find('.city').html('<option value="0"></option>');
				for(var key in data) {
					$(evt.target).parents('form').find('.city').append('<option value="'+data[key].id+'">'+data[key].label+' ('+data[key].code_dep+')</option>');
				}
			});
		});
	});
</script>
<script type="text/javascript" language="Javascript" src="./common/js/functions.js"></script>
