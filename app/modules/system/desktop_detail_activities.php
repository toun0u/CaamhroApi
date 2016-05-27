<?

if (!isset($_SESSION['dims']['action_activities']) ) $_SESSION['dims']['action_activities']='events';

if (isset($_GET['action'])) {

}

if (isset($_GET['action'])) {
	$act== dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true, false);
	$oldact=substr($_SESSION['dims']['action_activities'],0,3);
	$newact=substr($act,0,3);
	if ($oldact!=$newact) {
		$_SESSION['dims']['tag_filter']=0;
	}
}

if ($fileinclude!='') {
	require_once($fileinclude);
}
?>