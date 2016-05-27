
<script type="text/javascript">
	function pwgen() {
		var length = 6,
			charset = "abcdefghijklnopqrstuvwxyz0123456789",
			retVal = "";
		for (var i = 0, n = charset.length; i < length; ++i) {
			retVal += charset.charAt(Math.floor(Math.random() * n));
		}
		return retVal;
	}

	function generatePassword() {
		pwd = pwgen();
		$('#user_password').val(pwd);
		$('#user_password_confirmation').val(pwd);
		$('#visible_password').val(pwd);
	}
</script>

<?php $view = view::getInstance(); ?>
<h1>
	<img src="<?= $view->getTemplateWebPath('gfx/clients50x30.png'); ?>">
	<?= dims_constant::getVal('CATA_CLIENTS'); ?>
</h1>

<?php
$additional_js = <<< ADDITIONAL_JS
function refreshCityOfCountry(id,idCity){
	tmpSearchOpp = null;
	if(tmpSearchOpp != null) clearInterval(tmpSearchOpp);
	$.ajax({
		type: "POST",
		url: "/admin.php",
		data: {
			'dims_op': 'desktopv2',
			'action' : 'client_refresh_city',
			'ref': idCity,
			'id': id
		},
		dataType: "text",
		async: false,
		success: function(data){
			$('#'+idCity).html(data).trigger("liszt:updated");
		},
		error: function(data){}
	});
}

var alreadyCity = false;
function addNewCity(idSelect,idCountry){
	if (!alreadyCity){
		alreadyCity = true;
		var mId = document.getElementById(idCountry).options[document.getElementById(idCountry).selectedIndex].value;
		var val = $("#"+idSelect+" div.chzn-search input").val();
		$.ajax({
			type: "POST",
			url: "./admin.php",
			data: {
				'dims_op' : 'add_new_city',
				'val': val,
				'id': mId
			},
			dataType: "json",
			success: function(data){
				if(data != null){
					$("#"+idSelect+" select").append('<option value="'+data['id']+'" selected=true>'+data['label']+'</option>').trigger("liszt:updated");
				}
				alreadyCity = false;
			},
			error: function(data){
				alreadyCity = false;
			}
		});
	}
}

$("select#company_country")
	.chosen({allow_single_deselect:true})
	.change(function(){
		if($(this).val() != '') {
			$('#company_city').removeAttr('disabled');
		}
		else {
			$('#company_city').attr('disabled','disabled');
		}
		refreshCityOfCountry($(this).val(),'company_city');
});

$("select#company_city").chosen({
	allow_single_deselect:true,
	no_results_text: "<div class=\"button_add_city\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"/modules/catalogue/admin/views/gfx/ajouter16.png\" /><div style=\"float:right;margin-top:3px;\">{$_SESSION['cste']['ADD_IT_LA']}</div></div>{$_SESSION['cste']['NO_RESULT']}"
});
$('div.button_add_city').live('click',function(){
	$(this).die('click');
	addNewCity('company_city_block','company_country');
});

$("select#company_liv_country")
	.chosen({allow_single_deselect:true})
	.change(function(){
		if($(this).val() != '') {
			$('#company_liv_city').removeAttr('disabled');
		}
		else {
			$('#company_liv_city').attr('disabled','disabled');
		}
		refreshCityOfCountry($(this).val(),'company_liv_city');
});

$("select#company_liv_city").chosen({allow_single_deselect:true});

$("#liv_same_as_facturation").change(function() {
	if($("#liv_same_as_facturation").attr('checked')) {
		$("#delivery_address_detail").hide();
	}
	else {
		$("#delivery_address_detail").show();
	}
})

function codeClientUnique() {
	var err = false;
	var message = '';

	$.ajax({
		type: 'GET',
		url: 'admin.php',
		data: {
			dims_op: 'code_client_unique',
			code_client: $('#code_client').val()
		},
		dataType: 'json',
		async: false,
		success: function(data) {
			if (data > 0) {
				err = true;
				message = "Ce code client est déjà utilisé"
			}
		}
	});

	return {error: err, message: message}
}
ADDITIONAL_JS;


