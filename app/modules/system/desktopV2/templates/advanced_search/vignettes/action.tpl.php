<?php
$mode = $this->getLightAttribute('mode');
?>

<div class="bloc item">
	<div class="as_picto">
	<?php
	$type = $this->getSearchableType();
	$url_type = null;

	switch($type){
		case search::RESULT_TYPE_MISSION:
		case search::RESULT_TYPE_FAIR:
			$url_type = 'event';
			$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/event_default_search.png" />';
			?>
			<?php
			break;
		case search::RESULT_TYPE_ACTIVITY:
			$url_type = 'activity';
			$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/activity_default_search.png" />';
			break;
		case search::RESULT_TYPE_OPPORTUNITY:
			$url_type = 'opportunity';
			$image = '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/activity_default_search.png" />';
			break;
		default:
			$image = "";
			break;
	}
	if(file_exists($this->fields['banner_path'])){
		?>
		<img class="avatar_action" src="<?php echo $this->fields['banner_path'];?>">
		<?php
	}
	else echo $image;

	?>
	</div>
	<div>
		<span class="label">
			<?php
			if(!isset($url_type)){
				switch($type){
					case search::RESULT_TYPE_MISSION:
					case search::RESULT_TYPE_FAIR:
						$url_type = 'event';
						break;
					case search::RESULT_TYPE_ACTIVITY:
						$url_type = 'activity';
						break;
					case search::RESULT_TYPE_OPPORTUNITY:
						$url_type = 'opportunity';
						break;
				}
			}
			if ($mode == 'concept') {
				$filter_type = $this->getLightAttribute('filter_type');
				?>
				<a class="remove_item" href="/admin.php?action=drop_filter&filter_type=<?php echo $filter_type; ?>&filter_value=<?php echo $this->fields['id_globalobject'];?>" title="delete this filter">
				<?php
			}
			else {
				?>
				<a class="remove_item" href="/admin.php?dims_op=desktopv2&action=as_managefilter&faction=del&type=<?php echo $url_type; ?>&val=<?php echo $this->fields['id_globalobject'];?>" title="delete this filter">
				<?php
			}
			?>
				<span><?php echo dims_strcut($this->fields['libelle'],15); ?></span>
			</a>
		</span>
	</div>
</div>
