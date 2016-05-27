<?php
$view = view::getInstance();

$label = $view->get('label');
$type = $view->get('type');
$id_workflow = $view->get('id_workflow');
$id_state = $view->get('id_state');
$id_client = $view->get('id_client');
$id_responsable = $view->get('id_responsable');

$workflows = $view->get('workflows');
$steps = $view->get('steps');
$managers = $view->get('managers');

$dossiers = $view->get('dossiers');
$dossier = $view->get('dossier');
?>
<h1><span class="icon-archive"></span>&nbsp;Dossiers</h1>

<?php
$f = new Dims\form(array(
	'name' 			=> 'form_search',
	'action'		=> dims::getInstance()->getScriptEnv(),
	'submit_value' 	=> $_SESSION['cste']['_SEARCH'],
	'method'		=> 'GET',
	'back_name'		=> $_SESSION['cste']['_DIMS_RESET'],
	'back_url'		=> Gescom\get_path(array('c'=>'dossier','a'=>"index")),
));
$f->add_hidden_field(array(
	'name'						=> 'c',
	'value'						=> $view->get('c'),
));
$f->add_hidden_field(array(
	'name'						=> 'a',
	'value'						=> $view->get('a'),
));
$f->add_text_field(array(
	'name' 						=> 'l',
	'value'						=> $label,
	'label' 					=> "Label",
	'classes'					=> 'form-control',
	'additionnal_attributes' 	=> 'placeholder="'.$_SESSION['cste']['_FORMS_FILTER'].'"',
));
$f->add_select_field(array(
	'name' 						=> 't',
	'value'						=> $type,
	'label' 					=> "Type",
	'classes'					=> 'form-control select-submit',
	'additionnal_attributes' 	=> '',
	'options' 					=> array(
									"" => "Tous",
									gescom_workflow_step::_TYPE_WAITING => "En cours",
									gescom_workflow_step::_TYPE_FINISHED => "Fini",
									gescom_workflow_step::_TYPE_CANCELLED => "Annulé",
								),
));
$f->add_select_field(array(
	'name' 						=> 'w',
	'value'						=> $id_workflow,
	'label' 					=> "Workflow",
	'classes'					=> 'form-control select-submit',
	'additionnal_attributes' 	=> '',
	'options' 					=> $workflows,
));
$f->add_select_field(array(
	'name' 						=> 's',
	'value'						=> $id_state,
	'label' 					=> "Étape",
	'classes'					=> 'form-control select-submit',
	'additionnal_attributes' 	=> (empty($steps)?'disabled="true"':""),
	'options' 					=> $steps,
));
$f->add_text_field(array(
	'name' 						=> 'i',
	'value'						=> $id_client,
	'label' 					=> "Client",
	'classes'					=> 'form-control',
	'additionnal_attributes' 	=> "",
	//'options' 					=> array(),
));
$f->add_select_field(array(
	'name' 						=> 'r',
	'value'						=> $id_responsable,
	'label' 					=> "Responsable",
	'classes'					=> 'form-control select-submit',
	'additionnal_attributes' 	=> "",
	'options' 					=> $managers,
));
$f->build();
?>
<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="w5"></th>
				<th class="w30">Label</th>
				<th class="w10">Client</th>
				<th class="w10">Responsable</th>
				<th>Worflow</th>
				<th>Étape</th>
				<th class="w5">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php if(empty($dossiers)){ ?>
				<tr>
					<td colspan="7" class="txtcenter"><?= $_SESSION['cste']['NO_RESULT']; ?></td>
				</tr>
			<?php }else{
				foreach($dossiers as $d){
					?>
					<tr>
						<td>
							<?php switch ($d['workflow_step']['type']) {
								case gescom_workflow_step::_TYPE_FINISHED:
									?><p class="bg-success center" style="width:18px;border-radius: 50%;">&nbsp;</p><?php
									break;
								case gescom_workflow_step::_TYPE_CANCELLED:
									?><p class="bg-danger center" style="width:18px;border-radius: 50%;">&nbsp;</p><?php
									break;
								case gescom_workflow_step::_TYPE_WAITING:
								default:
									?><p class="bg-warning center" style="width:18px;border-radius: 50%;">&nbsp;</p><?php
									break;
							} ?>
						</td>
						<td>
							<a style="text-decoration: none;" href="<?= Gescom\get_path(array('c' => 'dossier', 'a' => 'show', 'id' => $d['dossier']['id'])); ?>">
								<?= $d['dossier']['label']; ?>
								<span class="text-muted"><?= $d['dossier']['long_label']; ?></span>
							</a>
						</td>
						<td><a href="#" data-tabable="true"><?= $d['contact']['firstname']." ".$d['contact']['lastname']; ?></a></td>
						<td><a href="#" data-tabable="true"><?= $d['responsable']['firstname']." ".$d['responsable']['lastname']; ?></a></td>
						<td><?= $d['workflow']['label']; ?></td>
						<td><?= $d['workflow_step']['label']; ?></td>
						<td>
							<a style="text-decoration: none;" href="<?= Gescom\get_path(array('c' => 'dossier', 'a' => 'show', 'id' => $d['dossier']['id'])); ?>">
								<span class="icon-eye"></span>
							</a>
						</td>
					</tr>
					<?php
				}
			} ?>
		</tbody>
	</table>
</div>
<?php
$pagin = $dossier->getPagination();
if(!empty($pagin)){
	?>
	<div class="text-right">
		<?php
		foreach($pagin as $p){
			if(empty($p['url'])){
				?>
				<button type="button" disabled="disabled" class="btn btn-default btn-xs"><?= $p['label']; ?></button>
				<?php
			}else{
				?>
				<a href="<?= $p['url']; ?>" class="btn btn-default btn-xs" alt="<?= $p['title']; ?>" title="<?= $p['title']; ?>"><?= $p['label']; ?></a>
				<?php
			}
		}
		?>
	</div>
	<?php
}
$ct = contact::find_by(array('id'=>$id_client),null,1);
?>
<style type="text/css">
.select2-container {width: 100%;}
</style>
<script type="text/javascript" src="/assets/javascripts/common/select2-3.5.1/select2.min.js"></script>
<script type="text/javascript" src="/assets/javascripts/common/select2-3.5.1/select2_locale_fr.js"></script>
<link type="text/css" rel="stylesheet" href="/assets/javascripts/common/select2-3.5.1/select2.css" />
<script type="text/javascript">
$(document).ready(function(){
	$('.select-submit').change(function(){
		document.form_search.submit();
	}).select2();
	$('input#i').select2({
		minimumInputLength: 2,
		quietMillis: 100,
		ajax: {
			url: '<?= Gescom\get_path(array('c'=>'dossier','a'=>"search_ct")); ?>',
			dataType: 'json',
			data: function(term,page){
				return {
					s: term
				};
			},
			results: function(data, page){
				return {results: data};
			}
		}<?php if(!empty($ct)){ ?>,
		initSelection:function(elem, callback){
			callback({id:'<?= $ct->get('id'); ?>',text:'<?= $ct->get('firstname')." ".$ct->get('lastname'); ?>'});
		}<?php } ?>
	}).change(function(){
		document.form_search.submit();
	});
});
</script>
