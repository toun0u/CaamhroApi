<?php
$us = new user();
$us->open($this->fields['id_user']);

if($this->fields['typeaction'] == '_DIMS_EVENT_ACTIVITIES') {
	$url = '/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=view&activity_id='.$this->fields['id'];
}
else {
	$url = '/admin.php?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$this->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_ACTIVITY.'&init_filters=1&from=desktop';
}
?>
<div class="zone_recent_activities">
    <div class="bloc_ligne ro_avatar">
		<a href="<?php echo $url; ?>">
		<?
		if ($this->fields['banner_path'] != '' && file_exists($this->fields['banner_path']))
			echo '<img class="recent_companies_img" style="width:40px;" src="'.$this->fields['banner_path'].'" />';
		else{
			$type_action = $this->getSearchableType();
			if($type_action==search::RESULT_TYPE_ACTIVITY)
				echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/activity40.png" />';
			else echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/event40.png" />';
		}
		?>
		</a>
    </div>

	<div class="bloc_ligne  ro_details">
		<div class="title">
			<p>
				<a href="<?php echo $url; ?>">
					<?
					require_once  DIMS_APP_PATH.'modules/system/activity/class_type.php';
					$ot = new activity_type();
					$ot->open($this->fields['activity_type_id']);
					if(!$ot->isNew() && !empty($ot->fields['label'])){
						echo $ot->fields['label']. ' : ';
					}
					if(!empty($this->fields['libelle'])){
						echo $this->fields['libelle'];
					}
					else echo ucfirst($_SESSION['cste']['ACTIVITY']);
					?>
				</a>
				<?
				require_once DIMS_APP_PATH."modules/system/class_country.php";
				$country = new country();
				$location = explode(',',$this->fields['lieu']);
				$country->open($location[0]);
				if(!$country->isNew()){
					$imgC = $country->getFlag();
					if ($imgC != ''){
						?>
						<img src="<? echo $imgC; ?>" title="<? echo $country->fields['printable_name']; ?>" alt="<? echo $country->fields['printable_name']; ?>">
						<?
					}
				}
				?>
			</p>
		</div>
		<div class="details">
			<?php
			$current_date = current(explode(" ", dims_getdatetime()));

			if(strcmp($this->fields['datejour'], $current_date) < 0 && $this->fields['close'] == 0) {
				?>
					<div class="close">
						<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('admin.php?action=close_activity&id=<?php echo $this->fields['id'] ?>','<?php echo $_SESSION['cste']['EVENT_CONFIRM_CLOSE']; ?>');">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/remove_tag.png"/>
							<span><? echo $_SESSION['cste']['_DIMS_CLOSE']; ?></span>
						</a>
					</div>
				<?php
			}
			?>
				<div class="delete">
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('admin.php?action=delete_activity&id=<?php echo $this->fields['id'] ?>','<?php echo $_SESSION['cste']['EVENT_CONFIRM_DELETE']; ?>');">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png"/>
						<span><? echo $_SESSION['cste']['_DELETE']; ?></span>
					</a>
				</div>
			<?php

			require_once DIMS_APP_PATH.'modules/system/class_search.php';
			$matrix = new search();
			$my_context = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], array(), array($this->fields['id_globalobject']), array(), array(),array(), array(), array(), array(), array());

			if(isset($my_context['distribution'])){
				$distrib = $my_context['distribution'];
				if(isset($distrib['activities'][$this->fields['id_globalobject']]['ref']) && !empty($distrib['activities'][$this->fields['id_globalobject']]['ref'])){
					$ref = new action();
					$ref->openWithGB($distrib['activities'][$this->fields['id_globalobject']]['ref']);
					if(!$ref->isNew()){
							//hack -> apparement Ils arrivent à créer des activités sans date
							if ($this->fields['datejour']=='0000-00-00'){
								$this->fields['datejour']=$ref->fields['datejour'];
								$this->save();//à priori c'est un hack pour corriger un truc improbable, autant qu'on n'y passe rarement en sauvegardant tout ça et le pb ne se reproduira plus
							}
							//traitement spécifique sur les dates qui sont stockée au format américain yyyy-mm-dd
							$deb = explode('-',$ref->fields['datejour']);
							$one_day = true;
							$str_fin = '';
							$str_deb = '';
							$date_compare = '';
							if($deb[2] != 0){
								$str_deb .= $deb[2].'/';
								$date_compare = $deb[2];
							}
							if($deb[1] != 0){
								$str_deb .= $deb[1].'/';
								$date_compare = $deb[1] . $date_compare;
							}
							if($deb[0] != 0){
								$str_deb .= $deb[0];
								$date_compare = $deb[0] . $date_compare;
							}

							if($ref->fields['datejour'] != $ref->fields['datefin'] && $ref->fields['datefin']!= '0000-00-00'){
								$one_day = false;
								$fin = explode('-',$ref->fields['datefin']);
								$str_fin = ' '.$_SESSION['cste']['DATE_TO_THE'].' ';
								if($fin[2] != 0){
									$str_fin .= $fin[2].'/';
								}
								if($fin[1] != 0){
									$str_fin .= $fin[1].'/';
								}
								if($fin[0] != 0){
									$str_fin .= $fin[0];
								}
							}

							$past_verb = true;
							if(date('Ymd') < $date_compare) $past_verb = false;

							$type_action = $ref->getSearchableType();
							?>
							<div class="event_reference">
								<?php
								$ref_system_object = dims_const::_SYSTEM_OBJECT_EVENT;
								if($type_action==search::RESULT_TYPE_ACTIVITY){
									$ref_system_object = dims_const::_SYSTEM_OBJECT_ACTIVITY;
									$label = $_SESSION['cste']['THE_ACTIVITY'];
								?>
									<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_red_picto.png" title="<?php echo ucfirst($_SESSION['cste']['ACTIVITY']); ?>"/>
								<?php
								}
								else{
									if($type_action == search::RESULT_TYPE_MISSION){
										$title = $_SESSION['cste']['_DIMS_IMPORT_LABEL_MISSION'];
										$label = $_SESSION['cste']['THE_MISSION'];
									}
									else{
										$title = ucfirst($_SESSION['cste']['SIMPLE_FAIR']);
										$label = $_SESSION['cste']['THE_FAIR'];
									}
									?>
										<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/event_mini.png" title="<?php echo $title; ?>"/>
									<?php
								}
								if($past_verb){
									$verb = $_SESSION['cste']['TOOK_PLACE'];
								}
								else $verb = $_SESSION['cste']['WILL_TAKE_PLACE'];
								?>
								<?php echo $_SESSION['cste']['SINGLE_THIS_FEMININ'].' '.$_SESSION['cste']['ACTIVITY'].' '.$verb.' '.$_SESSION['cste']['DURING'].' '.$label;?>
								<a href="/admin.php?mode=activity&action=view&activity_id=<? echo $this->fields['id']; ?>">
									<?php echo $ref->fields['libelle']; ?>
								</a>
								<?php echo ' '.(($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM']).' '.$str_deb.$str_fin;?>
							</div>
							<?php
					}
				}//fin traitement de l'event de référence

				//début traitement des entreprises rencontrées durant l'activité
				if(isset($distrib['tiers']) && !empty($distrib['tiers'])){
					?>
					<div class="tiers">
					<?php
						$total = count($distrib['tiers']);
						$tiers = array_keys($distrib['tiers']);

						//calcul du temps pour THIS
						$deb = explode('-',$this->fields['datejour']);
						$date_compare = '';
						if($deb[2] != 0){
							$date_compare = $deb[2];
						}
						if($deb[1] != 0){
							$date_compare = $deb[1] . $date_compare;
						}
						if($deb[0] != 0){
							$date_compare = $deb[0] . $date_compare;
						}
						//calcul du temps
						$verb = $_SESSION['cste']['PARTICIPATED_TO'];
						if(date('Ymd') < $date_compare) $verb = $_SESSION['cste']['WILL_PARTICIPATE_TO'];

						//affichage de la première company
						//$first = $tiers[0];
						//$t = new tiers();
						//$t->openWithGB($first);


						//traitement des autres
						if($total > 0){
							?>
							<a class="see_more" href="javascript:void(0);" onclick="javascript:toggle_menu('tiers_<?php echo $this->fields['id_globalobject'];?>', $(this).children().first());">
								<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png">
								&nbsp;<?php echo $_SESSION['cste']['_DIMS_PARTICIP'];?>
							</a>
							<div id="tiers_<?php echo $this->fields['id_globalobject']; ?>" <?php if (isset($_SESSION['desktopV2']['content_content']['zone_participants']) && $_SESSION['desktopV2']['content_content']['zone_participants'] == 0) echo 'style="display:none;'; ?>>
								<ul>
								<?php
								for($i=0; $i < $total; $i++){
									$t = new tiers();
									$t->openWithGB($tiers[$i]);
									?>
									<li>&nbsp;
									<?php
									echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/company_picto.png"/><a href="/admin.php?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$t->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_TIERS.'&init_filters=1&from=desktop">'.$t->fields['intitule'].'</a> ';
									?>
									</li>
									<?php
								}
								?>
								</ul>
							</div>
							<?php
						}
						?>
						</div>
						<?php
				}
			}
			?>
		</div>
	</div>
	<div class="bloc_ligne calendar">
		<table class="ro_calendar">
	        <tr>
	            <td class="bloc_calendar">
                        <?
                        if ($this->fields['datejour']!='0000-00-00') {
                        ?>

	                <table cellspacing="0" cellpadding="0" width="100%">
						<?
						$date = explode('-',$this->fields['datejour']);
						?>
	                    <tbody>
	                        <tr>
	                            <td align="center" class="calendar_top"><? if ($date[1] > 0){ echo date('M',mktime(00,00,00,$date[1]))?>. <? } echo $date[0]; ?></td>
	                        </tr>
	                        <tr>
	                            <td align="center" class="calendar_bot"><? if ($date[2] > 0) echo $date[2]; else echo '-' ?></td>
	                        </tr>
	                    </tbody>
	                </table>
                        <?
                        }
                        ?>
	            </td>
	        </tr>
	    </table>
	</div>
	<div style="clear:both"></div>
	<div class="footer">
	<?php
	if(isset($this->fields['id_user']) && !empty($this->fields['id_user'])){
		$author = new user();
		$author->open($this->fields['id_user']);
		if(!$author->isNew()){

			$contact = new contact();
			$contact->open($author->fields['id_contact']);
			$a_open = '';
			$a_close = '';
			$date = '';
			$verb = $_SESSION['cste']['HAS_BEEN_CREATED_BY_FEMININ'];
			if(!$contact->isNew()){
				$a_open = '<a href="/admin.php?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$contact->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1&from=desktop">';
				$a_close = '</a>';
				if(isset($this->fields['timestp_create']) && !empty($this->fields['timestp_create'])){
					$date = ' '.$_SESSION['cste']['SINGLE_THE'];
					$d = dims_timestamp2local($this->fields['timestp_create']);
					$date .= ' '.$d['date'].' '.strtolower($_SESSION['cste']['_DIMS_LABEL_FROM']);
					$verb = $_SESSION['cste']['HAS_BEEN_CREATED_BY_FEMININ'];
				}
			}
			echo $_SESSION['cste']['SINGLE_THIS_FEMININ'].' '.$_SESSION['cste']['ACTIVITY'].' '.$verb.$a_open.' <strong>'.ucfirst(substr($author->fields['firstname'],0,1)).'. '.$author->fields['lastname'].'</strong>'.$a_close;
		}
	}
	?>
	</div>
</div>
