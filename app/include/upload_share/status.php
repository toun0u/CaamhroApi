<?
	if (!isset($_SESSION)) die();
	$sid = session_id();
	$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
	$session_dir = $temp_dir.$sid;
	$upload_size_file = $session_dir."/upload_size";
?>
<html>
	<head>
		<meta http-equiv="refresh" content="2">
		<title> File Upload Form</title>
	</head>
	<body>
<?
	if( ini_get('safe_mode') ) { echo "Safe Mode Active<br>";}
	if (!empty($sid)) {
		echo "Upload Session ID = <b style='color:green;'>". $sid."</b><br>";
	} else {
		echo "<b style='color:red;'>No Session ID Found!!!!!</b><br>";
	}
	flush();
	echo "Checking if temp directory exists -> ";
	if (is_dir($temp_dir)) { echo "<b style='color:green;'>Found</b><br>"; } else { echo "<b style='color:red;'>Temp Not Found!!!!!</b><br>";}
	flush();
	echo "Checking if session temp directory exists -> ";

	//if (!is_dir($session_dir)) mkdir($session_dir);

	if (is_dir($session_dir)) { echo "<b style='color:green;'>Found</b><br>"; } else { echo "<b style='color:red;'>Not Found!!!!!</b><br>";}
	flush();
	echo "Checking if upload_size file exists -> ";
	if (is_file($upload_size_file)) {
		echo "<b style='color:green;'>Found</b><br>";
		$fp = @fopen(realpath($upload_size_file), "r");
		$_SESSION["Upload File Size"] = filesize(realpath($upload_size_file));
		$_SESSION["Upload Size"] = (fread($fp,$_SESSION["Upload File Size"])-$_SESSION["Upload File Size"]);
		echo "Upload Size -> <b style='color:green;'>".$_SESSION["Upload Size"]."</b><br>";
		$uploaded = GetBytesRead($session_dir);
		echo "Uploaded Amount -> <b style='color:green;'>".$uploaded."</b><br>";
	} else { echo "<b style='color:red;'>Not Found!!!!!</b><br>";}
	flush();

?>

	</body>
</html>


<?

//** FUNCTION Return the current size of upload
	function GetBytesRead($tmp_dir){
		$bytesRead = 0;
		if(is_dir($tmp_dir)){
			if($handle = opendir($tmp_dir)){
				while(false !== ($file = readdir($handle))){
					if($file != '.' && $file != '..' && $file != 'upload_size'){ $bytesRead += filesize($tmp_dir . "/" . $file); }
				}
				closedir($handle);
			}
		}
		$bytesRead = trim($bytesRead);
		return $bytesRead;
	}
?>
