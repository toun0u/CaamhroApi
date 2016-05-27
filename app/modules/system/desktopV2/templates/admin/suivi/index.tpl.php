<h2><?= $_SESSION['cste']['_PARAMETERS']; ?></h2>
<?php
$params = class_gescom_param::getAllParams();
$form = new Dims\form(array(
	'name' 			=> "params",
	'method'		=> "POST",
	'action'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=suivis&action=save_params",
	'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
	'back_name'		=> $_SESSION['cste']['_DIMS_CANCEL'],
	'back_url'		=> dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=suivis&action=index",
));
$form->add_text_field(array(
	'name'						=> 'param[exercice]',
	'label' 					=> $_SESSION['cste']['_DUTY'],
	'value'						=> isset($params['exercice'])?$params['exercice']:"",
	'additionnal_attributes'	=> '',
));
$form->add_text_field(array(
	'name'						=> 'param[datedeb]',
	'label' 					=> "Début exercice",
	'value'						=> isset($params['datedeb'])?$params['datedeb']:"",
	'additionnal_attributes'	=> 'maxlength="10" size="20"',
	'revision'					=> 'date_jj/mm/yyyy',
));
$form->add_text_field(array(
	'name'						=> 'param[datefin]',
	'label' 					=> "Fin exercice",
	'value'						=> isset($params['datefin'])?$params['datefin']:"",
	'additionnal_attributes'	=> 'maxlength="10" size="20"',
	'revision'					=> 'date_jj/mm/yyyy',
));
$country = country::getAllCountries();
$lstC = array(0=>"");
foreach($country as $c){
	$lstC[$c->get('id')] = $c->get('name');
}
$form->add_select_field(array(
	'name'						=> 'param[pays]',
	'label' 					=> $_SESSION['cste']['_DIMS_LABEL_COUNTRY'],
	'value'						=> isset($params['pays'])?$params['pays']:0,
	'options'					=> $lstC,
));
$form->add_textarea_field(array(
	'name'						=> 'param[conditionpaiement]',
	'label' 					=> "Conditions de Paiement",
	'value'						=> isset($params['conditionpaiement'])?$params['conditionpaiement']:"",
	'additionnal_attributes'	=> '',
));
$form->build();
?>

<h2><?= $_SESSION['cste']['_TYPE']; ?></h2>
<div class="sub">
	<div class="action">
		<a href="<?= dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=add_type';?>"><img src="<?= _DESKTOP_TPL_PATH.'/gfx/common/add.png';?>"/>Ajouter un type</a>
	</div>
	<?php
	$types = suivi_type::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'])," ORDER BY label ");
	if(count($types)){
		?>
		<table class="list_models">
			<tr>
				<th></th>
				<th><?= $_SESSION['cste']['_DIMS_LABEL']; ?></th>
				<!--<th><?= $_SESSION['cste']['_PRIVATE']; ?></th>-->
				<th class="actions"><?= $_SESSION['cste']['_DIMS_ACTIONS']; ?></th>
			</tr>
			<?php
			foreach($types as $t){
				?>
				<tr>
					<td>
						<img src="<?= _DESKTOP_TPL_PATH.'/gfx/common/'.($t->get('status')?"":"in").'actif16.png';?>" />
					</td>
					<td><?= $t->fields['label']; ?></td>
					<!--<td><?= $t->fields['public']?$_SESSION['cste']['_DIMS_NO']:$_SESSION['cste']['_DIMS_YES']; ?></td>-->
					<td>
						<a href="<?= dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=add_type&id='.$t->get('id');?>"><?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></a>
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
		<div class="empty">Aucun type n'a été défini pour cet espace de travail</div>
		<?php
	}
	?>
</div>

<h2><?= $_SESSION['cste']['PRINTING_MODELS_LIST']; ?></h2>
<div class="sub">
	<div class="action">
		<a href="<?= dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=add_model';?>"><img src="<?= _DESKTOP_TPL_PATH.'/gfx/common/add.png';?>"/><?= $_SESSION['cste']['NEW_MODEL']; ?></a>
	</div>
	<?php
	$models = print_model::all();
	if(count($models)){
		?>
		<table class="list_models">
			<tr><th><?= $_SESSION['cste']['_TYPE']; ?></th><th><?= $_SESSION['cste']['_DIMS_LABEL']; ?></th><th><?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></th><th class="actions"><?= $_SESSION['cste']['_DIMS_ACTIONS']; ?></th></tr>
			<?php
			foreach($models as $model){
				?>
				<tr>
					<td><?= $model->fields['type_label']; ?></td>
					<td><?= $model->getLabel(); ?></td>
					<td><?= nl2br($model->getDescription()); ?></td>
					<td>
						<a href="<?= dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=add_modelt&id='.$model->getId();?>"><?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></a>&nbsp;|&nbsp;
						<a href="javascript:void(0);" onclick="javascript:if(confirm('<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>')) document.location.href='<?= dims::getInstance()->getScriptEnv().'?submenu=1&mode=admin&o=suivis&action=delete_model&id='.$model->getId();?>';"><?= $_SESSION['cste']['_DELETE']; ?></a>&nbsp;|&nbsp;
						<a  href="<?= dims_urlencode(dims::getInstance()->getScriptEnv()."?dims_op=doc_file_download&docfile_md5id=".$model->fields['md5id']); ?>"><?= $_SESSION['cste']['_DIMS_DOWNLOAD']; ?></a>
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
		<div class="empty"><?= $_SESSION['cste']['NO_MODEL_FOR_THIS_WORKSPACE']; ?></div>
		<?php
	}
	?>
</div>