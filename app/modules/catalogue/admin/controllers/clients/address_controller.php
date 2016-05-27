<?php
$view = view::getInstance();
$sub_action = $view->get('sa');

switch($sub_action){
    default:
    case 'show':
        $a_countries = country::getAllCountries();
        $a_countries_list = array();
        foreach ($a_countries as $country) {
            $a_countries_list[$country->get('id')] = $country->getLabel();
        }
        $view->assign('pays',$a_countries_list);

        $view->assign('addresses',$client->getDepots());

        $view->render('clients/show/address.tpl.php');
        break;
    case 'save':
        $client = $view->get('client');
        $client->fields['use_add_client'] = 0;
        $client->setvalues($_POST,'cli_');
        $client->save();
        dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        break;
    case 'showliv':
        include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
        $client = $view->get('client');
        $idLiv = dims_load_securvalue('livid',dims_const::_DIMS_NUM_INPUT,true,true,true);
        if($idLiv != '' && $idLiv > 0){
            $elem = new cata_depot();
            $elem->open($idLiv);
            if($elem->fields['client'] == $client->fields['code_client']){
                $view->assign('adr',$elem);
                $view->assign('_DEFAULT_ZOOM',9);
                $view->assign('_DEFAULT_LAT',48.623);
                $view->assign('_DEFAULT_LON',6.26);
                $view->render('clients/show/address_liv_show.tpl.php');
            }else
                dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        }else
            dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        break;
    case 'editliv':
        include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
        $client = $view->get('client');
        $idLiv = dims_load_securvalue('livid',dims_const::_DIMS_NUM_INPUT,true,true,true);
        $elem = new cata_depot();
        if($idLiv != '' && $idLiv > 0){
            $elem->open($idLiv);
            if($elem->fields['client'] != $client->fields['code_client'])
                dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        }else
            $elem->init_description();
        $a_countries = country::getAllCountries();
        $a_countries_list = array(0=>"");
        foreach ($a_countries as $country) {
            $a_countries_list[$country->get('id')] = $country->getLabel();
        }
        $view->assign('pays',$a_countries_list);
        $view->assign('adr',$elem);

        $view->assign('back_path',get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        $view->assign('action_path',get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses','sa'=>'saveliv','livid'=>$elem->get('id'))));

        $view->render('clients/show/address_liv_edit.tpl.php');
        break;
    case 'saveliv':
        include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
        $client = $view->get('client');
        $idLiv = dims_load_securvalue('livid',dims_const::_DIMS_NUM_INPUT,true,true,true);
        $elem = new cata_depot();
        if($idLiv != '' && $idLiv > 0){
            $elem->open($idLiv);
            if($elem->fields['client'] != $client->fields['code_client'])
                dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        }else
            $elem->init_description();
        $elem->fields['client'] = $client->fields['code_client'];
        $elem->setvalues($_POST,'adr_');
        $elem->save();
        dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        break;
    case 'delliv':
        include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";
        $client = $view->get('client');
        $idLiv = dims_load_securvalue('livid',dims_const::_DIMS_NUM_INPUT,true,true,true);
        if($idLiv != '' && $idLiv > 0){
            $elem = new cata_depot();
            $elem->open($idLiv);
            if($elem->fields['client'] == $client->fields['code_client']){
                $elem->delete();
            }
        }
        dims_redirect(get_path('clients', 'show',array('id'=>$client->get('id_client'),'sc'=>'addresses')));
        break;
}
?>
