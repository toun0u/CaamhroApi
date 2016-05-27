<?php
$view = view::getInstance();
$foldercourant = $view->get('foldercourant');
$objcourant =  $view->get('objcourant');
$document = $view->get('document');
$error = $view->get('error');
if( empty($error) ){
	$form = new Dims\form();
	echo $form->textarea_field(array(
		'name'		=> 'description',
		'value'		=> $document->get('description'),
		'classes'	=> 'w100'
		));
	?>
	<div class="line right">
		<a href="javascript:void(0);" class="mr10" onclick="validDescription(this);"><?= dims_constant::getVal('_DIMS_VALID'); ?></a>
		<a href="javascript:void(0);" onclick="annulEdition(this);"><?= dims_constant::getVal('_DIMS_CANCEL'); ?></a>
	</div>
	<?php
}
else{
	echo $error;
}