$form = new Dims\form(array(
	'name' 					=> 'identification_client',
	'action'				=> dims::getInstance()->getScriptEnv() . '?c=clients&a=save',
	'back_name'				=> dims_constant::getVal('_DIMS_LABEL_CANCEL'),
	'back_url'				=> dims::getInstance()->getScriptEnv() . '?c=clients&a=index',
	'submit_value'			=> dims_constant::getVal('_DIMS_SAVE'),
	'continue'				=> true,
	'include_actions' 		=> true,
	'additional_js'			=> $additional_js,
	'extended_controls' 	=> 'extended_controls: {code_client: codeClientUnique}',
));

$form->addBlock ('identification_client',dims_constant::getVal('IDENTIFICATION'));

$form->add_text_field(array(
	'block'						=> 'identification_client',
	'name' 						=> 'code_client',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_GROUP_CODE'),
	'mandatory'					=> true,
	'revision' 					=> 'custom'
));

$form->add_checkbox_field(array(
	'block'						=> 'identification_client',
	'name'						=> 'blocked',
	'id'						=> 'blocked',
	'label'						=> dims_constant::getVal('_DIMS_LOCKED'),
	'value'						=> 1,
));

$form->add_textarea_field(array(
	'block'						=> 'identification_client',
	'name'						=> 'client_commentaire',
	'label'						=> dims_constant::getVal('_DIMS_COMMENTS'),
));

// $form->add_checkbox_field(array(
// 	'block'						=> 'identification_client',
// 	'name'						=> 'clients_validate',
// 	'id'						=> 'clients_validate',
// 	'label'						=> dims_constant::getVal('_DIMS_LABEL_VALIDATION'),
// 	'value'						=> 1,
// ));

// $form->add_checkbox_field(array(
// 	'block'						=> 'identification_client',
// 	'name'						=> 'budget',
// 	'id'						=> 'budget',
// 	'label'						=> dims_constant::getVal('BUDGET'),
// 	'value'						=> 1,
// ));

$form->addBlock ('info_societe',dims_constant::getVal('COMPANY_INFORMATION'),$view->getTemplatePath('clients/info_company_block.tpl.php'));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_name',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_ENT_NAME'),
		'mandatory'				=> true
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_email',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_EMAIL'),
	));


$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_number_siren',
		'label'					=> dims_constant::getVal('NUMBER_SIREN'),
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_nic',
		'label'					=> dims_constant::getVal('NIC'),
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_ape_code',
		'label'					=> dims_constant::getVal('APE_CODE'),
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_address_1',
		'label'					=> dims_constant::getVal('NO.,_STREET'),
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_address_2',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'),
	));

$form->add_text_field(array(
		'block'					=> 'info_societe',
		'name'					=> 'company_address_3',
		'label'					=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'),
	));

$a_countries = $view->get('a_countries');
$form->add_select_field(array(
	'block'						=> 'info_societe',
	'name' 						=> 'company_country',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
	'options'					=> $a_countries,
	'classes'					=> 'pays_select',
	'empty_message'				=> dims_constant::getVal('SELECT_A_COUNTRY')
));


$form->add_text_field(array(
	'block'						=> 'info_societe',
	'name'						=> 'company_postalcode',
	'label'						=> dims_constant::getVal('ZIP___CITY'),
));

$a_cities = $view->get('a_cities');
$form->add_select_field(array(
	'block'						=> 'info_societe',
	'name' 						=> 'company_city',
	'options'					=> $a_cities,
	'empty_message'				=> dims_constant::getVal('_SELECT_A_CITY')
));

$form->addBlock ('contact_first_user',dims_constant::getVal('CONTACT___FIRST_USER'),$view->getTemplatePath('clients/clients_contact_first_user.tpl.php'));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_firstname',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_FIRSTNAME'),
	'mandatory'					=> true
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_lastname',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_NAME'),
	'mandatory'					=> true
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_email',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_EMAIL'),
	'mandatory'					=> true
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_phone',
	'label'						=> dims_constant::getVal('_DIRECTORY_PHONE'),
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_login',
	'label'						=> dims_constant::getVal('_LOGIN'),
	'mandatory'					=> true,
	'additionnal_attributes' 	=> 'rev="dims_login" autocomplete="off"'
));

$form->add_text_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_fax',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_FAX'),
));

$form->add_password_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_password',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_PASSWORD'),
	'mandatory'					=> true,
	'additionnal_attributes' 	=> 'rev="dims_pwd"'
));

