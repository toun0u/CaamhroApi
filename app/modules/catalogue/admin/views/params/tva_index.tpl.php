<?php $view = view::getInstance(); ?>
<div class="params_content">
	<h2><?= dims_constant::getVal('GESTION_DE_LA_TVA'); ?></h2>

	<div class="actions">
	    <a href="<?= get_path('params', 'tva_edit'); ?>" class="link_img">
	        <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('ADD_TAXE'); ?>" alt="<?= dims_constant::getVal('ADD_TAXE'); ?>" />
	        <span><?= dims_constant::getVal('ADD_TAXE'); ?></span>
	    </a>
	</div>

	<?php
	$taux = $view->get("taux");
	if( isset($taux) && count($taux)){
		?>
		<table class="tableau">
		    <tr>
		        <td class="title_tableau">
		            <?= dims_constant::getVal('_DIMS_LABEL_GROUP_CODE'); ?>
		        </td>
		        <td class="title_tableau">
		            <?= dims_constant::getVal('_DIMS_LABEL_COUNTRY'); ?>
		        </td>
		        <td class="title_tableau">
		            <?= dims_constant::getVal('RATE_TVA'); ?>
		        </td>
		        <td class="w70p title_tableau">
		            <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
		        </td>
		    </tr>
		    <?php
		    foreach($taux as $tva){
		    	?>
		    	<tr>
			    	<td>
			            <?= $tva->getCode(); ?>
			        </td>
			        <td>
			            <?= ($_SESSION['dims']['currentlang'] == dims_const::_SYSTEM_LANG_FR) ? $tva->getLightAttribute('fr') : $tva->getLightAttribute('en') ; ?>
			        </td>
			        <td>
			            <?= $tva->getTaux(); ?>
			        </td>
			        <td>
			            <a href="<?= get_path('params', 'tva_edit', array('id_tva' => $tva->getCode(), 'id_pays' => $tva->getCountry())); ?>"><img src="<?php echo $this->getTemplateWebPath("/gfx/edit16.png"); ?>" title="<?= dims_constant::getVal('EDIT_THIS_TAXE'); ?>" alt="<?= dims_constant::getVal('EDIT_THIS_TAXE'); ?>" /></a>
					    <a onclick="javascript:dims_confirmlink('<?= get_path('params', 'tva_delete', array('id_tva' => $tva->getCode(), 'id_pays' => $tva->getCountry())); ?>','<?= dims_constant::getVal('SURE_TO_DELETE_THIS_TAXE'); ?>');" href="javascript:void(0);" ><img src="<?php echo $this->getTemplateWebPath("/gfx/supprimer16.png"); ?>" title="<?= dims_constant::getVal('DELETE_TVA'); ?>" alt="<?= dims_constant::getVal('DELETE_TVA'); ?>" /></a>
			        </td>
			    </tr>
		    	<?php
		    }
		    ?>
		</table>
		<?php
	}
	else{
		?>
		<div class="div_no_elem"><?= dims_constant::getVal('ANY_TAXE_DEFINED'); ?></div>
		<?php
	}
	?>
</div>