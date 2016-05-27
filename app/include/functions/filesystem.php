<?

###############################################################################
#
# copy / delete functions
#
###############################################################################

/**
* recursive copy of src folder into dest folder
*
* @param string path to source file
* @param string path to destination file
* @param int chmod'like
* @return boolean
*
* @version 2.09
* @since 0.1
*
* @category files manipulations
*/
function dims_copydir($src , $dest, $mask = 0777)
{
	$ok = true;
	$folder=opendir($src);

	if (!file_exists($dest))
	{
		mkdir($dest, $mask);
		//chmod($dest, $mask);
	}

	while ($file = readdir($folder))
	{
		$l = array('.', '..');
		if (!in_array( $file, $l))
		{
			if (is_dir($src."/".$file))
			{
				$ok = dims_copydir("$src/$file", "$dest/$file", $mask);
			}
			else
			{
				// test if writable
				if (!(file_exists("$dest/$file") && !is_writable("$dest/$file")))
				{
					copy("$src/$file", "$dest/$file");
					//chmod("$dest/$file", $mask);
				}
				else $ok = false;
			}
		}
	}
	return $ok;
}

/**
* recursive delete of folder
*
* @param string path to delete
* @return void
*
* @version 2.09
* @since 0.1
*
* @category files manipulations
*/
function dims_deletedir($src)
{
	if (file_exists($src))
	{
		$folder=opendir($src);

		while ($file = readdir($folder))
		{
			$l = array('.', '..');
			if (!in_array( $file, $l))
			{
				if (is_dir($src."/".$file))
				{
					dims_deletedir("$src/$file");
				}
				else
				{
					unlink("$src/$file");
				}
			}
		}

		if (is_dir($src)) rmdir($src);
	}
}

/**
* recursive create of folder
*
* @param string path to create
* @return void
*
* @version 2.09
* @since 0.1
*
* @category files manipulations
*/

function dims_makedir($path)
{
	$array_folder = explode(_DIMS_SEP, $path);
	$old_path = '';
	foreach($array_folder as $current_path)
	{
		if ($current_path != '')
		{
			$current_path = $old_path. _DIMS_SEP .$current_path;

			if (!is_dir($current_path)) mkdir ($current_path, 0777);

			$old_path = $current_path;
		}
	}
	return $current_path;
}

function dims_downloadfile($filepath, $destfilename, $deletefile = false, $attachment = true) {
	//if (substr($path,-1) == '/') $path = substr($path, 0, strlen($path)-1);
	ob_end_clean();
	error_reporting(0);

	global $dims;
	$dims->debugmode = false;

	if (file_exists($filepath)) {
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		$UserBrowser = "Opera";
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		$UserBrowser = "IE";
		else
		$UserBrowser = '';

		/// important for download im most browser
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?
		 'application/octetstream' : 'application/octet-stream';

		switch(strrchr(basename($destfilename), '.')) {
			case ".gz": $mime_type = "application/x-gzip"; break;
			case ".tgz": $mime_type = "application/x-gzip"; break;
			case ".zip": $mime_type = "application/zip"; break;
			case ".pdf": $mime_type = "application/pdf"; break;
			case ".doc": $mime_type = "application/msword"; break;
			case ".ppt": $mime_type = "application/mspowerpoint"; break;
			case ".xls": $mime_type = "application/excel"; break;
			case ".png": $mime_type = "image/png"; break;
			case ".gif": $mime_type = "image/gif"; break;
			case ".jpeg": $mime_type = "image/jpeg"; break;
			case ".jpg": $mime_type = "image/jpeg"; break;
			case ".txt": $mime_type = "text/plain"; break;
			case ".htm": $mime_type = "text/html"; break;
			case ".html": $mime_type = "text/html"; break;
		}

		ini_set('memory_limit','512M');
		if ($attachment) {

			@ini_set('zlib.output_compression', 'Off');

			// new download function works with IE6+SSL(http://fr.php.net/manual/fr/function.header.php#65404)
			$filepath = rawurldecode($filepath);
			$size = filesize($filepath);

			header('Content-Type: ' . $mime_type);
			header('Content-Disposition: attachment; filename="'.$destfilename.'"');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Accept-Ranges: bytes');
			header('Cache-control: private');
			header('Pragma: private');

			@ob_end_clean();
			//while (ob_get_contents()) @ob_end_clean();
			//@set_time_limit(3600);

			ob_end_flush();

			/////  multipart-download and resume-download
			if(isset($_SERVER['HTTP_RANGE']))
			{

				list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
				str_replace($range, "-", $range);
				$size2 = $size-1;
				$new_length = $size-$range;
				header("HTTP/1.1 206 Partial Content");
				header("Content-Length: $new_length");
				header("Content-Range: bytes $range$size2/$size");
			}
			else
			{
				$size2=$size-1;
				header("Content-Length: ".$size);
			}


			/*
			set_time_limit(0);
			$seek_start = 0;
			fseek($filepath, $seek_start);

			while(!feof($filepath)) {
				print(@fread($filepath, 1024*8));
				ob_flush();
				flush();
				if (connection_status()!=0)
				{
					@fclose($filepath);
					exit;
				}
			}

			// file save was a success
			@fclose($filepath);*/
			@readfile($filepath);
			@ob_flush();
			@flush();
			/*
			 *
			$chunksize = 1*(1024*1024);

			$bytes_send = 0;

			if ($fp = fopen($filepath, 'r'))
			{
				if(isset($_SERVER['HTTP_RANGE'])) fseek($fp, $range);

				while(!feof($fp) and (connection_status()==0))
				{
					$buffer = fread($fp, $chunksize);
					print($buffer);//echo($buffer); // is also possible
					flush();
					$bytes_send += strlen($buffer);
					//sleep(1);//// decrease download speed
				}
				fclose($fp);
			}
			else die('error can not open file');
			*/

			/*
			if ($fp = fopen($filepath, 'r')) {
				echo filesize($filepath);die();
				$buffer = fread($fp, filesize($filepath));
				print $buffer;
				flush();
				$bytes_send += strlen($buffer);

				fclose($fp);
			}
			else die('error can not open file');
			 */
			if(isset($new_length)) $size = $new_length;
		}
		else
		{
			header("Content-disposition: inline; filename={$destfilename}");
			header('Content-Type: ' . $mime_type);
			header("Content-Length: ".filesize($filepath));
			header("Pragma: no-cache");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
			header("Expires: 0");

			@readfile($filepath);
		}

		if ($deletefile) unlink($filepath);

		die();

	}
	else return(false);
}

function dims_encodefile($str)
{
	$str = urlencode($str);
	$str = str_replace('+','%20',$str);
	$str = str_replace('%2F','/',$str);
	return($str);
}

function dims_file_getextension($filename)
{
  $filename_array = explode('.',$filename);
  return(strtolower($filename_array[sizeof($filename_array)-1]));
}

function dims_unzip($zip_file, $src_dir, $extract_dir) {
	$cwd = getcwd();

	if (!copy($src_dir . "/" . $zip_file, $extract_dir . "/" . $zip_file)) echo "Error";
	else {
		//chdir($extract_dir."/");
		$cmd= _DIMS_BINPATH."unzip ".escapeshellarg($extract_dir . "/" . $zip_file)." -d ".escapeshellarg($extract_dir."/");

		exec(escapeshellcmd($cmd));
		exec(escapeshellcmd("rm ".escapeshellarg($zip_file)));
		exec(escapeshellcmd("chmod -R 777 *"));
		//chdir($cwd);
	}
}

?>
