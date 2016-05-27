<?php
$view = view::getInstance();
$elem = $view->get('sel_elem');
if(isset($elem->fields) && $elem->fields['id_parent'] != '' && $elem->fields['id_parent'] > 0){
    ?>
    <div class="infos_elem">
        <h3>
            <?= $elem->getLabel(); ?>
        </h3>
        <div class="infos">
            <?
            $user = $elem->getUserCreate();
            echo dims_constant::getVal('_CREATED_FEM')." ".$elem->getDateCreatedHum()." ".strtolower(dims_constant::getVal('_DIMS_LABEL_FROM'))." ".$user->fields['firstname']." ".$user->fields['lastname'];
            if($elem->fields['date_modify'] != '' && $elem->fields['date_modify'] > 0 && $elem->fields['date_modify'] > $elem->fields['date_create']){
                echo " - ".dims_constant::getVal('_LAST_UPDATED_AT')." ".substr($elem->fields['date_modify'],8,2).":".substr($elem->fields['date_modify'],10,2)." ".strtolower(dims_constant::getVal('_AT'))." ".$elem->getDateUpdatedHum();
            }
            ?>
        </div>
    </div>
    <?
}
?>