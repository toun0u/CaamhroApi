<style type="text/css">
	table.appointment td{
		line-height: 25px;
		vertical-align: middle;
	}
	table.appointment td.ok{
		background: #D1F3D1;
		text-align: center;
	}
	table.appointment td.non_ok{
		background: #FFCCCA;
	}
	table.appointment td.selected_prop{
		border: 2px solid #D0E3FB;
	}
	table.appointment td.new_entry{
		text-align: center;
		background: #D0E3FB;
	}
	table.appointment td.nb_particip{
		font-weight:bold;
	}
	table.appointment td.save_button{
		text-align: right;
	}
	table.appointment td.propositions{
		text-align: center;
		padding-left:5px;
		padding-right:5px;
	}
	table.appointment td.results{
		text-align: center;
		font-weight:bold;
	}
	table.appointment img.contact{
		float:left;
		width: 25px;
	}
	table.appointment span.contact{
		margin-left:5px;
		margin-right:5px;
	}
	div.global_message{
		padding:10px;
		margin-top: 5px;
		border: 1px solid #B4E226;
		line-height: 20px;
	}
	div.global_message a{
		color: #B4E226;
	}
</style>
<?
if($this->getLightAttribute('submitApp')){
	?>
	<div class="global_message">
		Merci d'avoir répondu à cette proposition de rendez-vous.<br />
		Retourner à la <a href="/index.php">page d'accueil</a> du site.
	</div>
	<?
}
?>
<h3>
	<? echo stripslashes($this->fields['libelle']); ?>
</h3>
<h4>
	<?
	echo $this->fields['address']."<br />".$this->fields['cp']." ".$this->fields['lieu'];
	?>
