<div class="actions">
	<a href="Javascript:void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $popupid; ?>');">
		<img src="./common/img/close.png" />
	</a>
</div>
<div style="padding:2px;" ?>
<?
//if (file_exists(LANG_PATH.".php")) require_once(LANG_PATH.".php");
// Test if a DOC module is present
// -------------------------------
$isdoc = false;
$mods=$dims->getModules($_SESSION['dims']['workspaceid']);
foreach($mods as $instance) {
	if($instance['contenttype'] == 'doc') {
		$isdoc = true;
		break;
	}
}

if(!$isdoc) die('<b><font color="red">Module DOC absent</font></b>');
else
{
	require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
	require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_workspace.php');
	dims_init_module('doc');
}

$workspaces = dims_viewworkspaces();
$sqllimitworkspace=" AND dims_module_workspace.id_workspace IN ($workspaces)";

// get all folders from all available doc modules
$select		=	"
			SELECT	dims_module.label,
					fold.id,
					fold.name,
					fold.id_module,
					fold.parents,
					fold.foldertype,
					fold.id_folder

			FROM	dims_mod_doc_folder fold,
					dims_module,
					dims_module_type,
					dims_module_workspace

			WHERE	dims_module_type.label = 'doc'
			AND		fold.foldertype = 'public'
			AND		dims_module_workspace.id_module = dims_module.id
			AND		dims_module.id_module_type = dims_module_type.id
			AND		fold.id_module = dims_module.id
			AND		dims_module_workspace.id_workspace = :workspaceid
			ORDER BY	dims_module.label,
					fold.parents,fold.name";
// ajouter :
//			AND		fold.id_workspace = {$_SESSION['dims']['workspaceid']}

$res=$db->query($select, array(':workspaceid' => $_SESSION['dims']['workspaceid']));
?>
Dossier :
<select class="select" name="choosefolder" id="choosefolder" onchange="javascript:switch_folder(this.value);">
<option value="0"></option>
<?
$default_folder = 0;
$default_idmodule = 0;
$arrayfolders=array();

while ($fields = $db->fetchrow($res)) {

    if (!isset($arrayfolders[$fields['id_folder']])) {
        $arrayfolders[$fields['id_folder']]=''; // premiere racine
    }

    $namefolder='';
    if (!isset($arrayfolders[$fields['id']])) {
        if ($arrayfolders[$fields['id_folder']]!='') {
            $namefolder.=  $arrayfolders[$fields['id_folder']]." > ".$fields['name'];
        }
        else {
            $namefolder.=$fields['name'];
        }

        $arrayfolders[$fields['id']]=$namefolder;
    }

	if (!$default_folder) $default_folder = $fields['id'];
	if (!$default_idmodule) $default_idmodule = $fields['id_module'];
	?>
	<option value="<? echo $fields['id']; ?>"><? echo "{$fields['label']} > {$arrayfolders[$fields['id']]}"; ?></option>
	<?
}
?>
</select>

<?
// build dir sel for all modules available
$select =	"
		SELECT	distinct doc.*
		FROM	dims_mod_doc_file doc,
				dims_mod_doc_folder fold,
				dims_module,
				dims_module_type,
				dims_module_workspace

		WHERE	doc.id_module = dims_module.id
		AND		dims_module_workspace.id_module = dims_module.id
		AND		dims_module_workspace.id_workspace = :workspaceid
		AND		dims_module.id_module_type = dims_module_type.id

		AND		doc.id_folder = fold.id
		AND		fold.foldertype = 'public'

		ORDER BY	doc.id_folder,
				doc.id_module,
				doc.name
		";

$rs = $db->query($select, array(':workspaceid' => $_SESSION['dims']['workspaceid']));
?>
<script type="text/javascript" language="javascript">
var lf = new Array();
<?
if (!isset($dims_op)) $dims_op="";

$filter_ext_video=array();

switch($dims_op) {
	case 'doc_selectimage':
			//$filter_ext = array('jpg', 'gif', 'png', 'bmp');
			$filter_ext['jpg']='jpg';
			$filter_ext['gif']='gif';
			$filter_ext['png']='png';
			$filter_ext['bmp']='bmp';
	break;

	case 'doc_selectflash':
			//$filter_ext = array('swf');
			$filter_ext['swf']='swf';
			$filter_ext['flv']='flv';
	break;

	case 'doc_selectvideo':
			//$filter_ext = array('mp4', 'mkv', 'avi', 'mpeg', 'mpg');
			$filter_ext['mp4']='mp4';
			$filter_ext['mkv']='mkv';
			$filter_ext['avi']='avi';
			$filter_ext['mpeg']='mpeg';
			$filter_ext['mpg']='mpg';
			$filter_ext_video=$filter_ext;
	break;

	default:
		$filter_ext = array();
	break;
}

