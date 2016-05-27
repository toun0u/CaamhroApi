<?php
$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, false);
$this->setLightAttribute('from', $from);
?>

<link href="./common/js/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./common/js/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="./common/js/base64.js"></script>

<div class="title_new_opportunity">
    <h1><?php echo $_SESSION['cste']['NEW_OPPORTUNITY']; ?></h1>
</div>
<form id="docfile_add" name="docfile_add" method="post" action="admin.php" enctype="multipart/form-data">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("mode", "opportunity");
		$token->field("action", "save");
		$token->field("opp_id", $this->fields['id']);
		$token->field("redirection", "1");
		$token->field("uploadForm");
		$token->field("enregistrement");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
    <input type="hidden" name="mode" value="opportunity" />
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="opp_id" value="<?php echo $this->fields['id']; ?>" />
	<input type="hidden" name="redirection" id="redirection" value="1" />
	<?php
	$this->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_opportunity_desc.tpl.php');
	$this->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_opportunity_tags.tpl.php');
	$this->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_opportunity_contacts.tpl.php');
	?>
    <div class="title_description">
        <h2>
			<? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 7 - <? echo $_SESSION['cste']['EXTRAS']; ?> : <? echo $_SESSION['cste']['_DOCS']; ?> / <? echo $_SESSION['cste']['PICTURES']; ?> / Videos
			<span style="font-weight: normal;color: #a9a9a9; font-size:10px;">
				<i>(<? echo $_SESSION['cste']['PICTURES_AND_VIDEOS_WILL_COMPLETE_THE_GALLERY_OF_THE_OPPORTUNITY']; ?>)</i>
			</span>
		</h2>
    </div>

    <div class="add_extras_doc">
        <div class="title_add_extras" onclick="javascript:$('div.title_add_extras span').html('<? echo str_replace("'","\'",$_SESSION['cste']['_DOC_LABEL_ADD_OTHER_FILE']); ?>'); createFileInput();">
			<img border="0" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" />
			<span><?php echo $_SESSION['cste']['ADD_FILE']; ?></span>
			<script type="text/javascript">
				var uploads = new Array();
				var upload_cell, file_name;
				var count=0;
				var checkCount = 0;
				var check_file_extentions = true;
				var sid = '<? echo session_id() ; ?>';
				var page_elements = ["toolbar","page_status_bar"];
				var img_path = "../common/img/";
				var path = "";
				var bg_color = false;
				var status;
				var debug = false;
				var param1=<? echo (isset($op) && $op == 'file_add') ? 'true' : 'false'; ?>;
				var param2=<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>;
			</script>
			<script type="text/javascript" src="/common/js/upload/javascript/uploader.js"></script>
		</div>
		<div id="ScrollBox" style="overflow:auto;float:left;clear:both;">
			<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
			<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;height:10px;" src=""></iframe>
		</div>
    </div>

    <div class="zone_contact_opportunity_enregistrement">
    	<?php
    	if ($from == 'planning') {
	        ?>
	        <input class="bouton_enregistrement" type="button" onclick="javascript:document.location.href='/admin.php?submenu=<? echo dims_const_desktopv2::DESKTOP_V2_DESKTOP; ?>&mode=planning';" value="<?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?>" />
	        <span> <? echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
	        <input class="bouton_enregistrement" onclick="javascript:$('input#redirection').val(3); if(dims_controlform('docfile_add', 'global_error', 'champ obligatoire non saisi'))upload();else return false;" type="button" value="<?php echo $_SESSION['cste']['_SAVE_OPPORTUNITY_AND_ADD_NEW_ONE']; ?>" name="enregistrement" />
	        <span> <? echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
	        <input class="bouton_enregistrement" onclick="javascript:$('input#redirection').val(2); if(dims_controlform('docfile_add', 'global_error', 'champ obligatoire non saisi'))upload();else return false;" type="button" value="<?php echo $_SESSION['cste']['_SAVE_OPPORTUNITY']; ?>" name="enregistrement" />
	        <?php
    	}
    	else {
	        ?>
	        <input class="bouton_enregistrement" type="button" onclick="javascript:document.location.href='/admin.php?submenu=<? echo dims_const_desktopv2::DESKTOP_V2_DESKTOP; ?>&mode=default';" value="<?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?>" />
	        <span> <? echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
	        <input class="bouton_enregistrement" onclick="javascript:$('input#redirection').val(1); if(dims_controlform('docfile_add', 'global_error', 'champ obligatoire non saisi'))upload();else return false;" type="button" value="<?php echo $_SESSION['cste']['_SAVE_OPPORTUNITY_AND_ADD_NEW_ONE']; ?>" name="enregistrement" />
	        <span> <? echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
	        <input class="bouton_enregistrement" onclick="javascript:$('input#redirection').val(0); if(dims_controlform('docfile_add', 'global_error', 'champ obligatoire non saisi'))upload();else return false;" type="button" value="<?php echo $_SESSION['cste']['_SAVE_OPPORTUNITY']; ?>" name="enregistrement" />
	        <?php
    	}
    	?>
    </div>
    <div id="global_error" class="opportunity_form_error"></div>
</form>
