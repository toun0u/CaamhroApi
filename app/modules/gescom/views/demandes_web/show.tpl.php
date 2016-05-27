<?php
$view = view::getInstance();
$web_ask = $view->get('web_ask');
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8','fra');

$user = $view->get('webaskuser');
$cataclient = $view->get('cataclient');

$form = forms::find_by(array('id'=>$web_ask->get('id_form')),null,1);
$reply = reply::find_by(array('id'=>$web_ask->get('id_reply')),null,1);
$fields = $form->getAllFields();
$values = $reply->getFields();
?>
<h1><span class="icon-podcast"></span>&nbsp;Demandes web <span style="color:#444444;">#<?= $web_ask->get('id'); ?></span></h1>
<div class="row">
	<div class="col w10 txtcenter web-ask-date">
		<?= date('F Y</b\r><\sp\a\n>d</\sp\a\n>',dims_timestamp2unix($web_ask->get('timestp_create'))); ?>
	</div>
	<div class="col web-ask-infos">
		Demande émise par <?= $user->get('firstname').' '.$user->get('lastname'); ?>
		<?php
		if($user->get('email') != '') {
			?>
			(<a href="mailto: <?= $user->get('email'); ?>"><?= $user->get('email'); ?></a>)
			<?php
		}
		?>
		<div class="row mt1">
			<div class="col label w20">Type :</div>
			<div class="col">
				<?php
				if($cataclient->isParticular()) {
					echo dims_constant::getVal('CATA_NATURAL_PERSON');
				} elseif ($cataclient->isProfessional()) {
					echo dims_constant::getVal('CATA_LEGAL_ENTITY');
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col label w20">Tél :</div>
			<div class="col"><?= $user->get('phone'); ?></div>
		</div>
		<div class="row">
			<div class="col label w20">Adresse :</div>
			<div class="col"><?= $user->get('address'); ?></br><?= $user->get('postalcode'); ?> <?= $user->get('city'); ?></div>
		</div>
		<div class="txtright mt1">
			<a href="javascript:void(0);" class="btn btn-success start-case">Démarrer le dossier</a>
			<a href="<?= Gescom\get_path(array('c'=>'web_ask','a'=>"delete",'id'=>$web_ask->get('id'))); ?>" class="ml1 btn btn-error">Signaler comme indésirable</a>
		</div>
	</div>
	<div class="col w20 txtright">
		<a href="<?= Gescom\get_path(array('c'=>'dashboard','a'=>"index")); ?>" style="text-decoration:none;"><span class="icon-exit">&nbsp;Revenir au tableau de bord</a>
	</div>
</div>
<div class="table-responsive mt2">
	<table class="table w100">
		<tbody>
			<?php foreach($fields as $f){ ?>
				<tr>
					<td class="w20 label">
						<?= $f->get('name'); ?> :
					</td>
					<td>
						<?php if($f->get('type') == 'file'){
							$path = _DIMS_PATHDATA.'forms-'.$form->get('id_module')._DIMS_SEP.$form->get('id')._DIMS_SEP.$a->get('id')._DIMS_SEP;
							if(isset($values[$f->get('id')]) && file_exists($path.$values[$f->get('id')]->get('value'))){
								?>
								<a href="/data/forms-<?= $form->get('id_module')._DIMS_SEP.$form->get('id')._DIMS_SEP.$a->get('id')._DIMS_SEP.$values[$f->get('id')]->get('value'); ?>"><?= $values[$f->get('id')]->get('value'); ?></a>
								<?php
							}
						}else{
							echo isset($values[$f->get('id')])?nl2br($values[$f->get('id')]->get('value')):"";
						} ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php $view->partial($view->getTemplatePath('todos/dashboard.tpl.php')); ?>

<script type="text/javascript">
$(document).ready(function(){
	$('.start-case').click(function(){
		var id_popup = dims_openOverlayedPopup(800,600);
		dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', '<?= http_build_query(array('c'=>'dossier','a'=>'popup_create','idw'=>$web_ask->get('id'),'id_popup'=>'')); ?>'+id_popup,'','p'+id_popup);
	});
});
</script>
