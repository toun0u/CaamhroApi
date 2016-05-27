<div class="search_result">
	<div class="add_to_context">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=document&val=<?php echo $this->fields['id_globalobject'];?>">
			<?php
			if(!isset($_SESSION['dims']['advanced_search']['filters']['documents'][$this->fields['id_globalobject']])){
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
		$type = $this->getLightAttribute('type');
		switch($type){
			case search::RESULT_TYPE_DOCUMENT:
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60.png">
				<?php
				break;
			case search::RESULT_TYPE_PICTURE:
				if(file_exists($this->getPicturePath(60))){
					?>
					<img src="<?php echo $this->getPictureWebPath(60);?>" />
					<?php
				}
				else
				{
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60picture.png">
					<?php
				}
				break;
			case search::RESULT_TYPE_MOVIE:
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc60movie.png">
				<?php
				break;
		}
		?>
	</div>
	<div class="detail">
		<div class="title">
			<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=doc&action=show&id=<?= $this->get('id'); ?>" class="title_result">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc16.png" />
				<span><?php echo $this->fields['name']; ?></span>
			</a>
		</div>
		<div class="context">
			<?php
			$field = $this->getLightAttribute('extra_field');
			$label = $this->getLightAttribute('extra_label');
			$sentence = $this->getLightAttribute('sentence');

			if(	isset($field)
			&&	isset($label)
			&&	isset($this->fields[$field])){
				$prewords = $this->getLightAttribute('prewords');
				if(isset($prewords['possible']))//à priori root est quoi qu'il arrive set sinon y'aurait pas de résultat
					$words = array_merge($prewords['root'], $prewords['possible']);
				else $words = $prewords['root'];
				if($label != 'content')
					echo '<strong>'. ucfirst($label) .'</strong> : '.dims_getManifiedWords(strip_tags($this->fields[$field]), $words, '<span class="founded_result">', '</span>').' ...';
				else if($type==search::RESULT_TYPE_DOCUMENT && isset($sentence))
					echo '<strong>'. ucfirst($label) .'</strong> : ... '.dims_getManifiedWords($sentence, $words, '<span class="founded_result">', '</span>').' ...';
			}
			?>
		</div>
		<?php
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
				if($deb[2] != 0){
					$str_deb .= $deb[2].'/';
				}
				if($deb[1] != 0){
					$str_deb .= $deb[1].'/';
				}
				if($deb[0] != 0){
					$str_deb .= $deb[0];
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
				?>
				<div class="advanced_src">
					<?php
					$type_action = $action->getSearchableType();
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
					?>
					<?php echo $_SESSION['cste']['DOCUMENT_HAS_BEEN_ADDED'].' '. $label; ?> <a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $action->fields['id']; ?>&type=<? echo $ref_system_object; ?>&init_filters=1&from=search"><?php echo $action->fields['libelle']; ?></a><?php echo ' '.(($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM'].' ').$str_deb.$str_fin;?>
				</div>
				<?php
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
