<?php
dims_init_module('doc');
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docgallery.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfolder.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfiledraft.php';

$dims_op=dims_load_securvalue('dims_op',dims_const::_DIMS_CHAR_INPUT,true,true);

if ($dims_op!="") {
	switch($dims_op) {
        case 'keepCurrentFolder':
            $id_folder = dims_load_securvalue('id_folder',dims_const::_DIMS_CHAR_INPUT,true,true);
            if ($id_folder>0) {
                $_SESSION['dims']['currentwcefolderid']=$id_folder;
            }
            break;
		case 'refreshPreviewDocumentPage':
			ob_clean();
			$namepreview = dims_load_securvalue('namepreview',dims_const::_DIMS_CHAR_INPUT,true,true);
			$page = dims_load_securvalue('page',dims_const::_DIMS_NUM_INPUT,true,true);

			if (isset($_SESSION['dims']['preview'][$namepreview])) {
				// init de la classe dims_preview
				require_once(DIMS_APP_PATH.'modules/system/class_dims_preview.php');
				$dpreview = new dims_preview($namepreview,'','','',false);

				$dpreview->setValues('page',$page);

				$dpreview->display($_SESSION['dims']['preview'][$namepreview]['template'],$page);
			}
			die();
			break;

		case 'refreshPreviewDocument':
			ob_clean();
			$namepreview = dims_load_securvalue('namepreview',dims_const::_DIMS_CHAR_INPUT,true,true);
			$widthform = dims_load_securvalue('widthform',dims_const::_DIMS_NUM_INPUT,true,true);
			$heightform = dims_load_securvalue('heightform',dims_const::_DIMS_NUM_INPUT,true,true);

			// hack le temps de trouver pour la demo
			if ($heightform <800) $heightform=800;

			if (isset($_SESSION['dims']['preview'][$namepreview])) {
				// init de la classe dims_preview
				require_once(DIMS_APP_PATH.'modules/system/class_dims_preview.php');
				$dpreview = new dims_preview($namepreview,'','','',false);

				$dpreview->setValues('widthform',$widthform);
				$dpreview->setValues('heightform',$heightform);

				$dpreview->display($_SESSION['dims']['preview'][$namepreview]['template']);
			}
			die();
			break;
		case 'getcropcontentimage':
				ob_clean();

				break;
		case 'cropcontentimage':
			ob_clean();
			// initialsation du contenu
			$_SESSION['dims']['currentTextSelected']='';

			$x = dims_load_securvalue('x', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$y = dims_load_securvalue('y', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$width = dims_load_securvalue('w', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$height = dims_load_securvalue('h', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$curfolder = dims_load_securvalue('curfolder', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$image = dims_load_securvalue('image', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$pathscript=realpath(".")."/scripts/";
			$currentpath=realpath(".");

			chdir($currentpath."/data".$curfolder);
			//echo $currentpath."/data/preview/".$curfolder."<br>";
			$exec="bash ".escapeshellarg($pathscript)."extrait-contenu-image.sh ".escapeshellarg($image)." ".escapeshellarg($x)." ".escapeshellarg($y)." ".escapeshellarg($width)." ".escapeshellarg($height);

			if ($exec!="") exec(escapeshellcmd($exec),$tabres,$return);
			$content='';
			if (file_exists($currentpath."/data".$curfolder."/texte.txt")) {
				$content=file_get_contents($currentpath."/data".$curfolder."/texte.txt");
				//$content=  str_replace(" \n", " ", $content);
				//$content=  str_replace(".\n", " ", $content);
			}
			if ($content!="") {
				$_SESSION['dims']['currentTextSelected']=$content;
			}

			chdir($currentpath);
			echo $content;
			die();
			break;

		case 'preview_docfile':
			global $_DIMS;
			///require_once DIMS_APP_PATH . '/modules/doc/templates/preview_docfile.tpl.php';
			$md5id = dims_load_securvalue('md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			$popupid = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, true, true);

			$doc = docfile::openByMd5($md5id);

			//preview_docfile::display($doc, $popupid);
						?>
						 <link rel="stylesheet" type="text/css" href="<?php echo 'modules/doc/templates/preview_doc/styles.css'; ?> ">
						<div class="record_sub_container">
							<div style="width:24%;float:left;height:80px;"><h3>Visualisation du document&nbsp;</h3>
							<a id="copy-button" class=" hover" href="#">Copier</a> <span id="result-copy"></span></div>

							<div style="float:right;width:16px;">
							<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $popupid; ?>');">
								<img src="img/close.png" />
							</a>
							</div>
<div  style="float:right;height:80px;overflow:auto;width:65%" id="contentselectedtext"></div>
								<div style="clear:both;width: 100%; height: 820px;">
										<?
										echo $doc->getPreview(true,null,'modules/doc/templates/preview_doc/display_file.tpl.php');
										?>
								</div>
						</div>
						<?php
			die();
			break;

		case 'doc_add_virtual_folder' :
			ob_clean();
			$name_fold = dims_load_securvalue('name_fold',dims_const::_DIMS_CHAR_INPUT,true,true);
			$parent = dims_load_securvalue('parent',dims_const::_DIMS_NUM_INPUT,true,true);

			$fold_dir = new docfolder();
			$fold_dir->init_description();
			$fold_dir->setugm();
			$fold_dir->fields['name'] = $name_fold;
			$fold_dir->fields['timestp_create'] = dims_createtimestamp();
			$fold_dir->fields['foldertype'] = 'private';
			$fold_dir->fields['id_folder'] = $parent;
			$fold_dir->save();

			$folders = doc_getfolders($_SESSION['dims']['uploadfile']['id_module']);
			function echo_folder_select($fold,$parent,$decal){
				$select = '';
				foreach($fold['tree'][$parent] as $num => $key){
					$select .= '<option value="'.$key.'">'.$decal.'&nbsp;'.$fold['list'][$key]['name'].'</option>';
					$select .= '<option value="new_'.$key.'">'.$decal.$decal.'&nbsp; Nouveau dans '.$fold['list'][$key]['name'].'</option>';
					if (isset($fold['tree'][$key]))
						$select .= echo_folder_select($fold,$key,$decal.'&#151;');
				}
				return $select ;
			}
			//dims_print_r($folders);
			$select = '<select name="docfile_id_folder" id="docfile_id_folder" onchange="javascript: if (this.options[this.selectedIndex].value.substr(0,4) == \'new_\') document.getElementById(\'new_type_doc\').style.display=\'block\'; else document.getElementById(\'new_type_doc\').style.display=\'none\';">';
			foreach($folders['tree'][-1] as $tree){
				$select .= '<option value="'.$tree.'">'.$folders['list'][$tree]['name'].'</option>';
				$select .= '<option value="new_'.$tree.'">'.$decal.'&#151; Nouveau dans '.$folders['list'][$tree]['name'].'</option>';
				if (isset($folders['tree'][$tree]))
					$select .= echo_folder_select($folders,$tree,'&#151;');
			}
			$select .= '</select>';
			//dims_print_r($folders);
			?>
				<label><? echo $_DIMS['cste']['_DOC_FOLDER']; ?> :</label>
				<? echo $select ; ?>
				<div id="new_type_doc" style="display:none;margin-left:39%;">
					<input type="text" id="val_new_type_doc"value="" style="width:175px;">
					<img onclick="javascript:if (document.getElementById('val_new_type_doc').value != '') dims_xmlhttprequest_todiv('admin.php','dims_op=doc_add_virtual_folder&name_fold='+document.getElementById('val_new_type_doc').value+'&parent='+document.getElementById('docfile_id_folder').options[document.getElementById('docfile_id_folder').selectedIndex].value.substr(4),'','select_type_fold');" src="./common/img/add.gif" title="Ajouter type" alt="Ajouter type" style="cursor:pointer;">
				</div>
			<?
			die();
			break;
		case 'doc_uploadform_file':
			ob_end_clean();
			ob_start();
			require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
			$skin=new skin();
			echo $skin->open_simplebloc("");
			echo	"<div style=\"background-color:#FFFFFF;width:100%\">";
			$op = 'file_add';
			// affichage du formulaire
			//if (_DIMS_PROGRESSBAR_USED)
			//	require_once DIMS_APP_PATH . '/modules/doc/public_file_form_progressbar_object.php';
			//else
			require_once DIMS_APP_PATH . '/modules/doc/public_file_form_object.php';

			/*$_SESSION['dims']['current_object']['id_record'] = 0;
			$_SESSION['dims']['current_object']['id_object'] = 0;
			$_SESSION['dims']['current_object']['id_module'] = 0;*/
			$_SESSION['dims']['temp_tag']=array();

				// creation d'un lien éventuel pour afficher les tags
			echo	"<div style='width:100%;text-align:center;'><a href=\"javascript:void(0);\" onclick=\"javascript:SwitchViewDiv('displaytagdoc');\">".$_DIMS['cste']['_DIMS_LABEL_TAGS']." &nbsp;<img src=\"./common/img/view.png\"></a></div>";

			echo $skin->close_simplebloc();
				echo "<div id='displaytagdoc' style='width:100%;text-align:center;display:none; visibility:hidden;'>";
					echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
					require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag.php');
					echo $skin->close_simplebloc();
				echo "</div>";
			echo "</div>";

			die();
			break;
		case 'doc_uploadsave_file':
			// save du fichier
			$docfile = new docfile();
			$docfile->init_description();

			if (!empty($_POST['docfile_id'])) {// file already exists
				$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
			}
			else {
				$docfile->setugm();
			}

			if (!empty($_FILES['docfile_file']['name']) && !$docfile->new) $docfile->createhistory();

			$docfile->setvalues($_POST,'docfile_');
			$docfile->fields['parents'] = "";
			if (!empty($_FILES['docfile_file']['name'])) {
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpfile = $_FILES['docfile_file']['tmp_name'];
				$docfile->fields['name'] = $_FILES['docfile_file']['name'];
				$docfile->fields['size'] = $_FILES['docfile_file']['size'];
			}

			// on ajoute le lien vers l'objet concern�
			$docfile->fields['id_object'] = $_SESSION['dims']['uploadfile']['id_object'];
			$docfile->fields['id_module'] = $_SESSION['dims']['uploadfile']['id_module'];
			$docfile->fields['id_record'] = $_SESSION['dims']['uploadfile']['id_record'];
			$error = $docfile->save(dims_const::_SYSTEM_OBJECT_DOCFILE);
			//dims_print_r($docfile);die();
			$url=$_SESSION['dims']['uploadfile']['url'];

			if (strpos($docfile->fields['id_folder'],'new_') !== false)
				$docfile->fields['id_folder'] = substr($docfile->fields['id_folder'],4);
			if (isset($_SESSION['dims']['uploadfile']['id_globalobject'])){
				require_once(DIMS_APP_PATH . '/include/class_dims_globalobject.php');
				$gb_object = new dims_globalobject();
				$gb_object->open($docfile->fields['id_globalobject']);
				$gb_object->addLink($_SESSION['dims']['uploadfile']['id_globalobject']);

			}
						//die($url);
						//dims_print_r($docfile);die();
			unset($_SESSION['dims']['uploadfile']);
			dims_redirect($url);
			break;
		case 'reset_currentobject':
			if (isset($_SESSION['dims']['current_object'])) {
				unset($_SESSION['dims']['current_object']);
			}
			break;
		case 'preview':
		case 'object_detail_properties':
			$objectid=$_SESSION['dims']['current_object']['id_object'];

			// generation du contenu par unoconv
			if ($idobject==_DOC_OBJECT_FILE) {
				$obj=new docfile();
				$obj->open($idrecord);
				echo $obj->getPreview();
			}
		break;
		case 'object_properties':
		case 'refreshDesktop':
		$moduleid=$_SESSION['dims']['current_object']['id_module'];
		$objectid=$_SESSION['dims']['current_object']['id_object'];
		$recordid=$_SESSION['dims']['current_object']['id_record'];

		if($objectid==dims_const::_SYSTEM_OBJECT_DOCFILE || $objectid==_DOC_OBJECT_FILE) {
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
			$obj=new docfile();
			$obj->open($recordid);
			$type=dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true);

			$_SESSION['dims']['current_object']['label']=$obj->fields['name'];
			$_SESSION['dims']['current_object']['id_workspace']=$obj->fields['id_workspace'];
			$_SESSION['dims']['current_object']['id_user']=$obj->fields['id_user'];
			$_SESSION['dims']['current_object']['timestp_modify']=$obj->fields['timestp_modify'];

			$workspaceid=$_SESSION['dims']['current_object']['id_workspace'];
			$_SESSION['dims']['current_object']['cmd']=array();
			// t�l�charger
			$elem['name']=$_DIMS['cste']['_DIMS_DOWNLOAD'];
			$elem['src']="./common/img/save.gif";
			$elem['width']= "width:130px";
			$elem['link']= dims_urlencode("admin.php?dims_moduleid={$moduleid}&dims_desktop=block&dims_action=public&op=file_download&docfile_id=$recordid");
			$_SESSION['dims']['current_object']['cmd'][]=$elem;

			/*$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
			$elem['src']="./common/img/arrow-blue-right.gif";
			$elem['width']= "width:115px";
			$elem['link']= dims_urlencode("admin.php?dims_moduleid={$moduleid}&dims_desktop=block&dims_action=public&currentfolder=".$obj->fields['id_folder']);
			$_SESSION['dims']['current_object']['cmd'][]=$elem;*/

			$elem['name']=$_DIMS['cste']['_PREVIEW'];
			$elem['id']=dims_const::_DIMS_SUBMENU_PREVIEW;
			$elem['src']="./common/img/view.png";
			$elem['width']= "";
			$elem['link']= "javascript:desktopViewPreview();";
			// structure decrite dans /modules/system/desktop_object.php
			$desktopObject[$idDesktopObject++]=$elem;

			// ajout d'un onglet pour voir les contacts importés avec cette vcard
			if ($obj->fields['extension'] == 'vcf'){
				$elem['name']=$_DIMS['cste']['_DIMS_LABEL_VCARD'];
				$elem['id']=dims_const::_DIMS_SUBMENU_VCARD;
				$elem['src']="./common/img/contact26.png";
				$elem['width']= "width:115px";
				$elem['link']= "javascript:desktopVcard();";
								$desktopObject[$idDesktopObject++]=$elem;
			}



		}
		else {
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');
			$obj=new docfolder();
			$obj->open($idrecord);

			$_SESSION['dims']['current_object']['label']=$obj->fields['name'];
			$_SESSION['dims']['current_object']['id_workspace']=$obj->fields['id_workspace'];
			$_SESSION['dims']['current_object']['id_user']=$obj->fields['id_user'];
			$_SESSION['dims']['current_object']['timestp_modify']=$obj->fields['timestp_modify'];
			$_SESSION['dims']['current_object']['cste']=_DOC_OBJECT_FILE;

			$_SESSION['dims']['current_object']['cmd']=array();
			$elem['name']=$_DIMS['cste']['_DIMS_OPEN'];
			$elem['src']="./common/img/arrow-blue-right.gif";
			$elem['link']= dims_urlencode("admin.php?dims_moduleid={$moduleid}&dims_desktop=block&dims_action=public&op=folder_modify&currentfolder=$recordid");
			$_SESSION['dims']['current_object']['cmd'][]=$elem;
		}
		break;
		case "resize":
			ob_end_clean();
			header("Content-type:image/jpeg");
			if (!empty($_GET['docfile_md5id'])) {
				$res=$db->query("SELECT * FROM dims_mod_doc_file WHERE md5id = :md5", array(':md5' => addslashes(dims_load_securvalue('docfile_md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true)) ) );
				if ($fields = $db->fetchrow($res)) {
					$path = _DIMS_PATHDATA._DIMS_SEP."doc-".$fields['id_module'];

					if (!is_dir(_DIMS_PATHDATA)) mkdir(_DIMS_PATHDATA);
					if ($path != '' && !is_dir($path)) mkdir($path);

					$basepath = $path._DIMS_SEP.substr($fields['timestp_create'],0,8);
					dims_makedir($basepath);

					$fichierSource=$basepath._DIMS_SEP."{$fields['id']}_{$fields['version']}.{$fields['extension']}";
					//$fichierSource = "http://{$_SERVER['SERVER_NAME']}/{$_GET['path']}";
					//$fichierSource =$docfile->getfilepath();
					$filename_array = explode('.',$fichierSource);
					$extension = strtolower($filename_array[sizeof($filename_array)-1]);

					switch ($extension) {
						case "jpg":
						case "jpeg":
							$src_im = ImageCreateFromJpeg($fichierSource);
							break;
						case "png":
							$src_im = imagecreatefrompng($fichierSource);
							break;
						case "gif":
							$src_im = imagecreatefromgif($fichierSource);
							break;
					}


					$size = GetImageSize($fichierSource);

					$src_w = $size[0];
					$src_h = $size[1];

					$dst_w = dims_load_securvalue('width', dims_const::_DIMS_NUM_INPUT, true, true, true);
					$dst_h = dims_load_securvalue('height', dims_const::_DIMS_NUM_INPUT, true, true, true);

					$dst_im = ImageCreateTrueColor($dst_w,$dst_h);

					ImageCopyResampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);

					$blanc = ImageColorAllocate ($dst_im, 255, 255, 255);
					ImageString($dst_im, 0, 12, $dst_h-18, "{$_SERVER['SERVER_NAME']}", $blanc);

					ImageJpeg($dst_im);

					ImageDestroy($dst_im);
					ImageDestroy($src_im);
				}
			}
			die();
			break;
		case "thumbnail":
			ob_end_clean();
			header("Content-type:image/jpeg");
			if (!empty($_GET['docfile_md5id'])) {
				$res=$db->query("SELECT * FROM dims_mod_doc_file WHERE md5id = :md5", array(':md5' => addslashes(dims_load_securvalue('docfile_md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true)) ) );
				if ($fields = $db->fetchrow($res)) {
					$path = _DIMS_PATHDATA._DIMS_SEP."doc-".$fields['id_module'];

					if (!is_dir(_DIMS_PATHDATA)) mkdir(_DIMS_PATHDATA);
					if ($path != '' && !is_dir($path)) mkdir($path);

					$basepath = $path._DIMS_SEP.substr($fields['timestp_create'],0,8);
					dims_makedir($basepath);

					$fichierSource=$basepath._DIMS_SEP."{$fields['id']}_{$fields['version']}.{$fields['extension']}";
					//$fichierSource = "http://{$_SERVER['SERVER_NAME']}/{$_GET['path']}";
					//$fichierSource =$docfile->getfilepath();
					$filename_array = explode('.',$fichierSource);
					$extension = strtolower($filename_array[sizeof($filename_array)-1]);

					$largeurDestination = dims_load_securvalue('width', dims_const::_DIMS_NUM_INPUT, true, true, true);
					$hauteurDestination = dims_load_securvalue('height', dims_const::_DIMS_NUM_INPUT, true, true, true);
					$im = ImageCreateTrueColor ($largeurDestination, $hauteurDestination) or die ("Erreur lors de la cr�ation de l'image");
					switch ($extension) {
						case "jpg":
						case "jpeg":
							$source = ImageCreateFromJpeg($fichierSource);
							break;
						case "png":
							$source = imagecreatefrompng($fichierSource);
							break;
						case "gif":
							$source = imagecreatefromgif($fichierSource);
							break;
					}

					$largeurSource = imagesx($source);
					$hauteurSource = imagesy($source);

					$blanc = ImageColorAllocate ($im, 255, 255, 255);
					$gris[0] = ImageColorAllocate ($im, 90, 90, 90);
					$gris[1] = ImageColorAllocate ($im, 110, 110, 110);
					$gris[2] = ImageColorAllocate ($im, 130, 130, 130);
					$gris[3] = ImageColorAllocate ($im, 150, 150, 150);
					//$gris[4] = ImageColorAllocate ($im, 170, 170, 170);
					//$gris[5] = ImageColorAllocate ($im, 190, 190, 190);
					//$gris[6] = ImageColorAllocate ($im, 210, 210, 210);
					//$gris[7] = ImageColorAllocate ($im, 230, 230, 230);

					for ($i=0; $i<=3; $i++) {
						ImageFilledRectangle ($im, $i, $i, $largeurDestination-$i, $hauteurDestination-$i, $gris[$i]);
					}

					ImageCopyResampled($im, $source, 3, 3, 0, 0, $largeurDestination-(2*3), $hauteurDestination-(2*3), $largeurSource, $hauteurSource);

					ImageJpeg($im);
				}
			}
			die();
			break;
		case 'choice_docfolder':
			ob_start();
			if(file_exists(DIMS_APP_PATH."templates/backoffice/".$_SESSION['dims']['template_name']."/class_skin.php"))
				require_once(DIMS_APP_PATH."templates/backoffice/".$_SESSION['dims']['template_name']."/class_skin.php");
			else
				require_once(DIMS_APP_PATH."include/class_skin.php");
			$skin=new skin();
			echo $skin->open_simplebloc("");
			echo "<div style=\"background-color:#FFFFFF;width:100%\">";
				require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
				$folders=doc_getfolders();

				//echo doc_build_maintree($folders,-1,'',1,'',$_GET['currentfolder'],true);
				$selectfolders = array();
				if (isset($_GET['selectfolders'])) {
					$selectfolders = explode(",",dims_load_securvalue('selectfolders', dims_const::_DIMS_NUM_INPUT, true, true, true));
				}

				if (isset($_GET['currentfolder']))
					echo doc_build_tree($folders,array(dims_load_securvalue('currentfolder', dims_const::_DIMS_NUM_INPUT, true, true, true)),0,0,'',$selectfolders);
				else
					echo _DIMS_ERROR;

				echo "<div style=\"background-color:#FFFFFF;width:100%;text-align:center;\">
						<input type=\"button\" onclick=\"dims_getelem('dims_popup').style.visibility='hidden';document.getElementById('op').selectedIndex=0;\" value=\"".$_DIMS['cste']['_DIMS_LABEL_CANCEL']."\" class=\"flatbutton\"/>
						</div>";
				echo "</div>";
			echo $skin->close_simplebloc();
			die();
			break;

		case 'doc_uploadfile':
			ob_end_clean();
			ob_start();

			if(isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'])){
				require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");
				require_once(DIMS_APP_PATH . "/modules/doc/class_docfolder.php");
				$docfile = new docfile();
				$docfile->init_description();
				$docfile->setugm();
				$docfile->setvalues($_POST,'docfile_');
				$docfile->fields['parents'] = "";
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpfile = $_FILES['file_upload']['tmp_name'];
				$docfile->fields['name'] = $_FILES['file_upload']['name'];
				$docfile->fields['size'] = $_FILES['file_upload']['size'];
				$error = $docfile->save();
			}else{
				require_once(DIMS_APP_PATH . '/modules/doc/fck_save_file.php');
			}

			//generate return code
			$CKEditorFuncNum=dims_load_securvalue('CKEditorFuncNum',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if (isset($_POST['currentsel']) && isset($docfile) && $docfile->fields['id']>0) {
				$pathdoc=dims_urlencode("./index-quick.php?dims_op=doc_file_download&docfile_md5id=".$docfile->fields['md5id']);
                $pathdocimage=$docfile->getwebpath();
                if (substr($pathdocimage,0,2)=="./") $pathdocimage=substr($pathdocimage,1);
				echo "<script language=\"javascript\">";

				if ($_POST['currentsel']=="doc_selectimage")
					echo "opener.applyTxtUrl('".$pathdocimage."');opener.UpdatePreview();window.close();";
				elseif ($_POST['currentsel']=="doc_selectvideo"){
					$pathdoc=dims_urlencode(md5($docfile->getfilepath()));
					echo "opener.applyTxtUrl('".$pathdoc."','".$docfile->fields['id']."_".$docfile->fields['version']."');window.close();";
				}elseif($_POST['currentsel']=="doc_selectflash"){
					$pathdoc=dims_urlencode($docfile->getwebpath());
					echo "window.opener.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$pathdoc');window.close();";
				}else
					echo "opener.applyTxtUrl('".$pathdoc."');opener.switchProtocol();window.close();";
				echo "</script>";
			}
			die();
			break;
		case 'doc_uploadfile_alone':
			ob_end_clean();
			ob_start();
			$urlreturn = dims_load_securvalue('urlreturn',dims_const::_DIMS_CHAR_INPUT,true,true);

			if(isset($_FILES['file_upload']) && !empty($_FILES['file_upload']['name'])){
				require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");
				require_once(DIMS_APP_PATH . "/modules/doc/class_docfolder.php");
				$docfile = new docfile();
				$docfile->init_description();
				$docfile->setugm();
				$docfile->setvalues($_POST,'docfile_');
				$docfile->fields['parents'] = "";
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpfile = $_FILES['file_upload']['tmp_name'];
				$docfile->fields['name'] = $_FILES['file_upload']['name'];
				$docfile->fields['size'] = $_FILES['file_upload']['size'];
				$error = $docfile->save();
			}else{
				require_once(DIMS_APP_PATH . '/modules/doc/fck_save_file.php');
			}

			//generate return code
			if (isset($docfile) && $docfile->fields['id']>0) {
				/*echo "<script language=\"text/javascript\">";

								echo "setDocUrl('".$docfile->fields['id']."','".addslashes($docfile->fields['name'])."','".$popupid."');";

				echo "</script>";*/
			}
						if ($urlreturn=='') $urlreturn='/admin.php';
						else $urlreturn.="&id_docfileselected=".$docfile->fields['id'];
						dims_redirect($urlreturn);
			die();
			break;
		case 'doc_selectfile':
		case 'doc_selectimage':
		case 'doc_selectflash':
		case 'doc_selectvideo':
			$popupid = dims_load_securvalue('id_popup',dims_const::_DIMS_NUM_INPUT,true,true);

			if (dims_load_securvalue('video',dims_const::_DIMS_NUM_INPUT,true,true))
				$dims_op = 'doc_selectvideo';

			ob_start();
			echo "<script type=\"text/javascript\" src=\"/assets/javascripts/common/jquery/jquery-2.1.1.min.js\"></script>";
			echo "<script type=\"text/javascript\" src=\"/common/js/portal_v5.js\"></script>";
			if (!isset($additional_javascript)) $additional_javascript="";

			$mode = dims_load_securvalue('mode',dims_const::_DIMS_CHAR_INPUT,true,true);
			if ($mode=='simple')
				require_once DIMS_APP_PATH . '/modules/doc/fck_explorer_simple.php';
			else
				require_once DIMS_APP_PATH . '/modules/doc/fck_explorer.php';
			/*
			$main_content = ob_get_contents();
			@ob_end_clean();

			$template_body->assign_vars(array(
				'TEMPLATE_PATH'			=> $_SESSION['dims']['template_path'],
				'ADDITIONAL_JAVASCRIPT' => $additional_javascript,
				'PAGE_CONTENT'			=> $main_content
				)
			);

			$template_body->pparse('body');
			*/
			die();
		break;

		case 'doc_file_delete':
			require_once DIMS_APP_PATH . '/include/class_dims_data_object.php';
			require_once DIMS_APP_PATH . '/include/functions/date.php';
			require_once DIMS_APP_PATH . '/include/functions/filesystem.php';
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
			if (!empty($_GET['docfile_id'])) {

				$docfile = new docfile();
				$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
				if (dims_isadmin() || $docfile->fields['id_user']==$_SESSION['dims']['userid']) $docfile->delete();
			}
			elseif (!empty($_GET['docfile_md5id'])) {
				$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5", array(':md5' => addslashes(dims_load_securvalue('docfile_md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true)) ) );
				if ($fields = $db->fetchrow($res)) {

					$docfile = new docfile();
					$docfile->open($fields['id']);
					if (dims_isadmin() || $docfile->fields['id_user']==$_SESSION['dims']['userid']) $docfile->delete();
				}
			}

			if (isset($_SESSION['dims']['uploadfile']['url'])) {
				$url=$_SESSION['dims']['uploadfile']['url'];
				unset($_SESSION['dims']['uploadfile']);
				dims_redirect($url);
			}
			else{
				global $dims;
				dims_redirect($dims->getScriptEnv());
			}
			die();
			break;

		case 'doc_file_download':
			require_once DIMS_APP_PATH . '/include/class_dims_data_object.php';
			require_once DIMS_APP_PATH . '/include/functions/date.php';
			require_once DIMS_APP_PATH . '/include/functions/filesystem.php';
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

/*
			if (!empty($_GET['docfile_id'])) {

				$docfile = new docfile();
				$docfile->open($_GET['docfile_id']);

				if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
				elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);
			}
*/

			$version = dims_load_securvalue('version',dims_const::_DIMS_NUM_INPUT, true, true);
			$docfile_md5id = dims_load_securvalue('docfile_md5id',dims_const::_DIMS_NUM_INPUT, true, true);

			if (!empty($docfile_md5id)) {
				$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5", array(':md5' => addslashes($docfile_md5id) ) );
				if ($fields = $db->fetchrow($res)) {

					$docfile = new docfile();
					$docfile->open($fields['id']);
					if (file_exists($docfile->getfilepath($version))) dims_downloadfile($docfile->getfilepath($version),$docfile->fields['name']);
					elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);

				}
			}
			die();
		break;

		case 'doc_file_downloadvideo':
			require_once DIMS_APP_PATH . '/include/class_dims_data_object.php';
			require_once DIMS_APP_PATH . '/include/functions/date.php';
			require_once DIMS_APP_PATH . '/include/functions/filesystem.php';
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

			if (!empty($_GET['docfile_id'])) {
				$docfile = new docfile();
				$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));

				if (file_exists($docfile->getfilepath())) dims_downloadfile($docfile->getfilepath(),$docfile->fields['name']);
				elseif (file_exists($docfile->getfilepath_deprecated())) dims_downloadfile($docfile->getfilepath_deprecated(),$docfile->fields['name']);
			}

			if (!empty($_GET['docfile_md5id'])) {

				$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5", array (':md5' => addslashes(dims_load_securvalue('docfile_md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true))));
				if ($fields = $db->fetchrow($res)) {

					$docfile = new docfile();
					$docfile->open($fields['id']);
					$webpath=$dims->getProtocol().$dims->getHttpHost();

					//if (file_exists($docfile->getfilepath())) {
						header("Location: ".$webpath."/".$docfile->getwebpath());
						exit();
					//}
				}

			}
			die();
		break;
		case 'doc_image_get':
			require_once DIMS_APP_PATH . '/include/class_dims_data_object.php';
			require_once DIMS_APP_PATH . '/include/functions/date.php';
			require_once DIMS_APP_PATH . '/include/functions/filesystem.php';
			require_once DIMS_APP_PATH . '/include/functions/image.php';
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
			require_once DIMS_APP_PATH . '/include/functions/image.php';

			if (!empty($_GET['docfile_id'])) {
				$docfile = new docfile();
				$docfile->open(dims_load_securvalue('docfile_id', dims_const::_DIMS_NUM_INPUT, true, true, true));
			}

			if (!empty($_GET['docfile_md5id'])) {
				$res=$db->query("SELECT id FROM dims_mod_doc_file WHERE md5id = :md5", array (':md5' => addslashes(dims_load_securvalue('docfile_md5id', dims_const::_DIMS_CHAR_INPUT, true, true, true))));
				if ($fields = $db->fetchrow($res)) {
					$docfile = new docfile();
					$docfile->open($fields['id']);
				}
			}

			if (!empty($docfile)) {
				$height = (isset($_GET['height'])) ? dims_load_securvalue('height', dims_const::_DIMS_NUM_INPUT, true, true, true) : 0;
				$width = (isset($_GET['width'])) ? dims_load_securvalue('width', dims_const::_DIMS_NUM_INPUT, true, true, true) : 0;
				$coef = (isset($_GET['coef'])) ? dims_load_securvalue('coef', dims_const::_DIMS_NUM_INPUT, true, true, true) : 0;

				if (file_exists($docfile->getfilepath())) dims_resizeimage($docfile->getfilepath(), $coef, $width, $height);
			}
		break;
		case 'jquery_upload_file':
			ob_clean();
			error_reporting(0);
			require(DIMS_ROOT_PATH.'www/common/js/jQuery-File-Upload/server/php/UploadHandler.php');
			$upload_handler = new UploadHandler(array(
				'user_dirs'		=> true,
				'upload_dir'	=> DIMS_ROOT_PATH.'www/data/uploads/',
				'upload_url'	=> './data/uploads/',
				'script_url'	=> dims::getInstance()->getScriptEnv()."?dims_op=jquery_upload_file",
				'orient_image'	=> false,
			));
			die();
			break;
	}
}
?>
