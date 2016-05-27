<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
$skin=new skin();

if (isset($_SESSION['dims']['docs']['start'])) {
	echo $skin->open_simplebloc('','width:100%;','','');

	$_GET['docfile_id']=$_SESSION['dims']['docs']['docfile_id'];

	switch($_SESSION['dims']['docs']['start']) {
		case 0:
			echo "Initialisation";
			break;
		case 1:
			echo "Extract files, please wait !";
			break;
		case 2:
			$pathdest=$_SESSION['dims']['docs']['pathdest'];

			if (file_exists($pathdest)) {
				$result=explode("\t",exec(escapeshellcmd("du -s ".escapeshellarg($pathdest))),2);
				if (isset($result)) {
					$_SESSION['dims']['docs']['current']= $result[0];
				}
			}
			echo "<img src='./common/img/ajax-small-indicator.gif' border='0'>";
			echo "Extraction en cours ".display_avancement(100-($_SESSION['dims']['docs']['current'])*100/$_SESSION['dims']['docs']['total']);
			break;
		case 3:
			echo "<script type=\"text/javascript\">finishExtract();</script>";
			break;
	}
	echo $skin->close_simplebloc();
}
?>
