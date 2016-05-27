<style type="text/css">
	table.appointment{
		width:100%;
	}
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
</style>
<?
$id_popup = $this->getLightAttribute('id_popup');
?>
<div>
	<div class="actions">
		<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
			<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
		</a>
	</div>
	<h2>
		<? echo stripslashes($this->fields['libelle']); ?>
	</h2>
	<h3>
		<?
		echo $this->fields['address']."<br />".$this->fields['cp']." ".$this->fields['lieu'];
		?>
	</h3>
	<?
	$propositions = $this->getListProp();
	$reponses = $this->getListRep();
	$resultats = array();
	?>
	<form method="POST" action="<? echo dims::getInstance()->getScriptEnv(); ?>">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("mode",			"appointment_offer");
			$token->field("action",			"valide");
			$token->field("app_offer_id",	$this->fields['id']);
			$token->field("date");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" name="mode" value="appointment_offer" />
		<input type="hidden" name="action" value="valide" />
		<input type="hidden" name="app_offer_id" value="<? echo $this->fields['id']; ?>" />
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
			foreach($reponses as $rep){
				?>
				<tr>
					<td>
						<?
						$photo = "./common/img/contacts40.png";
						if($rep->fields['id_contact'] != '' && $rep->fields['id_contact'] > 0){
							$ct = new contact();
							$ct->open($rep->fields['id_contact']);
							if(file_exists($ct->getPhotoPath(25)))
								$photo = "/".$ct->getPhotoWebPath(25);
						}
						?>
						<img class="contact" src="<? echo $photo; ?>" />
						<span class="contact">
						<?
						echo $rep->fields['name'];
						?>
						</span>
					</td>
					<?
					foreach($propositions as $prop){
						if (isset($rep->reponses[$prop->fields['id']])){
							if($rep->reponses[$prop->fields['id']]->fields['presence']){
								$resultats[$prop->fields['id']] ++;
								?>
								<td class="ok">
									<img src="./common/img/checkdo2.png" />
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
					?>
				</tr>
				<?
			}
			?>
			<tr>
				<td></td>
				<?
				$idSupp = $resSupp = 0;
				foreach($resultats as $key => $res){
					if ($idSupp == 0 || $res > $resSupp){
						$idSupp = $key;
						$resSupp = $res;
					}
					?>
					<td class="results">
						<? echo $res; ?>
					</td>
					<?
				}
				?>
			</tr>
			<? if($this->fields['status'] != dims_appointment_offer::STATUS_VALIDATED && $this->fields['id_user'] == $_SESSION['dims']['userid']){ ?>
			<tr>
				<td class="nb_particip">
				</td>
				<?
				foreach($propositions as $prop){
					?>
					<td class="propositions">
						<input <? echo ($idSupp==$prop->fields['id'])?'checked=true':''; ?> type="radio" name="date" value="<? echo $prop->fields['id']; ?>" />
					</td>
					<?
				}
				?>
			</tr>
			<tr>
				<td class="save_button" colspan="<? echo count($propositions)+1; ?>">
					<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
					<?= $_SESSION['cste']['_DIMS_OR']; ?>
					<a onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');" href="javascript:void(0);">
						<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
					</a>
				</td>
			</tr>
			<? }else{ ?>
			<tr>
				<td class="save_button" colspan="<? echo count($propositions)+1; ?>">
					<input onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');" type="button" value="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" />
				</td>
			</tr>
			<? } ?>
		</table>
	</form>
</div>
