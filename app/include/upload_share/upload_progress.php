<?
/***************************************************************************************************
**	Description : AJAX Uploader																 **
**	File Name	: upload_progress.php								  **
**	Version	: 1.060506									  **
**	Author	: Steven Miles									  **
**												  **
****************************************************************************************************
	Start Session
***************************************************************************************************/

/**************************************************************************************************
	Set Path Variables
***************************************************************************************************/
	$sid = session_id();
	$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
	$session_dir = $temp_dir.$sid;
	$upload_size_file = $session_dir."/upload_size";
//** Reset Session Variables if a new File Name is passed
	if (isset($_GET["filename"])) {
		$_SESSION["Upload Size"] = NULL;
		$_SESSION["Upload File Size"] = NULL;
		$_SESSION["Start Time"] = time();
		$_SESSION["Filename"] = dims_load_securvalue("filename", dims_const::_DIMS_CHAR_INPUT, true, true, true);
	}

//* Check if application temp directory exists

	if (is_dir($temp_dir))
	{

//***** Check if session temp dir has been created, if so file has either started uploading or finished

		if (is_dir($session_dir))
		{

//********* Check if upload_size file exists, if so file has started uploading

			if (is_file($upload_size_file))
			{

//************** Check if Upload Size has been set, if so send update back to browser

				if (isset($_SESSION["Upload Size"])) {
					$uploaded_size = GetBytesRead($session_dir);

//***************** Checking if upload is complete or still uploading
					if ($uploaded_size < $_SESSION["Upload Size"]) {
						$lapsed = time() - $_SESSION["Start Time"];
						$lapsed_sec = ($lapsed % 60);
						$lapsed_min = ((($lapsed-$lapsed_sec)% 3600)/60);
						$lapsed_hours = (((($lapsed - $lapsed_sec) - ($lapsed_min * 60)) % 86400) / 3600);
						$lapsedf = $lapsed_hours.":".$lapsed_min.":".$lapsed_sec;

						// Calculate Upload Speed

						$upload_speed = 0;
						if($lapsed > 0){ $upload_speed = $uploaded_size / $lapsed; }
						$remaining = 0;
						if($upload_speed > 0){ $remaining = (($_SESSION["Upload Size"] - $uploaded_size) / $upload_speed); }

						//Calculate Time Remaining for Current Upload

						$remaining = round($remaining);
						$remaining_sec = ($remaining % 60);
						$remaining_min = ((($remaining - $remaining_sec) % 3600) / 60);
						$remaining_hours = (((($remaining - $remaining_sec) - ($remaining_min * 60)) % 86400) / 3600);
						if($remaining_sec < 10){ $remaining_sec = "0$remaining_sec"; }
						if($remaining_min < 10){ $remaining_min = "0$remaining_min"; }
						if($remaining_hours < 10){ $remaining_hours = "0$remaining_hours"; }
						$remainingf = "$remaining_hours:$remaining_min:$remaining_sec";

						// Calculate Percentage Complete

						$percent = round(100 *	$uploaded_size / $_SESSION["Upload Size"]);
						$speed = $lapsed ? round( $uploaded_size / $lapsed) : 0;

						// Format Uploaded Size
						echo "downloading|".$_SESSION["Filename"].
						 "|".$remainingf."|".$percent."|".formatSize($uploaded_size)."|".formatSize($_SESSION["Upload Size"])."|".formatSize($speed);
					} else {
//***************** Upload is complete & copying or moving file to upload Folder
						echo "copying|".$_SESSION["Filename"]."|".formatSize($_SESSION["Upload Size"]);
					}
//************* Check if Upload Size has been set,
				} else {
					$fp = @fopen(realpath($upload_size_file), "r");
					if (filesize(realpath($upload_size_file))>0)
						$_SESSION["Upload Size"] = fread($fp,filesize(realpath($upload_size_file)));
					echo "started|".$_SESSION["Filename"];
				}
			}
			else if (isset($_SESSION["Upload Size"]))
				{
//********* Upload has completed
				echo "Success|".$_SESSION["Filename"]."|".formatSize($_SESSION["Upload Size"]);
				$_SESSION["Upload Size"] = NULL;
				}
				else
				{
//********* Upload Has Started But we need to wait
				echo "wait|".$_SESSION["Filename"]."|No upload_size file";
				}
		}
		else if (isset($_SESSION["Upload Size"]))
		{
//***** Upload has completed
			echo "Success|".$_SESSION["Filename"]."|".formatSize($_SESSION["Upload Size"]);
			$_SESSION["Upload Size"] = NULL;
		}
		else
		{
//******Upload has started but we need to wait
			echo "wait|".$_SESSION["Filename"];
		}
	}
	else {
		echo "Upload Temp Directory has not been setup";
	}



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

//** FUNCTION Format File Size
	function formatSize($size) {
		$suffix = "b";
		if ($size>1024) {
			$size = round($size/1024);
			$suffix = "Kb";
			if ($size>1024) {
				$size = round($size/1024,2);
				$suffix="Mb";
			}
		}
		return $size.$suffix;
	}

?>
