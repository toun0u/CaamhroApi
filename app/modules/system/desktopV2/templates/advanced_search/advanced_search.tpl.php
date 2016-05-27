<?php
//------------ Alimentation des listes d'éléments une seule fois en session
// -- ANNEES
$desktop->getAvailableYears($_SESSION['dims']['advanced_search']['available_years']);
$desktop->getCountries($_SESSION['dims']['advanced_search']['available_countries']);
?>
<div class="filters">
	<div class="header">
		<h2 style="float:left"><?php echo $_SESSION['cste']['ADVANCED_SEARCH']; ?></h2>
		<div style="clear:both"></div>
	</div>
	<div class="fields">
			<form name="form_as_keywords" action="admin.php?dims_op=desktopv2&action=as_managefilter&faction=kw_filter" method="POST">
			<?
				$listeToken = array(); // Liste des entrées du formulaire
			?>
			<div class="fields_gauche">
				<table>
					<tr>
						<td class="label"  style="width:150px;">
							<label for="date_from"><?php echo $_SESSION['cste']['DATE_PLURIEL_OU_PAS'].' : '; ?></label>
						</td>
						<td>
							<p class="date_band">
							<?php
							if(!empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])){
								echo $_SESSION['cste']['FROM_DU'].' ';
							}
							?>
								<input type="text" class="date datepicker" name="date_from" id="date_from" <?php if(!empty($_SESSION['dims']['advanced_search']['filters']['from']['date_from'])) echo 'value="'.$_SESSION['dims']['advanced_search']['filters']['from']['date_from'].'"'; ?>/>

								<?php
								if(!empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])){
									echo $_SESSION['cste']['DATE_TO_THE'];
								}
								//Gestion éventuelle du to s'il est set en session
								if(!empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])){
									?>
									<input type="text" class="date datepicker" name="date_to" id="date_to" <?php if(!empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])) echo 'value="'.$_SESSION['dims']['advanced_search']['filters']['to']['date_to'].'"'; ?>/>
									<a class="range" href="javascript:void(0);" onclick="javascript:closeDateRange();"><?echo $_SESSION['cste']['_DIMS_CLOSE']; ?></a>
									<?php
								}
								else{
									?>
									<a class="range" href="javascript:void(0);" onclick="javascript:selectDateRange();"><?php echo $_SESSION['cste']['SELECT_A_DATE_RANGE']; ?></a>
									<?php
								}
								?>
								<a class="searching" href="javascript:void(0);" onclick="javascript:document.form_as_keywords.submit();" title="<?php echo $_SESSION['cste']['_DIMS_FILTER'];?>"></a>
							</p>
						</td>
						<td class="label" style="width:190px;">
							<label for="area_tag"><?php echo $_SESSION['cste']['_DIMS_LABEL_GEOGRAPHIC_AREA'].' : '; ?></label>
						</td>
						<td style="width: 100px;position: absolute;display:table">
							<select style="float: left; width: 100%;" name="tag" class="select_tag" id="area_tag">
								<option value="dims_nan">-<?php echo $_SESSION['cste']['_NOT_DEFINED_FEM']; ?>-</option>
									<?php
									$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'type_tag'=>tag_category::_TYPE_GEO),' ORDER BY label ');
									foreach($categs as $cat){
										$lstTag = $cat->getTagLink();
										if(count($lstTag)){
											?>
											<optgroup label="<?= $cat->get('label'); ?>">
												<?php
												foreach($lstTag as $t){
													?>
													<option value="<?= $t->get('id'); ?>" <?= (isset($_SESSION['desktop']['search']['tags']) && in_array($t->get('id'), $_SESSION['desktop']['search']['tags']))?"selected=true":""; ?>><?=$t->get('tag'); ?></option>
													<?php
												}
												?>
											</optgroup>
											<?php
										}
									}
									$tags = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id_category'=>0,'type'=>tag::TYPE_GEO));
									if(count($tags)){
										?>
										<optgroup label="<?= $_SESSION['cste']['_UNCATEGORIZED']; ?>">
											<?php
											foreach($tags as $t){
												?>
												<option value="<?= $t->get('id'); ?>" <?= (isset($_SESSION['desktop']['search']['tags']) && in_array($t->get('id'), $_SESSION['desktop']['search']['tags']))?"selected=true":""; ?>><?=$t->get('tag'); ?></option>
												<?php
											}
											?>
										</optgroup>
										<?php
									}
									?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label" style="width: 50px;height: 30px;">
							<label for="year"><?php echo $_SESSION['cste']['_DIMS_LABEL_YEAR']; ?> :</label>
						</td>
						<td style="width: 100px;position: absolute;display:table;">
							<select style="float: left; width: 100%;" name="year" class="select_year" id="year">
								<option value="-1">-<?php echo $_SESSION['cste']['_DIMS_ALL']; ?>-</option>
									<?php
									foreach($_SESSION['dims']['advanced_search']['available_years'] as $year){
										if(!isset($_SESSION['dims']['advanced_search']['filters']['years']) || !in_array($year,$_SESSION['dims']['advanced_search']['filters']['years'])){
											?>
											<option value="<?php echo $year; ?>" ><?php echo $year; ?></option>
											<?php
										}
									}
									?>
							</select>
						</td>
						<td class="label" style="width: 50px;height: 30px;">
							<label for="year"><?php echo $_SESSION['cste']['_TEMPORAL_TAGS']; ?> :</label>
						</td>
						<td style="width: 100px;position: absolute;display:table;">
							<select style="float: left; width: 100%;" name="tag_tmp" class="select_tag_tmp" id="tag_tmp">
								<option value="dims_nan">-<?php echo $_SESSION['cste']['_NOT_DEFINED_FEM']; ?>-</option>
									<?php
									$categs = tag_category::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'type_tag'=>tag_category::_TYPE_DURATION),' ORDER BY label ');
									foreach($categs as $cat){
										$lstTag = $cat->getTagLink();
										if(count($lstTag)){
											?>
											<optgroup label="<?= $cat->get('label'); ?>">
												<?php
												foreach($lstTag as $t){
													?>
													<option value="<?= $t->get('id'); ?>" <?= (isset($_SESSION['desktop']['search']['tags']) && in_array($t->get('id'), $_SESSION['desktop']['search']['tags']))?"selected=true":""; ?>><?=$t->get('tag'); ?></option>
													<?php
												}
												?>
											</optgroup>
											<?php
										}
									}
									$tags = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id_category'=>0,'type'=>tag::TYPE_DURATION));
									if(count($tags)){
										?>
										<optgroup label="<?= $_SESSION['cste']['_UNCATEGORIZED']; ?>">
											<?php
											foreach($tags as $t){
												?>
												<option value="<?= $t->get('id'); ?>" <?= (isset($_SESSION['desktop']['search']['tags']) && in_array($t->get('id'), $_SESSION['desktop']['search']['tags']))?"selected=true":""; ?>><?=$t->get('tag'); ?></option>
												<?php
											}
											?>
										</optgroup>
										<?php
									}
									?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label" style="height: 30px;">
							<label for="region"><?php echo $_SESSION['cste']['_REGION']; ?> :</label>
						</td>
						<td style="width: 250px;position: absolute;display:table;">
							<select name="region" class="select-region" multiple="multiple">
								<?php
								require_once DIMS_APP_PATH.'/modules/system/class_region.php';
								$regs = region::all(' ORDER BY name ');
								foreach($regs as $r){
									if(isset($_SESSION['dims']['advanced_search']['filters']['region'][$r->get('id_globalobject')])){ ?>
										<option selected=true value="<?= $r->get('id_globalobject'); ?>"><?= $r->get('name'); ?></option>
									<?php }else{ ?>
										<option value="<?= $r->get('id_globalobject'); ?>"><?= $r->get('name'); ?></option>
									<?php
									}
								}
								?>
							</select>
						</td>
						<td class="label" style="height: 30px;">
							<label for="departement"><?php echo $_SESSION['cste']['_DEPARTMENT']; ?> :</label>
						</td>
						<td style="width: 250px;position: absolute;display:table;">
							<select name="departement" class="select-departement" multiple="multiple">
								<?php
								require_once DIMS_APP_PATH.'/modules/system/class_departement.php';
								foreach($regs as $r){
									$deps = departement::find_by(array('code_reg'=>$r->get('code')), ' ORDER BY name ');
									if(count($deps)){
										?>
										<optgroup label="<?= $r->get('name'); ?>">
										<?php
										foreach($deps as $d){
											if(isset($_SESSION['dims']['advanced_search']['filters']['departement'][$d->get('id_globalobject')])){ ?>
												<option selected=true value="<?= $d->get('id_globalobject'); ?>"><?= $d->get('name'); ?></option>
											<?php }else{ ?>
												<option value="<?= $d->get('id_globalobject'); ?>"><?= $d->get('name'); ?></option>
											<?php
											}
										}
										?>
										</optgroup>
										<?php
									}
								}
								?>
							</select>
						</td>
					</tr>

					<?php
					// Recherche sur champs dynamiques
					require_once DIMS_APP_PATH . "/modules/system/class_metafield.php";
					require_once DIMS_APP_PATH . "/modules/system/class_business_metacateg.php";
					$sql = "SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield, mb.id as mbid
							FROM		".metafield::TABLE_NAME." as mf
							INNER JOIN	".mb_field::TABLE_NAME." as mb
							ON			mb.id = mf.id_mbfield
							LEFT JOIN	".business_metacateg::TABLE_NAME." as mc
							ON			mf.id_metacateg = mc.id
							WHERE		mf.id_object IN (:idobject, :idobject2)
							AND			mf.used = 1
							AND 		mb.indexed = 1
							ORDER BY	mc.position, mf.position";
					$params = array(
						":idobject" => dims_const::_SYSTEM_OBJECT_TIERS,
						":idobject2" => dims_const::_SYSTEM_OBJECT_CONTACT,
					);
					$db = dims::getInstance()->getDb();
					$res = $db->query($sql,$params);
					$lstFields = array();
					while($r = $db->fetchrow($res)){
						$lstFields[$r['id_object']][$r['id_cat']]['label'] = empty($r['categlabel'])?"Non défini":$r['categlabel'];
						$lstFields[$r['id_object']][$r['id_cat']]['fields'][] = array(
							'label' => isset($_SESSION['cste'][$r['titlefield']])?$_SESSION['cste'][$r['titlefield']]:$r['name'],
							'id' => $r['namefield'],
						);
					}
					//$_SESSION['dims']['advanced_search']['filters']['arrondissement'][$a->get('id_globalobject')]
					// TODO: input display:inline-block
					?>
					<tr>
						<?php if(empty($lstFields[dims_const::_SYSTEM_OBJECT_CONTACT])): ?>
							<td colspan="2"></td>
						<?php else: ?>
							<td class="label" style="height: 30px;">
								<label for="dyn_ct"><?= "Champs contact"; ?> :</label>
							</td>
							<td style="width: 250px;position: absolute;display:table;">
								<select id="dyn_ct" name="dyn_ct" class="select-ct-dyn">
									<optgroup>
										<option value="dims_nan"></option>
									</optgroup>
									<?php foreach($lstFields[dims_const::_SYSTEM_OBJECT_CONTACT] as $f): ?>
										<optgroup label="<?= $f['label']; ?>">
											<?php foreach($f['fields'] as $ff): ?>
												<option <?= (!empty($_SESSION['dims']['advanced_search']['filters']['dyn_ct']['field']) && $_SESSION['dims']['advanced_search']['filters']['dyn_ct']['field'] == $ff['id'])?'selected="selected"':""; ?> value="<?= $ff['id']; ?>"><?= $ff['label']; ?></option>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
								</select>
								<input type="text" value="<?= (!empty($_SESSION['dims']['advanced_search']['filters']['dyn_ct']['val']))?$_SESSION['dims']['advanced_search']['filters']['dyn_ct']['val']:''; ?>" name="dyn_ct_val" id="dyn_ct_val" style="display:<?= empty($_SESSION['dims']['advanced_search']['filters']['dyn_ct']['val'])?'none':'display-inline'; ?>;width:45%;" />
							</td>
						<?php endif; ?>
						<?php if(empty($lstFields[dims_const::_SYSTEM_OBJECT_TIERS])): ?>
							<td colspan="2"></td>
						<?php else: ?>
							<td class="label" style="height: 30px;">
								<label for="dyn_tiers"><?= "Champs entreprise"; ?> :</label>
							</td>
							<td style="width: 250px;position: absolute;display:table;">
								<select id="dyn_tiers" name="dyn_tiers" class="select-tiers-dyn">
									<optgroup>
										<option value="dims_nan"></option>
									</optgroup>
									<?php foreach($lstFields[dims_const::_SYSTEM_OBJECT_TIERS] as $f): ?>
										<optgroup label="<?= $f['label']; ?>">
											<?php foreach($f['fields'] as $ff): ?>
												<option <?= (!empty($_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['field']) && $_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['field'] == $ff['id'])?'selected="selected"':""; ?> value="<?= $ff['id']; ?>"><?= $ff['label']; ?></option>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
								</select>
								<input type="text" value="<?= (!empty($_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['val']))?$_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['val']:''; ?>" name="dyn_tiers_val" id="dyn_tiers_val" style="display:<?= empty($_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['val'])?'none':'display-inline'; ?>;width:45%;" />
							</td>
						<?php endif; ?>
					</tr>
					<!--<tr>
						<td></td>
						<td colspan="3">
						<?php
						if(isset($_SESSION['dims']['advanced_search']['filters']['only_me']) && $_SESSION['dims']['advanced_search']['filters']['only_me'])
							$checked='checked="checked"';
						else $checked = '';
						?>
							<input type="checkbox" onchange="javascript:changeOnlyUserActivity();" name="only_me" id="only_me" <?php echo $checked; ?>/><label for="only_me"><?php echo $_SESSION['cste']['ONLY_MY_ACTIVITY']; ?></label>
						</td>
					</tr>-->
				</table>
			</div>
			<!--<div class="fields_droite">
				<?php
					if(isset($_SESSION['dims']['advanced_search']['filters']['count']) && $_SESSION['dims']['advanced_search']['filters']['count'] > 0){
						?>
						<td class="label">
							<label for="as_keywords" style="float: left; width: 28%;"><?php echo $_SESSION['cste']['FILTER_WITH']; ?> :</label>
						</td>
						<td>
							<input class="as_keywords" autocomplete="off" type="text" name="as_keywords" id="as_keywords" value="<?php echo (!empty($_SESSION['dims']['advanced_search']['filters']['keywords']))?$_SESSION['dims']['advanced_search']['filters']['keywords']:'';?>"/>
							<div id="bloc_display_suggestions" style="display:none;">
								<div class="results"></div>
								<div class="footer">
									<a href="javascript:document.form_as_keywords.submit();"><?php echo $_SESSION['cste']['USE_AS_FILTER'];?></a>
								</div>
							</div>
						</td>
						<?php
							if(!empty($_SESSION['dims']['advanced_search']['filters']['keywords'])){
								?>
								<td class="button20">
									<a style="float:left; padding-top: 4px;" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=init_kwfilter"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/delete_filter_kw.png"/></a>
								</td>
								<?php
							}
							?>
						<td class="button20">
							<input style="float:left; padding-top: 8px;" type="image" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/as_loupe.png"/>
						</td>
					<?php
					}
					?>
			</div>-->
			</form>
	</div>
	<?php
	if((isset($_SESSION['dims']['advanced_search']['filters']['count']) && $_SESSION['dims']['advanced_search']['filters']['count'] > 0) || !empty($_SESSION['dims']['advanced_search']['filters']['from']) || !empty($_SESSION['dims']['advanced_search']['filters']['to'])){
		?>
		<div class="close_section">
			<a href="admin.php?submenu=<? echo _DESKTOP_V2_DESKTOP; ?>&force_desktop=1&mode=default">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close22.png">
				<span>
					<?php echo $_SESSION['cste']['RESET_ADVANCED_SEARCH']; ?>
				</span>
			</a>
		</div>
		<div style="clear:both;"></div>
		<?php
	}
	?>
