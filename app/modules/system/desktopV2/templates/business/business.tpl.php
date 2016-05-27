<?
require_once DIMS_APP_PATH.'modules/system/desktopV2/templates/business/view_business_factory.php';

if (!isset($_SESSION['desktopv2']['business']['business_op'])) $_SESSION['desktopv2']['business']['business_op']='';

$business_op = dims_load_securvalue('business_op',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['business']['business_op']);

view_business_factory::buildAccueil();
?>


<div style="clear:both"></div>
<?

if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (true)) {
	// select action to execute
	switch($business_op){
		case 'save_business_param':
			require_once DIMS_APP_PATH.'modules/system/desktopV2/include/class_gescom_param.php';
			$params = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
			foreach($params as $key => $value) {
				if (substr($key,0,6) == 'param_') {
					$key = substr($key,6);
					if ($key == 'datedeb' || $key == 'datefin') $value = business_datefr2us($value);

					$param = new class_gescom_param();
					if (!$param->open($key, $_SESSION['dims']['workspaceid'])) {
						$param->init_description();
						$param->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					}
					$param->fields['param'] = addslashes($key);
					$param->fields['value'] = addslashes($value);
					$param->save();
				}
			}
			dims_redirect('/admin.php?business_op=admin_business_param');
			break;
		case 'admin_business_param':
			view_business_factory::buildGetAdminParam();
			break;
		case 'admin_models_param': //Cyril - Gestion des modèles de document dédiés aux devis, facture etc...
			$action = dims_load_securvalue('models_action',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
			require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";
			switch($action){
				default:
				case 'index':
					$models = print_model::all();
					require_once _DESKTOP_TPL_LOCAL_PATH.'/business/print_models/index.tpl.php';
					break;
				case 'edit':
					$model = new print_model();
					$types = suivi_type::all();
					$id_model = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if(!empty($id_model)){
						$model->open($id_model);
					}
					else{
						$model->init_description(true);
						$model->setugm();
					}
					//on contrôle quand même qu'un petit malin n'essaye pas d'accéder aux modèles des autres
					if(isset($model->fields['id_workspace']) && $model->fields['id_workspace'] == $_SESSION['dims']['workspaceid']) require_once _DESKTOP_TPL_LOCAL_PATH.'/business/print_models/edit.tpl.php';
					else dims_redirect($dims->getScriptEnv().'?models_action=index');
					break;
				case 'save':
					$id_model = dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,true);
					$model = new print_model();
					if( ! empty($id_model) ){
						$model->open($id_model);
						$new = false;
					}
					else{
						$model->init_description(true);
						$model->setugm();
						$new = true;
					}
					if(isset($model->fields['id_workspace']) && $model->fields['id_workspace'] == $_SESSION['dims']['workspaceid']){
						$model->setvalues($_POST, 'model_');
						$file = $_FILES['document'];
						if( (! $new || ( ! empty($file) && !empty($file['name']) ) ) && $id_print_model = $model->save() ){ //en gros on ne fait le save que si on est pas new ou qu'on a bien le doc
							//gestion du document
							if( ! empty($file) && !empty($file['name']) ){
								$doc = new docfile();
								$doc->init_description();
								$doc->setugm();
								$doc->fields['id_record'] = $id_print_model;

								$doc->fields['name'] = $file['name'];
								move_uploaded_file($file['tmp_name'], DIMS_TMP_PATH . $file['name']);
								$doc->tmpuploadedfile = DIMS_TMP_PATH . $file['name'] ;
								$doc->fields['size'] = $file['size'];
								$doc->fields['id_folder'] = 0;
								$doc->save();

								$model->fields['id_doc'] = $doc->getId();
								$model->save();
							}

							//tout s'est bien passé on peut rediriger
							if(empty($_POST['continue']))
								dims_redirect($dims->getScriptEnv().'?models_action=index');
							else dims_redirect($dims->getScriptEnv()."?models_action=edit");
						}
						else $error = true;
					}
					else $error = true;

					if($error){//pas cool --> erreur
						$types = suivi_type::all();
						$model->setLightAttribute("global_error", $_SESSION['cste']['ERROR_THROWN']);
						require_once _DESKTOP_TPL_LOCAL_PATH.'/business/print_models/edit.tpl.php';
					}
					break;

				case 'delete':
					$model = new print_model();
					$id_model = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
					if(!empty($id_model)){
						$model->open($id_model);
						if( isset($model->fields['id_workspace']) && $model->fields['id_workspace'] == $_SESSION['dims']['workspaceid'] ){ //on contrôle qu'on a le droit de le supprimer

							$model->delete();
						}
					}
					dims_redirect($dims->getScriptEnv().'?models_action=index');
					break;
			}
			break;
		case 'suivis':
		case 'suivis_recherche':
		default:
			//Si aucune action n'est définie alors on retourne à l'accueil de l'onglet.
			view_business_factory::buildGetSuivis();
			break;
		case 'activity_type_management':
			view_business_factory::buildActivityTypesList();
			break;
		case 'activity_type_management_edit':
			view_business_factory::buildActivityTypesEdit(dims_load_securvalue('id_type', dims_const::_DIMS_NUM_INPUT, true, true, true));
			break;
		case 'activity_type_management_save':
			require_once DIMS_APP_PATH."modules/system/activity/class_type.php";
			$id_type = dims_load_securvalue('id_type', dims_const::_DIMS_NUM_INPUT, true, true, true);

			$type = new activity_type();
			if(!empty($id_type)) {
				$type->open($id_type);
			}
			else {
				$type->init_description();
			}

			$type->setvalues($_POST, 'type_');
			$type->save();

			dims_redirect(dims_urlencode('/admin.php?business_op=activity_type_management'));
			break;
		case 'activity_type_management_delete':
			require_once DIMS_APP_PATH."modules/system/activity/class_type.php";
			$id_type = dims_load_securvalue('id_type', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if(!empty($id_type)) {
				$type = new activity_type();
				$type->open($id_type);

				$type->delete();
			}

			dims_redirect(dims_urlencode('/admin.php?business_op=activity_type_management'));
			break;
	}
}
?>
