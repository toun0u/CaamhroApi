<?
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_article_object.php';
require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow.php";
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
?>
<div class="title_h2">
	<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_obj_dynamique.png'); ?>" />
	<h2>
		<? echo $_SESSION['cste']['_DYNAMIC_OBJECTS']; ?>
	<?
	switch($action){
		default:
		case module_wce::_DYN_DEF:
			echo " : ".$_SESSION['cste']['_ACTIVE_OBJECTS'];
			?>
			</h2>
			<div class="actions">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_EDIT; ?>">
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_add_obj_dynamique.png'); ?>" alt="<? echo $_SESSION['cste']['_ADD_OBJECT']; ?>" title="<? echo $_SESSION['cste']['_ADD_OBJECT']; ?>" />
				</a>
			</div>
			<?
			break;
		case module_wce::_DYN_OBJ_EDIT:
		case module_wce::_DYN_OBJ_VIEW:
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$obj = new article_object();
			if ($id != '' && $id > 0){
				$obj->open($id);
				echo " : ".$obj->fields['label'];
			}
			?>
			</h2>
			<?
			break;
		case module_wce::_DYN_OBJ_EDIT_BREVE:
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$obj = new article_object();
			if ($id != '' && $id > 0){
				$obj->open($id);
				echo " : ".$obj->fields['label'];
				$id_obj = dims_load_securvalue('id_obj',dims_const::_DIMS_NUM_INPUT,true,true,false);
				$obj = new wce_article();
				if ($id_obj != '' && $id_obj > 0){
					$obj->open($id_obj);
					echo " > ".$obj->fields['title'];
				}
			}
			?>
			</h2>
			<?
			break;
		case module_wce::_DYN_OBJ_EDIT_ART:
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$obj = new article_object();
			if ($id != '' && $id > 0){
				$obj->open($id);
				echo " : ".$obj->fields['label'];
			}
			?>
			</h2>
			<?
			break;
		case module_wce::_DYN_SLID_EDIT:
		case module_wce::_DYN_SLID_VIEW:
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$obj = new wce_slideshow();
				$obj->open($id);
				echo " : ".$obj->fields['nom'];
			}
			?>
			</h2>
			<?
			break;
		case module_wce::_DYN_SLID_EDIT_ELEM:
			$id_elem = dims_load_securvalue('id_obj', dims_const::_DIMS_NUM_INPUT, true, true, true);
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow_element.php";
			$elem = new wce_slideshow_element();
			if ($id_elem != '' && $id_elem > 0){
				$elem->open($id_elem);
				echo " : ".$elem->fields['titre'];
			}
			?>
			</h2>
			<?
			break;
		case 'edit_rss':
			echo " : ".$_SESSION['cste']['_RSS_ADD'];
			break;
	}
	?>
