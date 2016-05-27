<div class="sugst_kw_bloc">
	<div class="bloc picto">
	<?php
		$type = $this->getSearchableType();
		switch($type){
			case search::RESULT_TYPE_DOCUMENT:
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc40.png">
				<?php
				break;
			case search::RESULT_TYPE_PICTURE:
				if(file_exists($this->getPicturePath(40))){
					?>
					<img src="<?php echo $this->getPictureWebPath(40);?>" />
					<?php
				}
				else
				{
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc40picture.png">
					<?php
				}
				break;
			case search::RESULT_TYPE_MOVIE:
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc40movie.png">
				<?php
				break;
		}
	?>
	</div>
	<div class="bloc title">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=document&val=<?php echo $this->fields['id_globalobject'];?>">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc16.png" />
			<span><?php echo dims_strcut($this->fields['name'],28); ?></span>
		</a>
	</div>
	<div class="bloc action">
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
	<div style="clear:both;"></div>
</div>