<?php
$view = view::getInstance();
$dossier = $view->get('dossier');
$web_ask = $view->get('web_ask');
$steps = $view->get('steps');
$workflow = $view->get('workflow');
$ct = $view->get('ct');
$client = $view->get('client');
$quotations = $view->get('quotations');
?>
<h1><span class="icon-archive"></span>&nbsp;Dossier <span style="color:#444444;">#<?= $dossier->get('label'); ?></span></h1>

<?php
if(!empty($web_ask)){
	?>
	<h2>Dossier issu de la demande web #<?= $web_ask->get('id'); ?></h2>
	<?php
}
?>
<div class="text-muted mb1">
	<?php
	$date = dims_timestamp2local($dossier->get('datestart'));
	$user = user::find_by(array('id'=>$dossier->get('id_user')),null,1);
	?>
	Créé le <?= $date['date']; ?> par <?= empty($user)?"Inconnu":($user->get('firstname')." ".$user->get('lastname')); ?>
</div>

<div class="web-ask-infos">
	<div class="row">
		<div class="col label">
			<?php $user = user::find_by(array('id'=>$dossier->get('id_manager')),null,1); ?>
			Suivi par <?= empty($user)?"Inconnu":($user->get('firstname')." ".$user->get('lastname')); ?>
		</div>
		<div class="col label">Statut</div>
		<div class="col">
			<select class="change-state">
				<?php
				$step = null;
				foreach($steps as $s){
					if($s->get('id') == $dossier->get('status')){
						$step = $s->get('type');
						?><option value="<?= $s->get('id'); ?>" selected="true"><?= $s->get('label'); ?></option><?php
					}else{
						?><option value="<?= $s->get('id'); ?>"><?= $s->get('label'); ?></option><?php
					}
				}
				?>
			</select>
		</div>
		<div class="col">
			<?php switch ($step) {
				case gescom_workflow_step::_TYPE_FINISHED:
					?><p class="bg-success right" style="width:18px;border-radius: 50%;">&nbsp;</p><?php
					break;
				case gescom_workflow_step::_TYPE_CANCELLED:
					?><p class="bg-danger right" style="width:18px;border-radius: 50%;">&nbsp;</p><?php
					break;
				case gescom_workflow_step::_TYPE_WAITING:
				default:
					?><p class="bg-warning right" style="width:18px;border-radius: 50%;">&nbsp;</p><?php
					break;
			} ?>
		</div>
	</div>
	<?php if(!empty($ct)){ ?>
		<div class="row">
			<div class="col label w20">Client/Prospect :</div>
			<div class="col">
				<?php
				if($client->isProfessional() && !$client->getTiers()->isNew()) {
					?>
					<a data-tabable="true" href="/admin.php?dims_mainmenu=0&mode=company&action=show&id=<?= $client->getTiers()->getId(); ?>">
						<?= $client->get('nom'); ?>
					</a>
					<?php
				} elseif($client->isParticular()& !$client->getMainUser()->getContact()->isNew()) {
					?>
					<a data-tabable="true" href="/admin.php?dims_mainmenu=0&mode=contact&action=show&id=<?= $client->getMainUser()->getContact()->getId(); ?>">
						<?= $client->get('nom'); ?>
					</a>
					<?php
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col label w20">Tél :</div>
			<div class="col"><?= $ct->get('phone'); ?></div>
		</div>
	<?php } ?>
	<div class="row">
		<div class="col label w20">Procédure associée :</div>
		<div class="col"><?= $workflow->get('label'); ?></div>
	</div>
	<div class="row">
		<div class="col label w20">Description :</div>
		<div class="col"><?= nl2br($dossier->get('description')); ?></div>
	</div>
	<div class="row">
		<a href="<?= Gescom\get_path(array('c'=>'dossier','a'=>"edit",'id'=>$dossier->get('id'))); ?>" class="btn">
			<span class="icon-pencil">
				&nbsp;&Eacute;diter le dossier
			</span>
		</a>
		<a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?= Gescom\get_path(array('c'=>'dossier','a'=>"delete",'id'=>$dossier->get('id'))); ?>', '<?= dims_constant::getVal('_DIMS_CONFIRM'); ?>')" class="ml1 btn btn-error">
			<span class="icon-trash">
				&nbsp;Supprimer le dossier
			</span>
		</a>
	</div>
</div>
<div>
	<h1>
		<span class="icon-coin"></span>&nbsp;
		<?= dims_constant::getVal('COMMERCIAL_DOCUMENTS'); ?>
	</h1>
	<div>
		<a href="<?= \get_path('quotations', 'new', array('dims_mainmenu' => 'catalogue', 'clientid' => $client->getId(), 'caseid' => $dossier->getId())); ?>">
			<?= dims_constant::getVal('CREATE_A_QUOTATION_FOR_THIS_CASE'); ?>
		</a>
	</div>
	<?php
	if(empty($quotations)) {
		?>
		<div>
			<?= dims_constant::getVal('NO_COMMERCIAL_DOCUMENT_FOR_NOW'); ?>
		</div>
		<?php
	} else {
		?>
		<div>
			<table class="tableau">
				<tr>
					<td class="w10 title_tableau"><?= dims_constant::getVal('_STATE'); ?></td>
					<td class="w10 title_tableau"><?= dims_constant::getVal('_DIMS_DATE'); ?></td>
					<td class="w50 title_tableau"><?= dims_constant::getVal('_DIMS_LABEL'); ?></td>
					<td class="w10 title_tableau"><?= dims_constant::getVal('_DISCOUNT'); ?></td>
					<td class="w10 title_tableau"><?= dims_constant::getVal('_DIMS_ACTIONS'); ?></td>
				</tr>
				<?php
				foreach($quotations as $quotation) {
					$localdate = array('date' => '', 'time' => '');
					if($quotation->get('date_cree') > 0) {
						$localdate = dims_timestamp2local($quotation->get('date_cree'));
					}
					?>
					<tr>
						<td>
							<img src="<?= cata_facture::getstatepicture($quotation->fields['state']); ?>" alt="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" title="<?= cata_facture::getstatelabel($quotation->fields['state']); ?>" />
						</td>
						<td><?= (!empty($localdate['date']) ? $localdate['date'] : '<em>n/a</em>'); ?></td>
						<td>
							<a href="<?= get_path('clients', 'show', array('dims_mainmenu' => 'catalogue', 'id' => $quotation->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->getId())); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_VIEW'); ?>">
								<?= $quotation->fields['libelle']; ?>
							</a>
						</td>
						<td><?= $quotation->fields['discount']; ?>&nbsp;%</td>
						<td>
							<a href="<?= get_path('clients', 'show', array('dims_mainmenu' => 'catalogue', 'id' => $quotation->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->getId())); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_VIEW'); ?>">
								<span class="icon-eye"></span>
							</a>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
		<?php
	}
	?>
</div>

<div>
	<h1>
		<span class="icon-coin"></span>&nbsp;
		<?= dims_constant::getVal('_DOCS'); ?>
	</h1>
	<?php
	$dossier->display(DIMS_APP_PATH . 'modules/gescom/public_ged.php');
	?>
</div>


<script type="text/javascript">
$(document).ready(function(){
	$(".change-state").change(function(){
		document.location.href='<?= Gescom\get_path(array('c'=>'dossier','a'=>"change_state",'id'=>$dossier->get('id'),'state'=>'')); ?>'+$(this).val();
	});
});
</script>
