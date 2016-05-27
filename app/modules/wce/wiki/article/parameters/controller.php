<?php
$contactadd = new contact();
$contactadd->init_description();
$dd = dims_timestamp2local($this->fields['timestp_modify']);
require_once(DIMS_APP_PATH . '/modules/wce/wiki/include/class_wce_reference.php');

if( ! isset($_SESSION['dims']['wiki']['article']['params_op'])) $_SESSION['dims']['wiki']['article']['params_op'] = module_wiki::_SHOW_INFO_GENERALES;
$params_op =dims_load_securvalue('params_op',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['wiki']['article']['params_op'], module_wiki::_SHOW_INFO_GENERALES);

if ($this->fields['id'] != '' && $this->fields['id'] > 0){
	$new_article = false;
	$user=new user();
	$user->open($this->fields['id_user']);
	$contactadd->open($user->fields['id_contact']);
		$this->setLightAttribute('contactadd', $contactadd);
		$this->setLightAttribute('user', $user);
		$this->setLightAttribute('dd', $dd);
}

$this->display(module_wiki::getTemplatePath('/article/edition_article_menu_properties.tpl.php'));


/*
?>

<h3><?php echo $_SESSION['cste']['_PARAMETERS'] ?></h3>

<ul class="sub_menus">
	<li <?php if($params_op == module_wiki::_SHOW_INFO_GENERALES) echo 'class="selected"'; ?> ><a href="<?= module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_SHOW_INFO_GENERALES."&lang=".$this->fields['id_lang']); ?>"><?= $_SESSION['cste']['GENERAL_PARAMETERS']; ?></a></li>
	<li <?php if($params_op == module_wiki::_REFERENCING) echo 'class="selected"'; ?> ><a href="<?= module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCING."&lang=".$this->fields['id_lang']); ?>"><?= $_SESSION['cste']['REFERENCING']; ?></a></li>
	<li <?php if($params_op == module_wiki::_REFERENCES || $params_op == module_wiki::_ADD_REFERENCES) echo 'class="selected"'; ?> >
		<a href="<?= module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCES."&lang=".$this->fields['id_lang']); ?>">
			<?= $_SESSION['cste']['REFERENCES']; ?>
		</a>
	</li>
	<li <?php if($params_op == module_wiki::_LINKS) echo 'class="selected"'; ?> >
		<a href="<?= module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_LINKS."&lang=".$this->fields['id_lang']); ?>">
			<?= strtoupper($_SESSION['cste']['_DIMS_LABEL_LINKS']); ?>
		</a>
	</li>
	<li><a href="<?= module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&articleid=".$this->getId()."&wce_mode=edit"."&lang=".$this->fields['id_lang']); ?>"><?= $_SESSION['cste']['_DIMS_BACK']; ?></a></li>
</ul>
<?php
 */
?>
<div>
<form method="POST" action="<?= module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.$params_op); ?>" name="changeLang">
	<label>
		<? echo $_SESSION['cste']['_DIMS_LABEL_LANG']; ?> :&nbsp;
	</label>
	<select name="lang" onchange="javascript:document.changeLang.submit();">
		<?
		foreach($this->getListArticleLangVersion() as $lan){
			?>
			<option <? echo ($lan->fields['id'] == $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])?'selected=true':''; ?> value="<? echo $lan->fields['id']; ?>">
				<? echo $lan->fields['label']; ?>
			</option>
			<?
		}
		?>
	</select>
</form>
<?php

