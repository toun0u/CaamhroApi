<?php
$view = view::getInstance();
$workflow = $view->get('workflow');
$steps = $view->get('steps');
?>
<h1>Workflow : <?= $workflow->get('label'); ?></h1>
<div class="txtright">
	<a href="javascript:void(0);" class="btn add-step"><span class="icon-plus-alt"></span>&nbsp;Ajouter une &eacute;tape</a>
</div>

<p class="text-muted"><?= nl2br($workflow->get('description')); ?></p>

<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="w5">Position</th>
				<th class="">Label</th>
				<th class="w10">Type</th>
				<th class="w5">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			if(empty($steps)){
				?>
				<tr>
					<td colspan="4" class="txtcenter">
						Aucune étape définie
					</td>
				</tr>
				<?php
			}else{
				foreach($steps as $s){
					?>
					<tr class="<?= $s->get('state')==gescom_workflow_step::_STATE_DISABLED?'text-muted':''; ?>">
						<td class="txtcenter">
							<?= $s->get('position'); ?>
						</td>
						<td>
							<?= $s->get('label'); ?>
						</td>
						<td>
							<?php switch($s->get('type')){
								case gescom_workflow_step::_TYPE_WAITING:
									echo 'En cours';
									break;
								case gescom_workflow_step::_TYPE_FINISHED:
									echo 'Fini';
									break;
								case gescom_workflow_step::_TYPE_CANCELLED:
									echo 'Annulé';
									break;
							} ?>
						</td>
						<td class="txtcenter">
							<a href="javascript:void(0);" class="edit-step" dims-data-value="<?= $s->get('id'); ?>" alt="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" title="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" style="text-decoration:none;"><span class="icon-pencil"></span></a>&nbsp;
							<a href="<?= Gescom\get_path(array('c'=>'admin','a'=>'switch_workflow_step','id'=>$workflow->get('id'),'ids'=>$s->get('id'))); ?>" alt="<?= $s->get('state')==gescom_workflow_step::_STATE_DISABLED?'Activer':'Désactiver'; ?>" title="<?= $s->get('state')==gescom_workflow_step::_STATE_DISABLED?'Activer':'Désactiver'; ?>" style="text-decoration:none;"><span class="icon-<?= $s->get('state')==gescom_workflow_step::_STATE_DISABLED?'lock':'unlocked'; ?>"></span></a>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('.add-step').click(function(){
		var id_popup = dims_openOverlayedPopup(800,600);
		dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', '<?= http_build_query(array('c'=>'admin','a'=>'add_workflow_step','id'=>$workflow->get('id'),'id_popup'=>'')); ?>'+id_popup,'','p'+id_popup);
	});
	$('.edit-step').click(function(){
		var id_popup = dims_openOverlayedPopup(800,600);
		dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', '<?= http_build_query(array('c'=>'admin','a'=>'add_workflow_step','id'=>$workflow->get('id'),'id_popup'=>'')); ?>'+id_popup+"&ids="+$(this).attr('dims-data-value'),'','p'+id_popup);
	});
});
</script>