$i=0;
while ($fields = $db->fetchrow($rs)) {
	if (empty($filter_ext) || //in_array(dims_file_getextension($fields['name']),$filter_ext)){
		isset($filter_ext[$fields['extension']])) {
		$doc = new docfile();
		//$doc->openFromResultSet($fields);
		$doc->fields = $fields ;
		$doc->setNew(false);
		$pathdownload="";
		$pathdownloadmini="";
		$pathfile=$doc->getwebpath();
		switch ($fields['extension']) {
			case "jpeg":
			case "jpg":
			case "gif":
			case "png":
			case "bmp":
			case "tiff":
			case "tif":
			case "swf":
			case "flv":
				$pathdownload=$pathfile;
				require_once(DIMS_APP_PATH . "/include/functions/image.php");
				if (file_exists($doc->getfilepath()) && !file_exists($doc->getfilepathmini()))
					dims_resizeimage($doc->getfilepath(), 0, 150, 80,'',0,$doc->getfilepathmini());
				if (file_exists($doc->getfilepathmini()))
					$pathdownloadmini=$doc->getwebpathmini();
				break;
			case "mp4":
			case "mkv":
			case "avi":
			case "mpeg":
			case "mpg":
				$pathdownload = $doc->getPreview(false);
				break;
			default:
				$pathdownload=dims_urlencode("./index-quick.php?dims_op=doc_file_download&docfile_md5id={$fields['md5id']}");
				break;
			}
			/*
			 lf[<? echo $i; ?>][4]= "<? echo $pathdownload; ?>";
			lf[<? echo $i; ?>][5]= "<? echo $pathfile; ?>";*/
			?>
		lf[<? echo $i; ?>]=new Array(8);
		lf[<? echo $i; ?>][0]= "<? echo $fields['id']; ?>";
		lf[<? echo $i; ?>][1]= "<? echo str_replace("'","_",$fields['name']); ?>";
		lf[<? echo $i; ?>][2]= "<? echo $fields['id_module']; ?>";
		lf[<? echo $i; ?>][3]= "<? echo $fields['id_folder']; ?>";
		lf[<? echo $i; ?>][6]="<? printf("%.2f",round($fields['size']/1024,2)); ?>";
			<?
			//if (in_array($fields['extension'],array('mp4','mkv','avi','mpeg','mpg'))) {
			if (isset($filter_ext_video[$fields['extension']])) {
				echo "lf[".$i."][7]= \"".md5($doc->getfilepath())."\";";
				echo "lf[".$i."][9]= \"".$doc->fields['id']."_".$doc->fields['version']."\";";
			}
			else
				echo "lf[".$i."][7]= \"\";";
		?>
			lf[<? echo $i; ?>][8]= "<? echo $pathdownloadmini; ?>";
			<?
			$i++;
	}
}
if (isset($dims_op) && $dims_op=="doc_selectflash") $currentsel="doc_selectflash";
else $currentsel="doc_selectfile";
?>

window['switch_folder']=function switch_folder(idfolder) {
	sr = document.getElementById('showroom');
	var chresultat = '<table>';
	idmodule=0;
	var chpath="";

    $.ajax({
        type: 'GET',
        url: '/admin.php',
        data: {
            'dims_op' : 'keepCurrentFolder',
            'id_folder' : idfolder
        },
        dataType: 'text',
        success: function(data) {
        }
    });
	for (i=0;i<lf.length;i++) {
		if (lf[i][3] == idfolder) {
			idmodule=lf[i][2];
			<?
			switch ($dims_op) {

				default:
					?>
					extra = '&nbsp;';

					chresultat +=	'<a style="display:block;clear:both;margin:0;border-bottom:1px solid #d0d0d0;background-color:#f0f0f0;font-size:0.8em;overflow:auto;" href="#"onclick="javascript:setDocUrl(\''+lf[i][0]+'\',\''+lf[i][1]+'\',\'<?php echo $popupid; ?>\');">'+
														'<div style="float:left;width:210px;padding:2px;font-weight:bold;text-align:left;">'+lf[i][1]+'</div>'+
														'<div style="float:left;width:60px;padding:2px;text-align:right;">'+lf[i][6]+' ko</div></a>';

					<?
				break;
			}
			?>
		}
	}

	<?
	if ($dims_op=="doc_selectimage") echo "chresultat+='</table>';"; ?>
	sr.innerHTML=chresultat;
	/* update current id_folder for uploading file */
	document.getElementById('docfile_id_folder').value=idfolder;
	if (idmodule==0)
			document.getElementById('docfile_id_module').value=<? echo $default_idmodule; ?>;
	else document.getElementById('docfile_id_module').value=idmodule;
}

function set_folder(idfolder) {
	cf = document.getElementById('choosefolder');
	trouve = false;
	i=0;
	while (i<=cf.length && !trouve)
	{
		if (cf.options[i].value == idfolder) {cf.selectedIndex = i; trouve=true;}
		i++;
	}
	 switch_folder(idfolder);
}
</script>
</div>
<div style="width:100%;clear:both;">
<?
require_once DIMS_APP_PATH . "modules/doc/fck_upload_file_alone.php";
?>
</div>
<div id="showroom" style="border-top:1px solid #c0c0c0; padding:4px;overflow:auto;"></div>

<script language="javascript">
set_folder('<? echo $default_folder; ?>');
</script>