$form->add_password_field(array(
	'block'						=> 'contact_first_user',
	'name'						=> 'user_password_confirmation',
	'label'						=> dims_constant::getVal('CONFIRM'),
	'mandatory'					=> true,
	'additionnal_attributes' 	=> 'rev="dims_pwd_confirm"'
));

$selected_level = $view->get('niveau');
$niveau = $view->get('niveau');

$form->add_select_field(array(
	'block'						=> 'contact_first_user',
	'name' 						=> 'niveau_select',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_LEVEL'),
	'options'					=> $niveau,
));

$form->addBlock ('clients_tarification',dims_constant::getVal('PRICING'),$view->getTemplatePath('clients/clients_prices.tpl.php'));

$form->add_text_field(array(
		'block'					=> 'clients_tarification',
		'name'					=> 'escompte',
		'label'					=> dims_constant::getVal('DISCOUNT'),
	));

$form->add_text_field(array(
		'block'					=> 'clients_tarification',
		'name'					=> 'minimum_order',
		'label'					=> dims_constant::getVal('MINIMUM_ORDER'),
	));


$form->add_text_field(array(
		'block'					=> 'clients_tarification',
		'name'					=> 'franco',
		'label'					=> dims_constant::getVal('FRANCO_DE_PORT'),
	));

$means_of_payment = $view->get('means_of_payment');
$form->add_select_field(array(
	'block'						=> 'clients_tarification',
	'name' 						=> 'means_of_payment[]',
	'label'						=> dims_constant::getVal('MEANS_OF_PAYMENT'),
	'options'					=> $means_of_payment,
	'additionnal_attributes'	=> 'multiple="multiple"',
));

// $form->addBlock ('billing_address',dims_constant::getVal('BILLING_ADDRESS'));

// $form->add_checkbox_field(array(
// 	'block'						=> 'billing_address',
// 	'name'						=> 'clients_active',
// 	'id'						=> 'clients_active',
// 	'label'						=> dims_constant::getVal('USE_THE_ADDRESS_OF_THE_COMPANY'),
// 	'value'						=> 1,
// ));

$form->addBlock ('delivery_address', dims_constant::getVal('DELIVERY_ADDRESS'), $view->getTemplatePath('clients/delivery_block.tpl.php'));

$form->add_checkbox_field(array(
	'block'						=> 'delivery_address',
	'name'						=> 'liv_same_as_facturation',
	'id'						=> 'liv_same_as_facturation',
	'label'						=> dims_constant::getVal('USE_THE_SAME_AS_THE_BILLING'),
	'value'						=> 1,
	'additionnal_attributes' 	=> 'checked="checked"'
));

$form->add_text_field(array(
	'block'						=> 'delivery_address',
	'name'						=> 'company_liv_name',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_NAME'),
));

$form->add_text_field(array(
	'block'						=> 'delivery_address',
	'name'						=> 'company_liv_address_1',
	'label'						=> dims_constant::getVal('NO.,_STREET'),
));

$form->add_text_field(array(
	'block'						=> 'delivery_address',
	'name'						=> 'company_liv_address_2',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_2'),
));

$form->add_text_field(array(
	'block'						=> 'delivery_address',
	'name'						=> 'company_liv_address_3',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_ADDRESS_3'),
));

$a_countries = $view->get('a_countries');
$form->add_select_field(array(
	'block'						=> 'delivery_address',
	'name' 						=> 'company_liv_country',
	'label'						=> dims_constant::getVal('_DIMS_LABEL_COUNTRY'),
	'options'					=> $a_countries,
	'classes'					=> 'pays_select w200p',
	'empty_message'				=> dims_constant::getVal('SELECT_A_COUNTRY')
));


$form->add_text_field(array(
	'block'						=> 'delivery_address',
	'name'						=> 'company_liv_postalcode',
	'label'						=> dims_constant::getVal('ZIP___CITY'),
));

$a_cities = $view->get('a_cities');
$form->add_select_field(array(
	'block'						=> 'delivery_address',
	'name' 						=> 'company_liv_city',
	'options'					=> $a_cities,
	'classes' 					=> 'w200p',
	'empty_message'				=> dims_constant::getVal('_SELECT_A_CITY')
));

$form->build();
