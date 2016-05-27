<?php
$type = $this->getLightAttribute('type');
$urlType = 'event';
switch($type){
	case search::RESULT_TYPE_MISSION:
	case search::RESULT_TYPE_FAIR:
		$system_object = dims_const::_SYSTEM_OBJECT_EVENT;
		$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/event_default_search.png" />';
		break;
	case search::RESULT_TYPE_ACTIVITY:
		$system_object = dims_const::_SYSTEM_OBJECT_ACTIVITY;
		$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/activity_default_search.png" />';
		$urlType = 'activity';
		break;
	case search::RESULT_TYPE_OPPORTUNITY:
		$system_object = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
		$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/activity_default_search.png" />';
		$urlType = 'opportunity';
		break;
}
?>
<div class="search_result">
	<div class="add_to_context">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=<?php echo $urlType; ?>&val=<?php echo $this->fields['id_globalobject'];?>">
			<?php
			if(!isset($_SESSION['dims']['advanced_search']['filters']['events'][$this->fields['id_globalobject']])
				&& !isset($_SESSION['dims']['advanced_search']['filters']['activities'][$this->fields['id_globalobject']])){
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/fleche_result2.png">
				<?php
			}
			else{
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/already_in_search2.png">
				<?php
			}
			?>
		</a>
	</div>
	<div class="avatar">
		<?php
		if(file_exists($this->fields['banner_path'])){
			?>
			<img class="to_resize" src="<?php echo $this->fields['banner_path'];?>">
			<?php
		}
		else echo $image;
		?>
	</div>
	<div class="detail">
		<div class="title">
			<a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $this->fields['id']; ?>&type=<? echo $system_object; ?>&init_filters=1&from=search" class="title_result">
				<?php
				if($type==search::RESULT_TYPE_ACTIVITY) $picto = 'activity_red_picto.png';
				else $picto = 'event_mini.png';
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo $picto; ?>" />
				<?php
				//traitement spécifique sur les dates qui sont stockée au format américain yyyy-mm-dd
				$deb = explode('-',$this->fields['datejour']);
				$one_day = true;
				$str_fin = '';
				$str_deb = '';
				if($deb[2] != 0){
					$str_deb .= $deb[2].'/';
				}
				if($deb[1] != 0){
					$str_deb .= $deb[1].'/';
				}
				if($deb[0] != 0){
					$str_deb .= $deb[0];
				}

				if($this->fields['datejour'] != $this->fields['datefin'] && $this->fields['datefin']!= '0000-00-00'){
					$one_day = false;
					$fin = explode('-',$this->fields['datefin']);
					if ($str_deb != '')
						$str_fin = (($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM'].' ').$str_deb.' '.$_SESSION['cste']['DATE_TO_THE'].' ';
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


				?>
				<span><?php echo $this->fields['libelle']; ?></span>
			</a>
			<span class="dates">
				<?php echo ' [ '.$str_fin.' ] '; ?>
			</span>
		</div>
		<div class="context">
			<?php
			$field = $this->getLightAttribute('extra_field');
			$label = $this->getLightAttribute('extra_label');
			if(	isset($field)
			&&	isset($label)
			&&	isset($this->fields[$field]) ){
				$value = strip_tags($this->fields[$field]);
				$root = false;
				$prewords = $this->getLightAttribute('prewords');
				if(strlen($value) > 150){
					$value2 = dims_getExtract($value,  $prewords['root'] , 6);
					if($value2 != $value){
						$value=$value2;
						$root = true;
					}
					else $value = dims_getExtract($value, $prewords['possible'], 6);
				}

				if($value == '[...] ') $value = dims_strcut(strip_tags($this->fields[$field]), 150);
				$words = $prewords['root'];
				if(!$root && isset($prewords['possible'])){
					$words = array_merge($prewords['root'], $prewords['possible']);
				}

				echo '<strong>'. ucfirst($label) .'</strong> : '.dims_getManifiedWords($value, $words, '<span class="founded_result">', '</span>');
			}
			?>
		</div>
		<?php
		if($type==search::RESULT_TYPE_ACTIVITY){
			$advanced_src = $this->getLightAttribute('advanced_src');
			if(isset($advanced_src) && !empty($advanced_src)){//si on a un lien avec une activité
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

					if($action->fields['datejour'] != $action->fields['datefin'] && $action->fields['datefin']!= '0000-00-00'){
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

					$type_action = $action->getSearchableType();
					?>
					<div class="advanced_src">
						<?php
						$ref_system_object = dims_const::_SYSTEM_OBJECT_ACTION;
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
						<?php echo $_SESSION['cste']['SINGLE_THIS_FEMININ'].' '.$_SESSION['cste']['ACTIVITY'].' '.$verb.' '.$_SESSION['cste']['DURING'].' '.$label;?> <a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $action->fields['id']; ?>&type=<? echo $ref_system_object; ?>&init_filters=1&from=search"><?php echo $action->fields['libelle']; ?></a><?php echo ' '.(($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM']).' '.$str_deb.$str_fin;?>
					</div>
					<?php
				}

			}
		}
		?>
		<div class="tags_elem_search">
			<?php
			$tags = $this->getMyTags();
			if(count($tags)){
				$i = 0;
				foreach($tags as $tag){
					$tag->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
					if($i < count($tags)-1) echo '<div class="tag_separator"> | </div>';
					$i++;
				}
			}
			?>
		</div>
	</div>
</div>
