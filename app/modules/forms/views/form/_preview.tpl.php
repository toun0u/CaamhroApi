<?php
// Ne pas changer ce fichier pour un tpl spécifique ajouter un fichier _preview.tpl.php dans templates/frontoffice/tpl/forms/
?>

<script type="text/javascript">
if($.fn.dims_validForm === undefined){
	jQuery.ajax({
		dataType: 'script',
		cache: true,
		url: "/assets/javascripts/common/dims_validForm.js",
		async: false
	});
}
</script>
<?php
$view = view::getInstance();
$form = $view->get('form');
if(!$form->isNew()){
	$backUrl = $view->get('back_form_'.$form->get('id'));
	$values = $view->get('values');
	$fo = new Dims\form(array(
		'name' 						=> 'form_display_'.$form->get('id'),
		'action'					=> $view->get('action_form_'.$form->get('id')),
		'additionnal_attributes'	=> 'role="form"',
		'enctype'					=> true,
	));
	$fields = $form->getAllFields();
	$nbRows = $form->get('nb_col')>0?$form->get('nb_col'):1;
	$suppJsLib = array(
		'date' => false,
		'color' => false,
	);
	echo $fo->get_header();
	?>
	<div class="row">
		<?php
		$i = 0;
		$nbElems = count($fields);
		foreach($fields as $f){
			$i++;
			if($f->get('format') == 'date' || $f->get('format') == 'time'){
				$suppJsLib['date'] = true;
			}elseif($f->get('format') == 'color'){
				$suppJsLib['color'] = true;
			}
			?>
			<div class="col-xs-<?= round(12/$nbRows); ?>">
				<div class="form_row mb1<?= (true)?"":"has-error"; ?>">
					<span class="label"><?= $f->getLabel(); ?></span>
						<span class="field">
						<?= $f->getFields($fo,(isset($values[$f->get('id')])?$values[$f->get('id')]:""),$view->get('reply_id')); ?>
					</span>
				</div>
			</div>
			<?php
			if(($i%$nbRows) === 0 && $i < $nbElems){
				?>
				</div><div class="row">
				<?php
			}
		}
		?>
	</div>
	<?php
	// captcha : honeypot
	?>
	<div class="human-div">
		<?= $fo->text_field(array(
				'name'						=> 'comment',
				'id'						=> 'comment',
				'additionnal_attributes' 	=> 'placeholder="Si vous êtes un humain, laissez ce champ vide"',
				'value'						=> "",
			)); ?>
	</div>
	<div class="txtright">
		<span>* champ obligatoire&nbsp;</span>
		<?php if(!empty($backUrl)){ ?>
			<a href="<?= $backUrl; ?>" class="btn btn-link btn-sm "><?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?></a>
			<span><?= $_SESSION['cste']['_DIMS_OR']; ?></span>
		<?php } ?>
		<button type="submit" class="btn btn-primary"><?= $_SESSION['cste']['_SUBMIT']; ?></button>
	</div>
	<?php
	echo $fo->close_form();
	if($suppJsLib['date']){
		// on charge la lib datepicker avec $('input[rev="date_jj/mm/yyyy"]') && $('input[rev="heure_hh:mm"]')
		?>
		<script type="text/javascript">
		$(document).ready(function(){
			$('#form_display_<?= $form->get('id'); ?> input[rev="date_jj/mm/yyyy"]').datepicker({
				dateFormat: "dd/mm/yy"
			});
		});
		</script>
		<?php
	}
	if($suppJsLib['color']){
		// on charge la lib colorpicker avec $('input[rev="color"]')
		// TODO: géré dans getFields
	}
}
?>
<style>
.human-div{
	display:none;
	position: relative;
	top: -8000px;
	left: -8000px;
}
</style>
