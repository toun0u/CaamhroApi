<div class="display-user" dims-data-value="<?= $this->get('id'); ?>">
	<table cellpadding="0" cellspacing="0" style="width:100%;">
		<tr>
			<?php
			$url = _DESKTOP_TPL_PATH."/gfx/common/human40.png";
			$file = $this->getPhotoPath(40);//real_path
			if(file_exists($file)){
				$url = $this->getPhotoWebPath(40);
			}
			?>
			<td class="image-user" style="background: url('<?= $url; ?>') no-repeat;">
				<!--<img src="..." />-->
			</td>
			<td class="name-user">
				<?= $this->get('firstname')." ".$this->get('lastname'); ?>
			</td>
			<?php if($this->getLightAttribute('extended')){ ?>
				<td style="width:20px;">
					<a href="javascript:void(0);" class="del-added-user"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
				</td>
			<?php } ?>
		</tr>
	</table>
</div>