</h4>
<?
$lstDoc = $this->getLightAttribute('linkedDoc');
if (count($lstDoc) > 0){
	?>
	<div class="linked_doc">
		<ul>
			<?
			foreach($lstDoc as $doc){
				?>
				<li>
					<a href="<? echo $doc->getDownloadLink(); ?>">
						<? echo $doc->fields['name']; ?>
					</a>
				</li>
				<?
			}
			?>
		</ul>
	</div>
	<?
}
$reponses = $this->getLightAttribute('reponses');
$propositions = $this->getLightAttribute('propositions');
$resultats = array();
?>
<table cellpadding="0" cellspacing="2" class="appointment">
	<tr>
		<td class="nb_particip">
			<?
			$nbRep = count($reponses);
			echo "$nbRep ".strtolower(($nbRep>1)?$_SESSION['cste']['_DIMS_PARTICIP']:substr($_SESSION['cste']['_DIMS_PARTICIP'],0,-1));
			?>
		</td>
		<?
		foreach($propositions as $prop){
			$resultats[$prop->fields['id']] = 0;
			?>
			<td class="propositions <? echo ($this->fields['status'] == dims_appointment_offer::STATUS_VALIDATED && $this->fields['datejour'] == $prop->fields['datejour'] && $this->fields['heuredeb'] == $prop->fields['heuredeb'] && $this->fields['heurefin'] == $prop->fields['heurefin'])?"selected_prop":""; ?>">
				<?
				$dd = explode('-',$prop->fields['datejour']);
				if (count($dd) == 3)
					echo $dd[2]."/".$dd[1]."/".$dd[0];
				else
					echo $prop->fields['datejour'];
				echo "<br />";
				if($prop->fields['heuredeb'] == '08:00:00' && $prop->fields['heurefin'] == '08:00:00'){
					echo $_SESSION['cste']['_ALL_DAY'];
				}else{
					echo substr($prop->fields['heuredeb'],0,5)." - ".substr($prop->fields['heurefin'],0,5);
				}
				?>
			</td>
			<?
		}
		?>
	</tr>
	<?
	$alreadyRep = false;
	foreach($reponses as $rep){
		?>
		<tr>
			<td>
				<?
				$photo = "/common/img/contacts40.png";
				if($rep->fields['id_contact'] != '' && $rep->fields['id_contact'] > 0){
					$ct = new contact();
					$ct->open($rep->fields['id_contact']);
					if(file_exists($ct->getPhotoPath(25)))
						$photo = "/".$ct->getPhotoWebPath(25);
				}
				?>
				<img class="contact" src="<? echo $photo; ?>" />
				<?
				/*if($rep->getLightAttribute('currentCt') && $this->fields['status'] != dims_appointment_offer::STATUS_VALIDATED){
					?>
					<input style="margin-left:5px;" type="text" value="<? echo $rep->fields['name']; ?>" name="name" />
					<?
				}else{*/
					?>
					<span class="contact">
					<?
					echo $rep->fields['name'];
					?>
					</span>
				<? //} ?>
			</td>
			<?
			if($rep->getLightAttribute('currentCt') && $this->fields['status'] != dims_appointment_offer::STATUS_VALIDATED){
				$alreadyRep = true;
				foreach($propositions as $prop){
					if (isset($rep->reponses[$prop->fields['id']])){
						if($rep->reponses[$prop->fields['id']]->fields['presence']){
							$resultats[$prop->fields['id']] ++;
							?>
							<td class="new_entry">
								<input checked=true type="checkbox" name="lstReponses[]" value="<? echo $prop->fields['id']; ?>" />
							</td>
							<?
						}else{
							?>
							<td class="new_entry">
								<input type="checkbox" name="lstReponses[]" value="<? echo $prop->fields['id']; ?>" />
							</td>
							<?
						}
					}else{
						?>
						<td class="new_entry">
							<input type="checkbox" name="lstReponses[]" value="<? echo $prop->fields['id']; ?>" />
						</td>
						<?
					}

				}
			}else{
				foreach($propositions as $prop){
					if (isset($rep->reponses[$prop->fields['id']])){
						if($rep->reponses[$prop->fields['id']]->fields['presence']){
							$resultats[$prop->fields['id']] ++;
							?>
							<td class="ok">
								<img src="/common/img/checkdo2.png" />
							</td>
							<?
						}else{
							?>
							<td class="non_ok">

							</td>
							<?
						}
					}else{
						?>
						<td class="non_ok">

						</td>
						<?
					}

				}
			}
			?>
		</tr>
		<?
	}
	$infos = $this->getLightAttribute('infos');
	if ($this->fields['status'] != dims_appointment_offer::STATUS_VALIDATED && !$alreadyRep){
	?>
	<tr>
		<td>
			<img class="contact" src="<? echo $infos['photo']; ?>" />
			<?
			if(trim($infos['name']) != ''){
				?>
				<span class="contact">
				<?
				echo $infos['name'];
				?>
				</span>
				<?
			}else{
				?>
				<input style="margin-left:5px;" type="text" value="<? echo $infos['name']; ?>" name="name" id="name_appoint" />
				<?
			}
			?>
		</td>
		<?
		for($i=0;$i<count($propositions);$i++){
			?>
			<td class="new_entry">
				<input type="checkbox" name="lstReponses[]" value="<? echo $propositions[$i]->fields['id']; ?>" />
			</td>
			<?
		}
		?>
	</tr>
	<? } ?>
	<tr>
		<td></td>
		<?
		foreach($resultats as $res){
			?>
			<td class="results">
				<? echo $res; ?>
			</td>
			<?
		}
		?>
	</tr>
	<?
	if ($this->fields['status'] != dims_appointment_offer::STATUS_VALIDATED){
	?>
	<tr>
		<td class="save_button" colspan="<? echo count($propositions)+1; ?>">
			<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
			<input onclick="javascript:refuseAllDates();" type="button" value="<? echo $_SESSION['cste']['_NO_DATE_FOR_ME']; ?>" />
		</td>
	</tr>
	<?
	}else{
		$date = $this->fields['datejour'];
		$dd = explode('-',$this->fields['datejour']);
		if (count($dd) == 3)
			$date = $dd[2]."/".$dd[1]."/".$dd[0];
	?>
	<tr>
		<td class="save_button" colspan="<? echo count($propositions)+1; ?>">
			<? echo $_SESSION['cste']['_RESTAINT_DATE']; ?> : <?= $date; ?>
		</td>
	</tr>
	<?
	}
	?>
</table>
<?
if(trim($infos['name']) == ''){
?>
	<script type="text/javascript">
		function refuseAllDates() {
			if ($('#name_appoint') && $('#name_appoint').val() == '') {
				alert('<?php echo $_SESSION['cste']['_APP_OFFER_NAME_OBLIGATORY']; ?>');
				return false;
			}
			else {
				$('table.appointment input[type=\'checkbox\']').attr('checked',false);
				document.appointment_submit.submit();
			}
		}

		$(document).ready(function(){
			$('form[name="appointment_submit"]').submit(function(){
				return ($('input#name_appoint').val() != '');
			});
		})
	</script>
<?
}
?>
