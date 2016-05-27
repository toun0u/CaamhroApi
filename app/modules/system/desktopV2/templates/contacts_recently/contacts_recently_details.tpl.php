<?
$us = new user();
//if ($this->fields['timestp_modify'] == $this->fields['date_creation'])
if (isset($this->fields['id_user_create']) && $this->fields['id_user_create']>0 && $this->fields['timestp_modify'] == $this->fields['date_create'] ) {
		$us->open($this->fields['id_user_create']);
}
else if(isset($this->fields['id_user']) && $this->fields['id_user'] > 0){
		$us->open($this->fields['id_user']);
}
?>
<div class="zone_companies_recently">
	<table class="zone_companies_recently">
		<tbody>
			<tr>
				<td rowspan="2">
					<?
					global $_DIMS;
		$file = $this->getPhotoPath(40);//real_path
		if(file_exists($file)){
			?>
			<img class="picture" src="<?php echo $this->getPhotoWebPath(40); ?>">
			<?php
		}
		else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/contacts40.png">
			<?php
		}
		?>
	</td>
	<td class="ro_zone_details">
		<div class="ro_title">
			<p style="cursor: pointer;" onclick="javascript:document.location.href='/admin.php?submenu=1&mode=contact&action=show&id=<? echo $this->fields['id']; ?>';">
				<span><?php echo $this->getFirstname().' '.$this->getLastname(); ?></span>
				<?
				if (isset($this->fields['id_country']) && $this->fields['id_country'] != '' && $this->fields['id_country'] > 0){
					require_once DIMS_APP_PATH."modules/system/class_country.php";
					$country = new country();
					$country->open($this->fields['id_country']);
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

		<div class="context" style="clear:both;">
			<?php
			$field = $this->getLightAttribute('extra_field');
			$label = $this->getLightAttribute('extra_label');
			if(	isset($field)
			&&	isset($label)
			&&	isset($this->fields[$field])
			&&	isset($_DIMS['cste'][$label])){
				$prewords = $this->getLightAttribute('prewords');
				if(isset($prewords['possible']))//à priori root est quoi qu'il arrive set sinon y'aurait pas de résultat
					$words = array_merge($prewords['root'], $prewords['possible']);
				else $words = $prewords['root'];
				echo '<strong>'. ucfirst($_DIMS['cste'][$label]) .'</strong> : '.dims_getManifiedWords(strip_tags($this->fields[$field]), $words, '<span class="founded_result">', '</span>');
			}
			?>
		</div>
		<div class="contact_employer">
			<?php
				$employers = $this->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR');

				if(count($employers)){
					?>
					<span><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png"/><strong>Companies :</strong>
					<?php
					$i=0;
					foreach($employers as $employer){
						?>
						<a href="/admin.php?submenu=1&mode=company&action=show&id=<? echo $employer['id']; ?>"><?php echo $employer['intitule'];?></a><?php if(!empty($employer['function'])) echo ' ('.$employer['function'].')'; ?>
						<?php
						if($i < count($employers)-1) echo '<span class="company_pipe"> | </span>';
						$i++;
					}
					?>
					</span>
				<?php
				}
			?>
		</div>
		<?php
		$advanced_src = $this->getLightAttribute('advanced_src');
		if(isset($advanced_src) && !empty($advanced_src)){//si on a un lien avec une opportunité
			require_once DIMS_APP_PATH . '/modules/system/class_action.php';
			$action = new action();
			$action->openWithGB($advanced_src);
			if(!$action->isNew()){

				//traitement spécifique sur les dates qui sont stockée au format américain yyyy-mm-dd
				$deb = explode('-',$action->fields['datejour']);
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

				if($action->fields['datejour'] != $action->fields['datefin']  && $action->fields['datefin']!= '0000-00-00'){
					$one_day = false;
					$fin = explode('-',$action->fields['datefin']);
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

				if($hasAccount){
					if($past_verb){
						$verb = $_SESSION['cste']['PARTICIPATED_TO'];
					}
					else $verb = $_SESSION['cste']['WILL_PARTICIPATE_TO'];


				}
				else{
					if($past_verb){
						$verb = $_SESSION['cste']['MET_MINUSCULE'];
					}
					else $verb = $_SESSION['cste']['WILL_BE_MET'];

					$verb .= ' '.$_SESSION['cste']['DURING'];
				}


				$type_action = $action->getSearchableType();
				?>
				<div class="advanced_src">
					<?php
					$ref_system_object = dims_const::_SYSTEM_OBJECT_ACTION;
					if($type_action==search::RESULT_TYPE_OPPORTUNITY){
						$ref_system_object = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
						$label = $_SESSION['cste']['THE_OPPORTUNITY'];
					?>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/opportunity_red_picto.png" title="<?php echo ucfirst($_SESSION['cste']['OPPORTUNITY']); ?>"/>
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
					echo $verb.' '.$label; ?> <a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $action->fields['id']; ?>&type=<? echo $ref_system_object; ?>&init_filters=1&from=search"><?php echo $action->fields['libelle']; ?></a><?php echo ' '.(($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM'].' ').$str_deb.$str_fin;?></div>
				<?php
			}
		}
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="footer">
					<?
					if(!$us->new){

						$contact = new contact();
						$contact->open($us->fields['id_contact']);
						$a_open = '';
						$a_close = '';
						if(!$contact->isNew()){
							$a_open = '<a href="'.dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$contact->fields['id'].'">';
							$a_close = '</a>';
						}

						if ($this->fields['timestp_modify'] == $this->fields['date_create']){
							if(isset($this->fields['date_create']) && !empty($this->fields['date_create'])){
								$date = ' ';
								$d = dims_timestamp2local($this->fields['date_create']);
								$date .= ' '.$d['date'];
							}
							echo $_SESSION['cste']['_SYSTEM_LABEL_FICHCREATED'].$date." ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM'])." ".$a_open.'<strong>'.ucfirst(substr($us->fields['firstname'],0,1)).". ".$us->fields['lastname'].'</strong>'.$a_close;
						}
						else{
							if(isset($this->fields['timestp_modify']) && !empty($this->fields['timestp_modify'])){
								$date = ' '.$_SESSION['cste']['SINGLE_THE'];
								$d = dims_timestamp2local($this->fields['timestp_modify']);
								$date .= ' '.$d['date'];
							}
							echo $_SESSION['cste']['_DIMS_LABEL_MOD_SHEET_IMP'].$date." ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM'])." ".$a_open.'<strong>'.ucfirst(substr($us->fields['firstname'],0,1)).". ".$us->fields['lastname'].'</strong>'.$a_close;
						}
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
