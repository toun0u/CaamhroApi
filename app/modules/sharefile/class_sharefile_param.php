<?
class sharefile_param extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_mod_sharefile_param');
	}

	function verifParam($moduleid) {
		global $db;
		$res=$db->query("select * from dims_mod_sharefile_param where id_module= :idmodule ", array(':idmodule' => $moduleid));

		if ($db->numrows($res)>0) {
			if ($f=$db->fetchrow($res)) {
				$this->open($f['id']);
			}
		}
		else {
			// on initialise
			$this->fields['uniquecode']=true;
			$this->fields['nbcar']=5;
			$this->fields['title_message']="Un ou plusieurs fichiers ont &eacute;t&eacute; mis à disposition. Cliquez sur le lien pour les t&eacute;l&eacute;charger";
			$this->fields['message']="";
			$this->fields['send_message']="";
			$this->fields['nbdays']=60;
			$this->fields['id_module']=$moduleid;
			$this->fields['id_user']=$_SESSION['dims']['userid'];
			$this->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
			$this->save();
		}
	}
}
?>