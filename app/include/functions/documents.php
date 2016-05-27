<?php
function dims_documents($id_object, $id_record, $default_folders = array(), $id_user = -1, $id_workspace = -1, $id_module = -1)
{
	$db = dims::getInstance()->getDb();

	if ($id_user == -1) $id_user = $_SESSION['dims']['userid'];
	if ($id_workspace == -1) $id_workspace = $_SESSION['dims']['workspaceid'];
	if ($id_module == -1) $id_module = $_SESSION['dims']['moduleid'];

	// generate documents id
	$documents_id = base64_encode("{$id_module}_{$id_object}_".addslashes($id_record));

	$_SESSION['documents']['id_object'] = $id_object;
	$_SESSION['documents']['id_record'] = $id_record;
	$_SESSION['documents']['id_user'] = $id_user;
	$_SESSION['documents']['id_workspace'] = $id_workspace;
	$_SESSION['documents']['id_module'] = $id_module;
	$_SESSION['documents']['documents_id'] = $documents_id;

	require_once(DIMS_APP_PATH . '/include/class_documentsfolder.php');

	// on va chercher la racine
	$res=$db->query("SELECT id FROM dims_documents_folder WHERE id_folder = 0 and id_object = :idobject and id_record = :idrecord", array(
		':idobject' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['documents']['id_object']),
		':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['documents']['id_record']),
	));

	if ($row = $db->fetchrow($res)) $currentfolder = $row['id'];
	else // racine inexistante, il faut la créer
	{
		$documentsfolder = new documentsfolder();
		$documentsfolder->fields['name'] = 'Racine';
		$documentsfolder->fields['id_folder'] = 0;
		$documentsfolder->fields['id_object'] = $_SESSION['documents']['id_object'];
		$documentsfolder->fields['id_record'] = $_SESSION['documents']['id_record'];
		$documentsfolder->fields['id_module'] = $_SESSION['documents']['id_module'];
		$documentsfolder->fields['id_user'] = $_SESSION['documents']['id_user'];
		$documentsfolder->fields['id_workspace'] = $_SESSION['documents']['id_workspace'];
		$currentfolder = $documentsfolder->save();

		foreach ($default_folders as $foldername)
		{
			$documentsfolder = new documentsfolder();
			$documentsfolder->fields['id_folder'] = $currentfolder;
			$documentsfolder->fields['id_object'] = $_SESSION['documents']['id_object'];
			$documentsfolder->fields['id_record'] = $_SESSION['documents']['id_record'];
			$documentsfolder->fields['id_module'] = $_SESSION['documents']['id_module'];
			$documentsfolder->fields['id_user'] = $_SESSION['documents']['id_user'];
			$documentsfolder->fields['id_workspace'] = $_SESSION['documents']['id_workspace'];
			$documentsfolder->fields['name'] = $foldername;
			$documentsfolder->fields['system'] = 1;
			$documentsfolder->save();
		}
	}
	?>
	<div id="dimsdocuments_<? echo $documents_id; ?>">
	</div>
	<script type="text/javascript">
		dims_documents_browser('','<? echo $documents_id; ?>');
		//dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_browser','','dimsdocuments_<? echo $documents_id; ?>');
	</script>
	<?
}

function dims_documents_getpath($createpath = false)
{
	$path = _DIMS_PATHDATA._DIMS_SEP."documents";

	if ($createpath)
	{
		// test for existing _DIMS_PATHDATA path
		if (!is_dir(_DIMS_PATHDATA)) mkdir(_DIMS_PATHDATA);

		if ($path != '' && !is_dir($path)) mkdir($path);
	}

	return($path);
}

function dims_documents_countelements($id_folder)
{
	$db = dims::getInstance()->getDb();

	$c = 0;

	$res=$db->query("SELECT count(id) as c FROM dims_documents_folder WHERE id_folder = :idfolder", array(':idfolder' => array('type' => PDO::PARAM_INT, 'value' => $id_folder)));
	if ($row = $db->fetchrow($res)) $c += $row['c'];

	$res=$db->query("SELECT count(id) as c FROM dims_documents_file WHERE id_folder = :idfolder", array(':idfolder' => array('type' => PDO::PARAM_INT, 'value' => $id_folder)));
	if ($row = $db->fetchrow($res)) $c += $row['c'];

	return($c);
}


