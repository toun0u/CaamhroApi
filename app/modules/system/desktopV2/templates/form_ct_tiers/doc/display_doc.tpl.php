<style type="text/css">
div.zone_address_book a{
	text-decoration: underline !important;
}
div.zone_address_book{
	margin-right: 30px;
}
</style>
<link type="text/css" rel="stylesheet" href="/common/modules/doc/templates/styles.css" media="screen" />
<?php
$fold = docfolder::find_by(array('id'=>$this->get('id_folder'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
$foldTitle = $foldFiled = "";
if(!empty($fold)){
	if($fold->get('id_folder') != '' && $fold->get('id_folder') > 0){
		$foldFiled = " ".$_SESSION['cste']['_IN_THE_DIRECTORY']." ".$fold->get('name');
		$foldTitle = " (".$fold->get('name').")";
	}
}
$objLk = $this->getAttachedObj();
$urlAttach = $labelAttach = $imgAttach = "";
$rowspan = 4;
$urlRemove = dims::getInstance()->getScriptEnv().'?submenu=1&mode=doc&action=delete&id='.$this->get('id');
if(!is_null($objLk)){
	switch ($objLk->getid_object()) {
		case contact::MY_GLOBALOBJECT_CODE:
			$urlAttach = dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$objLk->get('id');
			$labelAttach = $objLk->get('firstname')." ".$objLk->get('lastname');
			$file = $objLk->getPhotoPath(20);
			if(file_exists($file)){
				$imgAttach = $objLk->getPhotoWebPath(20);
			}else{
				$imgAttach = _DESKTOP_TPL_PATH.'/gfx/common/human20.png';
			}
			$urlRemove = dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=remove_file&id_ct='.$objLk->get('id').'&id='.$this->get('id');
			$rowspan ++;
			break;
		case tiers::MY_GLOBALOBJECT_CODE:
			$urlAttach = dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$objLk->get('id');
			$labelAttach = $objLk->get('intitule');
			$file = $objLk->getPhotoPath(20);
			if(file_exists($file)){
				$imgAttach = $objLk->getPhotoWebPath(20);
			}else{
				$imgAttach = _DESKTOP_TPL_PATH.'/gfx/common/company40.png';
			}
			$urlRemove = dims::getInstance()->getScriptEnv().'?submenu=1&mode=tiers&action=remove_file&id_ct='.$objLk->get('id').'&id='.$this->get('id');
			$rowspan ++;
			break;
	}
}
?>
<table style="width:100%;">
	<tr>
		<td style="width:110px;vertical-align:top;" rowspan="<?= $rowspan; ?>">
			<?php
			$file = $this->getThumbnail(100);//real_path
			if(!is_null($file)){
				?>
				<img class="picture" src="<?= $file; ?>">
				<?php
			}
			else{
				?>
				<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/doc40.png">
				<?php
			}
			?>
		</td>
		<th style="font-size:15px;height:20px;border:0px;text-align:left;">
			<?= $this->get('name').$foldTitle; ?>
		</th>
	</tr>
	<tr>
		<td style="height:15px;">
			<?= $_SESSION['cste']['_WEIGHT']; ?> : <?= round($this->get('size')/1024,2); ?> ko
		</td>
	</tr>
	<?php if($urlAttach != '' && $labelAttach != '' && $imgAttach != ''){ ?>
	<tr>
		<td style="height:15px;">
			<img src="<?= $imgAttach; ?>" style="width:20px;" />
			<span style="vertical-align: super;">
				<?= $_SESSION['cste']['_ATTACHED_TO_PLUG_FROM']; ?>
				<a href="<?= $urlAttach; ?>"><?= $labelAttach; ?></a>
			</span>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td style="vertical-align:top;">
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
	<tr>
		<td style="color:#8A8A8A;">
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
			echo $foldFiled;
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="actions" style="padding-top: 10px;">
			<input class="edit" type="button" value="<?= $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv()."?submenu=1&mode=doc&action=edit&id=".$this->get('id'); ?>';" />
			<input class="download" type="button" value="<?= $_SESSION['cste']['_DIMS_DOWNLOAD']; ?>" onclick="javascript:document.location.href='<?= $this->getDownloadLink(); ?>';" />
			<input onclick="javascript:dims_confirmlink('<?= $urlRemove; ?>','<?= $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ELEMENT_?']; ?>');" class="delete" type="button" value="<?= $_SESSION['cste']['DELETE_DOCUMENT']; ?>" />
		</td>
	</tr>
</table>
<div class="description-doc">
	<?= $this->get('description'); ?>
</div>
<?php
if (in_array($this->fields['extension'],array("mp4","mkv","avi","mpeg","mpg"))){
	?>
	<div style="width: 100%; height: 870px;text-align:center;">
		<?= $this->getPreview(true,null); ?>
	</div>
	<?
}else{
	?>
	<div style="width: 100%; height: 870px;">
		<?= $this->getPreview(true,null,_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/doc/preview_doc.tpl.php'); ?>
	</div>
<?php } ?>