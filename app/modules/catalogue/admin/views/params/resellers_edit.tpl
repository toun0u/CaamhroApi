<?php
$reseller = $this->get('reseller');

$js = <<< ADD_JS
$("select#id_country").chosen({allow_single_deselect:true});
ADD_JS;
?>

<div class="params_content">
	<h2><?= ($reseller->isNew()) ? dims_constant::getVal('CATA_NEW_RESELLER') : dims_constant::getVal('CATA_RESELLER_EDITION'); ?></h2>

	<?php
	$form = new Dims\form(array(
		'name' 				=> 'form_reseller',
		'action'			=> get_path('params', 'resellers', array('sa' => 'save')),
		'object'			=> $reseller,
		'validation'		=> true,
		'back_name'			=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
		'back_url'			=> get_path('params', 'resellers'),
		'submit_value'		=> dims_constant::getVal('CATA_SAVE_THE_RESELLER'),
		'continue'			=> true,
		'additional_js'		=> $js
	));

	$form->addBlock('default', dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'));

	if (!$reseller->isNew() ){
		// valeurs initiales pour les clefs primaires
		$form->add_hidden_field(array(
			'name'			=> 'id',
			'db_field'		=> 'id'
		));
	}

	$form->add_text_field(array(
		'name'				=> 'name',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_NAME'),
		'value'				=> htmlspecialchars($reseller->getName())
		));

	$form->add_text_field(array(
		'name'				=> 'address1',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_ADDRESS'),
		'value'				=> htmlspecialchars($reseller->getAddress1())
		));

	$form->add_text_field(array(
		'name'				=> 'address2',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'),
		'value'				=> htmlspecialchars($reseller->getAddress2())
		));

	$form->add_text_field(array(
		'name'				=> 'address3',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'),
		'value'				=> htmlspecialchars($reseller->getAddress3())
		));

	$form->add_text_field(array(
		'name'				=> 'postal_code',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_CP'),
		'value'				=> htmlspecialchars($reseller->getPostalCode())
		));

	$form->add_text_field(array(
		'name'				=> 'city',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_CITY'),
		'value'				=> htmlspecialchars($reseller->getCity())
		));

	$a_countries = $this->get('a_countries');
	$form->add_select_field(array(
		'name' 						=> 'id_country',
		'label'						=> dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
		'options'					=> $a_countries,
		'classes'					=> 'pays_select',
		'empty_message'				=> dims_constant::getVal('SELECT_A_COUNTRY'),
		'value' 					=> $reseller->get('id_country')
	));

	$form->add_text_field(array(
		'name'				=> 'website',
		'label'				=> dims_constant::getVal('CATA_WEBSITE'),
		'value'				=> htmlspecialchars($reseller->getWebSite())
		));

	$form->add_text_field(array(
		'name'				=> 'email',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_EMAIL'),
		'value'				=> htmlspecialchars($reseller->getEmail())
		));

	$form->add_text_field(array(
		'name'				=> 'phone',
		'label'				=> dims_constant::getVal('PHONE_NUMBER'),
		'value'				=> htmlspecialchars($reseller->getPhone())
		));

	$form->add_text_field(array(
		'name'				=> 'fax',
		'label'				=> dims_constant::getVal('_DIMS_LABEL_FAX'),
		'value'				=> htmlspecialchars($reseller->getFax())
		));

	// Logo actuel
	$logo = $reseller->getLogo();
	if (!is_null($logo)) {
		$dom_extension = '
			<tr>
				<td class="label_field"><label>'.dims_constant::getVal('CATA_ACTUAL_LOGO').'</label></td>
				<td class="value_field"><img src="'.$logo->getWebPath().'" alt="'.dims_constant::getVal('CATA_ACTUAL_LOGO').'"></td>
			</tr>';
	}
	else {
		$dom_extension = '';
	}

	$form->add_file_field(array(
		'name'				=> 'logo',
		'label'				=> dims_constant::getVal('CATA_COMPANY_LOGO'),
		'dom_extension' 	=> $dom_extension
	));

	$form->build();
	?>
</div>
