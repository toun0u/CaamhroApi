<?php
$view = view::getInstance();
$obj = $view->get('obj');
$obj->getContentMail();
?>
<table cellspacing="0" cellpadding="0">
	<tr>
		<td class="label">
			<?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?> :&nbsp;
		</td>
		<td>
			<?= nl2br($obj->get('description')); ?>
		</td>
	</tr>
	<tr>
		<td class="label">
			<?= $_SESSION['cste']['DATES']; ?> :&nbsp;
		</td>
		<td>
			<?= implode('<br />',$obj->getSimpleDatesLink()); ?>
		</td>
	</tr>
</table>
<h3><?= $_SESSION['cste']['_DIMS_LABEL_CONTACTS']; ?></h3>
<table cellspacing="0" cellpadding="0" class="tableau_invitation">
	<tr>
		<th></th>
		<th><?= $_SESSION['cste']['_DIMS_LABEL_NAME']; ?></th>
		<th><?= $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?></th>
		<th></th>
	</tr>
	<?php
	$cts = $obj->getCtLinks();
	if(count($cts)){
		foreach($cts as $ct){
			$photo = "/common/modules/invitation/contacts40.png";
			if(file_exists($ct->getPhotoPath(40))){
				$photo = $ct->getPhotoWebPath(40);
			}
			?>
			<tr class="border">
				<td style="width:5%;text-align:center"><img src="<?= $photo; ?>" /></td>
				<td style="width:45%"><?= $ct->get('firstname')." ".$ct->get('lastname'); ?></td>
				<td style="width:40%"><a href="mailto:<?= $ct->get('email'); ?>"><?= $ct->get('email'); ?></a></td>
				<td style="text-align:center">
					<a href="<?= dims::getInstance()->getScriptEnv()."?c=obj&a=send&id=".$obj->get('id')."&ct=".$ct->get('id'); ?>"><img src="/common/modules/invitation/icon_envoyer.png" /></a>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr class="border">
			<td colspan="4" class="no-elements">
				<?= $_SESSION['cste']['_DIMS_LABEL_NO_CT_ATTACHED']; ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<h3><?= $_SESSION['cste']['_ANSWERS']; ?></h3>
<table cellspacing="0" cellpadding="0" class="tableau_invitation">
	<tr>
		<th></th>
		<th><?= $_SESSION['cste']['_DIMS_LABEL_NAME']; ?></th>
		<th><?= $_SESSION['cste']['_SELECTED_DATE']; ?></th>
		<th><?= $_SESSION['cste']['_DIMS_ACCOMPANY']; ?></th>
	</tr>
	<?php
	$cts = $obj->getCtReponse();
	if(count($cts)){
		foreach($cts as $ct){
			$photo = "/common/modules/invitation/contacts40.png";
			if(file_exists($ct->getPhotoPath(40))){
				$photo = $ct->getPhotoWebPath(40);
			}
			?>
			<tr class="border">
				<td style="width:5%;text-align:center"><img src="<?= $photo; ?>" /></td>
				<td style="width:45%"><?= $ct->get('firstname')." ".$ct->get('lastname'); ?></td>
				<td style="width:20%">
					<?php
					$d = $ct->getLightAttribute('reponse');
					$d1 = implode('/',array_reverse(explode('-',$d->get('datejour'))));
					$d2 = implode('/',array_reverse(explode('-',$d->get('datefin'))));
					$de = $d1." ".substr($d->get('heuredeb'), 0, 5);
					if($d1 == $d2){
						$de .= " - ".substr($d->get('heurefin'), 0, 5);
					}else{
						$de .= " - ".$d2." ".substr($d->get('heurefin'), 0, 5);
					}
					echo $de;
					?>
				</td>
				<td>
					<?php
					$acc = $obj->getAccompanyValues($ct->get('id'));
					$lstAcc = array();
					foreach($acc as $a){
						if($a->get('age') != '')
							$lstAcc[] = $a->get('name')." (".$a->get('age').")";
						else
							$lstAcc[] = $a->get('name');
					}
					echo implode('<br />',$lstAcc);
					?>
				</td>
			</tr>
			<?php
		}
	}else{
		?>
		<tr class="border">
			<td colspan="4" class="no-elements">
				<?= $_SESSION['cste']['_NO_ANSWER']; ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>