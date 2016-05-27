<h2>Moyens de paiement</h2>

<?php
$view = view::getInstance();
$mp = $view->get('mp');

$additional_js = "
	$(document).ready(function(){
		try{
			var instance=CKEDITOR.replace('description',
				{
					customConfig : '/assets/javascripts/libs/ckeditor/ckeditor_config_simple_fr.js',
					stylesSet:'default:/common/templates/frontoffice/default/ckstyles.js',
					contentsCss:'/common/templates/frontoffice/default/ckeditorarea.css'
				});
		}
		catch(e){

		}
	});";


// formulaire
$form = new Dims\form(array(
	'name'				=> 'f_params',
	'action'			=> get_path('params', 'payment_mean_save'),
	'submit_value'		=> dims_constant::getVal('_DIMS_SAVE'),
	'back_name'			=> dims_constant::getVal('_DIMS_RESET'),
	'back_url'			=> get_path('params', 'payment_means'),
	'additional_js' 	=> $additional_js
	));

// $form->addBlock('payment_mean', dims_constant::getVal('CATA_YOUR_CORPORATE_IDENTITY'), $this->getTemplatePath('params/identity_block.tpl.php'));
$form->addBlock('payment_mean', $mp->getLabel());

$form->add_hidden_field(array(
	'block'			=> 'payment_mean',
	'name'			=> 'id',
	'id'			=> 'id',
	'value'			=> $mp->get('id')
));

$form->add_textarea_field(array(
	'block'			=> 'payment_mean',
	'name'			=> 'fck_description',
	'id'			=> 'description',
	'label'			=> dims_constant::getVal('_DIMS_LABEL_DESCRIPTION'),
	'value'			=> $mp->getDescriptionHTML()
));


$form->build();
