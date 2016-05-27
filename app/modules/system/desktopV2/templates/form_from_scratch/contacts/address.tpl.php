<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($this->fields['id']) || $this->fields['id']=='') {
	$id_adr=0;

	if (isset($_SESSION['dims']['form_scratch']['contacts']['success']) && !$_SESSION['dims']['form_scratch']['contacts']['success']) {
		if (isset($_SESSION['dims']['form_scratch']['contacts']['adr']) && !empty($_SESSION['dims']['form_scratch']['contacts']['adr'])) {
			foreach ($_SESSION['dims']['form_scratch']['contacts']['adr'] as $f => $value) {
				if (isset($this->fields[$f])) $this->fields[$f]=$value;
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
		}
	}
}
else {
	$id_adr=$this->fields['id'];
}

?>
<form name="form_address<?php echo $id_adr;?>" id="form_address<?php echo $id_adr;?>" class="ajaxForm" action="<?php echo '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=save_adr';?>" method="POST" enctype="multipart/form-data">
<input type="hidden" value="<?php echo $id_adr;?>" name="id_adr">
<div class="sub_bloc">
	<div class="sub_bloc_form">
		<table>
			<tr>
				<td class="label_field">
					<label for="type_address"><?php echo $_SESSION['cste']['_TYPE']; ?></label>
				</td>
				<td class="value_field" colspan="3">
					<select name="type_address" id="type_address" style="width:250px;">
						<?php
						$typeAdd = address_type::all("WHERE is_active=1 AND id_workspace = :idwork", array(':idwork'=>$_SESSION['dims']['workspaceid']));
						$tt = $this->getLightAttribute('type');
						foreach($typeAdd as $add){
							//TODO : gérer le selected
							if($tt == $add->get('id')){
								?>
								<option selected="true" value="<?= $add->get('id'); ?>"><?= $add->getLabel(); ?></option>
								<?
							}else{
								?>
								<option value="<?= $add->get('id'); ?>"><?= $add->getLabel(); ?></option>
								<?
							}
						}
						?>
					</select>
					<img onclick="javascript:$('td#add_type input:first',$(this).parents('table:first')).val('');$('td#add_type',$(this).parents('table:first')).show();" style="cursor:pointer;" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/ajouter16.png" />
				</td>
			</tr>
			<tr><td></td>
				<td style="display:none;" id="add_type" colspan="3">
					<input type="text" style="width:175px;" />
					<img onclick="javascript:submitNewTypeAddr(this);" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/valid.png" style="cursor:pointer;" />
					<img style="cursor:pointer;" onclick="javascript:$('td#add_type',$(this).parents('table:first')).hide();" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" />
				</td>
			</tr>
			<tr><td></td><td colspan="3"><div class="mess_error" id="def_type_address"></div></td></tr>
			<tr>
				<td class="label_field">
					<label for="adr_address"><?php echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?></label><span class="required">*</span>
				</td>
				<td class="value_field" colspan="3">
					<input type="text" name="adr_address" id="adr_address" value="<?php echo (!isset($this->fields["address"]))?'':$this->fields["address"];?>"/>
				</td>
			</tr>
			<tr><td></td><td colspan="3"><div class="mess_error" id="def_adr_address"></div></td></tr>
			<tr>
				<td class="label_field">
					<label for="adr_postalcode"><?php echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?></label><span class="required">*</span>
				</td>
				<td class="value_field">
					<input type="text" name="adr_postalcode" id="adr_postalcode" value="<?php echo (!isset($this->fields["postalcode"]))?'':$this->fields["postalcode"];?>"/>
				</td>
				<td></td><td></td>
			</tr>
			<tr><td></td><td colspan="3"><div class="mess_error" id="def_adr_postalcode"></div></td></tr>
			<tr>
				<td class="label_field">
					<label for="adr_id_country"><?php echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?></label><span class="required">*</span>
				</td>
				<td class="value_field">
					<select name="adr_id_country" id="adr_id_country" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_COUNTRY']; ?>">
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
					<label for="adr_id_city"><?php echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?></label><span class="required">*</span>
				</td>
				<td class="value_field" id="opportunity_rech_add_city" >
					<select id="adr_id_city" type="text" name="adr_id_city" <?php echo ($sel_Country != null && $sel_Country->fields['id'] > 0) ? '' : 'disabled="disabled"'; ?> style="width:100%" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_CITY']; ?>">
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
		<div><input type="button" onclick="javascript:saveLittleAddress(<? echo $id_adr; ?>);" value="<?php echo $_SESSION['cste']['_DIMS_SAVE']; ?>"></div>
		<div> <?php echo " ".$_SESSION['cste']['_DIMS_OR']." ";?><a href="<?= $this->getLightAttribute('back_op'); ?>"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a></div>
	</div>
</div>
</form>

