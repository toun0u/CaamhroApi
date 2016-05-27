<?php
$view = view::getInstance();
$list = $view->get('invitations');
?>
<table cellpadding="0" cellspacing="0" class="list-invitations">
	<tr>
		<th>
			<?= $_SESSION['cste']['_DIMS_LABEL']; ?>
		</th>
		<th>
			<?= $_SESSION['cste']['DATE_PLURIEL_OU_PAS']; ?>
		</th>
		<th>
			<?= $_SESSION['cste']['_DIMS_ACTIONS']; ?>
		</th>
	</tr>
	<?php
	if(count($list)){
		$ct = contact::find_by(array('id'=>$_SESSION['dims']['user']['id_contact']),null,1);
		if(empty($ct)){
			$ct = new contact();
			$ct->init_description();
		}
		foreach($list as $i){
			?>
			<tr>
				<td>
					<?= $i->get('libelle'); ?>
				</td>
				<td>
					<?= implode('<br />',$i->getSimpleDatesLink()); ?>
				</td>
				<td>
					<a href="<?= dims::getInstance()->getScriptEnv()."?c=obj&a=view&id=".$i->get('id'); ?>"><img src="/common/modules/invitation/visu_picto.png" /></a>
					<a href="<?= dims::getInstance()->getScriptEnv()."?c=obj&a=delete&id=".$i->get('id'); ?>"><img src="/common/modules/invitation/delete16.png" /></a>
					<a target="_blank" href="<?= $i->getFrontUrl("&id=".$ct->get('ref')); ?>"><img src="/common/modules/invitation/open_record16.png" /></a>
					<a href="<?= dims::getInstance()->getScriptEnv()."?c=obj&a=send&id=".$i->get('id'); ?>"><img src="/common/modules/invitation/icon_envoyer.png" /></a>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr>
			<td colspan="3" class="no-elements">
				<?= $_SESSION['cste']['_NO_INVITATION']; ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