// Function    : pdf2txt()
// Arguments   : $filename - Filename of the PDF you want to extract
// Description : Reads a pdf file, extracts data streams, and manages
//				 their translation to plain text - returning the plain
//				 text at the end
// Authors		: Jonathan Beckett, 2005-05-02
//							  : Sven Schuberth, 2007-03-29

function pdf2txt($filename){

	$data = getFileData($filename);

	$s=strpos($data,"%")+1;

	$version=substr($data,$s,strpos($data,"%",$s)-1);
	if(substr_count($version,"PDF-1.2")==0)
		return handleV3($data);
	else
		return handleV2($data);


}
// handles the verson 1.2
function handleV2($data){

	// grab objects and then grab their contents (chunks)
	$a_obj = getDataArray($data,"obj","endobj");

	foreach($a_obj as $obj){

		$a_filter = getDataArray($obj,"<<",">>");

		if (is_array($a_filter)){
			$j++;
			$a_chunks[$j]["filter"] = $a_filter[0];

			$a_data = getDataArray($obj,"stream\r\n","endstream");
			if (is_array($a_data)){
				$a_chunks[$j]["data"] = substr($a_data[0],
strlen("stream\r\n"),
strlen($a_data[0])-strlen("stream\r\n")-strlen("endstream"));
			}
		}
	}

	// decode the chunks
	foreach($a_chunks as $chunk){

		// look at each chunk and decide how to decode it - by looking at the contents of the filter
		$a_filter = split("/",$chunk["filter"]);

		if ($chunk["data"]!=""){
			// look at the filter to find out which encoding has been used
			if (substr($chunk["filter"],"FlateDecode")!==false){
				$data =@ gzuncompress($chunk["data"]);
				if (trim($data)!=""){
					$result_data .= ps2txt($data);
				} else {

					//$result_data .= "x";
				}
			}
		}
	}

	return $result_data;
}

//handles versions >1.2
function handleV3($data){
	// grab objects and then grab their contents (chunks)
	$a_obj = getDataArray($data,"obj","endobj");
	$result_data="";
	foreach($a_obj as $obj){
		//check if it a string
		if(substr_count($obj,"/GS1")>0){
			//the strings are between ( and )
			preg_match_all("|\((.*?)\)|",$obj,$field,PREG_SET_ORDER);
			if(is_array($field))
				foreach($field as $data)
					$result_data.=$data[1];
		}
	}
	return $result_data;
}

function ps2txt($ps_data){
	$result = "";
	$a_data = getDataArray($ps_data,"[","]");
	if (is_array($a_data)){
		foreach ($a_data as $ps_text){
			$a_text = getDataArray($ps_text,"(",")");
			if (is_array($a_text)){
				foreach ($a_text as $text){
					$result .= substr($text,1,strlen($text)-2);
				}
			}
		}
	} else {
		// the data may just be in raw format (outside of [] tags)
		$a_text = getDataArray($ps_data,"(",")");
		if (is_array($a_text)){
			foreach ($a_text as $text){
				$result .= substr($text,1,strlen($text)-2);
			}
		}
	}
	return $result;
}

function getFileData($filename){
	$handle = fopen($filename,"rb");
	$data = fread($handle, filesize($filename));
	fclose($handle);
	return $data;
}

function getDataArray($data,$start_word,$end_word){

	$start = 0;
	$end = 0;
	unset($a_result);

	while ($start!==false && $end!==false){
		$start = strpos($data,$start_word,$end);
		if ($start!==false){
			$end = strpos($data,$end_word,$start);
			if ($end!==false){
				// data is between start and end
				$a_result[] = substr($data,$start,$end-$start+strlen($end_word));
			}
		}
	}
	return $a_result;
}

?>
