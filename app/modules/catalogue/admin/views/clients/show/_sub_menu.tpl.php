<?php
// onglets
$view = view::getInstance();
$sc = $view->get('sc');
$client = $view->get('client');

$a_subs = array(
	'identity' 		=> dims_constant::getVal('CATA_IDENTITY_CONTACT'),
	'tarification' 	=> dims_constant::getVal('PRICING'),
	'addresses' 	=> dims_constant::getVal('CATA_ADDRESSES'),
	'services' 		=> dims_constant::getVal('CATA_SERVICES_USERS'),
	'quotations'    => dims_constant::getVal('QUOTATION'),
	'crm'           => dims_constant::getVal('CRM'),
);

echo '<div class="sous_rubrique"><ul>';
foreach ($a_subs as $key => $label) {
	echo '<li><a href="'.get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => $key)).'"';
	if ($sc == $key) echo ' class="selected"';
	echo '>'.$label.'</a></li>';
}
echo '</ul></div>';
