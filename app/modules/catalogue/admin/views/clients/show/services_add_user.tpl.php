<?php
$view = view::getInstance();
$client = $view->get('client');
$current = $view->get('current');
$user = $view->get('editedUser');

$additional_js = <<< ADDITIONAL_JS

ADDITIONAL_JS;
$form = new Dims\form(array(
	'name'              => 'user',
	'action'            => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services','sa'=>'saveuser')),
	'back_name'         => dims_constant::getVal('_DIMS_LABEL_CANCEL'),
	'back_url'          => get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'services', 'grid' => $current->get('id'))),
	'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
	'include_actions'   => true,
	'validation'        => true,
	'additional_js'     => $additional_js,
));

$form->addBlock ('default', ($user->isNew())?dims_constant::getVal('_OEUVRE_CREATE_A_NEW_ACCOUNT'):dims_constant::getVal('_OEUVRE_EDITION_OF_AN_ACCOUNT'));

$form->add_hidden_field(array(
	'name'          => 'grid',
	'value'         => $current->get('id'),
));
if(!$user->isNew()){
	$form->add_hidden_field(array(
		'name'          => 'uid',
		'value'         => $user->get('id'),
	));

	$form->add_hidden_field(array(
		'name'          => 'dims_login_reference',
		'id'            => 'dims_login_reference',
		'value'         => $user->fields['login']
	));
}

$form->add_text_field(array(
	'name'          => 'user_firstname',
	'value'         => $user->fields['firstname'],
	'label'         => dims_constant::getVal('_DIMS_LABEL_FIRSTNAME'),
	'mandatory'     => true
));

$form->add_text_field(array(
	'name'          => 'user_lastname',
	'value'         => $user->fields['lastname'],
	'label'         => dims_constant::getVal('_DIMS_LABEL_NAME'),
	'mandatory'     => true
));

$form->add_text_field(array(
	'name'          => 'user_email',
	'value'         => $user->fields['email'],
	'label'         => dims_constant::getVal('_DIMS_LABEL_EMAIL'),
	'mandatory'     => true
));

if($view->get('active_serv')){
	$selected_level = cata_param::getSelectServicesDispo();
	if(!empty($selected_level)){
		$form->add_select_field(array(
			'name'          => 'level',
			'value'         => $view->get('default_lvl'),
			'label'         => dims_constant::getVal('_DIMS_LABEL_LEVEL'),
			'options'       => $selected_level,
		));
	}
}

$form->add_text_field(array(
	'name'          => 'user_login',
	'value'         => $user->fields['login'],
	'label'         => dims_constant::getVal('_LOGIN'),
	'mandatory'     => $user->isNew(),
	'additionnal_attributes' => ' autocomplete="off" rev="dims_login"'.(!$user->isNew()?' disabled=true':""),
	''
));

$form->add_password_field(array(
	'name'          => 'pwd',
	'value'         => "",
	'label'         => dims_constant::getVal('_DIMS_LABEL_PASSWORD'),
	'mandatory'     => $user->isNew(),
	'additionnal_attributes' => ' autocomplete="off" rev="dims_pwd"'
));

$form->add_password_field(array(
	'name'          => 'conf_pwd',
	'value'         => "",
	'label'         => dims_constant::getVal('_DIMS_LABEL_PASSWORD_CONFIRM'),
	'mandatory'     => $user->isNew(),
	'additionnal_attributes' => ' autocomplete="off" rev="dims_pwd_confirm"'
));

$form->build();
?>