switch($params_op){
	case module_wiki::_CHANGEPOS_REFERENCES:
		$ref = new wce_reference();
		$id_ref = dims_load_securvalue('id_reference',dims_const::_DIMS_NUM_INPUT,true,true);
		$sens=dims_load_securvalue('sens',dims_const::_DIMS_NUM_INPUT,true,true);

		if ($id_ref>0) {
			$ref->open($id_ref);

			$lstRef = $this->getReferences();
			$nbref=sizeof($lstRef);

			if ($sens==1) {
				$ref->updatePosition('',$ref->fields['position']-1);
			}
			elseif ($ref->fields['position']<$nbref) {
				$ref->updatePosition('',$ref->fields['position']+1);
			}
		}

		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCES));
		break;

	case module_wiki::_SAVE_REFERENCES:
		$ref = new wce_reference();
		$id_ref = dims_load_securvalue('id_reference',dims_const::_DIMS_NUM_INPUT,true,true);
		$oldposition=dims_load_securvalue('id_oldposition',dims_const::_DIMS_NUM_INPUT,true,true);

		if ($id_ref>0) {
			$ref->open($id_ref);
		}
		else {
			$ref = new wce_reference();
			$ref->init_description();
			$ref->fields['id_module']=$this->fields['id_module'];
			$ref->fields['id_article']=$this->getId();
			$ref->fields['position']=$oldposition;
		}

		$ref->updatePosition("reference_position");

		// on alloue
		$ref->setvalues($_POST,"reference_");


		$ref->fields['id_article']=$this->getId();
		$ref->setugm();
		$ref->save();
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCES."&lang=".$ref->fields['id_lang']));
		break;
	case module_wiki::_ADD_REFERENCES:
		$id_ref = dims_load_securvalue('id_reference',dims_const::_DIMS_NUM_INPUT,true,true);

		$ref = new wce_reference();
		if ($id_ref>0) $ref->open($id_ref);
		else {
			// on lui attribue une position max
			$ref->init_description();
			$ref->fields['id_module']=$this->fields['id_module'];
			$ref->fields['id_article']=$this->getId();
			$ref->fields['id_lang'] = $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];
			$ref->fields['position']=$ref->getMaxPosition()+1;
		}

		$ref->setLightAttribute('action_path', module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_SAVE_REFERENCES));
		$ref->display(module_wiki::getTemplatePath('/article/parameters/references_add.tpl.php'));
		break;
	case module_wiki::_REFERENCES:
		$this->display(module_wiki::getTemplatePath('/article/parameters/references.tpl.php'));
		break;
	case module_wiki::_DUPLICATE_REFERENCES:
		$id_ref = dims_load_securvalue('id_ref',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id_ref != '' && $id_ref > 0){
			$ref = new wce_reference();
			$ref->open($id_ref);
			$newRef = new wce_reference();
			$newRef->fields = $ref->fields;
			$newRef->fields['id_lang'] = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true);
			$newRef->fields['id'] = 0;
			$newRef->setugm();
			$newRef->fields['position'] = $newRef->getMaxPosition()+1;
			$newRef->save();
			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCES."&lang=".$ref->fields['id_lang']));
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCES));
		break;
	default:
	case module_wiki::_SHOW_INFO_GENERALES:
		$this->setLightAttribute('action_path', module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_SAVE_PARAMETERS));
		$this->setLightAttribute('redirect_path', module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_SHOW_INFO_GENERALES."&lang=".$this->fields['id_lang']));
		$this->display(module_wiki::getTemplatePath('/article/parameters/infos_generales.tpl.php'));
		break;

	case module_wiki::_SAVE_PARAMETERS:
		$id_go = dims_load_securvalue('id_globalobject',dims_const::_DIMS_NUM_INPUT,true,true);
		if (isset($this->fields['id_globalobject'])){
			$this->setvalues($_POST,"article_");
			if(isset($_POST['article_timestp_published'])){
				$dd = explode('/',$this->fields['timestp_published']);
				if(count($dd) == 3)
					$this->fields['timestp_published'] = $dd[2].$dd[1].$dd[0]."000000";
			}
			if(isset($_POST['article_timestp_unpublished'])){
				$dd = explode('/',$this->fields['timestp_unpublished']);
				if(count($dd) == 3)
					$this->fields['timestp_unpublished'] = $dd[2].$dd[1].$dd[0]."000000";
			}
			$this->save();

			//gestion des tags
			//on supprime d'abord tous les éventuels lien de l'article
			tag::removeAllTagsOn($id_go);
			$tags = dims_load_securvalue($_POST['item']['tags'], dims_const::_DIMS_CHAR_INPUT, true, true);
			if(!empty($tags)){
				foreach($tags as $tagname){
					$tag_trimmed = trim($tagname);
					if(!empty($tag_trimmed)){
						$tag = new tag();
						$tag->openWithTagName($tag_trimmed, $_SESSION['dims']['workspaceid']);
						if($tag->isNew()){//on le crée
							$tag->init_description();
							$tag->setugm();
							$tag->fields['tag'] = $tag_trimmed;
							$tag->save();
						}
						$tag->linkToGlobalObject((int)$this->fields['id_globalobject']);
					}
				}
			}

			//gestion de la catégorie
			$id_categ = dims_load_securvalue('id_categ',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if (! empty( $id_categ) ){
				$categ = new category();
				$categ->open($id_categ);

				$gb = new dims_globalobject();
				$gb->open($this->fields['id_globalobject']);
				$gb->deleteLink($this->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY));
				$gb->addLink($categ->fields['id_globalobject']);
			}
		}
		$redirect_path = dims_load_securvalue('redirect_path',dims_const::_DIMS_CHAR_INPUT,true,true,false);
		if(!empty($redirect_path))
			dims_redirect(module_wiki::getScriptEnv($redirect_path));
		else dims_redirect(module_wiki::getScriptEnv());
		break;

	case module_wiki::_REFERENCING:
		$this->setLightAttribute('action_path', module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_SAVE_PARAMETERS."&lang=".$this->fields['id_lang']));
		$this->setLightAttribute('redirect_path', module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_REFERENCING."&lang=".$this->fields['id_lang']));
		$this->display(module_wiki::getTemplatePath('/article/parameters/referencing.tpl.php'));
		break;
	case module_wiki::_LINKS:
		$this->display(module_wiki::getTemplatePath('/article/parameters/links.tpl.php'));
		break;
	case module_wiki::_REPLACE_LINKS:
		if(isset($_POST['internals']) && count($_POST['internals']) > 0){
			$id_replace = dims_load_securvalue('id_replace',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($id_replace != '' && $id_replace > 0){
				$internals = dims_load_securvalue('internals', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($internals as $id) {
					$this->replaceLinks($id,$id_replace);
				}
			}
		}elseif(isset($_POST['externals']) && count($_POST['externals']) > 0){
			$id_replace = dims_load_securvalue('id_replace2',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($id_replace != '' && $id_replace > 0){
				$externals = dims_load_securvalue('externals', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($externals as $id){
					$art = new wce_article();
					$art->open($id);
					$art->replaceLinks($this->fields['id'],$id_replace);
				}
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_LINKS));
		break;
	case module_wiki::_DELETE_LINKS:
		if(isset($_POST['internals']) && count($_POST['internals']) > 0){
			$internals = dims_load_securvalue('internals', dims_const::_DIMS_NUM_INPUT, true, true, true);
			foreach($internals as $id) {
				$this->deleteLinks($id);
			}
		}elseif(isset($_POST['externals']) && count($_POST['externals']) > 0){
			$externals = dims_load_securvalue('externals', dims_const::_DIMS_NUM_INPUT, true, true, true);
			foreach($externals as $id){
				$art = new wce_article();
				$art->open($id);
				$art->deleteLinks($this->fields['id']);
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_LINKS));
		break;
}
