<?php $view = view::getInstance();
$actions = $view->get('actions');
?>
<h3><?= dims_constant::getVal('SALES_ARTICLE'); ?></h3>


<select onchange="" name="selection">
<option value="1">Sur le mois</option>
</select>

<div class="nb_sales"><?= dims_constant::getVal('NB_SALES'); ?> :</div>
<div class="ca_total_ht"><?= dims_constant::getVal('CA_TOTAL_HT'); ?> :</div>
<img src="<?=  $view->getTemplateWebPath('gfx/graph_lateral.png'); ?> "/>
