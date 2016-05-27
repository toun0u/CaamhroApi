<div class="mini-doc">
	<table>
		<tr>
			<td rowspan="3" style="width:70px;">
				<?php
				$path = $this->getThumbnail(60);
				if(!is_null($path)){
					?>
					<img src="<?= $path; ?>" alt="<?= $this->get('name'); ?>" title="<?= $this->get('name'); ?>" />
					<?php
				}
				?>
			</td>
			<td class="name-file" style="height:15px;">
				<?= $this->get('name'); ?>
				<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=doc&action=show&id=<?= $this->get('id'); ?>" style="margin-left:10px;"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/eye12.png" /></a>
				<a href="<?= $this->getDownloadLink(); ?>" style="margin-left:5px;"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/download.png" /></a>
				<a onclick="javascript:dims_confirmlink('<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=<?= $this->getLightAttribute('mode'); ?>&action=remove_file&id_ct=<?= $this->getLightAttribute('id_ct'); ?>&id=<?= $this->get('id'); ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');" href="javascript:void(0);" style="margin-left:5px;"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/remove_black.png" /></a>
			</td>
		</tr>
		<tr>
			<td style="height:15px;">
				<?= $_SESSION['cste']['_WEIGHT']; ?> : <?= round($this->get('size')/1024,2); ?> ko
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<?= $this->get('description'); ?>
			</td>
		</td>
		<?php
		$myTags = $this->getMyTags();
		if(count($myTags)){ ?>
			<tr>
				<td colspan="2">
					<?php
					$myTags = $this->getMyTags();
					foreach($myTags as $t){
						?>
						<span class="tag" dims-data-value="<?= $t->get('id'); ?>">
							<?= $t->get('tag'); ?>
						</span>
						<?php
					}
					?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" style="color:#8A8A8A;">
				<?php
				$dd = dims_timestamp2local($this->get('timestp_modify'));
				echo $_SESSION['cste']['_FILED_THE']." ".$dd['date'];
				if($this->get('id_user') != '' && $this->get('id_user') > 0){
					if($this->get('id_user') == $_SESSION['dims']['userid']){
						$url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$_SESSION['dims']['user']['id_contact'];
						echo " ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM']).' <a href="'.$url.'">'.$_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_YOURSELF']."</a>";
					}else{
						$user = new user();
						$user->open($this->get('id_user'));
						if(!$user->isNew()){
							$url = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$user->get('id_contact');
							echo " ".strtolower($_SESSION['cste']['_DIMS_LABEL_FROM']).' <a href="'.$url.'">'.$user->get('firstname')." ".$user->get('lastname')."</a>";
						}
					}
				}
				if($this->get('id_folder') != '' && $this->get('id_folder') > 0){
					$folders = $this->getLightAttribute('folders');
					if(isset($folders[$this->get('id_folder')]) && $folders[$this->get('id_folder')]->get('id_folder') != '' && $folders[$this->get('id_folder')]->get('id_folder') > 0){
						echo " ".$_SESSION['cste']['_IN_THE_DIRECTORY']." ".$folders[$this->get('id_folder')]->get('name');
					}
				}
				?>
			</td>
		</tr>
	</table>
</div>