<?
$db = dims::getInstance()->db;
$title = $this->fields['title'];

$ldate = ($this->fields['timestp']) ? dims_timestamp2local($this->fields['timestp']) : array('date' => '');
$article_timestp = $ldate['date'];

$ldate = ($this->fields['timestp_published']) ? dims_timestamp2local($this->fields['timestp_published']) : array('date' => '');
$article_timestp_published = $ldate['date'];

$ldate = ($this->fields['timestp_unpublished']) ? dims_timestamp2local($this->fields['timestp_unpublished']) : array('date' => '');
$article_timestp_unpublished = $ldate['date'];

$ldate = ($this->fields['lastupdate_timestp']) ? dims_timestamp2local($this->fields['lastupdate_timestp']) : array('date' => '', 'time' => '');
$lastupdate_timestp = "{$ldate['date']} {$ldate['time']}";

$user = new user();
if ($user->open($this->fields['lastupdate_id_user']) && $user->fields['id'] > 0) $lastupdate_user = "{$user->fields['firstname']} {$user->fields['lastname']} ({$user->fields['login']})";
else $lastupdate_user = '';
$readonly = false;

$wce_models = wce_getmodels();

if ($this->fields['model'] =='' && in_array("wiki", $wce_models['pages_publiques'])) $this->fields['model'] ='wiki';
if ($this->fields['title'] =='') $this->fields['title']='New Article';
?>
<form name="form_wce_article" style="margin:0;" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_ART_SAVE_PROPERTIES); ?>" method="post" enctype="multipart/form-data">

	<div style="clear:both;padding:2px;display:block;visibility:visible;">
		<input type="hidden" name="id_article" value="<? echo $this->fields['id']; ?>">
		<div id="wce_article_options" class="wce_form_row" style="width:100%;padding: 0px;">
			<div class="dims_form" style="float:left; width:50%;">
				<div style="padding:2px;">
					<p>
						<label><? echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?></label>
						<?
						if (!$readonly) {
							?>
							<input class="text" type="text" id="wce_article_title" name="wce_article_title" value="<? echo dimsEncodeString($this->fields['title']); ?>" tabindex="1" />
							<?
						}
						else echo '<span>'.($this->fields['title']).'</span>';
						?>
					</p>
					<p>
						<label><? echo $_SESSION['cste']['CONTENT_MODEL']; ?> :</label>
						<?php // non fini
							if (!$readonly) {
							?>
							<select name="wce_article_model" id="wce_article_model" class="select" tabindex="2">

							<option <? echo ($this->fields['model'] == "" ) ? 'selected' : ''; ?> value=""><? echo "aucun"; ?></option>
							<?
							$models = $wce_models["pages_publiques"];
							if(is_array($wce_models["workspace"])) $models = array_merge($models,$wce_models["workspace"]);
							sort($models);
							foreach($models as $key => $model) {
								?>
								<option <? echo ($this->fields['model'] == $model ) ? 'selected' : ''; ?> value="<? echo $model; ?>"><? echo $model; ?></option>
								<?
							}
							?>
							</select>
							<?

							?>
							<!--<input type="button" class="flatbutton" value="Changer" onclick="javascript:window.open('admin-light.php?dims_action=admin&op=select_model','Mod&eacute;les', 'width=500,  resizable=yes, scrollbars=1')"/>-->
							<?php
							}
							else echo '<span>'.($this->fields["model"]).'</span>';
						?>
					</p>

				</div>
			</div>
			<?
			if (!$readonly) {
				//echo "<script>$("#next_button").live('click', function() { // your code });</script";
				echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:document.form_wce_article.submit();",'','');
				echo "&nbsp;";
				echo dims_create_button($_DIMS['cste']['_DIMS_CLOSE'],"","javascript:dims_hidepopup('dims_popup');",'','');
				// must create submit button

				echo '</div><div style="text-align:left;float:left;padding:0px;height:40px;width:140px;">';
				// if ($this->fields['id']>0 && dims_isactionallowed())
			   // echo dims_create_button($_DIMS['cste']['_DELETE'],"./common/img/delete.gif","javascript:dims_confirmlink('/admin.php?dims_op=wiki&op_wiki=articlewiki_delete&id_article={$this->fields['id']}','Etes-vous certain de vouloir supprimer l\'article &laquo; ".addslashes($this->fields['title'])." &raquo; ?');");
			}
			?>
		</div>
	</div>
	<div style="clear:both;float:left;width:95%;margin-top:10px;">
		<span style="width:150px;font-size:12px;font-weight:bold">
			<img src="<?= module_wiki::getTemplateWebPath('/gfx/icon_categ.png');?>" style="margin-right:5px;" />
			Cat&eacute;gorisation
		</span>
		<?
		$lst = $this->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
		?>
		<div>
			<?
			$root = module_wiki::getCategRoot();
			$load = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (count($lst) > 0){
				$par = new category();
				$par->openWithGB(current($lst));
				$root->setLightAttribute('parent',$par);
			}else
				$root->setLightAttribute('parent',null);
			$root->display(module_wiki::getTemplatePath('/categories/ajax_categ_browser.tpl.php'));
			?>
		</div>
	</div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#wce_article_title").focus();
        });
    </script>
</form>