</div>
<?php

if(isset($_SESSION['dims']['advanced_search']['filters']['count']) && $_SESSION['dims']['advanced_search']['filters']['count'] > 0){
	?>
	<div class="cadre_build_search">
		<div class="title_exploration"><span class="colorized"><?php echo $_SESSION['cste']['BUILD']; ?></span> <?php echo $_SESSION['cste']['YOUR']; ?> <span class="colorized"><?php echo $_SESSION['cste']['_SEARCH']; ?></span></div>
		<div class="building">
			<div class="bloc expression">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/build_your_search.png">
			</div>
			<div class="operator egal">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/egal.png">
			</div>

			<?php
			$precedent = false;
			//gestion des vecteurs de recherche, un groupe par axe
			//
			//-- Years
			if(isset($_SESSION['dims']['advanced_search']['filters']['years'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['years']);
				if($nb){
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['years'] as $y){
						?>

						<div class="bloc item">
							<div class="as_picto">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/build_year.png">
							</div>
							<div>
								<span class="label">
									<a class="remove_item" href="<?php echo $dims->getScriptEnv();?>?dims_op=desktopv2&action=as_managefilter&faction=del&type=year&val=<?php echo $y;?>" title="<?php echo $_SESSION['cste']['DELETE_THIS_FILTER']; ?>">
										<span><?php echo $y; ?></span>
									</a>
								</span>
							</div>
						</div>
						<?php
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}

					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}
			//-- Countries
			if(isset($_SESSION['dims']['advanced_search']['filters']['countries'])){
				require_once DIMS_APP_PATH . '/modules/system/class_country.php' ;
				$nb = count($_SESSION['dims']['advanced_search']['filters']['countries']);
				if($nb){
					if($precedent){
						?>
						<div class="operator">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
						</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['countries'] as $c){
						$country = new country();
						$country->open($c);
						?>
						<div class="bloc item">
							<div class="as_picto">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/build_country.png">
							</div>
							<div>
								<span class="label">
									<a class="remove_item" href="<?php echo $dims->getScriptEnv();?>?dims_op=desktopv2&action=as_managefilter&faction=del&type=country&val=<?php echo $c;?>" title="<?php echo $_SESSION['cste']['DELETE_THIS_FILTER']; ?>">
										<span><?php echo dims_strcut($country->fields['printable_name'], 15); ?></span>
									</a>
								</span>
							</div>
						</div>
						<?php
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}
			//-- Companies
			if(isset($_SESSION['dims']['advanced_search']['filters']['companies'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['companies']);
				if($nb){
					require_once DIMS_APP_PATH . '/modules/system/class_tiers.php' ;
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['companies'] as $c){
						$tiers = new tiers();
						$tiers->openWithGB($c);
						$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/company.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}
			//-- Contacts
			if(isset($_SESSION['dims']['advanced_search']['filters']['contacts'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['contacts']);
				if($nb){
					require_once DIMS_APP_PATH . '/modules/system/class_contact.php' ;
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['contacts'] as $c){
						$contact = new contact();
						$contact->openWithGB($c);
						$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/contact.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Activities
			if(isset($_SESSION['dims']['advanced_search']['filters']['activities'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['activities']);
				if($nb){
					require_once DIMS_APP_PATH . '/modules/system/class_action.php' ;
					require_once DIMS_APP_PATH . '/modules/system/class_search.php';
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['activities'] as $o){
						$action = new action();
						$action->openWithGB($o);
						$action->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/action.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Opportunités
			if(isset($_SESSION['dims']['advanced_search']['filters']['opportunities'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['opportunities']);
				if($nb){
					require_once DIMS_APP_PATH . '/modules/system/class_action.php' ;
					require_once DIMS_APP_PATH . '/modules/system/class_search.php';
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['opportunities'] as $o){
						$action = new action();
						$action->openWithGB($o);
						$action->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/action.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Events
			if(isset($_SESSION['dims']['advanced_search']['filters']['events'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['events']);
				if($nb){
					require_once DIMS_APP_PATH . '/modules/system/class_action.php' ;
					require_once DIMS_APP_PATH . '/modules/system/class_search.php';
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['events'] as $o){
						$action = new action();
						$action->openWithGB($o);
						$action->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/action.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Documents
			if(isset($_SESSION['dims']['advanced_search']['filters']['documents'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['documents']);
				if($nb){
					require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php' ;
					require_once DIMS_APP_PATH . '/modules/system/class_search.php';
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['documents'] as $d){
						$doc = new docfile();
						$doc->openWithGB($d);
						$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/document.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Documents
			if(isset($_SESSION['dims']['advanced_search']['filters']['suivis'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['suivis']);
				if($nb){
					require_once DIMS_APP_PATH.'modules/system/suivi/class_suivi.php';
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['suivis'] as $d){
						$doc = new suivi();
						$doc->openWithGB($d);
						$doc->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/suivi.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Régions
			if(isset($_SESSION['dims']['advanced_search']['filters']['region']) && !empty($_SESSION['dims']['advanced_search']['filters']['arrondissement'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['region']);
				if($nb){
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['region'] as $d){
						$region = new region();
						$region->openWithGB($d);
						$region->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/region.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Départements
			if(isset($_SESSION['dims']['advanced_search']['filters']['departement']) && !empty($_SESSION['dims']['advanced_search']['filters']['arrondissement'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['departement']);
				if($nb){
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['departement'] as $d){
						$departement = new departement();
						$departement->openWithGB($d);
						$departement->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/departement.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Arrondissements
			if(isset($_SESSION['dims']['advanced_search']['filters']['arrondissement']) && !empty($_SESSION['dims']['advanced_search']['filters']['arrondissement'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['arrondissement']);
				if($nb){
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;
					foreach($_SESSION['dims']['advanced_search']['filters']['arrondissement'] as $d){
						$arrondissement = new arrondissement();
						$arrondissement->openWithGB($d);
						$arrondissement->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/arrondissement.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}

			//-- Cantons
			if(isset($_SESSION['dims']['advanced_search']['filters']['canton']) && !empty($_SESSION['dims']['advanced_search']['filters']['canton'])){
				$nb = count($_SESSION['dims']['advanced_search']['filters']['canton']);
				if($nb){
					if($precedent){
						?>
						<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/multiplication.png">
							</div>
						<?php
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/left_bracket.png">
						</div>
						<?php
					}
					$precedent = true;
					$i = 0;

					foreach($_SESSION['dims']['advanced_search']['filters']['canton'] as $d){
						$canton = new canton();
						$canton->openWithGB($d);
						$canton->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/vignettes/canton.tpl.php');
						if($i < $nb - 1){
							?>
							<div class="operator">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus.png">
							</div>
							<?php
						}
						$i++;
					}
					if($nb>1){
						?>
						<div class="operator bracket">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/right_bracket.png">
						</div>
						<?php
					}
				}
			}
			?>
			<div style="clear:both;height:0px;"></div>
		</div>
	</div>
<?php
}

global $dims_agenda_months;
global $dims_agenda_days;

//initialisation des tableaux utilisés par le datepicker
$full_months = '[';
$full_days = '[';

$min_month = '[';
$min_days = '[';
$mega_min_days = '[';

$i=0;
foreach($dims_agenda_months as $m){
	$full_months .= "'".$m."'";
	$min_month .= "'".utf8_encode(substr(html_entity_decode(utf8_decode($m)),0,3))."'";
	if($i< 11){
		$full_months .= ',';
		$min_month .= ',';
	}
	$i++;
}

$i=0;
foreach($dims_agenda_days as $d){
	$full_days .= "'".$d."'";
	$min_days .= "'".utf8_encode(substr(utf8_decode($d),0,3))."'";
	$mega_min_days .= "'".utf8_encode(substr(utf8_decode($d),0,2))."'";
	if($i< 6){
		$full_days .= ',';
		$mega_min_days .= ',';
		$min_days .= ',';
	}
	$i++;
}

$min_month .= ']';
$min_days .= ']';
$mega_min_days .= ']';
$full_months .= ']';
$full_days .= ']';
?>

<script type="text/javascript">
	$("select#tag_tmp").chosen({width:"100%", no_results_text: "No data matching"}).change(function(){
		if($(this).val() != 'dims_nan'){
			document.location.href = 'admin.php?dims_op=desktopv2&action=selectTag&from_desktop=1&tag='+$(this).val();
		}
	});
	$("select#area_tag").chosen({width:"100%", no_results_text: "No area matching"}).change(function(){
		if($(this).val() != 'dims_nan'){
			document.location.href = 'admin.php?dims_op=desktopv2&action=selectTag&from_desktop=1&tag='+$(this).val();
		}
	});

	$("select#year").chosen({width:"100%", no_results_text: "No year matching"}).change(function(){
		if($(this).val() != -1){
			document.location.href = 'admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=year&val='+$(this).val();
		}
	});
	$("select.select-region").chosen({width:"100%", no_results_text: "No region matching"}).change(function(){
		if($(this).val() != 'dims_nan'){
			document.location.href = 'admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=region&val='+$(this).val();
		}
	});
	$("select.select-departement").chosen({width:"100%", no_results_text: "No departement matching"}).change(function(){
		if($(this).val() != 'dims_nan'){
			document.location.href = 'admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=departement&val='+$(this).val();
		}
	});
	$("select.select-arrondissement").chosen({width:"100%", no_results_text: "No arrondissement matching"}).change(function(){
		if($(this).val() != 'dims_nan'){
			document.location.href = 'admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=arrondissement&val='+$(this).val();
		}
	});
	$("select.select-canton").chosen({width:"100%", no_results_text: "No canton matching"}).change(function(){
		if($(this).val() != 'dims_nan'){
			document.location.href = 'admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=canton&val='+$(this).val();
		}
	});
	$("select.select-ct-dyn").chosen({width:"50%", no_results_text: "Champ non connu"}).change(function(){
		if($(this).val() != 'dims_nan'){
			$('#dyn_ct_val').css('display','inline-block');
		}else{
			$('#dyn_ct_val').css('display','none');
		}
	});
	$('#dyn_ct_val').keydown(function(e){
		if(e.keyCode == 13 && jQuery.trim($(this).val()) != '' && $("select.select-ct-dyn").val() != 'dims_nan'){
			document.location = '<?= dims::getInstance()->getScriptEnv(); ?>?dims_op=desktopv2&action=as_managefilter&faction=add&type=dyn_ct&val='+$(this).val()+"&field="+$("select.select-ct-dyn").val();
		}
	});
	$("select.select-tiers-dyn").chosen({width:"50%", no_results_text: "Champ non connu"}).change(function(){
		if($(this).val() != 'dims_nan'){
			$('#dyn_tiers_val').css('display','inline-block');
		}else{
			$('#dyn_tiers_val').css('display','none');
		}
	});
	$('#dyn_tiers_val').keydown(function(e){
		if(e.keyCode == 13 && jQuery.trim($(this).val()) != '' && $("select.select-tiers-dyn").val() != 'dims_nan'){
			document.location = '<?= dims::getInstance()->getScriptEnv(); ?>?dims_op=desktopv2&action=as_managefilter&faction=add&type=dyn_tiers&val='+$(this).val()+"&field="+$("select.select-tiers-dyn").val();
		}
	});
	$('document').ready(function(){
		$("#date_from").datepicker({
			buttonImage: '<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			monthNames: <?php echo $full_months;?>,
			monthNamesShort: <?php echo $min_month;?>,
			dayNames: <?php echo $full_days;?>,
			dayNamesShort: <?php echo $min_days;?>,
			dayNamesMin: <?php echo $mega_min_days;?>
		});

		if($('document').find($('#date_to'))){
			$("#date_to").datepicker({
				buttonImage: '<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/calendar.png',
				buttonImageOnly: true,
				showOn: 'button',
				constrainInput: true,
				defaultDate: 0,
				changeYear: true,
				dateFormat: 'dd/mm/yy',
				monthNames: <?php echo $full_months;?>,
				monthNamesShort: <?php echo $min_month;?>,
				dayNames: <?php echo $full_days;?>,
				dayNamesShort: <?php echo $min_days;?>,
				dayNamesMin: <?php echo $mega_min_days;?>
			});
		}

		//gestion du keyup sur les filters
		kw_keyup_timer = '';
		kw_value = $('as_keywords').val();
		$('#as_keywords').keyup(function(){
			if($(this).val() != kw_value){
				kw_value = $(this).val();
				clearTimeout(kw_keyup_timer);
				kw_keyup_timer = setTimeout(execFilterKW, 500);
			}
		});
		//initialisation de la position du bloc de suggestion
		initPositionToSuggestionKW();

		//gestion du clic arrière
		$('body').click(function() {
			$('#bloc_display_suggestions').fadeOut();
		});

		$('#bloc_display_suggestions').click(function(e) {
			e.stopPropagation();
		});
	});

	$(window).resize(function(){
		initPositionToSuggestionKW();
	});
	adapteImage('img.avatar_action',true,60);

	function initPositionToSuggestionKW(){
		var offset = $('#as_keywords').offset();
		if(offset != undefined){
			$('#bloc_display_suggestions').css('top', (offset.top + $('#as_keywords').height()+8)+'px');
			$('#bloc_display_suggestions').css('left', offset.left +'px');
		}
	}

	function execFilterKW(){
		if(kw_value != ''){
			$('#bloc_display_suggestions div.results').html('<div style="width: 100%; text-align: center;"><img src="<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/ajax-loader.gif"></div>');
			$('#bloc_display_suggestions').fadeIn();

			$.ajax({
				type: "POST",
				url: 'admin.php',
				async: true,
				data: {
					'dims_op' : 'desktopv2',
					'action': 'as_kw_keyup',
					'as_keywords': $('#as_keywords').val()
				},
				dataType: "text",
				success: function(data) {
					$('#bloc_display_suggestions div.results').html(data);
					adapteImage('img.to_resize40',true,40);
				}
			});
		}
		else{
			$('#bloc_display_suggestions').fadeOut();
		}
	}
	function selectDateRange(){
		var previous_val = $("#date_from").val();
		$('p.date_band').html('<?php echo $_SESSION['cste']['FROM_DU']; ?> <input type="text" class="date datepicker" name="date_from" id="date_from"/> <?php echo $_SESSION['cste']['DATE_TO_THE']; ?> <input type="text" class="date datepicker" name="date_to" id="date_to"/><a class="range" href="javascript:void(0);" onclick="javascript:closeDateRange();"><?echo $_SESSION['cste']['_DIMS_CLOSE']; ?></a><a class="searching" href="javascript:void(0);" onclick="javascript:document.form_as_keywords.submit();"	title="<?php echo $_SESSION['cste']['_DIMS_FILTER'];?>"></a>');

		$("#date_from").datepicker({
			buttonImage: '<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			monthNames: <?php echo $full_months;?>,
			monthNamesShort: <?php echo $min_month;?>,
			dayNames: <?php echo $full_days;?>,
			dayNamesShort: <?php echo $min_days;?>,
			dayNamesMin: <?php echo $mega_min_days;?>
		});

		$("#date_to").datepicker({
			buttonImage: '<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			monthNames: <?php echo $full_months;?>,
			monthNamesShort: <?php echo $min_month;?>,
			dayNames: <?php echo $full_days;?>,
			dayNamesShort: <?php echo $min_days;?>,
			dayNamesMin: <?php echo $mega_min_days;?>
		});

		$("#date_from").val(previous_val);
	}

	function closeDateRange(){
		var previous_val = $("#date_from").val();
		$('p.date_band').html('<input type="text" class="date datepicker" name="date_from" id="date_from"/><a class="range" href="javascript:void(0);" onclick="javascript:selectDateRange();"><?php echo $_SESSION['cste']['SELECT_A_DATE_RANGE']; ?></a><a class="searching" href="javascript:void(0);" onclick="javascript:document.form_as_keywords.submit();"	title="<?php echo $_SESSION['cste']['_DIMS_FILTER'];?>"></a>');
		$("#date_from").datepicker({
			buttonImage: '<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/calendar.png',
			buttonImageOnly: true,
			showOn: 'button',
			constrainInput: true,
			defaultDate: 0,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			monthNames: <?php echo $full_months;?>,
			monthNamesShort: <?php echo $min_month;?>,
			dayNames: <?php echo $full_days;?>,
			dayNamesShort: <?php echo $min_days;?>,
			dayNamesMin: <?php echo $mega_min_days;?>
		});
		$("#date_from").val(previous_val);
	}

	function changeOnlyUserActivity(){
		javascript:document.form_as_keywords.submit();
	}
</script>
