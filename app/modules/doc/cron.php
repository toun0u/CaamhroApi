<?
ini_set('max_execution_time',-1);

dims_init_module('doc');
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");
require_once(DIMS_APP_PATH . "/modules/doc/class_docfolder.php");

$select =	"
			SELECT		*
			FROM		dims_mod_doc_file";

$result = $db->query($select);
if ($db->numrows($result)>0) {
	$total=$db->numrows($result);
	echo "Cron for doc module : ".$total."\n";ob_flush();
	$cpte=1;
		$currentpath=realpath(".");
	while ($fields = $db->fetchrow($result)) {
		$doc= new docfile();
		$doc->new=false;
		$doc->fields=$fields;
		echo $cpte." / ".$total." => ".$doc->fields['name']." => ";
		ob_flush();
		echo "Content....";
		$doc->getcontent();
		chdir ($currentpath);
		echo "ok  Preview....";
		$doc->getPreview(false);
		chdir ($currentpath);
		$cpte++;
		echo "\n";ob_flush();
	}
}
?>
