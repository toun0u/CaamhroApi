<?php
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slideshow.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_cloud.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart.php';

$view = view::getInstance();
$view->setLayout('layouts/objects_layout.tpl.php');
$view->render('objects/sidebar.tpl.php', 'params_sidebar');

// infos contexuelles
$view->assign('a', $a);

switch ($a) {
	default:
	// Slideshows
	case 'slide':
		$view->assign('slideshows',slideshow::getAll());
		$view->render('objects/lst_slideshows.tpl.php');
		break;
	case 'editslid':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$slide = new slideshow();
		if($id != '' && $id > 0)
			$slide->open($id);
		else
			$slide->init_description();
		$tpl_list = cata_wceslideshows_gettpl();
		$view->assign('slide_tpl',array_combine($tpl_list,$tpl_list));
		$view->assign('elem',$slide);
		$view->render('objects/edit_slideshows.tpl.php');
		break;
	case 'saveslid':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$slide = new slideshow();
		if($id != '' && $id > 0){
			$slide->open($id);
			$slide->fields['timestp_modify'] = dims_createtimestamp();
		}else{
			$slide->init_description();
			$slide->fields['timestp_create'] = $slide->fields['timestp_modify'] = dims_createtimestamp();
			$slide->setugm();
		}
		$slide->setvalues($_POST,'slide_');
		$slide->save();
		dims_redirect(get_path('objects', 'showslid', array('id'=>$slide->get('id'))));
		break;
	case 'delslid':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			$slide = new slideshow();
			$slide->open($id);
			if($slide->fields['id_module'] == $_SESSION['dims']['moduleid'])
				$slide->delete();
		}
		dims_redirect(get_path('objects', 'slide'));
	case 'showslid':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			$slide = new slideshow();
			$slide->open($id);
			if($slide->fields['id_module'] == $_SESSION['dims']['moduleid']){
				$view->assign('elem',$slide);

				$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
				switch ($sa) {
					case 'addelemslid':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$elem = new slideshow_element();
						if($sid != '' && $sid > 0)
							$elem->open($sid);
						else{
							$elem->init_description();
							$elem->fields['id_slideshow'] = $slide->get('id');
						}
						$view->assign('elems',$elem);

						$a_descr_positions = array(
							'left'		=> dims_constant::getVal('_LEFT'),
							'right'		=> dims_constant::getVal('_RIGHT'),
							'top'		=> dims_constant::getVal('_TOP'),
							'bottom'	=> dims_constant::getVal('_BOTTOM'),
						);
						$view->assign('a_descr_positions',$a_descr_positions);

						$view->render('objects/edit_slideshows_elem.tpl.php');
						break;
					case 'rightelemslid':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new slideshow_element();
							$elem->open($sid);
							$elem->downElem();
						}
						dims_redirect(get_path('objects', 'showslid', array('id'=>$slide->get('id'))));
						break;
					case 'leftelemslid':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new slideshow_element();
							$elem->open($sid);
							$elem->upElem();
						}
						dims_redirect(get_path('objects', 'showslid', array('id'=>$slide->get('id'))));
						break;
					case 'delelemslid':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new slideshow_element();
							$elem->open($sid);
							$elem->delete();
						}
						dims_redirect(get_path('objects', 'showslid', array('id'=>$slide->get('id'))));
						break;
					case 'saveelemslid':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$elem = new slideshow_element();
						if($sid != '' && $sid > 0){
							$elem->open($sid);
							$elem->fields['timestp_modify'] = dims_createtimestamp();
						}else{
							$elem->init_description();
							$elem->fields['timestp_create'] = $elem->fields['timestp_modify'] = dims_createtimestamp();
							$elem->setugm();
						}
						$elem->fields['connected_only'] = 0;
						$elem->setvalues($_POST,'elem_');

						if(isset($_FILES) && !empty($_FILES)) {
							if(isset($_FILES['elem_image']) && !$_FILES['elem_image']['error']) {

								$image = new docfile();

								$image->setugm();
								$image->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

								$image->fields['id_folder'] = -1;
								$image->tmpuploadedfile = $_FILES['elem_image']['tmp_name'];
								$image->fields['name'] = $_FILES['elem_image']['name'];
								$image->fields['size'] = filesize($_FILES['elem_image']['tmp_name']);

								$error = $image->save();

								if(!$error) {
									if(!$elem->isNew() && $elem->fields['image']) {
										$old_image = new docfile();
										$old_image->open($elem->fields['image']);
										$old_image->delete();
									}
									$elem->fields['image'] = $image->fields['id'];
								}
							}
							if(isset($_FILES['elem_miniature']) && !$_FILES['elem_miniature']['error']) {

								$miniature = new docfile();

								$miniature->setugm();
								$miniature->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

								$miniature->fields['id_folder'] = -1;
								$miniature->tmpuploadedfile = $_FILES['elem_miniature']['tmp_name'];
								$miniature->fields['name'] = $_FILES['elem_miniature']['name'];
								$miniature->fields['size'] = filesize($_FILES['elem_miniature']['tmp_name']);

								$error = $miniature->save();

								if(!$error) {
									if(!$elem->isNew() && $elem->fields['miniature']) {
										$old_miniature = new docfile();
										$old_miniature->open($elem->fields['miniature']);
										$old_miniature->delete();
									}
									$elem->fields['miniature'] = $miniature->fields['id'];
								}
							}
						}
						$elem->save();
						dims_redirect(get_path('objects', 'showslid', array('id'=>$slide->get('id'))));
						break;
					default:
						$view->assign('elements',$slide->getElements());
						$view->render('objects/show_slideshows.tpl.php');
						break;
				}
			}else
				dims_redirect(get_path('objects', 'slide'));
		}else
			dims_redirect(get_path('objects', 'slide'));
		break;

	// Tag cloud
	case 'tags':
		$view->assign('clouds',cloud::getAll());
		$view->render('objects/lst_tag_cloud.tpl.php');
		break;
	case 'edittags':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$cloud = new cloud();
		if($id != '' && $id > 0)
			$cloud->open($id);
		else
			$cloud->init_description();
		$view->assign('elem',$cloud);
		$view->render('objects/edit_tag_cloud.tpl.php');
		break;
	case 'showtag':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			$cloud = new cloud();
			$cloud->open($id);
			if($cloud->fields['id_module'] == $_SESSION['dims']['moduleid']){
				$view->assign('elem',$cloud);

				$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
				switch ($sa) {
					case 'addelemtag':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$elem = new cloud_element();
						if($sid != '' && $sid > 0)
							$elem->open($sid);
						else{
							$elem->init_description();
							$elem->fields['id_cloud'] = $cloud->get('id');
						}
						$view->assign('elems',$elem);

						$view->render('objects/edit_tag_cloud_elem.tpl.php');
						break;
					case 'delelemtag':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new cloud_element();
							$elem->open($sid);
							$elem->delete();
						}
						dims_redirect(get_path('objects', 'showtag', array('id'=>$cloud->get('id'))));
						break;
					case 'saveelemtag':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$elem = new cloud_element();
						if($sid != '' && $sid > 0){
							$elem->open($sid);
							$elem->fields['timestp_modify'] = dims_createtimestamp();
						}else{
							$elem->init_description();
							$elem->fields['timestp_create'] = $elem->fields['timestp_modify'] = dims_createtimestamp();
							$elem->setugm();
						}
						$elem->setvalues($_POST,'elem_');
						$elem->save();
						dims_redirect(get_path('objects', 'showtag', array('id'=>$cloud->get('id'))));
						break;
					default:
						$view->assign('elements',$cloud->getElements());
						$view->render('objects/show_tag_cloud.tpl.php');
						break;
				}
			}else
				dims_redirect(get_path('objects', 'tags'));
		}else
			dims_redirect(get_path('objects', 'tags'));
		break;
	case 'deltag':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			$cloud = new cloud();
			$cloud->open($id);
			if($cloud->fields['id_module'] == $_SESSION['dims']['moduleid'])
				$cloud->delete();
		}
		dims_redirect(get_path('objects', 'tags'));
		break;
	case 'savetag':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$cloud = new cloud();
		if($id != '' && $id > 0){
			$cloud->open($id);
			$cloud->fields['timestp_modify'] = dims_createtimestamp();
		}else{
			$cloud->init_description();
			$cloud->fields['timestp_create'] = $cloud->fields['timestp_modify'] = dims_createtimestamp();
			$cloud->setugm();
		}
		$cloud->setvalues($_POST,'tag_');
		$cloud->save();
		dims_redirect(get_path('objects', 'showtag', array('id'=>$cloud->get('id'))));
		break;

	// Slideshow article
	case 'slidart':
		$view->assign('slidart',slidart::getAll());
		$view->render('objects/lst_slidart.tpl.php');
		break;
	case 'showart':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			$slidart = new slidart();
			$slidart->open($id);
			if($slidart->fields['id_module'] == $_SESSION['dims']['moduleid']){
				$view->assign('elem',$slidart);

				$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
				switch ($sa) {
					case 'addelemart':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$elem = new slidart_element();
						if($sid != '' && $sid > 0)
							$elem->open($sid);
						else{
							$elem->init_description();
							$elem->fields['id_slidart'] = $slidart->get('id');
						}
						$view->assign('elems',$elem);

						$view->render('objects/edit_slidart_elem.tpl.php');
						break;
					case 'delelemart':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new slidart_element();
							$elem->open($sid);
							$elem->delete();
						}
						dims_redirect(get_path('objects', 'showart', array('id'=>$slidart->get('id'))));
						break;
					case 'rightelemart':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new slidart_element();
							$elem->open($sid);
							$elem->downElem();
						}
						dims_redirect(get_path('objects', 'showart', array('id'=>$slidart->get('id'))));
						break;
					case 'leftelemart':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						if($sid != '' && $sid > 0){
							$elem = new slidart_element();
							$elem->open($sid);
							$elem->upElem();
						}
						dims_redirect(get_path('objects', 'showart', array('id'=>$slidart->get('id'))));
						break;
					case 'saveelemart':
						$sid = dims_load_securvalue('sid',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$ref_article = dims_load_securvalue('ref_article',dims_const::_DIMS_NUM_INPUT,true,true,true);
						$elem = new slidart_element();
						if($sid != '' && $sid > 0 && $ref_article != '' && $ref_article > 0){
							$elem->open($sid);
							$elem->fields['timestp_modify'] = dims_createtimestamp();
						}else{
							$elem->init_description();
							$elem->fields['timestp_create'] = $elem->fields['timestp_modify'] = dims_createtimestamp();
							$elem->setugm();
						}
						$elem->setvalues($_POST,'elem_');
						$art = new article();
						$art->open($ref_article);
						$elem->fields['ref'] = $art->fields['reference'];
						$elem->fields['id_article'] = $art->fields['id'];
						$elem->save();
						dims_redirect(get_path('objects', 'showart', array('id'=>$slidart->get('id'))));
						break;
					default:
						$view->assign('elements',$slidart->getElements());
						$view->render('objects/show_slidart.tpl.php');
						break;
				}
			}else
				dims_redirect(get_path('objects', 'slidart'));
		}else
			dims_redirect(get_path('objects', 'slidart'));
		break;
	case 'editart':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$slidart = new slidart();
		if($id != '' && $id > 0)
			$slidart->open($id);
		else
			$slidart->init_description();
		$view->assign('elem',$slidart);
		$view->render('objects/edit_slidart.tpl.php');
		break;
	case 'saveart':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$slidart = new slidart();
		if($id != '' && $id > 0){
			$slidart->open($id);
			$slidart->fields['timestp_modify'] = dims_createtimestamp();
		}else{
			$slidart->init_description();
			$slidart->fields['timestp_create'] = $slidart->fields['timestp_modify'] = dims_createtimestamp();
			$slidart->setugm();
		}
		$slidart->setvalues($_POST,'slidart_');
		$slidart->save();
		dims_redirect(get_path('objects', 'showart', array('id'=>$slidart->get('id'))));
		break;
	case 'delart':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			$slidart = new slidart();
			$slidart->open($id);
			if($slidart->fields['id_module'] == $_SESSION['dims']['moduleid'])
				$slidart->delete();
		}
		dims_redirect(get_path('objects', 'slidart'));
		break;
	case 'searchArticle':
		ob_clean();
		$text = dims_load_securvalue('text', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		if( isset($text)){
			$art = new article();
			$art->activePagination(false);
			$articles = $art->build_index($_SESSION['dims']['currentlang'], article::STATUS_OK, 'published', 'all', 'dims_nan', 0, $text);
			$tab = array();
			$i = 0;
			foreach($articles as $article){
				$tab[$i]['id'] = $article->fields['id'];
				$tab[$i]['label'] = $article->fields['reference'].' - '.$article->fields['label'];
				$i++;
			}
			echo json_encode($tab);
		}
		die();
		break;

	// Sélections des familles
	case 'families_selections':
		require DIMS_APP_PATH.'modules/catalogue/include/class_selection.php';
		require DIMS_APP_PATH.'modules/catalogue/include/class_selection_template.php';

		$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
		switch ($sa) {
			default:
			case 'index':
				$view->assign('selections', cata_selection::getAll($_SESSION['dims']['moduleid']));
				$view->render('objects/selections_index.tpl.php');
				break;
			case 'edit':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
				$selection = new cata_selection();
				if ($id > 0) {
					$selection->open($id);
				}
				else {
					$selection->init_description();
				}

				$view->assign('languages', cata_param::getActiveLang());
				$view->assign('selection', $selection);
				$view->assign('translations', $selection->getTranslations());
				$view->assign('templates', cata_selection_template::getAll($_SESSION['dims']['moduleid']));
				$view->render('objects/selections_edit.tpl.php');
				break;
			case 'save':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, false, true);

				$languages = cata_param::getActiveLang();
				foreach ($languages as $id_lang => $lang) {
					$selection = new cata_selection();

					if ($id > 0) {
						$selection->open($id, $id_lang);
					}
					else {
						$selection->init_description();
						$selection->setugm();
						$selection->setLang($id_lang);
					}

					$selection->setTemplateId(dims_load_securvalue('template_id', dims_const::_DIMS_NUM_INPUT, false, true));
					$selection->setSelectionTitle(dims_load_securvalue('title_'.$id_lang, dims_const::_DIMS_CHAR_INPUT, false, true));
					$selection->save();
				}

				if (isset($_POST['continue'])) {
					dims_redirect(get_path('objects', 'families_selections', array('sa' => 'edit')));
				}
				else {
					dims_redirect(get_path('objects', 'families_selections'));
				}
				break;
			case 'delete':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($id > 0) {
					$selection = new cata_selection();
					$selection->open($id);
					$selection->delete();
				}
				dims_redirect(get_path('objects', 'families_selections'));
				break;
		}
		break;
}
