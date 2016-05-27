<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article.php";
$view = view::getInstance();
$sub_action = $view->get('sa');

switch($sub_action){
    default:
    case 'edit':
        $client = $view->get('client');

        // liste de tous les moyens de paiement
        $means_of_payment = array();
        foreach (moyen_paiement::getActivePaiement() as $mp) {
            $means_of_payment[$mp->get('id')] = $mp->getLabel();
        }
        $view->assign('means_of_payment', $means_of_payment);

        // moyens de paiement du client
        $view->assign('client_payment_means', $client->getFixedPaymentMeans());

        $view->assign('lst_prix',$client->getPrixNets());

        $view->render('clients/show/tarification_edit.tpl.php');
        break;
    case 'save':
        $client = $view->get('client');
        $lstPrixNets = $client->getPrixNets();
        $components = dims_load_securvalue('kit_composition', dims_const::_DIMS_CHAR_INPUT, true,true, true);
        if(!empty($components))
            foreach($components as $key => $val){
                $art = new article();
                $art->open($key);
                if(!$art->isNew()){
                    if(isset($lstPrixNets[$art->fields['reference']])){
                        $lstPrixNets[$art->fields['reference']]->fields['puht'] = $val;
                        $lstPrixNets[$art->fields['reference']]->save();
                        unset($lstPrixNets[$art->fields['reference']]);
                    }else{
                        $client->createPrixNets($art->fields['reference'],$val);
                    }
                }
            }
        foreach($lstPrixNets as $elem)
            $elem->delete();
        $client->setvalues($_POST,'cli_');
        $client->save();

        // moyens de paiement
        $a_paymentMeans = dims_load_securvalue('means_of_payment', dims_const::_DIMS_NUM_INPUT, false, true, true);
        $client->setPaymentMeans($a_paymentMeans);

        dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'tarification')));
        break;
    case 'init': // réinitialisation du bloc tarification

        break;
}
?>
