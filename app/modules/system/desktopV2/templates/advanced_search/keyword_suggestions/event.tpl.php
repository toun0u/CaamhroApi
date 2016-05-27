<div class="sugst_kw_bloc">
	<div class="bloc picto">
	<?php
		$type = $this->getSearchableType();
		switch($type){
			case search::RESULT_TYPE_MISSION:
			case search::RESULT_TYPE_FAIR:
				$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/event40.png" />';
				break;
			case search::RESULT_TYPE_ACTIVITY:
				$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/activity40.png" />';
				break;
		}

		if(file_exists($this->fields['banner_path'])){
			?>
			<img class="to_resize40" src="<?php echo $this->fields['banner_path'];?>">
			<?php
		}
		else echo $image;
	?>
	</div>
	<div class="bloc title">
	<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=<?php echo ($type==search::RESULT_TYPE_ACTIVITY)?'activity':'event';?>&val=<?php echo $this->fields['id_globalobject'];?>">
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
			<span><?php echo $this->fields['libelle']; ?></span>
		</a>
		<span class="dates"><?php echo ' [ '.(($one_day)?' '. $_SESSION['cste']['SINGLE_THE'].' ':' '.$_SESSION['cste']['DATE_FROM'].' ').$str_deb.$str_fin.' ] '; ?></span>
	</div>
	<div class="bloc action">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=<?php echo ($type==search::RESULT_TYPE_ACTIVITY)?'activity':'event';?>&val=<?php echo $this->fields['id_globalobject'];?>">
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
	<div style="clear:both;"></div>
</div>