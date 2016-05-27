<div class="sugst_kw_bloc">
	<div class="bloc picto">
	<?php
		$file = $this->getPhotoPath(40);//real_path
		if(file_exists($file)){
			?>
			<img class="picture" src="<?php echo $this->getPhotoWebPath(40); ?>">
			<?php
		}
		else{
			?>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company40.png">
			<?php
		}
	?>
	</div>
	<div class="bloc title">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=company&val=<?php echo $this->fields['id_globalobject'];?>">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png">
			<span><?php echo $this->getIntitule(); ?></span>
		</a>
	</div>
	<div class="bloc action">
		<a title="<?php echo $_SESSION['cste']['ADD_TO_THE_ADVANCED_SEARCH'];?>" href="admin.php?dims_op=desktopv2&action=as_managefilter&faction=add&type=company&val=<?php echo $this->fields['id_globalobject'];?>">
			<?php
			if(!isset($_SESSION['dims']['advanced_search']['filters']['companies'][$this->fields['id_globalobject']])){
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