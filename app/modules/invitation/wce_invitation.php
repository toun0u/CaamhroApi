<?php
$contact = $this->getLightAttribute('contact');
?>
<section style="overflow: hidden;" class="white-area pa2">
	<div class="mw1280p m-auto pa1">
		<div class="invitation">
			<h2><?= $this->get('libelle'); ?></h2>
			<div class="welcome">
				Bienvenue <b><?= $contact->get('title')." ".$contact->get('firstname')." ".$contact->get('lastname'); ?></b>, nous avons le plaisir de vous convier à cet événement
			</div>
			<?php
			if($this->get('datefin') != '' && $this->get('datefin') != '0000-00-00'){
				setlocale(LC_TIME, 'fr_FR.utf8');
				$dd = explode('-',$this->get('datefin'));
			}
			if(!isset($dd) || (mktime(0,0,0,$dd[1],$dd[2],$dd[0]) >= mktime(0,0,0,date('n'),date('j'),date('Y')))){
				if($this->getLightAttribute('save')){
					?>
					<div class="confirm-save flash-bag success">
						Votre demande a bien &eacute;t&eacute; enregistr&eacute;e.
					</div>
					<?php
				}
				?>
				<form method="POST" action="<?= $this->getFrontUrl("&id=".$contact->get('ref')); ?>" name="invitation_submit">
					<input type="hidden" name="action" value="save" />
					<div class="mod grid2 fond_grid">
						<div class="choose-date">
							<h4>Choisissez une date :</h4>
							<div class="comment">
								<?= nl2br($this->get('description')); ?>
							</div>
							<ul class="dates">
								<?php
								$ds = $this->getLightAttribute('propositions');
								$selected = $this->getGoReponseVal($contact->get('id'));
								foreach($ds as $d){
									?>
										<li>
											<?php if($selected == $d->get('id_globalobject')){ ?>
												<input type="radio" name="date" value="<?= $d->get('id_globalobject'); ?>" checked=true />
											<?php }else{ ?>
												<input type="radio" name="date" value="<?= $d->get('id_globalobject'); ?>" />
											<?php } ?>
											<?php
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
										</li>
									<?php
								}
								?>
							</ul>
						</div>
						<?php
						$nbAllowed = $this->get('max_allowed');
						if($nbAllowed > 0){
							$arrayAge = array(
								"-14"=>"-14",
								"15-17"=>"15-17",
								"18-24"=>"18-24",
								"25-34"=>"25-34",
								"35-49"=>"35-49",
								"50-64"=>"50-64",
								"65+"=>"65+",
							);
							?>
							<div class="accompanying">
								<h4>Accompagnants :</h4>
								<div class="comment">
									Vous pouvez venir accompagné au maximum de <?= $nbAllowed; ?> personnes. Merci d'indiquer leurs noms et âges respectifs.
								</div>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<th>Nom complet de l'accompagnant</th>
										<th>Âge</th>
									</tr>
									<?php
									$acc = $this->getAccompanyValues($contact->get('id'));
									$a = current($acc);
									for($i=1;$i<=$nbAllowed;$i++){
										?>
										<tr>
											<td>
												<input type="text" name="name[]" placeholder="Accompagnant <?= $i; ?> ..." value="<?= ($a!==false)?$a->get('name'):""; ?>" />
											</td>
											<td>
												<select name="age[]">
													<option value="dims_nan">--</option>
													<?php
													foreach($arrayAge as $age){
														if($a!==false && $a->get('age') == $age){
															?>
															<option value="<?= $age; ?>" selected=true><?= $age; ?></option>
															<?php
														}else{
															?>
															<option value="<?= $age; ?>"><?= $age; ?></option>
															<?php
														}
													}
													?>
												</select>
											</td>
										</tr>
										<?php
										$a = next($acc);
									}
									?>
								</table>
							</div>
					</div>
					<div class="form_row_validate envoyer">
						<?php
							}
							if(isset($dd)){
								?>
								<div class="limit-response">
									Votre réponse est attendue avant le <?= strftime('%d %B %Y',mktime(0,0,0,$dd[1],$dd[2],$dd[0])); ?>
								</div>
								<?php
							}
						?>
						<input type="submit" value="Envoyer" />
					</div>
				</div>
			</form>
		<?php }else{ ?>
			<div class="date-passed">
				La date limite de réponse est passée.
			</div>
		<?php } ?>
	</div>
</section>
