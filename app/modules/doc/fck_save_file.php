<?
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");
require_once(DIMS_APP_PATH . "/modules/doc/class_docfolder.php");

dims_init_module('doc');

//if (!empty($_FILES['docfile_file']['name']) && !$docfile->new) $docfile->createhistory();
if (isset($_SESSION['dims']['uploaded_sid']) && $_SESSION['dims']['uploaded_sid']!='')
	$sid=$_SESSION['dims']['uploaded_sid'];
//$sid = session_id();

$upload_dir = realpath(DIMS_APP_PATH).'/data/uploads/'.$sid.'/';

if (is_dir($upload_dir)){
	if ($dh = opendir($upload_dir)) {
		while (($filename = readdir($dh)) !== false) {
			if ($filename!="." && $filename!="..") {

				$docfile = new docfile();
				$docfile->init_description();
				$docfile->setugm();
				$docfile->setvalues($_POST,'docfile_');
				$docfile->fields['parents'] = "";
				$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
				$docfile->tmpuploadedfile = $upload_dir.$filename;
				$docfile->fields['name'] = $filename;
				$docfile->fields['size'] = filesize($upload_dir.$filename);
				$error = $docfile->save();
			}
		}
	}
}
?>