</div>
<?php
switch($action){
	default:
	case module_wce::_DYN_DEF:
		require_once module_wce::getTemplatePath("obj_dynamics/lst_objects.tpl.php");
		require_once module_wce::getTemplatePath("obj_dynamics/lst_slideshows.tpl.php");
		break;
// -- Bloc des objets
	case module_wce::_DYN_OBJ_EDIT:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$obj = new article_object();
		if ($id != '' && $id > 0)
			$obj->open($id);
		else
			$obj->init_description();
		$obj->display(module_wce::getTemplatePath("obj_dynamics/edit_object.tpl.php"));
		break;
	case module_wce::_DYN_OBJ_VIEW:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$obj = new article_object();
			$obj->open($id);
			$obj->display(module_wce::getTemplatePath("obj_dynamics/display_object.tpl.php"));
		}else
			dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_EDIT);
		break;
	case module_wce::_DYN_OBJ_DEL:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$obj = new article_object();
		if ($id != '' && $id > 0){
			$obj->open($id);
			$obj->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_DEF);
		break;
	case module_wce::_DYN_OBJ_SAVE:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$obj = new article_object();
		if ($id != '' && $id > 0)
			$obj->open($id);
		else{
			$obj->init_description();
			$obj->setugm();
		}
		$obj->fields['pubfin_dependant'] = 0;
		$obj->setvalues($_POST,'obj_');

		if($obj->new && $obj->fields['type'] == WCE_OBJECT_TYPE_NEWSLETTER) {
			require_once DIMS_APP_PATH.'modules/system/class_mailinglist.php';
			$mailing_list = new mailinglist();
			$mailing_list->init_description();
			$mailing_list->fields['label']          = $obj->fields['label'].' - Inscription';
			$mailing_list->fields['public']         = 0;
			$mailing_list->fields['id_workspace']   = $_SESSION['dims']['workspaceid'];
			$mailing_list->save();

			$obj->fields['id_maillinglist'] = $mailing_list->fields['id'];
		}
		$obj->save();
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_DEF);
		break;
	// - Bloc objets : Brèves
	case module_wce::_DYN_OBJ_EDIT_BREVE:
		$id_obj = dims_load_securvalue('id_obj',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$obj = new wce_article();
		if ($id_obj != '' && $id_obj > 0){
			$lg = dims_load_securvalue('lg',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if($lg != '' && $lg > 0)
				$obj->open($id_obj,$lg);
			else
				$obj->open($id_obj);
		}else{
			$obj->init_description();
			$lg = dims_load_securvalue('lg',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if($lg != '' && $lg > 0)
				$obj->fields['id_lang'] = $lg;
		}
		$obj->setLightAttribute('returnId',dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false));
		$obj->display(module_wce::getTemplatePath("obj_dynamics/edit_object_breve.tpl.php"));
		break;
	case module_wce::_DYN_OBJ_SAVE_BREVE:
		$id_obj = dims_load_securvalue('id_obj',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$obj = new wce_article();
		$db = dims::getInstance()->getDb();
		if ($id_obj != '' && $id_obj > 0){
			$lg = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if($lg != '' && $lg > 0)
				$obj->open($id_obj,$lg);
			else
				$obj->open($id_obj);
		}else{
			$obj->init_description();
			$lg = dims_load_securvalue('id_lang',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if($lg != '' && $lg > 0)
				$obj->fields['id_lang'] = $lg;
			else{
				$wce_site= new wce_site(dims::getInstance()->getDb(),$_SESSION['dims']['moduleid']);
				$obj->fields['id_lang'] = $wce_site->getDefaultLanguage();
			}
			$obj->setugm();
			$select = "	SELECT 	MAX(position) as maxpos
						FROM 	".wce_article::TABLE_NAME."
						WHERE 	id_heading = 0
						AND 	id_module=:id_module";
			$res = $db->query($select,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
			$obj->fields['position'] = 1;
			if($r = $db->fetchrow($res))
				$obj->fields['position'] += $r['maxpos'];
			$obj->fields['id_heading'] = 0;
		}
		$obj->setvalues($_POST,'obj_');
		if($obj->fields['reference'] == "" && $obj->fields['title'] != "") {
			$obj->fields['reference']=$obj->fields['title'];
		}
		$obj->fields['timestp'] = dims_createtimestamp();
		if ($obj->fields['timestp_published'] != '') $obj->fields['timestp_published'] = dims_local2timestamp($obj->fields['timestp_published']);
		if ($obj->fields['timestp_unpublished'] != '') $obj->fields['timestp_unpublished'] = dims_local2timestamp($obj->fields['timestp_unpublished']);
		$obj->fields['lastupdate_timestp'] = dims_createtimestamp();
		$obj->fields['lastupdate_id_user'] = $_SESSION['dims']['userid'];
		$obj->save();

		// Gestion correspondance
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
		$sel = "SELECT	*
				FROM	".article_object_corresp::TABLE_NAME."
				WHERE	id_article = :id_article
				AND		id_heading = 0";
		$res = $db->query($sel,array(':id_article'=>array('value'=>$obj->fields['id'],'type'=>PDO::PARAM_INT)));
		while($r = $db->fetchrow($res)){
			$corr = new article_object_corresp();
			$corr->openFromResultSet($r);
			$corr->delete();
		}
		if(!empty($_POST['lst_affect'])){
			$lstaffect = dims_load_securvalue('lst_affect', dims_const::_DIMS_NUM_INPUT, true, true, true);
			foreach($lstaffect as $id_aff){
				$objt_corresp = new article_object_corresp();
				$objt_corresp->init_description();
				$objt_corresp->fields['id_article']= $obj->fields['id'];
				$objt_corresp->fields['id_object']= $id_aff;
				$objt_corresp->fields['id_heading']= 0;
				$objt_corresp->save();
			}
		}else{
			$objt_corresp = new article_object_corresp();
			$objt_corresp->init_description();
			$objt_corresp->fields['id_article']= $obj->fields['id'];
			$objt_corresp->fields['id_object']= $id;
			$objt_corresp->fields['id_heading']= 0;
			$objt_corresp->save();
		}

		// Gestion picto
		if (isset($_FILES['photo']) && !empty($_FILES['photo']) && $_FILES['photo']['name']!='') {
		    require_once DIMS_APP_PATH."include/class_input_validator.php";

            $valid = new \InVal\FileValidator('photo');
            $valid->rule(new \InVal\Rule\Image(true));

            if ($valid->validate()) {
    			$time = time();
    			if ($obj->fields['picto']!='' && file_exists(realpath('.').'/data/articles/'.$obj->fields['picto'])) {
    				unlink(realpath('.').'/data/articles/'.$obj->fields['picto']);
    			}

    			$logo_upload = $_FILES['photo'];

    			//on recupere l'extension du fichier
    			$ext = explode('.', $logo_upload['name']);
    			$ext = strtolower($ext[count($ext)-1]);
    			dims_makedir(realpath('.').'/data/articles');

    			$path = realpath('.').'/data/articles/art_'.$obj->fields['id']."_".$time.".".$ext;

    			// on va reziser l'image
    			$pathtemp=$logo_upload['tmp_name'];
    			if (move_uploaded_file($pathtemp,$path)) {
    				chmod($path, 0777);
    			}

    			$pathdest = realpath('.').'/data/articles/art_'.$obj->fields['id']."_500.".$ext;
    			dims_resizeimage($path, 0, 500, 0,'',0,$pathdest);

    			dims_resizeimage($path, 0, 0, 0,'',0,$path,50,50);

    			$obj->fields['picto']='art_'.$obj->fields['id']."_".$time.".".$ext;
    			$obj->save();
            }
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$id);
		break;
	case module_wce::_DYN_OBJ_DEL_BREVE:
		$id_obj = dims_load_securvalue('id_obj',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($id_obj != '' && $id_obj > 0){
			// TODO : peux être récupérer le bon id_lang
			$obj = new wce_article();
			$obj->open($id_obj);
			$obj->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false));
		break;
	// Bloc objets : Articles
	case module_wce::_DYN_OBJ_EDIT_ART:
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
		$def = 0;
		$id_art = dims_load_securvalue('id_art',dims_const::_DIMS_NUM_INPUT,true,true,false,$def);
		$def = 0;
		$id_head = dims_load_securvalue('id_head',dims_const::_DIMS_NUM_INPUT,true,true,false,$def);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$obj = new article_object_corresp();
		if ((($id_head != '' && $id_head) || ($id_art != '' && $id_art)) > 0 && $id != '' && $id > 0){
			$obj->open($id,$id_art,$id_head);
		}else{
			$obj->init_description();
		}
		$obj->setLightAttribute('returnId',$id);
		$obj->display(module_wce::getTemplatePath("obj_dynamics/edit_object_article.tpl.php"));
		break;
	case module_wce::_DYN_OBJ_SAVE_ART:
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
		$id_art = dims_load_securvalue('id_art',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_head = dims_load_securvalue('id_head',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if($id != '' && $id > 0){
			$sel = "";
			$params = array();
			if($id_art != '' && $id_art){
				$sel = "SELECT	*
						FROM	".article_object_corresp::TABLE_NAME."
						WHERE	id_object = :id_object
						AND		id_article = :id_article";
				$params = array(':id_article'=>array('value'=>$id_art,'type'=>PDO::PARAM_INT),
								':id_object'=>array('value'=>$id,'type'=>PDO::PARAM_INT));
			}elseif($id_head != '' && $id_head){
				$sel = "SELECT	*
						FROM	".article_object_corresp::TABLE_NAME."
						WHERE	id_object = :id_object
						AND		id_heading = :id_heading";
				$params = array(':id_heading'=>array('value'=>$id_head,'type'=>PDO::PARAM_INT),
								':id_object'=>array('value'=>$id,'type'=>PDO::PARAM_INT));
			}
			if ($sel != ''){
				$db = dims::getInstance()->getDb();
				$res = $db->query($sel,$params);
				while($r = $db->fetchrow($res)){
					$obj = new article_object_corresp();
					$obj->openFromResultSet($r);
					$obj->delete();
				}
			}
			if(!empty($_POST['lst_affect'])){
				$lstaffect = dims_load_securvalue('lst_affect', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($lstaffect as $id2){
					$objt_corresp = new article_object_corresp();
					$objt_corresp->init_description();
					$objt_corresp->setvalues($_POST,'obj_');
					$objt_corresp->fields['id_object']= $id2;
					$objt_corresp->save();
				}
			}else{
				$objt_corresp = new article_object_corresp();
				$objt_corresp->init_description();
				$objt_corresp->setvalues($_POST,'obj_');
				$objt_corresp->fields['id_object']= $id;
				$objt_corresp->save();
			}
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$id);
		break;
	case module_wce::_DYN_OBJ_DEL_ART:
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_object_corresp.php";
		$def = 0;
		$id_art = dims_load_securvalue('id_art',dims_const::_DIMS_NUM_INPUT,true,true,false,$def);
		$def = 0;
		$id_head = dims_load_securvalue('id_head',dims_const::_DIMS_NUM_INPUT,true,true,false,$def);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ((($id_head != '' && $id_head) || ($id_art != '' && $id_art)) > 0 && $id != '' && $id > 0){
			$obj = new article_object_corresp();
			$obj->open($id,$id_art,$id_head);
			$obj->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$id);
		break;
	// -- Bloc des RSS
	case 'edit_rss':
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_feed.php";
		//$id_obj = dims_load_securvalue('rss',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		//$rss = rss_feed::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id_object'=>$id,'id'=>$id_obj),null,1);
		//if(empty($rss)){
			$rss = new rss_feed();
			$rss->init_description();
			$rss->set('id_object',$id);
		//}
		$rss->display(module_wce::getTemplatePath("obj_dynamics/edit_object_rss.tpl.php"));
		break;
	case 'save_rss':
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_feed.php";
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$rss = new rss_feed();
		$rss->init_description();
		$rss->set('id_object',$id);
		$rss->set('url',trim(dims_load_securvalue('obj_url',dims_const::_DIMS_CHAR_INPUT,true,true,false)));
		$rss->updateInfos();
		$rss->save();
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$id);
		break;
	case 'del_rss':
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_feed.php";
		$id_obj = dims_load_securvalue('rss',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$rss = rss_feed::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id_object'=>$id,'id'=>$id_obj),null,1);
		if(!empty($rss)){
			$rss->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$id);
		break;
	case 'update_rss':
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_feed.php";
		$id_obj = dims_load_securvalue('rss',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$rss = rss_feed::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id_object'=>$id,'id'=>$id_obj),null,1);
		if(!empty($rss)){
			$rss->updateCache();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$id);
		break;
	case 'infos_rss':
		ob_clean();
		$url = trim(dims_load_securvalue('url',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		$return = array();
		if($url != ''){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_rss_feed.php";
			$rss = new rss_feed();
			$rss->init_description();
			$rss->set('url',$url);
			if($rss->updateInfos() !== false){
				$return = array(
					'title'=>$rss->get('title'),
					'description'=>$rss->get('description'),
					'link'=>$rss->get('link'),
					'ico'=>$rss->get('ico'),
				);
			}
		}
		ob_clean();
		echo json_encode($return);
		die();
		break;
	// -- Bloc des slideshows
	case module_wce::_DYN_SLID_EDIT:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$obj = new wce_slideshow();
		if ($id != '' && $id > 0)
			$obj->open($id);
		else
			$obj->init_description();
		$obj->display(module_wce::getTemplatePath("obj_dynamics/edit_slideshow.tpl.php"));
		break;
	case module_wce::_DYN_SLID_VIEW:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$obj = new wce_slideshow();
			$obj->open($id);
			$obj->display(module_wce::getTemplatePath("obj_dynamics/display_slideshow.tpl.php"));
		}else
			dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_DEF);
		break;
	case module_wce::_DYN_SLID_DEL:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$obj = new wce_slideshow();
			$obj->open($id);
			$obj->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_DEF);
		break;
	case module_wce::_DYN_SLID_SAVE:
		$id_slideshow = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);

        $slideshow = new wce_slideshow();
        $slideshow->init_description();

        if($id_slideshow != '' && $id_slideshow > 0) {
			$slideshow->open($id_slideshow);
        }else{
            $slideshow->setugm();
        }
		$slideshow->setvalues($_POST, 'obj_');
		$slideshow->save();
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_VIEW."&id=".$slideshow->fields['id']);
		break;
	case module_wce::_DYN_SLID_EDIT_ELEM:
		$id_slideshow = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_elem = dims_load_securvalue('id_obj', dims_const::_DIMS_NUM_INPUT, true, true, true);
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow_element.php";
		$elem = new wce_slideshow_element();
		if ($id_elem != '' && $id_elem > 0){
			$elem->open($id_elem);
		}else{
			$elem->init_description();
			$elem->fields['id_slideshow'] = $id_slideshow;
			$elem->fields['lien'] = "http://";
		}
		$slideshow = new wce_slideshow();
		$slideshow->open($id_slideshow);
		$elem->setLightAttribute('template',$slideshow->fields['template']);
		$elem->display(module_wce::getTemplatePath("obj_dynamics/edit_slideshow_elem.tpl.php"));
		break;
	case module_wce::_DYN_SLID_DEL_ELEM:
		$id_slideshow = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_elem = dims_load_securvalue('id_obj', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if ($id_elem != '' && $id_elem > 0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow_element.php";
			$elem = new wce_slideshow_element();
			$elem->open($id_elem);
			$elem->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_VIEW."&id=".$id_slideshow);
		break;
	case module_wce::_DYN_SLID_UP_ELEM:
		$id_slideshow = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_elem = dims_load_securvalue('id_obj', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if ($id_elem != '' && $id_elem > 0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow_element.php";
			$elem = new wce_slideshow_element();
			$elem->open($id_elem);
			$elem->positionUp();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_VIEW."&id=".$id_slideshow);
		break;
	case module_wce::_DYN_SLID_DOWN_ELEM:
		$id_slideshow = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_elem = dims_load_securvalue('id_obj', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if ($id_elem != '' && $id_elem > 0){
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow_element.php";
			$elem = new wce_slideshow_element();
			$elem->open($id_elem);
			$elem->positionDown();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_VIEW."&id=".$id_slideshow);
		break;
	case module_wce::_DYN_SLID_SAVE_ELEM: // kevin
		$id_elem = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);

		require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow_element.php";
		$elem = new wce_slideshow_element();
		$tmstp = dims_createtimestamp();
		if ($id_elem != '' && $id_elem > 0){
			$elem->open($id_elem);
		}else{
			$elem->init_description();
			$elem->setugm();
			$elem->fields['timestp_create'] = $tmstp;
		}
		$elem->setvalues($_POST,'obj_');
		if(trim($elem->fields['lien']) == 'http://')
			$elem->fields['lien'] = "";
		$elem->fields['timestp_modify'] = $tmstp;

		require_once DIMS_APP_PATH."modules/doc/class_docfile.php";
		if (isset($_SESSION['dims']['uploaded_sid']) && $_SESSION['dims']['uploaded_sid']!="") {
			//$sid = $_SESSION['dims']['uploaded_sid'];//session_id();
			unset($_SESSION['dims']['uploaded_sid']);
		}

		$upload_dir = _DIMS_PATHDATA."/uploads/".$tmstp."/";

		$global_error = false;
		$typeMedia = 0;
		$nomMedia = $tmstp;

		if (isset($_FILES['document']) AND $_FILES['document']['error'] == 0) {
			$doc = new docfile();

			$doc->setugm();
			$doc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

			$doc->fields['id_folder'] = -1;
			$doc->tmpuploadedfile = $_FILES['document']['tmp_name'];
			$doc->fields['name'] = $_FILES['document']['name'];
			$doc->fields['size'] = filesize($_FILES['document']['tmp_name']);

			$error = $doc->save();

			if(!$error) {
				if(!$elem->new && $elem->fields['image']) {
					$old_miniature = new docfile();
					$old_miniature->open($elem->fields['image']);
					$old_miniature->delete();
				}
				$elem->fields['image'] = $doc->fields['id'];
			}
		}

		// if (is_dir( _DIMS_PATHDATA."/uploads/".$sid)) {
		// 	$docfile = new docfile();
		// 	$docfile->init_description();
		// 	$docfile->setugm();
		// 	if ($dh = opendir($upload_dir)) {
		// 		$isVideo = false;
		// 		while (($filename = readdir($dh)) !== false) {
		// 			if ($filename!="." && $filename!="..") {
		// 				if (!$isVideo){
		// 					$docfile->fields['id_folder'] = 0;
		// 					$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
		// 					$docfile->tmpuploadedfile = $upload_dir.$filename;
		// 					$docfile->fields['name'] = $filename;
		// 					$docfile->fields['version'] = 1;
		// 					$docfile->fields['size'] = filesize($upload_dir.$filename);
		// 					if (in_array(strtolower(pathinfo($upload_dir.$filename, PATHINFO_EXTENSION)), array("mp4","ogv","webm"))) {
		// 						$typeMedia = 4;
		// 					}
		// 					$error = $docfile->save();
		// 					$docfile->publishDocFile();
		// 					if( !$error){
		// 						if(!$elem->new && $elem->fields['image']) {
		// 							$old_image = new docfile();
		// 							if ($old_image->open($elem->fields['image'])) {
		// 								$old_image->delete();
		// 							}
		// 						}

		// 						$pathToMedia = $docfile->getwebpath();

		// 						if(($typeMedia == 4)){
		// 							$isVideo = true;

		// 							passthru("ffprobe $pathToMedia -show_streams $1 2>/dev/null | grep 'width=' | cut -d'=' -f2");
		// 							$width = rtrim(ob_get_contents());
		// 							ob_end_clean();

		// 							ob_start();
		// 							passthru("ffprobe $pathToMedia -show_streams $1 2>/dev/null | grep 'height=' | cut -d'=' -f2");
		// 							$height = rtrim(ob_get_contents());
		// 							ob_end_clean();

		// 							$docfile->fields['resolution'] = $width."x".$height;

		// 							// copie en tant que preview
		// 							$path = pathinfo($docfile->getwebpath());
		// 							copy($docfile->getwebpath(),$path['dirname']."/_preview_".$path['filename'].".".$docfile->fields['extension']);

		// 							// génération de la miniature
		// 							ob_start();
		// 							exec(escapeshellcmd("ffmpeg -y -itsoffset -4  -i ".$docfile->getwebpath()." -vcodec mjpeg -vframes 1 -an -f rawvideo -s 640x480 ".$path['dirname']."/_preview_".$path['filename'].".jpg"));
		// 							ob_end_clean();
		// 						}
		// 						$elem->fields['image'] = $docfile->fields['id'];
		// 					} else {
		// 						$global_error = true;
		// 					}
		// 				}
		// 				elseif($docfile->fields['id'] > 0 && $isVideo){
		// 					$path = pathinfo($docfile->getwebpath());
		// 					$path2 = pathinfo($filename);
		// 					rename($upload_dir.$filename,$path['dirname']."/_preview_".$path['filename'].".".$path2['extension']);
		// 				}
		// 			}
		// 		}
		// 		closedir($dh);
		// 	}

		// 	rmdir($upload_dir);
		// 	//suppression du dossier temp
		// 	rmdir($tmp_dir);
		//}

		// Miniature
		if(isset($_FILES['elem_miniature']) && !$_FILES['elem_miniature']['error']) {
            require_once DIMS_APP_PATH."include/class_input_validator.php";

            $valid = new \InVal\FileValidator('elem_miniature');
            $valid->rule(new \InVal\Rule\Image(true));

            if ($valid->validate()) {
    			$miniature = new docfile();

    			$miniature->setugm();
    			$miniature->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

    			$miniature->fields['id_folder'] = -1;
    			$miniature->tmpuploadedfile = $_FILES['elem_miniature']['tmp_name'];
    			$miniature->fields['name'] = $_FILES['elem_miniature']['name'];
    			$miniature->fields['size'] = filesize($_FILES['elem_miniature']['tmp_name']);

    			$error = $miniature->save();

    			if(!$error) {
    				if(!$elem->new && $elem->fields['miniature']) {
    					$old_miniature = new docfile();
    					$old_miniature->open($elem->fields['miniature']);
    					$old_miniature->delete();
    				}
    				$elem->fields['miniature'] = $miniature->fields['id'];
    			}
            }
		}
		$elem->save();
		dims_redirect(module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_SLID_VIEW."&id=".$elem->fields['id_slideshow']);
		break;
	case 'breve_deletepicto':
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$articleid=dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true);
			$id_return=dims_load_securvalue('id_return',dims_const::_DIMS_NUM_INPUT,true,true);
			if ($articleid>0) {
				$article = new wce_article();
				// test de s&eacute;curit&eacute; si article appartient &eacute; l'utilisateur
				$article->open($articleid);
				if ($article->fields['picto']!='' && file_exists(realpath('.').'/data/articles/'.$article->fields['picto'])) {
					unlink(realpath('.').'/data/articles/'.$article->fields['picto']);
				}
				$article->fields['picto']='';
				$article->save();
			}
		}
		dims_redirect("$scriptenv?action=breve_edit&id=".$id_return."&id_obj=".$articleid);
		break;
}
?>
