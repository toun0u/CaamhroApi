<?php
/*
 * Created by Patrick Nourrissier - NETLOR SAS, 21/03/2008
 * patrick[at]netlor.fr
 */

class dims {
	private static $dims_instance = null;

	public $db;						// connector to database abstraction layer : Mysql, Oracle
	private $initsession;			// variable for state of init session
	private $scriptenv;				// nom du script : admin, index, admin-light, index-light
	private $http_host;				// current host name
	private $urlpath;				// current url path
	private $protocol;				// current protocol
	private $enabledbackoffice;		// is backoffice enabled
	private $webworkspaces;			// list of Web enabled workspaces
	private $adminworkspaces;		// list of Admin enabled workspaces
	private $workspaces;			// list of all enabled workspaces

	private $table_descriptions;	//cyril : contient dans la session les DESCRIBE des tables qui ont été réalisées durant le fonctionnement du dims
	private $mbobjects;				//cyril : liste des des mb_objects
	private $mbclasses;				//cyril : gestion des classes
	private $mbtables;				//cyril : gestion des tables
	private $mbrelations;			//cyril : gestion des relations entre les objets
	private $currentmoduletypeid;	//cyril : stocke le current module type id
	private $metadata;				//cyril : liste des meta-datas pour l'index


	private $current_dims;			//cyril : c'est pour la synchro, cela permet de connaître l'id du dims courant
	private $root_dims;				//cyril : correspond au dims qui est la référent du dims_courant
	private $sync_matrix;			//cyril : matrice de synchronisation entre les dims
	private $synchronized_dims;		//cyril : contient les infos des dims

	private $to_index_done;			//cyril : contient les to_index déjà executés

	private $currentlang;			// current language : french, english or spanish
	private $ssl;					// is ssl mod activated
	private $wcemodules;			// list of WCE moduleid enabled with current workspaces
	private $admintemplate;			// name of bakcoffice template, used with smarty engine
	private $modeoffice;			// switch from back or front office
	private $userid;				// current dims userid
	private $modules;				// list of current modules existing in current workspace
	private $allmodules;			// list of current modules existing in current workspace
	private $autoconnectmodules;	// list of current modules existing in current workspace
	private $actions;				// list of actions allowed

	public $error;					// code error
	public $debugmode;				// mode de debuggage
	public $allwcemodules;			// All available WCE modules with back et front office

	private $xmppObject = null;		// xmpp connection for dims
	private $timer;					// timer de generation

	private $shutdownqueries = array();

	/**
	* Class constructor
	*
	* @access public
	**/
	function __construct() {
		$this->initsession=false;
		$this->urlpath="";
		$this->http_host=(isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
		$this->initsession=false;
		$this->workspaces=array();
		$this->domainworkspaces=array();
		$this->webworkspaces=array();
		$this->adminworkspaces=array();
		$this->currentlang=1;
		$this->ssl=false;
		$this->allmodules=array();
		$this->wcemodules=array();
		$this->allwcemodules=array();
		$this->modules=array();
		$this->autoconnectmodules=array();
		$this->actions=array();
		$this->admintemplate="";
		$this->modeoffice="admin";
		$this->scriptenv="";
		$this->userid=0;
		$this->userfromid=0; // utilise si on peut switcher sur une autre personne
		$this->db="";
		$this->error=0;
		$this->timer=null;

		register_shutdown_function(array($this, 'execshutdownqueries'));
	}

	public function isDimsSync(){
		return (defined('_DIMS_SYNC_ACTIVE') && _DIMS_SYNC_ACTIVE);
	}
	public function isConnected() {
		return (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']);
	}

	/**
	 * ou provenant de mysql ou oracl
	 * @return dims_db
	**/
	public function getDb() {
		return($this->db);
	}

	public function setDb($datab) {
		$this->db=$datab;
	}

	public function getTimer() {
		return($this->timer);
	}

	public function setTimer($timer) {
		$this->timer=$timer;
	}

	public function setError($code) {
		$this->error=$code;
	}

	public function setUserId($userid) {
		$this->userid=$userid;
	}

	public function getUserId() {
		return($this->userid);
	}

	public function setUserFromId($userid) {
		$this->userfromid=$userid;
	}

	public function getUserFromId() {
		return($this->userfromid);
	}

	public function setLang($idlang) {
		$this->currentlang=$idlang;
	}

	public function getLang() {
		return $this->currentlang;
	}

	public function getSsl() {
		return($this->ssl);
	}

	public function setSsl($valssl) {
		$this->ssl=$valssl;
	}

	public function getProtocol() {
		return($this->protocol);
	}

	public function setProtocol($val) {
		$this->protocol=$val;
	}

	public function getAdminTemplate() {
		return($this->admintemplate);
	}

	public function setAdminTemplate($valadmintpl) {
		$this->admintemplate=$valadmintpl;
	}

	public function getUrlPath() {
		return($this->urlpath);
	}

	public function setUrlPath($path) {
		$this->urlpath=$path;
	}

	public function getModeOffice() {
		return($this->modeoffice);
	}

	public function setModeOffice($mode) {
		$this->modeoffice=$mode;
	}

	public function getScriptEnv() {
		return($this->scriptenv);
	}

	public function setScriptEnv($script) {
		$this->scriptenv=$script;
	}

	public function getWceModules($expand=false) {
		if (!$expand) {
			return $this->wcemodules;
		}
		else {
			return $this->allwcemodules;
		}
	}

	public function getAutoConnectModules($idworkspace=0) {
		if ($idworkspace>0) {
			if (isset($this->autoconnectmodules[$idworkspace])) return $this->autoconnectmodules[$idworkspace];
			else return array();
		}
		else return($this->autoconnectmodules);
	}

	public function getWorkspaces($idworkspace=0) {
		if ($idworkspace>0) {
			if (isset($this->workspaces[$idworkspace])) return $this->workspaces[$idworkspace];
			else return array();
		}
		else return($this->workspaces);
	}

	public function getModule($idmodule=0,$idworkspace=0) {
		if ($idmodule==0) $idmodule=$_SESSION['dims']['moduleid'];
		//if ($idworkspace==0) $idworkspace=$_SESSION['dims']['workspaceid'];

		if ($idworkspace>0 && isset($this->modules[$idworkspace]) && isset($this->modules[$idworkspace][$idmodule])) {
			return $this->modules[$idworkspace][$idmodule];
		}
		elseif (isset($this->allmodules[$idmodule])) return $this->allmodules[$idmodule];
			else return array();
	}

	public function getAllModules() {
		return($this->allmodules);
	}

	public function getCurrentModuleId() {
		return $_SESSION['dims']['moduleid'];
	}

	public function getCurrentWorkspaceId() {
		return $_SESSION['dims']['workspaceid'];
	}

	public function getModules($idworkspace=0) {
		if ($idworkspace>0) {
			if (isset($this->modules[$idworkspace])) return $this->modules[$idworkspace];
			else return array();
		}
		else return($this->modules);
	}

	public function setModules($mods) {
		$this->modules=$mods;
	}

	public function getWebWorkspaces($idworkspace=0) {
		if ($idworkspace>0) {
			if (isset($this->webworkspaces[$idworkspace])) return $this->webworkspaces[$idworkspace];
			else return array();
		}
		else {
			return($this->webworkspaces);
		}

	}

	public function getListWebWorkspaces() {
		$ch="";
		if (empty($this->webworkspaces)) return ("0");
		else {
			foreach ($this->webworkspaces as $id=>$w) {
				if ($ch=="") $ch=$id;
				else $ch.=",".$id;
			}
			return $ch;
		}
	}

	public function setWebWorkspaces($workspaces) {
		$this->webworkspaces=$workspaces;
	}

	// @deprecated return comma-separated values string - Does not scale with pdo.
	public function getListWorkspaces() {
		$ch="";
		if (empty($this->workspaces)) return ("0");
		else {
		foreach ($this->workspaces as $id=>$w) {
			if ($ch=="") $ch=$id;
			else $ch.=",".$id;
		}
		return $ch;
		}
	}

	public function getAdminWorkspaces() {
		return($this->adminworkspaces);
	}

	public function setAdminWorkspaces($workspaces) {
		$this->adminworkspaces=$workspaces;
	}

	public function getHttpHost() {
		return $this->http_host;
	}

	public function setHttpHost($httphost) {
		$this->http_host=$httphost;
	}

	public function getAllWorkspaces() {
		return $this->domainworkspaces;
	}

	/* Cyril - Fonction permettant de remonter tous les workspaces qui portent une instance du module_type dont le nom est donné en paramètre */
	public function getWorkspacesUsingModuleType($module_name){
		$lst = array();
		if(!empty($module_name)){
			$res = $this->db->query("SELECT w.*
									 FROM dims_workspace w
									 INNER JOIN dims_module_workspace dmw ON dmw.id_workspace = w.id
									 INNER JOIN dims_module m ON m.id = dmw.id_module
									 INNER JOIN dims_module_type mt ON mt.id = m.id_module_type
									 WHERE mt.label = ?", array($module_name));

			while($fields = $this->db->fetchrow($res)){
				$w = new workspace();
				$w->openFromResultSet($fields);
				$lst[$w->get('id')] = $w;
			}
		}
		return $lst;
	}

	public function checkMobile() {
		$checkmobile=0;
		if (!isset($_SESSION['dims']['MOBILE_VERSION'])) {
			$_SESSION['dims']['MOBILE_VERSION']=0;
			require_once DIMS_APP_PATH . '/include/Browser.php';

			$browser = new Browser();
			$checkmobile=$browser->isMobile();
		}
		else {
			$checkmobile=$_SESSION['dims']['MOBILE_VERSION'];
		}

		//echo $this->modeoffice;die();
		/*if (isset($_GET['dims_mobileversion'])) {
			echo $_SESSION['dims']['PREVIOUS_DOMAIN']."<br>";
		}*/

		if ($this->modeoffice!='admin') {
			if ($checkmobile) {
				// on a un mobile
				$dims_previousdomain=dims_load_securvalue('dims_previousdomain',dims_const::_DIMS_CHAR_INPUT,true,false,false);
				$dims_mobileversion=dims_load_securvalue('dims_mobileversion',dims_const::_DIMS_CHAR_INPUT,true,false,false);

				if ($dims_previousdomain!='') {
						$_SESSION['dims']['PREVIOUS_DOMAIN']=$dims_previousdomain;
				}

				if ($dims_mobileversion!="") {
					$_SESSION['dims']['MOBILE_VERSION']=$dims_mobileversion;

					// on veut revenir à une version normale
					if ($dims_mobileversion==0) {
						$newdomain='';
						// si on avait deja switche depuis une version normal, on reprend l'ancien domaine, sinon on recherche un valide
						if (!isset($_SESSION['dims']['PREVIOUS_DOMAIN']) || (isset($_SESSION['dims']['PREVIOUS_DOMAIN']) && $_SESSION['dims']['PREVIOUS_DOMAIN']==$this->getHttpHost()))  {

							$lstw="0";
							foreach ($this->webworkspaces as $w) {
									$lstw[] = $w['id'];
							}

							$params = array();
							$select = "SELECT		distinct dims_domain.access,
											admin_template,
											dw.access as accessdw,
											domain
									FROM		dims_domain
									inner join	dims_workspace_domain as dw
									on		dw.id_domain=dims_domain.id
									and		dw.id_workspace in (".$this->db->getParamsFromArray($lstw, 'idworkspace', $params).")
									and		dw.access>0
									and		dims_domain.mobile = 0
									INNER JOIN	dims_workspace as w
									ON		w.id=dw.id_workspace";

							$res=$this->db->query($select, $params);
							if ($this->db->numrows($res)>0) {
									$fdom=$this->db->fetchrow($res);
									$newdomain=$fdom['domain'];
							}
						}
						else {
							$newdomain=$_SESSION['dims']['PREVIOUS_DOMAIN'];
						}

						//	on a un domaine, on y retourne
						if ($newdomain!='') {
							//unset($_SESSION['dims']['MOBILE_VERSION']);
							header("location: ".dims_urlencode($this->getProtocol(). $newdomain."/index.php"));
						}
					}
					else {
						// on redirige sur la nouvelle url sans les parametres
						header("location: ".dims_urlencode($this->getProtocol().$this->getHttpHost()."/index.php"));
					}
					// on a traite la valeur 1, on reinit
					$dims_mobileversion=0;
				}

				// on fait un test si on doit rechercher qq chose ou non
				if ((!isset($_SESSION['dims']['MOBILE_VERSION']) && $dims_mobileversion=='') || $dims_mobileversion==1) {
					//|| $_SESSION['dims']['MOBILE_VERSION']==0
					// on regarde si il existe un domaine mobile ou non
					if (!empty($this->webworkspaces)) {
					//if (isset($tabdomains[0]) && strtolower($tabdomains[0]=='www')) {
						// requete sur la table des domaines, extraction du prefix de site
						// construction du domaine avec le prefix mobile
						$lstw="0";
						foreach ($this->webworkspaces as $w) {
								$lstw[] = $w['id'];
						}

						$params = array();
						$select = "SELECT		distinct dims_domain.access,
										admin_template,
										dw.access as accessdw,
										domain
								FROM		dims_domain
								inner join	dims_workspace_domain as dw
								on		dw.id_domain=dims_domain.id
								and		dw.id_workspace in (".$this->db->getParamsFromArray($lstw, 'idworkspace', $params).")
								and		dw.access>0
								and		dims_domain.mobile = 1
								INNER JOIN	dims_workspace as w
								ON		w.id=dw.id_workspace";

						$res=$this->db->query($select, $params);
						if ($this->db->numrows($res)>0) {
							$fdom=$this->db->fetchrow($res);
							$_SESSION['dims']['MOBILE_VERSION']=1;
							header("location: ".dims_urlencode($this->getProtocol(). $fdom['domain']."/index.php?dims_mobileversion=1&dims_previousdomain=".$this->getHttpHost()));
						}
					}
				}
			}
		}
		else {
			if ($checkmobile) {
				$_SESSION['dims']['MOBILE_VERSION']=1;
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// initialisation des workspaces repondant au nom de domaine courant
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function initWorkspaces(){
		$cache = false;
		$cached=false;
		$apcKey = "_DIMS_INITWORKSPACES_".md5($this->getHttpHost());
		if(APC_EXTENSION_LOADED)
			$timeCache = apc_fetch($apcKey."_TIME", $cached);
		$allRows = array();

		// On garde les workspace en cache pendant 60 seconds (peut etre plus long voir supprime si on vide le cache
		// au changement de workspace, mais c'est deja un debut)
		if (!$cached || (time()-$timeCache) > 60) {
			$select = "SELECT	distinct	dims_domain.access,
										admin_template,
										dw.access as accessdw,
										w.id,w.id_workspace_feedback,
										w.tickets,w.contact,w.project,w.planning,w.newsletter,
										w.iprules,w.macrules,w.ssl,w.mustdefinerule,w.label,
										w.twitter, w.facebook, w.web, w.google_plus, w.youtube,
										w.meta_description, w.meta_author, w.meta_copyright, w.meta_robots,
										domain, w.switchuser
					FROM				dims_domain
					inner join			dims_workspace_domain as dw
					on					dw.id_domain=dims_domain.id
					and					(dims_domain.domain='*' or dims_domain.domain= :domain )
					and					dims_domain.access>=1
					INNER JOIN			dims_workspace as w
					ON					w.id=dw.id_workspace";

			$res=$this->db->query($select,array(':domain' => array('type' => PDO::PARAM_STR, 'value' => $this->getHttpHost())));
			while ($fields = $this->db->fetchrow($res)) { $allRows[] = $fields; }

			if(APC_EXTENSION_LOADED) {
				apc_store($apcKey, $allRows);
				apc_store($apcKey."_TIME", time());
			}
		} else {
			$allRows = apc_fetch($apcKey);
		}

		// verify if actual domain is in array list
		$found=_DIMS_DEBUGMODE;

		foreach ($allRows as $fields) {
			if ($fields['domain']=="*") $this->enabledbackoffice=true;
			elseif ($this->http_host==$fields['domain']) $this->enabledbackoffice=true;

			// construction de la liste des workspaces qui repondent
			if (!isset($this->workspaces[$fields['id']])) {

				//$this->workspaces[$fields['id']]=$fields['id'];
				$fields['activeticket']=$fields['tickets'];
				$fields['activeannot']=false;
				$fields['adminlevel']=0;
				$fields['activeprofil']=false;
				$fields['activesearch']=false;
				$fields['activecontact']=$fields['contact'];
				$fields['activeproject']=$fields['project'];
				$fields['activeplanning']=$fields['planning'];
				$fields['activenewsletter']=$fields['newsletter'];
				$fields['activeswitchuser']=$fields['switchuser'];
				$fields['web']=$fields['web'];

				if (isset($fields['id_workspace_feedback'])) $fields['id_workspace_feedback']=$fields['id_workspace_feedback'];

				switch ($fields['accessdw']) {
					case 1:
						$this->webworkspaces[$fields['id']]=$fields;
						break;
					case 0:
					case 2:
						if ($fields['accessdw']==2) $this->webworkspaces[$fields['id']]=$fields;

						$workspace_ok = $ip_ok = $mac_ok = false;

						$iprules = dims_getiprules($fields['iprules']);
						$macrules = $fields['macrules'];
						// test ip rules if existing for current group
						if ($iprules) {
							$userip = ip2long($_SERVER['REMOTE_ADDR']);
							foreach($iprules as $startip => $endip)	{
							if ($userip >= $startip && $userip <= $endip) $ip_ok = true;
							}
						}
						else $ip_ok = true;

						// test mac rules if existing for current group
						if ($macrules) {
							foreach($macrules as $macaddress) {
							$macaddress = str_replace(array("-",".","/",",",";",":"," "),"",$macaddress);
							if (in_array($macaddress, $_SESSION['dims']['usermac'])) $mac_ok = true;
							}
						}
						else $mac_ok = true;

						// test ssl
						$verifssl=true;
						if ($fields['ssl']==1 && !$this->ssl) $verifssl=false;

						// test de connexion
						$workspace_ok = (($ip_ok && $mac_ok && $verifssl));

						if ($workspace_ok) {
							$this->adminworkspaces[$fields['id']]=$fields;
						}
						// traitement du template pour les workspaces admin
						if ($fields['admin_template']!="") $this->setAdminTemplate($fields['admin_template']);
						break;
				}
			}
		}

		// fusion
		$this->workspaces=$this->webworkspaces+$this->adminworkspaces;
		$this->domainworkspaces=$this->workspaces;

		// test web mode, if ok load wce array list
		if ($this->modeoffice=="web" && (!empty($this->webworkspaces) || !empty($this->workspaces))) {
			// construction de la liste des id de workspaces
			$params=array();
			$lstw=array();
			foreach ($this->webworkspaces as $w) {
				$lstw[] = $w['id'];
			}

			if (!empty($lstw)!='') {
				// recherche des modules "WCE"
				$params = array();
				$res=$this->db->query(	"
						SELECT		dims_module_workspace.id_module,
								dims_module_workspace.id_workspace
						FROM		dims_module
						INNER JOIN	dims_module_type
						ON		dims_module.id_module_type = dims_module_type.id
						AND		dims_module_type.label = 'WCE'
						INNER JOIN	dims_module_workspace
						ON		dims_module.id = dims_module_workspace.id_module
						AND		dims_module_workspace.id_workspace IN (".$this->db->getParamsFromArray($lstw, 'idworkspace', $params).")", $params);

				while ($fields = $this->db->fetchrow($res)) $this->wcemodules[$fields['id_module']]=$fields['id_module'];
			}

			$lstw=array();
			foreach ($this->workspaces as $w) {
				$lstw[] = $w['id'];
			}

			if ($lstw!='') {
				// recherche des modules "WCE"
				$params = array();
				$res=$this->db->query(	"
						SELECT		dims_module_workspace.id_module,
								dims_module_workspace.id_workspace
						FROM		dims_module
						INNER JOIN	dims_module_type
						ON		dims_module.id_module_type = dims_module_type.id
						AND		dims_module_type.label = 'WCE'
						INNER JOIN	dims_module_workspace
						ON		dims_module.id = dims_module_workspace.id_module
						AND		dims_module_workspace.id_workspace IN (".$this->db->getParamsFromArray($lstw, 'idworkspace', $params).")", $params);

				while ($fields = $this->db->fetchrow($res)) $this->allwcemodules[$fields['id_module']]=$fields['id_module'];
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// intersection des workspaces courants avec ceux actives pour le user courant
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function intersectUserWorkspaces($workspacesuser)  {
		// on filtre sur les workspaceuser
		foreach($workspacesuser as $wid => $value) {
			if (isset($this->workspaces[$wid])) {
				if ($this->workspaces[$wid]['mustdefinerule']) {
					$workspace_ok = (isset($this->actions[$wid]) || ($value['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN));
					// on ne peut se connecter car restriction
					if (!$workspace_ok) unset($workspacesuser[$wid]);
				}
			}
		}
		foreach($this->adminworkspaces as $key_adm => $tab_admin) {
			if(isset($workspacesuser[$key_adm])) {
				if($workspacesuser[$key_adm]['adminlevel'] > $tab_admin['adminlevel']) $this->adminworkspaces[$key_adm]['adminlevel'] = $workspacesuser[$key_adm]['adminlevel'];
			}
			else {
				unset($this->adminworkspaces[$key_adm]);
			}
		}
		$this->workspaces=$this->webworkspaces+$this->adminworkspaces;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	  optimisation des codes de chargement des informations de session	/////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// preparation des modules
	public function initUserModules() {
		if (!empty($this->workspaces)) {
			$params = array();
			$select =	"
						SELECT		distinct dims_module_type.*,
									dims_module.label AS instancename,
									dims_module.id AS instanceid,
									dims_module.id_workspace As instanceworkspace,
									dims_module.active,
									dims_module.shared,
									dims_module.herited,
									dims_module.adminrestricted,
									dims_module.public,
									dims_module.viewmode,
									dims_module.autoconnect,
									dims_module.transverseview,
									dims_module.id_module_type,
									dims_module_workspace.blockposition,
									dims_module_workspace.id_workspace,
									dims_module_workspace.visible,
									dims_module_workspace.autoconnect as autoconnect2
						FROM		dims_module_type
						INNER JOIN	dims_module ON dims_module.id_module_type = dims_module_type.id
						INNER JOIN	dims_module_workspace
						ON			dims_module_workspace.id_module = dims_module.id
						AND			dims_module_workspace.id_workspace in (".$this->db->getParamsFromArray(array_keys($this->workspaces), 'idworkspace', $params).")
						ORDER BY	dims_module_workspace.position
						";

			$result = $this->db->query($select, $params);
			$elemwork=array();
			$elemwork['id_module_type']=1;
			$elemwork['instanceid']=dims_const::_DIMS_MODULE_SYSTEM;
			$elemwork['instancename']="system";
			$elemwork['moduletype']="system";
			$elemwork['label']="system";
			$elemwork['contenttype']="system";
			$elemwork['system']=1;
			$elemwork['active']=1;
			$elemwork['visible']=0;
			$elemwork['viewmode']=dims_const::_DIMS_VIEWMODE_GLOBAL;

			$this->allmodules[dims_const::_DIMS_MODULE_SYSTEM]=$elemwork;

			foreach ($this->workspaces as $work) {
				$this->modules[$work['id']][dims_const::_DIMS_MODULE_SYSTEM]=$elemwork;
			}

			while ($module = $this->db->fetchrow($result)) {
				$module['autoconnect']=($module['autoconnect'] || $module['autoconnect2']) ? 1 : 0;
				$this->modules[$module['id_workspace']][$module['instanceid']]=$module;

				if ($module['autoconnect'] && $module['active']) $this->autoconnectmodules[$module['id_workspace']][$module['instanceid']]=$module;

				// view all modules available
				if (!isset($this->allmodules[$module['instanceid']])) {
					$this->allmodules[$module['instanceid']]=$module;
				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	  Initialisation de l'ensemble des modules existants  /////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// preparation des modules
	public function initAllModules() {

	$select =	"
			SELECT		distinct dims_module_type.*,
				dims_module.label AS instancename,
				dims_module.id AS instanceid,
				dims_module.id_workspace As instanceworkspace,
				dims_module.active,
				dims_module.shared,
				dims_module.herited,
				dims_module.adminrestricted,
				dims_module.public,
				dims_module.viewmode,
				dims_module.autoconnect,
				dims_module.transverseview,
				dims_module.id_module_type,
				dims_module_workspace.blockposition,
				dims_module_workspace.id_workspace,
				dims_module_workspace.visible,
				dims_module_workspace.autoconnect as autoconnect2
			FROM		dims_module_type
			INNER JOIN	dims_module ON dims_module.id_module_type = dims_module_type.id
			INNER JOIN	dims_module_workspace
			ON			dims_module_workspace.id_module = dims_module.id
			ORDER BY	dims_module_workspace.position
			";

	$result = $this->db->query($select);
	$elemwork=array();
	$elemwork['id_module_type']=1;
	$elemwork['instanceid']=dims_const::_DIMS_MODULE_SYSTEM;
	$elemwork['instancename']="system";
	$elemwork['moduletype']="system";
	$elemwork['label']="system";
	$elemwork['contenttype']="system";
	$elemwork['system']=1;
	$elemwork['active']=1;
	$elemwork['visible']=0;
	$elemwork['viewmode']=dims_const::_DIMS_VIEWMODE_GLOBAL;

	$this->allmodules[dims_const::_DIMS_MODULE_SYSTEM]=$elemwork;

	while ($module = $this->db->fetchrow($result)) {

		// view all modules available
		if (!isset($this->allmodules[$module['instanceid']])) {
		$this->allmodules[$module['instanceid']]=$module;
		}
	}

	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Initialisation des droits lies aux roles ou profils au user ou un des ses groupes rattaches
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function initUserProfile() {
		// prechargement des groups
		if (empty($this->workspaces))
			$this->workspaces[] = 0;
		$params = array();
		$select =	"
					SELECT		distinct
								dims_workspace_group.*,
								dims_group_user.adminlevel as guadminlevel
					FROM		dims_workspace_group
					INNER JOIN	dims_group_user
					ON			dims_workspace_group.id_workspace in (".$this->db->getParamsFromArray(array_keys($this->workspaces), 'idworkspace', $params).")
					AND			dims_workspace_group.id_group=dims_group_user.id_group and id_user = :iduser";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $this->userid);

		$result = $this->db->query($select, $params);
		$tabgroup=array();
		$activecontact = array();
		$activeproject = array();
		$activeplanning = array();
		$activenewsletter = array();
		$activeevent = array();
		$activeeventstep = array();
		$activeeventemail = array();
		$activeswitchuser = array();

		while ($fields = $this->db->fetchrow($result)) {
			if($fields['guadminlevel'] > $this->workspaces[$fields['id_workspace']]['adminlevel'])	$this->workspaces[$fields['id_workspace']]['adminlevel'] = $fields['guadminlevel'];

			if($fields['adminlevel'] > $this->workspaces[$fields['id_workspace']]['adminlevel'])	$this->workspaces[$fields['id_workspace']]['adminlevel'] = $fields['adminlevel'];
			$this->workspaces[$fields['id_workspace']]['activesearch'] = $this->workspaces[$fields['id_workspace']]['activesearch'] || $fields['activesearch'];
			$this->workspaces[$fields['id_workspace']]['activeticket'] = $this->workspaces[$fields['id_workspace']]['activeticket'] || $fields['activeticket'];
			$this->workspaces[$fields['id_workspace']]['activeprofil'] = $this->workspaces[$fields['id_workspace']]['activeprofil'] || $fields['activeprofil'];
			$this->workspaces[$fields['id_workspace']]['activeannot'] = $this->workspaces[$fields['id_workspace']]['activeannot'] || $fields['activeannot'];
			//Cyril - 29/06/2012 - Gestion des droits d'accès au front
			$this->workspaces[$fields['id_workspace']]['activefront'] = (isset($this->workspaces[$fields['id_workspace']]['activefront']) && $this->workspaces[$fields['id_workspace']]['activefront']) || $fields['activefront'];
			$this->workspaces[$fields['id_workspace']]['activeback'] = (isset($this->workspaces[$fields['id_workspace']]['activeback']) && $this->workspaces[$fields['id_workspace']]['activeback']) || $fields['activeback'];
			$activecontact[$fields['id_workspace']] = $fields['activecontact'];
			$activeproject[$fields['id_workspace']] = $fields['activeproject'];
			$activeplanning[$fields['id_workspace']] = $fields['activeplanning'];
			$activeswitchuser[$fields['id_workspace']] = $fields['activeswitchuser'];

			$this->workspaces[$fields['id_workspace']]['activeevent'] =  $fields['activeevent'];
			$this->workspaces[$fields['id_workspace']]['activeeventstep'] =  $fields['activeeventstep'];
		$this->workspaces[$fields['id_workspace']]['activeeventemail'] =  $fields['activeeventemail'];

		// 11/08/2010
			if (isset($this->webworkspaces[$fields['id_workspace']])) {
				$this->webworkspaces[$fields['id_workspace']]['activeevent']=$this->workspaces[$fields['id_workspace']]['activeevent'];
		$this->webworkspaces[$fields['id_workspace']]['activeeventstep']=$this->workspaces[$fields['id_workspace']]['activeeventstep'];
		$this->webworkspaces[$fields['id_workspace']]['activeeventemail']=$this->workspaces[$fields['id_workspace']]['activeeventemail'];
			}

			if (isset($this->adminworkspaces[$fields['id_workspace']])) {
				$this->adminworkspaces[$fields['id_workspace']]['activeevent']=$this->workspaces[$fields['id_workspace']]['activeevent'];
		$this->adminworkspaces[$fields['id_workspace']]['activeeventstep']=$this->workspaces[$fields['id_workspace']]['activeeventstep'];
		$this->adminworkspaces[$fields['id_workspace']]['activeeventemail']=$this->workspaces[$fields['id_workspace']]['activeeventemail'];
			}
		}

		$params = array();
		// prechargement des workspaces_user
		$select =	"
					SELECT		distinct
								id_workspace,
								activesearch,
								activeticket,
								activeprofil,
								activeannot,
								activecontact,
								activeproject,
								activeplanning,
								activenewsletter,
								activeevent,
								activeeventstep,
								activeeventemail,
								activeswitchuser,
								activefront,
								activeback,
								adminlevel
					FROM		dims_workspace_user
					WHERE		id_workspace in (".$this->db->getParamsFromArray(array_keys($this->workspaces), 'idworkspace', $params).")
					AND			id_user = :iduser";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

		$result = $this->db->query($select, $params);

		while ($fields = $this->db->fetchrow($result)) {

			$this->workspaces[$fields['id_workspace']]['activesearch'] = $this->workspaces[$fields['id_workspace']]['activesearch'] || $fields['activesearch'];
			$this->workspaces[$fields['id_workspace']]['activeticket'] = $this->workspaces[$fields['id_workspace']]['tickets'] || $fields['activeticket'];
			$this->workspaces[$fields['id_workspace']]['activeprofil'] = $this->workspaces[$fields['id_workspace']]['activeprofil'] || $fields['activeprofil'];
			$this->workspaces[$fields['id_workspace']]['activeannot'] = $this->workspaces[$fields['id_workspace']]['activeannot'] || $fields['activeannot'];
			$this->workspaces[$fields['id_workspace']]['activecontact'] = $this->workspaces[$fields['id_workspace']]['activecontact'] && ((isset($activecontact[$fields['id_workspace']]) && $activecontact[$fields['id_workspace']]) || $fields['activecontact']);
			$this->workspaces[$fields['id_workspace']]['activeproject'] = $this->workspaces[$fields['id_workspace']]['activeproject'] && ((isset($activeproject[$fields['id_workspace']]) && $activeproject[$fields['id_workspace']]) || $fields['activeproject']);
			$this->workspaces[$fields['id_workspace']]['activeplanning'] = $this->workspaces[$fields['id_workspace']]['activeplanning'] && ((isset($activeproject[$fields['id_workspace']]) && $activeproject[$fields['id_workspace']]) || $fields['activeplanning']);
			//Cyril - 29/06/2012 - Gestion des droits d'acc√®s au front
			$this->workspaces[$fields['id_workspace']]['activefront'] = (isset($this->workspaces[$fields['id_workspace']]['activefront']) &&  $this->workspaces[$fields['id_workspace']]['activefront']) || $fields['activefront'];
			$this->workspaces[$fields['id_workspace']]['activeback'] = (isset($this->workspaces[$fields['id_workspace']]['activeback']) &&	$this->workspaces[$fields['id_workspace']]['activeback'] ) || $fields['activeback'];

			$this->workspaces[$fields['id_workspace']]['activenewsletter'] = $this->workspaces[$fields['id_workspace']]['activenewsletter'] && ((isset($activenewsletter[$fields['id_workspace']]) && $activenewsletter[$fields['id_workspace']]) || $fields['activenewsletter']);
			$this->workspaces[$fields['id_workspace']]['activeswitchuser'] = $this->workspaces[$fields['id_workspace']]['activeswitchuser'] && ((isset($activeswitchuser[$fields['id_workspace']]) && $activeswitchuser[$fields['id_workspace']]) || $fields['activeswitchuser']);
			//$this->workspaces[$fields['id_workspace']]['activeevent'] = $this->workspaces[$fields['id_workspace']]['share_info'] && ((isset($activeevent[$fields['id_workspace']]) && $activeevent[$fields['id_workspace']]) || $fields['activeevent']);

			if (isset($this->workspaces[$fields['id_workspace']]['activeevent']))
				$this->workspaces[$fields['id_workspace']]['activeevent'] = $this->workspaces[$fields['id_workspace']]['activeevent'] ||  $fields['activeevent'];
			else
				$this->workspaces[$fields['id_workspace']]['activeevent'] = $fields['activeevent'];

		if (isset($this->workspaces[$fields['id_workspace']]['activeeventemail']))
				$this->workspaces[$fields['id_workspace']]['activeeventemail'] = $this->workspaces[$fields['id_workspace']]['activeeventemail'] ||	$fields['activeeventemail'];
			else
				$this->workspaces[$fields['id_workspace']]['activeeventemail'] = $fields['activeeventemail'];

			if (isset($this->workspaces[$fields['id_workspace']]['activeeventstep']))
				$this->workspaces[$fields['id_workspace']]['activeeventstep'] = $this->workspaces[$fields['id_workspace']]['activeeventstep'] ||  $fields['activeeventstep'];
			else
				$this->workspaces[$fields['id_workspace']]['activeeventstep'] = $fields['activeeventstep'];

			if (!isset($this->workspaces[$fields['id_workspace']]['adminlevel']) || ($fields['adminlevel'] > $this->workspaces[$fields['id_workspace']]['adminlevel']))	{
				$this->workspaces[$fields['id_workspace']]['adminlevel'] = $fields['adminlevel'];
			}

			// check for adminworkspace et webworkspace for adminevent option
			// 11/08/2010
			if (isset($this->webworkspaces[$fields['id_workspace']])) {
				$this->webworkspaces[$fields['id_workspace']]['activeevent']=$this->workspaces[$fields['id_workspace']]['activeevent'];
		$this->webworkspaces[$fields['id_workspace']]['activeeventstep']=$this->workspaces[$fields['id_workspace']]['activeeventstep'];
		$this->webworkspaces[$fields['id_workspace']]['activeeventemail']=$this->workspaces[$fields['id_workspace']]['activeeventemail'];
		$this->webworkspaces[$fields['id_workspace']]['activeswitchuser']=$this->workspaces[$fields['id_workspace']]['activeswitchuser'];
			}

			if (isset($this->adminworkspaces[$fields['id_workspace']])) {
				$this->adminworkspaces[$fields['id_workspace']]['activeevent']=$this->workspaces[$fields['id_workspace']]['activeevent'];
		$this->adminworkspaces[$fields['id_workspace']]['activeeventstep']=$this->workspaces[$fields['id_workspace']]['activeeventstep'];
		$this->adminworkspaces[$fields['id_workspace']]['activeeventemail']=$this->workspaces[$fields['id_workspace']]['activeeventemail'];
		$this->adminworkspaces[$fields['id_workspace']]['activeswitchuser']=$this->workspaces[$fields['id_workspace']]['activeswitchuser'];
			}

		// on libère ce qui a été testé avec le user et le groupe
		unset($activecontact[$fields['id_workspace']]);
		}

		// on regarde enfin l'attribution au niveau du groupe (ceux qui n'ont pas de correspondance de groupe
		foreach ($activecontact as $id_workspace => $value) {
			$this->workspaces[$id_workspace]['activecontact'] = $this->workspaces[$id_workspace]['activecontact'] && $activecontact[$id_workspace];
			$this->workspaces[$id_workspace]['activeproject'] = $this->workspaces[$id_workspace]['activeproject'] && isset($activeproject[$id_workspace]) && $activeproject[$id_workspace];
			$this->workspaces[$id_workspace]['activeplanning'] = $this->workspaces[$id_workspace]['activeplanning'] && isset($activeplanning[$id_workspace]) && $activeplanning[$id_workspace];
			$this->workspaces[$id_workspace]['activeswitchuser'] = $this->workspaces[$id_workspace]['activeswitchuser'] && isset($activeswitchuser[$id_workspace]) && $activeswitchuser[$id_workspace];
		}

	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Set list of current actions allowed
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getActions() {
		return($this->actions);
	}

	public function setActions($actions) {
		$this->actions=$actions;
	}

	public function verifyWorkspaceExists($workid) {
		return isset($this->workspaces[$workid]);
	}

	public function getEnabledBackoffice() {

		return(!empty($this->adminworkspaces) && $this->enabledbackoffice);
	}
	public function getInitSession() {
		return $this->initsession;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Initialisation de la session
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setInitSession($newinit) {
		$this->initsession=$newinit;
		$this->debugmode=constant('_DIMS_DEBUGMODE');
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Mise e jour de la session
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function sessionUpdate() {
		$_SESSION['dims']['currentrequesttime'] = time();

		if (!isset($_SESSION['dims']['lastrequesttime'])) {
			$_SESSION['dims']['lastrequesttime']=$_SESSION['dims']['currentrequesttime'];
			$this->sessionReset();
		}

		$diff = $_SESSION['dims']['currentrequesttime'] - $_SESSION['dims']['lastrequesttime'];

		if ($diff > _DIMS_SESSIONTIME && _DIMS_SESSIONTIME != '' && _DIMS_SESSIONTIME != 0) $this->sessionReset();
		else {
			$_SESSION['dims']['lastrequesttime'] = $_SESSION['dims']['currentrequesttime'];
			$_SESSION['dims']['remoteip'] = dims_getip();
		}

		$_SESSION['dims']['scriptname'] = $this->scriptenv;

		if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=="on")
				|| (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS'])=='on'))  {
			$this->setSsl(true);
		}
		else {
			$this->setSsl(false);
		}

		$_SESSION['dims']['ssl']=$this->ssl;

		switch($_SESSION['dims']['scriptname']) {
			case 'admin.php':
			case 'admin-light.php':
				if ($_SESSION['dims']['mode'] !=  'admin' && isset($_SESSION['dims']['back_workspaceid'])) {
					//die($_SESSION['dims']['workspaceid']." ".$_SESSION['dims']['back_workspaceid']);
					$_SESSION['dims']['workspaceid'] = $_SESSION['dims']['back_workspaceid'];
					if (isset($_SESSION['dims']['back_currentworkspace'])) {
						$_SESSION['dims']['currentworkspace']=$_SESSION['dims']['back_currentworkspace'];
					}
				}

				$_SESSION['dims']['mode'] = 'admin';
				$this->modeoffice='admin';
			break;

			case 'index.php':
				$this->modeoffice='web';
			break;
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Reset de la session
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function sessionReset() {
		$securule=false;

		$this->setInitSession(true);
		if (isset($_SESSION['dims']['security'])) {
			$securule=true;
			$security=$_SESSION['dims']['security'];
		}

		$_SESSION['dims'] = array(
						'login'			=> '',
						'password'		=> '',
						'userid'		=> '',
						'workspaceid'	=> '',
						'webworkspaceid'	=> '',
						'adminlevel'	=> 0,

						'connected'	=> false,
						'loginerror'	=> false,
						'paramloaded'	=> false,
						'mode'			=> 'admin',
						'remoteip'		=> dims_getip(),
						'host'			=> $_SERVER['HTTP_HOST'],
						'scriptname'	=> $this->scriptenv,
						'wcemoduleid'	=> 0,
						'allworkspaces' => '',
						'currentrequesttime'	=> time(),
						'lastrequesttime'		=> time(),
						'moduleid'		=>	'',
						'mainmenu'		=>	'',
						'action'		=>	'public',
						'moduletabid'	=>	'',
						'moduletype'	=>	'',
						'moduletypeid'	=>	'',
						'modulelabel'	=>	'',
						'moduleicon'	=>	'',
						'defaultskin'	=>	'',
						'template_name'	=>	'',
						'template_path'	=>	'',
						'newtickets'	=>	0,
						'ssl'		=> (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=="on") ? true : false,
						'browser'		=> $_SERVER['HTTP_USER_AGENT']
					);

		if ($securule) $_SESSION['dims']['security']=$security;
		//$_SESSION['dims']['browser']['pda'] = ($_SESSION['dims']['browser']['PDA_NAME'] != ''); /** ! Etrange : Voir pat :S ! **/
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification du niveau de restriction de dims vis e vis du user courant
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function verifSecurityLevel() {
		if (!_DIMS_DEBUGMODE) {
			unset($_SESSION['dims']['security']['ipallowed']);

			$res=$this->db->query("select * from dims_security_ipallowed");

			if ($this->db->numrows($res)>0) {
				while($sec=$this->db->fetchrow($res)) {
					$_SESSION['dims']['security']['ipallowed'][$sec['ip']]=$sec['ip'];
				}
			}

			if(isset($_SESSION['dims']['security']['level'])) {
				$timerestant=dims_timestamp_add(dims_createtimestamp(),0,0,-($sec['datefilter']+_DIMS_SECURITY_CRITICAL_TIMEROUT));
				if (!isset($_SESSION['dims']['security']['ipallowed'][$_SERVER['REMOTE_ADDR']])) {
					if ($timerestant<0) {
						$timerestant=abs($timerestant);
						$secondes=$timerestant%60;
						$min=($timerestant-$secondes)/60;
						echo "<p style=\"border-size:1px;border-color:#BCBCBC;text-align:center;margin-top:100px\">";
						switch($_SESSION['dims']['security']['level']) {
							case dims_const::_DIMS_SECURITY_LEVEL_CRITICAL :
								echo _DIMS_SECURITY_CRITICAL_ERROR;
								echo "<br> $min min. et $secondes ".$_DIMS['cste']['_DIMS_REST_SEC'];
								break;
						}
						echo "</p>";
						die();
					}
				}
			}
			else {
				$last_minutes_ago = date("YmdHis",mktime(date("H"),(date("i")),date("s")-_DIMS_SECURITY_CRITICAL_TIMEROUT,date("m"),date("d"),date("Y")));
				$sql="SELECT datefilter, level from dims_security_filter where ip = :ip and datefilter > :lastminute order by id desc";

				$res=$this->db->query($sql, array(
					':ip' => array('type' => PDO::PARAM_STR, 'value' => $_SERVER['REMOTE_ADDR']),
					':lastminute' => array('type' => PDO::PARAM_INT, 'value' => $last_minutes_ago),
				));

				if ($this->db->numrows($res)>0) {
					while($sec=$this->db->fetchrow($res)) {
						$timerestant=dims_createtimestamp()-($sec['datefilter']+(_DIMS_SECURITY_CRITICAL_TIMEROUT));

						if (!isset($_SESSION['dims']['security']['ipallowed'][$_SERVER['REMOTE_ADDR']])) {
							if ($timerestant<0) {
								$timerestant=abs($timerestant);
								$secondes=$timerestant%60;
								$min=($timerestant-$secondes)/60;
								setcookie("nocache", "1");
								echo "<p style=\"border-size:1px;border-color:#BCBCBC;text-align:center;margin-top:100px\">";
								switch($sec['level']) {
									case dims_const::_DIMS_SECURITY_LEVEL_CRITICAL :
										$_SESSION['dims']['security']['time']=$sec['datefilter'];
										$_SESSION['dims']['security']['level']=$sec['level'];
										echo _DIMS_SECURITY_CRITICAL_ERROR;

										echo "<br> $min min. et $secondes ".$_DIMS['cste']['_DIMS_REST_SEC'];
								}
								echo "</p>";
								die();
							}
							else {

								unset($_SESSION['dims']);
								unset($_SESSION);
								if ($_SESSION['dims']['scriptname']!='index.php' && $_SESSION['dims']['scriptname']!='./index.php' && $_SESSION['dims']['scriptname']!='') {
									dims_redirect("./index.php");
								}
							}
						}
					}
					if ($this->db->numrows($res) > 5) {
						// on garde seulement les 5 derniers; si on utilise beaucoup dims, on fait beaucoup d'enregistrements
						// dans la table, et mysql rale beaucoup sur 118k enregistrements a trier (vu lors des tests de charge)
						$sql = "delete from dims_security_filter where ip=:ip and datefilter>:lastminute order by id asc limit :nb";

						$res = $this->db->query($sql, array(
							":ip" => array('type' => PDO::PARAM_STR, 'value' => $_SERVER['REMOTE_ADDR']),
							":lastminute" => array('type' => PDO::PARAM_INT, 'value' => $last_minutes_ago),
							":nb" => array('type' => PDO::PARAM_INT, 'value' => 5-$this->db->numrows($res))));
					}
				}
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification de la procedure de connexion
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function verifyConnect($dims_login,$dims_password, $already_hashed = false, $dims_rfid = 0) {
		$scriptenv = $this->getScriptEnv();
		$before_connexion_url = !empty($_SESSION['dims']['before_connexion_url']) ? $_SESSION['dims']['before_connexion_url'] : null;
		$ok=false;
		$oldencoding=false;

		$previous_user = array();
		if($already_hashed && isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && empty($_SESSION['dims']['previous_user'])){
			$previous_user = array(
				'l' => $_SESSION['dims']['login'],
				'p' => $_SESSION['dims']['password'],
				'f' => (empty($_SESSION['dims']['urlpath'])?$this->getScriptEnv():$_SESSION['dims']['urlpath']),
			);
		}

		if ($dims_rfid > 0) {
			// ALTER TABLE  `dims_user` ADD  `rfid_uid` VARCHAR( 25 ) NOT NULL ;
			$rfid_append = "OR dims_user.rfid_uid=?";
		} else {
			$rfid_append = "";
		}

		$select =	'
			SELECT		dims_user.*,
					dims_user_type.label as type
			FROM		dims_user
			LEFT JOIN	dims_user_type
			ON		dims_user.id_type = dims_user_type.id
			WHERE		(dims_user.login = ? '.$rfid_append.')
			AND		(dims_user.status = 1 OR dims_user.status IS NULL)
			';//status différent de 0 pour dire que si c'est null et bah ça fait comme si c'est ok (pour la migration le temps de faire un update à 1 sur tous les comptes)

		// par defaut ds l'ensemble des configs
		if (defined('_DIMS_SECURITY_UNIQUE_LOGIN') && _DIMS_SECURITY_UNIQUE_LOGIN) {
			$select.=" limit 0,1";
		}

		$salexists=false;
		if ($dims_rfid > 0) {
			$res=$this->db->query($select,array($dims_login,$dims_rfid));
		} else {
			$res=$this->db->query($select,array($dims_login));
		}

		if ($this->db->numrows($res)>0) {// user found
			while (($fields=$this->db->fetchrow($res)) && !$ok) {
				// read stored password
				$hash=$fields['password'];
				if($already_hashed && $hash == $dims_password){
					$ok = true;
				}
				else{
					if (defined('_DIMS_SECURITY_CRYPTO_SALT') && _DIMS_SECURITY_CRYPTO_SALT !='' &&
						defined('_DIMS_SECURITY_CRYPTO_MODE') && _DIMS_SECURITY_CRYPTO_MODE !='' &&
						defined('_DIMS_SECURITY_CRYPTO_COST') && _DIMS_SECURITY_CRYPTO_COST !='' &&
						isset($fields['salt'])) {

						$saltuser=$fields['salt'];
						$salexists=true;

						if ($fields['salt']!='' && $this->comparePassword($dims_password,$hash,$saltuser,_DIMS_SECURITY_CRYPTO_SALT,$oldencoding) || $dims_rfid>0) {
							$ok=true;
						}
						elseif ($this->comparePassword( $dims_password, $hash )) { // verification ancienne méthode
							$oldencoding=true;
							$ok=true;
						}
					}
					else {
						if ($this->comparePassword( $dims_password, $hash )) {
							//$oldencoding=true;
							$ok=true;
						}
					}
				}

				//if ($ok || dims_comparePassword( $dims_password, $hash ) || ($already_hashed && $dims_password == $hash)) {
				if ($ok) {
					if ($fields['date_expire'] != '' && $fields['date_expire'] != '00000000000000') {

						if ($fields['date_expire'] <= dims_createtimestamp()) {
							dims_create_user_action_log(dims_const::_SYSTEM_ACTION_LOGIN_ERR, $dims_login,dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM);
							session_destroy();
							$_SESSION = array();

							if ($scriptenv=="")  $scriptenv="/index.php?";

							if(!empty($_SERVER['QUERY_STRING'])){
								$query = array();
								parse_str($_SERVER['QUERY_STRING'],$query);
								unset($query['dims_errorcode']);
								$query['dims_errorcode'] = _DIMS_ERROR_LOGINEXPIRE;
								dims_redirect(dims::getInstance()->getScriptEnv()."?".http_build_query($query));
							}else
								dims_redirect(dims::getInstance()->getScriptEnv()."?dims_errorcode="._DIMS_ERROR_LOGINEXPIRE);
						}
					}

					$this->sessionReset();
					$_SESSION['dims']['connected']	= 1;
					$_SESSION['dims']['login']		= $fields['login'];
					$_SESSION['dims']['password']	= $fields['password'];
					$_SESSION['dims']['userid']	= $fields['id'];
					if ($salexists) $_SESSION['dims']['salt']		= $fields['salt'];
					$_SESSION['dims']['usertype']	= $fields['type'];
					$_SESSION['dims']['user_code_of_conduct'] = $fields['code_of_conduct'];
					$this->userid = $fields['id'];

					if(!empty($previous_user) && empty($_SESSION['dims']['previous_user'])){
						$_SESSION['dims']['previous_user'] = $previous_user;
					}
				}

			}
		}

		if ($ok) {// user found
			if ($oldencoding) {
				if (defined('_DIMS_SECURITY_CRYPTO_SALT') && _DIMS_SECURITY_CRYPTO_SALT !='' &&
					defined('_DIMS_SECURITY_CRYPTO_MODE') && _DIMS_SECURITY_CRYPTO_MODE !='' &&
					defined('_DIMS_SECURITY_CRYPTO_COST') && _DIMS_SECURITY_CRYPTO_COST !='' &&
					$salexists ) {

					// update stronger security encoding with salt method
					// generate new password with salt value
					$this->getPasswordHash($dims_password,$hash,$saltuser);

					// update table for saving salt and password
					if ($saltuser!='' && $hash !='') {
						$_SESSION['dims']['password']	= $hash;
						$res=$this->db->query("UPDATE dims_user set password= ?,salt= ? where id= ? ",array($hash,$saltuser,$_SESSION['dims']['userid']));
					}
				}

			}

			// set main menu to dims_const::_DIMS_MENU_HOME
			$dims_mainmenu = dims_const::_DIMS_MENU_HOME;

			// build mac address filter
			if (isset($dims_usermac)) {
				$_SESSION['dims']['usermac'] = split(' ',trim($dims_usermac));
				foreach($_SESSION['dims']['usermac'] as $key => $value) {
					$_SESSION['dims']['usermac'][$key] = str_replace(array("-",".","/",",",";",":"," "),"",$value);
				}
			}
			// call init session
			$this->setInitsession(true);

			$oldSid = session_id();
			session_regenerate_id();
			require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_visite.php";
			article_visite::updateSidVisiste($oldSid);

			// log user connexion
			dims_create_user_action_log(dims_const::_SYSTEM_ACTION_LOGIN_OK, $dims_login,dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM);

			//Cyril - 19/07/2012 - Stockage dans la session du fait qu'on vient tout juste de se connecter
			$_SESSION['dims']['from_connexion_user'] = true; // on traite la redirection sur la page d'accueil privée dans le display.php du module wce

			// Do NOT use cache if user logged in.
			$_SESSION['dims']['nocache'] = true;

			//Cyril - 10/07/2013 - Redirection sur l'url passée en param si elle existe
			if(!empty($before_connexion_url)){
				unset($_SESSION['dims']['before_connexion_url']); //à priori inutile puisqu'on réinitialise la session, mas par sécurité
				dims_redirect($before_connexion_url);
			}else{
				dims_redirect($this->getScriptEnv());
			}
		}
		else {
			// log user connexion failed
			dims_create_user_action_log(dims_const::_SYSTEM_ACTION_LOGIN_ERR, $dims_login,dims_const::_DIMS_MODULE_SYSTEM,dims_const::_DIMS_MODULE_SYSTEM);
			/*session_destroy();
			$_SESSION = array();*/
			$this->userid = 0;
			// redirect to connexion form

			if(!empty($before_connexion_url)){
				unset($_SESSION['dims']['before_connexion_url']); //à priori inutile puisqu'on réinitialise la session, mas par sécurité
				dims_redirect($before_connexion_url);
			}

			if ($scriptenv=="")  $scriptenv="/index.php?";

			if (!isset( $_SERVER['QUERY_STRING']) || isset( $_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']=='') {

				if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
					$query = array();
					parse_str(parse_url($_SERVER['HTTP_REFERER'],PHP_URL_QUERY),$query);
					unset($query['dims_errorcode']);
					$query['dims_errorcode'] = _DIMS_ERROR_LOGINEXPIRE;
					dims_redirect(dims::getInstance()->getScriptEnv()."?".http_build_query($query));
				}
				else {
					if(!empty($_SERVER['QUERY_STRING'])){
						$query = array();
						parse_str($_SERVER['QUERY_STRING'],$query);
						unset($query['dims_errorcode']);
						$query['dims_errorcode'] = _DIMS_ERROR_LOGINEXPIRE;
						dims_redirect(dims::getInstance()->getScriptEnv()."?".http_build_query($query));
					}
					else{
						dims_redirect(dims::getInstance()->getScriptEnv()."?dims_errorcode="._DIMS_ERROR_LOGINEXPIRE);
					}
				}
			}
			else {
				if(!empty($_SERVER['QUERY_STRING'])){
					$query = array();
					parse_str($_SERVER['QUERY_STRING'],$query);
					unset($query['dims_errorcode']);
					$query['dims_errorcode'] = _DIMS_ERROR_LOGINEXPIRE;
					dims_redirect(dims::getInstance()->getScriptEnv()."?".http_build_query($query));
				}
				else{
					dims_redirect(dims::getInstance()->getScriptEnv()."?dims_errorcode="._DIMS_ERROR_LOGINEXPIRE);
				}
			}

		}
	}

	private function encodeBytes($input) {
		// The following is code from the PHP Password Hashing Framework
		$itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$output = '';
		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);

		return $output;
  }

	/*
	 * fonction permettant la comparaison des hash en fonction de l'algo utilise
	 */
	public function comparePassword($password,$hash,$saltuser = '',$saltdims = _DIMS_SECURITY_CRYPTO_SALT,&$oldencoding=false) {
		$result=false;
		if(defined('_DIMS_SECURITY_CRYPTO_MODE') && $saltuser!='') {
			switch(_DIMS_SECURITY_CRYPTO_MODE) {
				case 'crypt':
					$hashgen=crypt(hash_hmac('sha512',$password,$saltdims), '$2a$'._DIMS_SECURITY_CRYPTO_COST.'$'.$saltuser.'$') ;
					$result = "$hashgen"==="$hash";

					if(!$result){ // ancienne méthode
						$salt=$this->encodeBytes($saltdims.$saltuser);
						//$prefix='$2a$'._DIMS_SECURITY_CRYPTO_COST.'$'._DIMS_SECURITY_CRYPTO_SALT.$saltuser.'$';
						$prefix='$2a$'._DIMS_SECURITY_CRYPTO_COST.'$'.$salt.'$';
						$hashtocompare=crypt($password,$prefix);

						$hasfrom= substr($prefix,0,strlen($prefix)-1).$hash;
						$result= "$hashtocompare"==="$hasfrom";
						$oldencoding = true;
					}
					break;
			}
		} else {
			$result = dims_comparePassword($password,$hash);
			if(!$result && defined('_DIMS_DEBUGMODE') && _DIMS_DEBUGMODE){
				$result = md5($password) === "$hash";
			}
			$oldencoding = true;
		}

		return $result;
	}


	/*
	 * Fonction permettant la génération aléatoire d'un salt
	 */
	private function getPasswordSalt() {
		$bytes='';
		$len=11;

		$pattern="./123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$lenpattern=strlen($pattern);
		if (defined('_DIMS_SECURITY_CRYPTO_MODE') && _DIMS_SECURITY_CRYPTO_MODE=='crypt')
			$len=11;

		//if(function_exists('openssl_random_pseudo_bytes') &&
		//	(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL slow on Win
		//	$bytes = openssl_random_pseudo_bytes($len);
		//}
		//else {
			// boucle pour la generation d'un nombre aléatoire
			for ($i=0;$i<$len;$i++) {
				$offset = rand(0,$lenpattern-1);
				$bytes.=$pattern[$offset];
			}
		//}

		return $bytes;
	}

	/*
	 * fonction permettant la generation des hash en fonction de l'algo utilise
	 */
	public function getPasswordHash($password,&$hash,&$saltuser,$crypto_salt=_DIMS_SECURITY_CRYPTO_SALT) {
		$saltuser = str_shuffle(uniqid('', true));
		$hash=crypt(hash_hmac('sha512',$password,$crypto_salt), '$2a$'._DIMS_SECURITY_CRYPTO_COST.'$'.$saltuser.'$') ;

		/*$saltuser = $this->getPasswordSalt();
		$salt=$this->encodeBytes($crypto_salt.$saltuser);

		//$prefix='$2a$'._DIMS_SECURITY_CRYPTO_COST.'$'._DIMS_SECURITY_CRYPTO_SALT.$saltuser.'$';
	$prefix='$2a$'._DIMS_SECURITY_CRYPTO_COST.'$'.$salt.'$';


		$hash=crypt($password, $prefix);
		$hash=substr($hash,strlen($prefix)-1);*/
		return array($hash, $saltuser);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Update user connexion
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function updateConnect() {
		if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0) {
			// test if userfromid is defined
			if (isset($_SESSION['dims']['userformid']) && $_SESSION['dims']['userformid']>0) {
				$userid=$_SESSION['dims']['userformid'];
			}
			else {
				$userid=$_SESSION['dims']['userid'];
			}

			// Re-check credentials to make sure hacker isn't running session even if the password
			// has been changed.
			$query="SELECT id FROM dims_user
					 WHERE login=:login AND password=:password LIMIT 1";

			$res = $this->db->query($query, array(
				':login' => array('type' => PDO::PARAM_STR, 'value' => $_SESSION['dims']['login']),
				':password' => array('type' => PDO::PARAM_STR, 'value' => $_SESSION['dims']['password'])
			));

			$row = $this->db->fetchrow($res);
			if ($this->db->numrows($res) == 0 || $row['id'] != $_SESSION['dims']['userid']) {
				// The credentials has changed, destroy the session and get back to login
				session_destroy();
				$_SESSION = array();
				dims_redirect("./index.php");
				die();
			}

			$this->userid = $_SESSION['dims']['userid'];

			// Update last connection (delayed, on shutdown)
			$query="update
				dims_user
				set beforelastconnexion=lastconnexion,lastconnexion=".	dims_createtimestamp()." where id= :iduser LIMIT 1";

			// on met a jour la date de dernière action
			$this->addshutdownquery($query, array(
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $userid),
			));
		}
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Load header values
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function loadHeader() {
		//header('Content-type: text/html; charset=iso-8859-1');
		header('Content-type: text/html; charset='._DIMS_ENCODING);
		header( 'X-Frame-Options: SAMEORIGIN' );

		if (isset($_SERVER['SCRIPT_NAME'])) {

			if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=="on")
				|| (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS'])=='on'))  {
				if (substr(basename($_SERVER['SCRIPT_NAME']),4)!='http') {
					$this->setUrlPath("https://".$_SERVER['HTTP_HOST']."/".basename($_SERVER['SCRIPT_NAME']));
				}
				else {
					$this->setUrlPath(basename($_SERVER['SCRIPT_NAME']));
				}
				$this->setProtocol('https://');
			}
			else {

				if (substr(basename($_SERVER['SCRIPT_NAME']),4)!='http') {
					$this->setUrlPath("http://".$_SERVER['HTTP_HOST']."/".basename($_SERVER['SCRIPT_NAME']));

				}
				else {
					$this->setUrlPath(basename($_SERVER['SCRIPT_NAME']));
				}
				$this->setProtocol('http://');
			}
		}
		else {
			$this->setUrlPath("");
			$this->setProtocol('http://');
		}

		$this->scriptenv = basename($_SERVER['SCRIPT_FILENAME']);

		if (substr(PHP_OS, 0, 3) == 'WIN') define ('_DIMS_SERVER_OSTYPE', 'windows');
		else define ('_DIMS_SERVER_OSTYPE', 'unix');

		switch(_DIMS_SERVER_OSTYPE) {
		case 'windows':
				define ('_DIMS_SEP', '\\');
			break;
		default:
			case 'unix':
				define ('_DIMS_SEP', '/');

			break;
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Disconnect dims user
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function checkLogout() {
		$dims_logout=dims_load_securvalue('dims_logout',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($dims_logout){ //session_status() != PHP_SESSION_DISABLED) { // PHP >=5.4.0
			$previous_user = array();
			if(!empty($_SESSION['dims']['previous_user'])){
				$previous_user = $_SESSION['dims']['previous_user'];
			}
			
			session_destroy();
			setcookie ("cookie", "", time() - 3600);
			$_SESSION = array();
			if(empty($previous_user)){
				header("location: ".$this->getScriptEnv());
			}else{
				header("location: ".$previous_user['f']."?dims_url=".base64_encode("dims_login=".$previous_user['l']."&dims_password=".$previous_user['p']."&already_hashed=1"));
			}
			die();
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification d'une action d'un module pour un user courant dans un workspace
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function isActionAllowed($actionid = -1, $workspaceid = -1, $moduleid = -1) {
		if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
		if ($moduleid == -1) $moduleid = $_SESSION['dims']['moduleid']; // get session value if not defined

		if ($actionid == -1) return ($this->isAdmin($workspaceid) || isset($this->actions[$workspaceid][$moduleid]));
		else return ($this->isAdmin($workspaceid) || isset($this->actions[$workspaceid][$moduleid][$actionid]));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification des droits de gestionnaire d'espace
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function isManager($workspaceid=-1) {
		if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined

		return(isset($this->workspaces[$workspaceid]['adminlevel']) && $this->workspaces[$workspaceid]['adminlevel']>= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification des droits d'administrateur d'espace
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function isAdmin($workspaceid=-1) {
		if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
		return(isset($this->workspaces[$workspaceid]['adminlevel']) && $this->workspaces[$workspaceid]['adminlevel']>= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification des droits de gestionnaire de contenus
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function isContentManager($workspaceid) {
		if (isset($this->actions[$workspaceid])) {
			foreach($this->actions[$workspaceid] as $moduleid => $action) {
				$contentmanager |= $this->isModuleManager($workspaceid, $moduleid);
			}
		}
		return($this->isAdmin($workspaceid) || $contentmanager);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification des droits de gestionnaire de module
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function isModuleManager($workspaceid,$moduleid) {
		return ($this->isAdmin($workspaceid) || isset($this->actions[$workspaceid][$moduleid][0]));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification si module disponible
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function isModuleEnabled($moduleid,$workspaceid=-1) {
		if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
		if ($moduleid == -1) $moduleid = $_SESSION['dims']['moduleid']; // get session value if not defined
		//$mods=$this->getModules($workspaceid);
		$allmods= $this->getAllModules();
	//return (isset($moduleid) && (($moduleid==dims_const::_DIMS_MODULE_SYSTEM && $this->ismanager($workspaceid)) || $this->isAdmin($workspaceid)) && isset($mods[$moduleid])); // ||  isset($this->actions[$workspaceid][$moduleid])) || );
		return (isset($moduleid) && (($moduleid==dims_const::_DIMS_MODULE_SYSTEM && $this->ismanager($workspaceid)) || ($this->isAdmin($workspaceid)) || ($moduleid!=dims_const::_DIMS_MODULE_SYSTEM && isset($allmods[$moduleid])))); // ||  isset($this->actions[$workspaceid][$moduleid])) || );
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////	 Verification des donnees enregistrees
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function verif_data($element) {
		global $_DIMS;
		$error=false;

		if (isset($element->fields)) {
			// test existing object
			if ($element->new) $error=true;
			// test workspace accessible
			$lstwork=$this->getWorkspaces();
			if (isset($element->fields['id_workspace'])) {
				if ($element->fields['id_workspace']>0 && !isset($lstwork[$element->fields['id_workspace']])) $error=true;
				if (isset($element->fields['id_module']) && $element->fields['id_workspace']>0 && !$this->isModuleEnabled($element->fields['id_module'],$element->fields['id_workspace'])) $error=true;
			}
		}

		if ($error) {
			echo $_DIMS['cste']['_DIMS_SECURITY_ERROR'];
			die();
		}
	}

	public function getScriptAccessObject($id_module_type, $id_object, $id_record, $id_module=0,&$objectname='') {
		$scriptenv = $this->getScriptEnv();

		$select = 'SELECT * FROM dims_mb_object WHERE id_module_type = :idmoduletype AND id = :idmbobject';
		$res=$this->db->query($select, array(
			':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $id_module_type),
			':idmbobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
		));

		$tab_from = array();
		$tab_from[] = "<IDMODULE>";
		$tab_from[] = "<IDRECORD>";
		$tab_to = array();
		$tab_to[] = $id_module;
		$tab_to[] = $id_record;


		$fields = $this->db->fetchrow($res);
		$script = "admin.php".str_replace($tab_from, $tab_to,$fields['script']);

		$objectname=$fields['label'];
		return ($script);
	}

	public function updateLiveStats() {
		///////////////////////////////////////////////////////////////////////////
		// UPDATE LIVE STATS
		///////////////////////////////////////////////////////////////////////////

		if (session_id()!='') {
			if (_DIMS_SESSIONTIME <= 86400) {
				$timestplimit=date("YmdHis",mktime(0, 0, date("G")*3600+date("i")*60-1*_DIMS_SESSIONTIME, date("m"), date("d"), date("Y")));
			}
			else $timestplimit = date("YmdHis",mktime(0, 0, date("G")*3600+date("i")*60-1*86400, date("m"), date("d"), date("Y")));;

			$currentdate=dims_createtimestamp();
			// on va temporiser le controle de connexion

			$timecheck=120; // par defaut controle toute les 5 secondes
			if (defined('_DIMS_SESSIONTIMECHECK'))	$timecheck=_DIMS_SESSIONTIMECHECK;

			if (!isset($_SESSION['dims']['lastcheck']) || $_SESSION['dims']['lastcheck']+$timecheck <$currentdate) {

				$_SESSION['dims']['lastcheck']=$currentdate;
				$connecteduser = new connecteduser();
				$connecteduser->open(session_id());
				$connecteduser->fields['sid'] = session_id();
				if (isset($_SESSION['dims']['remoteip']) && is_array($_SESSION['dims']['remoteip']))
					$connecteduser->fields['ip'] = implode(',', $_SESSION['dims']['remoteip']);
				else
					$connecteduser->fields['ip'] = '';
				$connecteduser->fields['domain'] = (empty($_SESSION['dims']['host'])) ? '' : $_SESSION['dims']['host'];
				$connecteduser->fields['timestp'] = $currentdate;
				$connecteduser->fields['user_id'] = $_SESSION['dims']['userid'];
				$connecteduser->fields['workspace_id'] = $_SESSION['dims']['workspaceid'];
				$connecteduser->fields['module_id'] = $_SESSION['dims']['moduleid'];
				$connecteduser->fields['timestp'] = $currentdate;
				$connecteduser->save();


				if (isset($_SESSION['dims']['workspaceid']) && $_SESSION['dims']['workspaceid']>0) {
					// Utilisateurs connectés
					$res=$this->db->query("SELECT count(*) as c FROM dims_connecteduser WHERE user_id > 0 and timestp > :timestamplimit and workspace_id= :idworkspace group by user_id", array(
						':timestamplimit' => array('type' => PDO::PARAM_INT, 'value' => $timestplimit),
						':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					));
					$row = $this->db->fetchrow($res);
					$_SESSION['dims']['connectedusers'] = $row['c'];

					// visiteurs anonymes
					$res=$this->db->query("SELECT count(distinct(sid)) as c FROM dims_connecteduser WHERE timestp > :timestplimit ", array(':timestplimit' => $timestplimit));
					$row = $this->db->fetchrow($res);
					$_SESSION['dims']['connectedfrontusers'] = $row['c'];
				}
				else {
					$_SESSION['dims']['connectedusers'] = 0;
					$_SESSION['dims']['connectedfrontusers'] = 0;
				}
			}
		}
	}

	public function getConnexionStats($view='month') {
		$result=array();
		$datedeb_timestp='';
		$datefin_timestp='';
		$datedeb='';
		$datefin='';
		$maxelem=0;
		$jdeb=1;
		switch($view) {
			case 'week':
			case 0:
				$datedeb_timestp = mktime(0,0,0,date('n'),date('j')-date('N')+1,date('Y'));
				$datefin_timestp = mktime(0,0,0,date('n'),date('j')-date('N')+7,date('Y'));
				$maxelem=7;
				$posdeb=6;
				$poslen=2;
				$jdeb = date('d',$datedeb_timestp);

				break;
			case 'month':
			case 1:
				$datedeb_timestp = mktime(0,0,0,date('n'),1,date('Y'));
				$datefin_timestp = mktime(0,0,0,date('n')+1,0,date('Y'));
				$maxelem=31;
				$posdeb=6;
				$poslen=2;
				break;

			case 'year':
			case 2:
				$datedeb_timestp = mktime(0,0,0,1,1,date('Y'));
				$datefin_timestp = mktime(0,0,0,12,1,date('Y'));
				$maxelem=12;
				$posdeb=4;
				$poslen=2;
				break;
		}

		for ($i=1;$i<=$maxelem;$i++) {
			$result[$i]=0;
		}

		$datedeb = date('Ymd',$datedeb_timestp);
		$datedeb.='000000';

		$select = 'SELECT distinct sid,domain,user_id,timestp FROM dims_connecteduser WHERE timestp > :datedeb ';

		$res=$this->db->query($select, array(':datedeb' =>  $datedeb) );

		while ($a=$this->db->fetchrow($res)) {
			$val=intval(substr($a['timestp'],$posdeb,$poslen))-$jdeb+1;
			$result[$val]++;
		}
		return ($result);
	}

	public function getShares($id_module,$id_object,$id_record,$id_subrecord=0) {
		$params = array();
		$sql = "SELECT	distinct *
				from	dims_share
				where	id_module= :idmodule
				and		id_object= :idobject
				and		id_record= :idrecord
				and		id_sub_record= :idsubrecord";
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $id_module);
		$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_object);
		$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_record);
		$params[':idsubrecord'] = array('type' => PDO::PARAM_INT, 'value' => $id_subrecord);

		// ajout des filtres de securite supplementaires
		// plusieurs cas : proprietaire du partage par user ou workspace
		// destinaire du partage user ou workspace

		// construction de la liste des workspaces
		$arrayworkspaces = $this->getAdminWorkspaces();
		$lstworkspaces=array();
		if (!empty($arrayworkspaces)) {
			foreach($arrayworkspaces as $k => $w) {
				$lstworkspaces[] = $w['id'];
			}
		}
		else $lstworkspaces[]=0;

		// partage ouvert pour tous
		$sql .=" and		(type_share=0 and id_share=0 and level_share=0";
		if (!empty($lstworkspaces)){
			$sql .=" or		type_share=1 and id_share in (".$this->db->getParamsFromArray($lstworkspaces, 'idworkspace', $params).")";
		}
		$sql .=" or		type_share=2 and id_share = :iduser)";
		$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);

		$res=$this->db->query($sql, $params);

		$arrayshare = array();

		if ($this->db->numrows($res)>0) {
			while ($sh=$this->db->fetchrow($res)) {
				$arrayshare[]=$sh;
			}
		}

		// retour de la structure courante des partages
		return $arrayshare;
	}

	// fonction permettant le chargement des constantes de langue
	public function loadLanguage($idlang=-1,$defaultlanguage=1) {
		$changelang=false;

		if (!isset($_SESSION['cste']) || empty($_SESSION['cste']) || $idlang>-1) {
			if (!isset($_SESSION['dims']['currentlang'])) {
				$_SESSION['dims']['currentlang']=1; // par defaut
			}

			if (!isset($_SESSION['cste']) || empty($_SESSION['cste'])) {
				$changelang=true;
			}

			if ($idlang!=-1 && isset($_SESSION['dims']['lang'][$idlang])) {
				if ($idlang!=$_SESSION['dims']['currentlang']) {
					$_SESSION['dims']['currentlang']=$idlang;
					$this->setLang($_SESSION['dims']['currentlang']);
					$changelang=true;
				}
			}

			if ($changelang) {
				$struct=array();
				$params = array();

				if ($defaultlanguage!=$_SESSION['dims']['currentlang']) {
					$sql="SELECT phpvalue,value FROM dims_constant where id_lang = :idcurrentlang or id_lang= :iddefaultlang ORDER BY id_lang ";
					$params[':iddefaultlang'] = array('type' => PDO::PARAM_INT, 'value' => $defaultlanguage);
					$params[':idcurrentlang'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentlang']);
					if ($_SESSION['dims']['currentlang']>$defaultlanguage) $sql.=" DESC";
					else $sql.=" ASC";
				}
				else {
					$sql="SELECT phpvalue,value FROM dims_constant where id_lang = :idlang";
					$params[':idlang'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentlang']);
				}

				$res=$this->db->query($sql, $params);

				while ($f=$this->db->fetchrow($res)) {
					if (!isset($struct[$f['phpvalue']])) {
						$struct[$f['phpvalue']]=$f['value'];
					}
				}
				$_SESSION['cste']=$struct;
			}
		}
		return $_SESSION['cste'];
	}

	public function getImageByObject($id_module,$id_object) {
		/*if (!isset($modstemp[$id_module_type])) {
			require_once(DIMS_APP_PATH . '/modules/system/class_module_type.php');
			$mod = new module_type();
			$mod->open($id_module_type);
			$modstemp[$id_module_type]=$mod->fields;
		}*/
		$mod=$this->getModule($id_module);
		$label=$mod['contenttype'];

		if ($id_module==1) {
			switch($id_object) {
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$extimg="./common/img/contact.png";
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$extimg="./common/img/factory.gif";
					break;
				case dims_const::_SYSTEM_OBJECT_ACTION:
				case dims_const::_SYSTEM_OBJECT_EVENT:
					$extimg="./common/img/event.png";
					break;
			}
		}
		else {
			$extimg="./common/modules/".$label."./common/img/mod16.png";
		}

		return $extimg;
	}

	function getHistoryObject() {
		global $_DIMS;

		$result="";

		unset($_SESSION['dims']['history_object']);
		unset($_SESSION['dims']['history_object']);

		$rs =$this->db->query("SELECT distinct * from dims_user_history_object where id_user= :iduser order by position", array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
		));

		$pos=1;
		if ($this->db->numrows($rs)>0) {
			while ($fields = $this->db->fetchrow($rs)) {
				$idrecord=$fields['id_record'];
				$idmodule=$fields['id_module'];
				$idobject=$fields['id_object'];
				$key=$idrecord."_".$idobject."_".$idmodule;

				$_SESSION['dims']['history_object'][$key]=$fields;
				$pos++;
			}
		}

		$nb=0;
		if (isset($_SESSION['dims']['history_object'])) {
			$arrayhistory=$_SESSION['dims']['history_object'];
			$tot=sizeof($arrayhistory);
			$currentkey='';
			if (isset($_SESSION['dims']['current_object']['id_record'])) {
				$currentkey=$_SESSION['dims']['current_object']['id_record']."_".$_SESSION['dims']['current_object']['id_object']."_".$_SESSION['dims']['current_object']['id_module'];
			}

		if (!isset($dims_op)) $dims_op='';
			$dims_op_old=$dims_op;
			$dims_op='title';

			$found=false;
			$tot=sizeof($arrayhistory);
			foreach ($arrayhistory as $key=>$elem) {
				// test si on affiche deja en tag used
				if ($key==$currentkey) $style="font-weight:bold;";
				else $style='';

				// collect du nom

				$mod=$this->getModule($elem['id_module']);
				if (isset($mod['contenttype'])) {
					$label_moduletype=$mod['contenttype'];
					$label='';

					if (file_exists(DIMS_APP_PATH . '/modules/'.$label_moduletype.'/block_portal.php')) {
						$_GET['moduleid']=$elem['id_module'];
						$idobject=$elem['id_object'];
						$idrecord=$elem['id_record'];
						//echo DIMS_APP_PATH . '/modules/'.$label_moduletype.'/block_portal.php'."<br>";
						include(DIMS_APP_PATH . '/modules/'.$label_moduletype.'/block_portal.php');

						if ($label=='') {
							$this->db->query("UPDATE dims_user_history_object set position=position-1 where position> :position and id_user= :iduser", array(
								':position' => array('type' => PDO::PARAM_INT, 'value' => $elem['position']),
								':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							));
							// on supprime l'element courant
							$this->db->query("DELETE from dims_user_history_object where id= :idhistoryobject and id_user= :iduser", array(
								':idhistoryobject' => array('type' => PDO::PARAM_INT, 'value' => $elem['id']),
								':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
							));
						}
					}
					else {
						$label=$_DIMS['cste']['_DIMS_LABEL_URL'];
					}

					if ($nb<10 && $label!='') {
						if ($found) $result.="&nbsp;|&nbsp;";

						//$id_module_type=$this->getModule($elem['id_module']);
						$img=$this->getImageByObject($elem['id_module'],$elem['id_object']);
						if ($img!='') {
							$img="<img src=\"".$img."\" alt=\"\" style=\"border:0px;\">";
						}


						$result.='<a style="color:#5E5E5E;'.$style.'" href="javascript:void(0)" onclick="javascript:viewPropertiesObject('.$elem['id_object'].','.$elem['id_record'].','.$elem['id_module'].',1);">'.$img.dims_strcut($label,35).'</a>&nbsp;';

						$nb++;
						$found=true;
					}
		}
			}
			$dims_op=$dims_op_old;
		}

		return $result;
	}

	// fonction permettant de vérifier si une instance du type de module passé en parametre est présent
	public function isModuleTypeEnabled($moduletype,$workspaceid=-1) {
		if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined

		$mods=$this->getModules($workspaceid);

		// boucle sur les modules
		foreach($mods as $id=>$mod) {
			if ($mod['label']==$moduletype) {
				return true;
			}
		}
		return false;
	}

	// fonction permettant de vérifier si une instance du type de module passé en parametre est présent
	public function getModuleByType($moduletype,$workspaceid=-1) {
		if ($workspaceid == -1) $workspaceid = $_SESSION['dims']['workspaceid']; // get session value if not defined
		$arraymods=array();

		$mods=$this->getModules($workspaceid);
		// boucle sur les modules
		foreach($mods as $id=>$mod) {
			if ($mod['label']==$moduletype) {
				$arraymods[]=$mod;
			}
		}
		return $arraymods;
	}

	// fonction permettant la récupération des id
	public function getModulesType() {
		$modulestype=array();
		$params = array();

		$rs =$this->db->query("SELECT distinct mt.* from dims_module_type as mt inner join dims_module as m on m.id_module_type=mt.id and m.id in(".$this->db->getParamsFromArray(array_keys($this->getModules($this->getCurrentWorkspaceId())), 'idmodule', $params).")", $params);

		if ($this->db->numrows($rs)>0) {
		while ($fields = $this->db->fetchrow($rs)) {
			$modulestype[$fields['id']]=$fields['label'];
		}
		}

		return ($modulestype);
	}

	/*
		* Fonction permettant de controler que nous sommes bien présence d'un domain backoffice acceptant aussi le front
		*/
	public function getDomainToFrontEnabled($host='') {
		if ($host=='' && isset($_SESSION['dims']['host'])) {
			$host=$_SESSION['dims']['host'];
		}

		$select = "SELECT	distinct	w.id
					FROM				dims_domain
					inner join			dims_workspace_domain as dw
					on					dw.id_domain=dims_domain.id
					and					(dims_domain.domain=\"*\" or dims_domain.domain=\"".dims_sql_filter($host)."\")
					and					dw.access>=1
					INNER JOIN			dims_workspace as w
					ON					w.id=dw.id_workspace";
	}

	// function getWceModuleFromDomain, permet d'obtenir la liste des modules WCE pour un workspace donne
	public function getWceModulesFromDomain($host='') {
		$wcemodules=array();
		if ($host=='' && isset($_SESSION['dims']['host'])) {
			$host=$_SESSION['dims']['host'];
		}

		// recherche du workspace portant ce domaine
		$select = "SELECT	distinct	w.id
					FROM				dims_domain
					inner join			dims_workspace_domain as dw
					on					dw.id_domain=dims_domain.id
					and					(dims_domain.domain='*' or dims_domain.domain= ? )
					and					dims_domain.access>=1
					INNER JOIN			dims_workspace as w
					ON					w.id=dw.id_workspace";

		$id_workspace=0;
		$res=$this->db->query($select,array($host));
		if ($this->db->numrows($res)>0) {
			while ($fields = $this->db->fetchrow($res)) {
				$id_workspace=$fields['id'];
			}
		}

		// on a trouve l'espace qui porte cet host, on recherche maintenant l'instance de gestion de contenu
		if ($id_workspace>0) {

			// recherche des modules "WCE"
			$res=$this->db->query(	"
					SELECT		dims_module_workspace.id_module,
								dims_module_workspace.id_workspace
					FROM		dims_module
					INNER JOIN	dims_module_type
					ON			dims_module.id_module_type = dims_module_type.id
					AND			dims_module_type.label = 'WCE'
					INNER JOIN	dims_module_workspace
					ON			dims_module.id = dims_module_workspace.id_module
					AND			dims_module_workspace.id_workspace = :idworkspace", array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
			));

			while ($fields = $this->db->fetchrow($res)) $wcemodules[$fields['id_module']]=$fields['id_module'];
		}

		// retour du tableau de module WCE
		return $wcemodules;
	}

	// fonction permettant la mise a jour du relais intercom : modif group/workspace/user
	public function updateActionIntercom($type,$idobj) {

		require_once(DIMS_APP_PATH . '/include/class_dims_action_intercom.php');

		$res=$this->db->query("SELECT id from dims_action_intercom where type= :type and id_object= :idobject", array(
			':type' => array('type' => PDO::PARAM_STR, 'value' => $type),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $idobj),
		));
		$id_actionintercom=0;

		if($this->db->numrows($res)>0) {
			while ($f=$this->db->fetchrow($res)) {
				$id_actionintercom=$f['id'];
			}
		}

		$action_intercom = new dims_action_intercom($this->db);
		if ($id_actionintercom>0) {
			// on ouvre l'existant et on met à jour le timestp
			$action_intercom->open($id_actionintercom);
			$action_intercom->fields['timestp_modify']=dims_createtimestamp();
		}
		else {
			$action_intercom->fields['id_object']=$idobj;
			$action_intercom->fields['type']=$type;
			$action_intercom->fields['timestp_modify']=dims_createtimestamp();
		}
		$action_intercom->save();
	}

	 // fonction permettant de récupérer le nombre de contact
	public function getNbContacts() {
		if (!isset($_SESSION['dims']['desktop_view_date'])) $_SESSION['dims']['desktop_view_date']=1;

		switch($_SESSION['dims']['desktop_view_date']) {
		case 0;
			$jcount=1;
			break;
		case 1:
			$jcount=7;
			break;
		case 2:
			$jcount=14;
			break;
		case 3:
			$jcount=31;
			break;
		case 4:
			$jcount=90;
			break;
		default:
			$jcount=7;
			break;
		}

		if ($jcount==1) {
		$datedeb_timestp = mktime(0,0,0,date('n'),date('j'),date('Y'));
		}
		else {
		$datedeb_timestp = mktime(date('H'),date('i'),date('s'),date('n'),date('j')-$jcount,date('Y'));
		}
		$datedeb_timestp = date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL,$datedeb_timestp);

		$sqlbase= "SELECT		count(*) as cpte
			from		dims_mod_business_contact as c
			where		c.date_create > :datedeb ";

		// on compte
		$tab=array();
		$tab['ct_create']=0;
		$tab['ct_modify']=0;

		$res=$this->db->query($sqlbase, array(':datedeb' => $datedeb_timestp) );
		while ($row = $this->db->fetchrow($res)) {
		$tab['ct_create']=$row['cpte'];
	}

	$sqlbase= "select	count(*) as cpte
			from	dims_mod_business_contact as c
			where	c.timestp_modify > :datedeb ";

	$res=$this->db->query($sqlbase, array(':datedeb' => $datedeb_timestp) );
		while ($row = $this->db->fetchrow($res)) {
		$tab['ct_modify']=$row['cpte'];
	}

	//	on enleve les creations par rapport aux modifications
	if ($tab['ct_modify']>$tab['ct_create']) {
		$tab['ct_modify']-=$tab['ct_create'];
	}

	return($tab);
	}

	public function getRootPath() {
		$rootpath="";
		// traitement du protocole
		if((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=="on")
		   || (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS'])=='on'))  {
			$rootpath = "https://";
		} else {
			$rootpath = "http://";
		}

		/*
		if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $rootpath="http://";
		else $rootpath="https://";*/
		$rootpath.=$_SERVER['HTTP_HOST'];
		//$rootpath = dirname($_SERVER['HTTP_REFERER']);
		return($rootpath);
	}

	public function deleteActions($id_module,$id_object,$id_record) {
		require_once(DIMS_APP_PATH . "/include/class_dims_globalobject.php");

		$gobject = new dims_globalobject();
		$id_gobject=$gobject->getObject($id_module,$id_object,$id_record);

		if ($id_gobject>0) {
			// on recupere la liste des actions qui concernent cet objet
			$select = "SELECT distinct id_action from dims_action_matrix where id_globalobject= :idglobalobject";
			$res=$this->db->query($select, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_gobject),
			));

			if ($this->db->numrows($res)>0) {
				while ($f=$this->db->fetchrow($res)) {
					$lstactions[]=$f['id_action'];
				}
			}

			// on a la liste des actions, on recupere les actions rattache (id_parent)
			if ($lstactions!='') {
				$params = array();
				$select = "SELECT distinct id from dims_action where id_parent in (".$this->db->getParamsFromArray($lstactions, 'idaction', $params).")";
				$res=$this->db->query($select, $params);

				if ($this->db->numrows($res)>0) {
					while ($f=$this->db->fetchrow($res)) {
						$lstactions[]=$f['id'];
					}
				}
			}

			// on a des choses a supprimer maintenant
			if ($lstactions!='') {
				// suppression des actions
				$params = array();
				$select = "DELETE from dims_action where id in (".$this->db->getParamsFromArray($lstactions, 'idaction', $params).")";
				$res=$this->db->query($select, $params);

				// suppression des actions_matrix
				$params = array();
				$select = "DELETE from dims_action_matrix where id_action in (".$this->db->getParamsFromArray($lstactions, 'idaction', $params).")";
				$res=$this->db->query($select, $params);

				// suppression du global objet
				$select = "DELETE from dims_globalobject where id = :idglobalobject";
				$res=$this->db->query($select, array(
					':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_gobject),
				));
			}
		}
	}

	/*
	 * Load tables synchro
	 */
	public function loadTablesSynchro($_DIMS,$force=false) {
		// FIXME : Should be removed soon.
	}

	/**
	 *
	 * @return dims
	 */
	public static function getInstance(){
	if(dims::$dims_instance == null){
		//TODO Message Erreur exception - dims mal initialisé
		dims::setInstance(new dims());
	}
	return dims::$dims_instance;
	}

	public static function setInstance(dims $dims){
	if($dims != null){
		dims::$dims_instance = $dims ;
	}else{
		//TODO MEssage Erreur exception
	}
	}

	private $current_user = null ;
	private $current_workspace = null;

	/**
	 *
	 * @return user
	 */
	public function getCurrentUser(){
	if(isset($_SESSION['dims']['userid'])){
		if($this->current_user != null){
		if($this->current_user->getId() == $_SESSION['dims']['userid']){
			return $this->current_user ;
		}else{
			$this->current_user = new user();
			$this->current_user->open($_SESSION['dims']['userid']);
			return $this->current_user ;
		}
		}else{
		$this->current_user = new user();
		$this->current_user->open($_SESSION['dims']['userid']);
		return $this->current_user ;
		}
	}else{
		//TODO ERROR
		return null ;
	}
	}

	public function getCurrentWorkspace(){
	if(isset($_SESSION['dims']['workspaceid'])){
		if($this->current_workspace != null){
		if($this->current_workspace->getId() == $_SESSION['dims']['workspaceid']){
			return $this->current_workspace ;
		}else{
			$this->current_workspace = new Workspace();
			$this->current_workspace->open($_SESSION['dims']['workspaceid']);
			return $this->current_workspace ;
		}
		}else{
		$this->current_workspace = new workspace();
		$this->current_workspace->open($_SESSION['dims']['workspaceid']);
		return $this->current_workspace ;
		}
	}else{
		//TODO ERROR
		return null ;
	}
	}

	public function dims_levenshteinTiers($intitule,$myCoeff = 4){
		$tab_corresp = array();
		$params = array();

		$intitulesanit=trim(str_replace(array("SA","SARL","SAS","EURL","S.A.","S.A","S.A.R.L","S.A.S","E.U.R.L"),"",strtoupper($intitule)));

		// levenshtein
		if($intitule == ""){
			return $tab_corresp;
		}else{
			$sql = 'SELECT	*
				FROM	`dims_mod_business_tiers`
				WHERE	`intitule` = :intitule
				OR	`intitule` = :intitulesanit';
			$params[':intitule'] = array('type' => PDO::PARAM_STR, 'value' => $intitule);
			$params[':intitulesanit'] = array('type' => PDO::PARAM_STR, 'value' => $intitulesanit);
		}

		$res = $this->db->query($sql, $params);

		$nb_res = $this->db->numrows($res);

		if($nb_res > 0) {
			while($result = $this->db->fetchrow($res)) {
			$tab_corresp[$result['id']]['coef']	  = 0;
			$tab_corresp[$result['id']]['id_tiers'] = $result['id'];
			$tab_corresp[$result['id']]['intitule']   = $result['intitule'];
			}
		}else{

			$sql = 'SELECT	ct.id as id_tiers, ct.intitule
				FROM	dims_mod_business_tiers ct';

			$ress = $this->db->query($sql);

			if($this->db->numrows($ress) > 0) {
				$intitule	 = strtoupper($intitule);

				while($rslt = $this->db->fetchrow($ress)) {

					$lev_nom = 0;

					$coef_nom = 0;

					$coef_tot = 0;

					$intitulecompare=trim(str_replace(array("SA","SARL","SAS","EURL","S.A.","S.A","S.A.R.L","S.A.S","E.U.R.L"),"",strtoupper($rslt['intitule'])));

					$lev_nom = levenshtein($intitule, $intitulecompare);
					$coef_nom = $lev_nom - (ceil(strlen($intitule)/6));

					$coef_tot = $coef_nom ;

					$lev_nom2 = 0;

					$coef_nom2 = 0;

					$coef_tot2 = 0;

					$lev_nom2 = levenshtein($intitule, $intitulecompare);
					$coef_nom2 = $lev_nom2 - (ceil(strlen($intitule)/6));

					$coef_tot2 = $coef_nom2;

					if($coef_tot < $myCoeff || $coef_tot2 < $myCoeff) {
						if($rslt['intitule'] != ""){ //Pour éviter d'avoir des contacts vides
							$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
							$tab_corresp[$rslt['id_contact']]['id_tiers'] = $rslt['id_tiers'];
							$tab_corresp[$rslt['id_contact']]['intitule']	= $rslt['intitule'];
						}
					}

				}
				//dims_print_r($tab_corresp);die();
			}
		}
		sort($tab_corresp);
		return $tab_corresp;
	}

	public function dims_levenshtein($firstname, $lastname,$myCoeff = 4){
		$tab_corresp = array();
		$params = array();

		// levenshtein
		if($firstname == "" && $lastname == ""){
			return $tab_corresp;
		}else if ($firstname == ""){
			$sql = 'SELECT	*
				FROM	`dims_user`
				WHERE	`lastname` = :lastname' ;
			$params[':lastname'] = array('type' => PDO::PARAM_STR, 'value' => $lastname);
		}else if($lastname == ""){
			$sql = 'SELECT	*
				FROM	`dims_user`
				WHERE	`firstname` = :firstname';
			$params[':firstname'] = array('type' => PDO::PARAM_STR, 'value' => $firstname);
		}else{
			$sql = 'SELECT	*
				FROM	`dims_user`
				WHERE	`lastname` = :lastname
				AND		`firstname` = :firstname';
			$params[':firstname'] = array('type' => PDO::PARAM_STR, 'value' => $firstname);
			$params[':lastname'] = array('type' => PDO::PARAM_STR, 'value' => $lastname);
		}
		//echo $sql;
		$res = $this->db->query($sql, $params);

		$nb_res = $this->db->numrows($res);


		if($nb_res > 0) {
			while($result = $this->db->fetchrow($res)) {
			$tab_corresp[$result['id']]['coef']	  = 0;
			$tab_corresp[$result['id']]['id_contact'] = $result['id'];
			$tab_corresp[$result['id']]['lastname']   = $result['lastname'];
			$tab_corresp[$result['id']]['firstname']  = $result['firstname'];
			}
		}else{

			$sql = 'SELECT	ct.id as id_contact, ct.lastname, ct.firstname
				FROM	dims_mod_business_contact ct';

			$ress = $this->db->query($sql);

			if($this->db->numrows($ress) > 0) {
			$nom	= strtoupper($lastname);
			$prenom = strtoupper($firstname);

			while($rslt = $this->db->fetchrow($ress)) {

				$lev_nom = 0;
				$lev_pre = 0;

				$coef_nom = 0;
				$coef_pre = 0;

				$coef_tot = 0;

				$lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
				$coef_nom = $lev_nom - (ceil(strlen($nom)/6));

				$lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
				$coef_pre = $lev_pre - (ceil(strlen($prenom)/6));

				$coef_tot = $coef_nom + $coef_pre;

				$lev_nom2 = 0;
				$lev_pre2 = 0;

				$coef_nom2 = 0;
				$coef_pre2 = 0;

				$coef_tot2 = 0;

				$lev_nom2 = levenshtein($nom, strtoupper($rslt['firstname']));
				$coef_nom2 = $lev_nom2 - (ceil(strlen($nom)/6));

				$lev_pre2 = levenshtein($prenom, strtoupper($rslt['lastname']));
				$coef_pre2 = $lev_pre2 - (ceil(strlen($prenom)/6));

				$coef_tot2 = $coef_nom2 + $coef_pre2;

				if($coef_tot < $myCoeff || $coef_tot2 < $myCoeff) {
				if($rslt['lastname'] != "" || $rslt['firstname']){ //Pour éviter d'avoir des contacts vides
					$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
					$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
					$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
					$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
				}
				}

			}
			}
		}
		sort($tab_corresp);
		return $tab_corresp;
	}

	public function dims_levenshteinUser($firstname, $lastname){
		$tab_corresp = array();
		$params = array();

		// levenshtein
		if($firstname == "" && $lastname == ""){
			return $tab_corresp;
		}else if ($firstname == ""){
			$sql = 'SELECT	*
				FROM	`dims_user`
				WHERE	`lastname` = :lastname' ;
			$params[':lastname'] = array('type' => PDO::PARAM_STR, 'value' => $lastname);
		}else if($lastname == ""){
			$sql = 'SELECT	*
				FROM	`dims_user`
				WHERE	`firstname` = :firstname';
			$params[':firstname'] = array('type' => PDO::PARAM_STR, 'value' => $firstname);
		}else{
			$sql = 'SELECT	*
				FROM	`dims_user`
				WHERE	`lastname` = :lastname
				AND		`firstname` :firstname';
			$params[':firstname'] = array('type' => PDO::PARAM_STR, 'value' => $firstname);
			$params[':lastname'] = array('type' => PDO::PARAM_STR, 'value' => $lastname);
		}

		$res = $this->db->query($sql, $params);

		$nb_res = $this->db->numrows($res);

		if($nb_res > 0) {
			while($result = $this->db->fetchrow($res)) {
			$tab_corresp[$result['id']]['coef']	  = 0;
			$tab_corresp[$result['id']]['id_user'] = $result['id'];
			$tab_corresp[$result['id']]['lastname']   = $result['lastname'];
			$tab_corresp[$result['id']]['firstname']  = $result['firstname'];
			}
		}else{

			$sql = 'SELECT	id as id_user, lastname, firstname
				FROM	dims_user ct';

			$ress = $this->db->query($sql);

			if($this->db->numrows($ress) > 0) {
			$nom	= strtoupper($lastname);
			$prenom = strtoupper($firstname);

			while($rslt = $this->db->fetchrow($ress)) {

				$lev_nom = 0;
				$lev_pre = 0;

				$coef_nom = 0;
				$coef_pre = 0;

				$coef_tot = 0;

				$lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
				$coef_nom = $lev_nom - (ceil(strlen($nom)/6));

				$lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
				$coef_pre = $lev_pre - (ceil(strlen($prenom)/6));

				$coef_tot = $coef_nom + $coef_pre;

				$lev_nom2 = 0;
				$lev_pre2 = 0;

				$coef_nom2 = 0;
				$coef_pre2 = 0;

				$coef_tot2 = 0;

				$lev_nom2 = levenshtein($nom, strtoupper($rslt['firstname']));
				$coef_nom2 = $lev_nom2 - (ceil(strlen($nom)/6));

				$lev_pre2 = levenshtein($prenom, strtoupper($rslt['lastname']));
				$coef_pre2 = $lev_pre2 - (ceil(strlen($prenom)/6));

				$coef_tot2 = $coef_nom2 + $coef_pre2;

				if($coef_tot < 4 || $coef_tot2 < 4) {
				if($rslt['lastname'] != "" || $rslt['firstname']){ //Pour éviter d'avoir des contacts vides
					$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
					$tab_corresp[$rslt['id_contact']]['id_user'] = $rslt['id_user'];
					$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
					$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
				}
				}

			}
			}
			}
		sort($tab_corresp);
		return $tab_corresp;
	}

	public function getLastVisitedUrl(){
	if(isset($_SESSION['dims']['lastVisitedUrl'])){
		return $_SESSION['dims']['lastVisitedUrl'] ;
	}else{
		return "" ;
	}
	}

	public function setLastVisitedUrl($url){
	if($url != null && $url != ""){
		$_SESSION['dims']['lastVisitedUrl'] = $url ;
	}
	}

	// FIXME : This should return an int array instead of a comma separated values string.
	public function getViewWorkspaces($moduleid = -1, $mode = '', $viewmode = null) {
		if ($mode == 'web') {
			$current_workspaceid = $_SESSION['dims']['webworkspaceid'];
		}
		else {
			if ($_SESSION['dims']['workspaceid'] == '') $current_workspaceid = dims_const::_DIMS_SYSTEMGROUP; // HOME PAGE / NO GROUP;
			else $current_workspaceid = $_SESSION['dims']['workspaceid'];
		}
		$workspaces = '';

		if ($moduleid == -1) $moduleid = $_SESSION['dims']['moduleid']; // get session value if not defined
		$work = new workspace();

		$work->open($current_workspaceid);
		$mods=$this->getModules($current_workspaceid);

		if(is_null($viewmode)) $viewmode = $mods[$moduleid]['viewmode'];

		switch($viewmode) {
			default:
			case dims_const::_DIMS_VIEWMODE_PRIVATE:
				$workspaces = intval($current_workspaceid);
			break;

			case dims_const::_DIMS_VIEWMODE_DESC:
				$lst=$work->getparents();
				foreach($lst as $i => $w) {
					if ($workspaces!='') $workspaces.=',';
					$workspaces .= intval($w['id']);
				}

				if ($workspaces!='') $workspaces.=',';
				$workspaces .= intval($current_workspaceid);
			break;

			case dims_const::_DIMS_VIEWMODE_ASC:
				$lst=$work->getworkspacechildren();
				foreach($lst as $i => $w) {
					if ($workspaces!='') $workspaces.=',';
					$workspaces .= intval($w['id']);
				}

				if ($workspaces!='') $workspaces.=',';
				$workspaces .= intval($current_workspaceid);
			break;

			case dims_const::_DIMS_VIEWMODE_GLOBAL:
				$workspaces = dims_getAllWorkspaces();
			break;

			case dims_const::_DIMS_VIEWMODE_VERTICAL:
				$lst=$work->getparents();
				foreach($lst as $i => $w) {
					if ($workspaces!='') $workspaces.=',';
					$workspaces .= intval($w['id']);
				}

				$lst=$work->getworkspacechildren();
				foreach($lst as $i => $w) {
					if ($workspaces!='') $workspaces.=',';
					$workspaces .= intval($w['id']);
				}

				if ($workspaces!='') $workspaces.=',';
				$workspaces .= intval($current_workspaceid);
				break;
		}

		if ($mode == 'web') {
			$array_workspaces = explode(',',$workspaces);
			// TODO : filtrer sur les groupes web public !!!! (pas fait)
			$workspaces = implode(',',$array_workspaces);
		}
		return $workspaces;
	}

	public function getMbObjet($label, $id_module_type = -1) {
		if($id_module_type == -1) $id_module_type = $_SESSION['dims']['moduletypeid'];

		$mbObject = array();

		$sql = 'SELECT * FROM dims_mb_object WHERE label = :label AND id_module_type=:idmoduletype';

		$res = $this->getDb()->query($sql, array(
			':label' => array('type' => PDO::PARAM_STR, 'value' => $label),
			':idmoduletype' => array('type' => PDO::PARAM_STR, 'value' => $id_module_type),
		));

		if($this->getDb()->numrows($res))
			$mbObject = $this->getDb()->fetchrow($res);

		return $mbObject;
	}

	public function getStats($db,$dims_timer,$dims_content='') {
	$dims_stats=array();
	$dims_stats['pagesize'] = strlen($dims_content);

	if (isset($db)) {
		$dims_stats['numqueries'] = $db->num_queries;
		$dims_stats['sql_exectime'] = round($db->exectime_queries*1000,0);
	}else {
		$dims_stats['numqueries'] = 0;
		$dims_stats['sql_exectime'] = 0;
	}

	if (isset($dims_timer)) {
		$dims_stats['total_exectime'] = round($dims_timer->getexectime()*1000,0);
		$dims_stats['sql_ratiotime'] = round(($dims_stats['sql_exectime']*100)/$dims_stats['total_exectime'] ,0);
		$dims_stats['php_ratiotime'] = 100 - $dims_stats['sql_ratiotime'];
	}else {
		$dims_stats['total_exectime'] = 0;
		$dims_stats['sql_ratiotime'] = 0;
		$dims_stats['php_ratiotime'] = 0;
	}
	return $dims_stats;
	}

	//Cyril - FOnction réutilisable dans les crons pour initialiser la metabase
	public function init_metabase(){
		if (!isset($_SESSION['dims']['index'])){
			$_SESSION['dims']['index']= array();
			$this->prepareMetaData($_SESSION['dims']['index']);
		}
		else $this->setMetadata($_SESSION['dims']['index']);
		//Cyril - 16/03/2012 - Initialisation de la metabase pour gestion des autoregistrations des mb_object / mb_classes / mb_table -|--|--|--|--|--|--|--|--|--|--|--|--|--|--|--|--|--|--|--|--|-
		//--- MB_OBJECT
		if( !isset( $_SESSION['dims']['permanent_data']['mb_objects'] ) ){
			$_SESSION['dims']['permanent_data']['mb_objects'] = array();
			$this->initPermanentMBObjects($_SESSION['dims']['permanent_data']['mb_objects']);
		}
		else $this->setMBObjects($_SESSION['dims']['permanent_data']['mb_objects']);

		//-- mise en initialisation du module type
		if( !empty($_SESSION['dims']['moduletypeid']))
			$this->setCurrentModuleTypeID($_SESSION['dims']['moduletypeid']);

		//IMPORTANT : ces informations sont hyper importantes sinon Dims va faire des SELECT et des INSERT à crever
		//J'ai déplacé la condition sur le dims_is_sync plus bas, uniquement pour les tables liées réellement à la synchro
		//initialisation de la matrice de synchronisation
		if( !isset( $_SESSION['dims']['permanent_data']['table_descriptions'] ) ){
			$_SESSION['dims']['permanent_data']['table_descriptions'] = array();
			$this->initTableDescriptions($_SESSION['dims']['permanent_data']['table_descriptions']);
		}
		else $this->setTableDescriptions($_SESSION['dims']['permanent_data']['table_descriptions']);

		//--- MB_TABLES
		if( !isset( $_SESSION['dims']['permanent_data']['mb_tables'] ) ){
			$_SESSION['dims']['permanent_data']['mb_tables'] = array();
			$this->initPermanentMBTables($_SESSION['dims']['permanent_data']['mb_tables']);
		}
		else $this->setMBTables($_SESSION['dims']['permanent_data']['mb_tables']);

		//--- MB_CLASSES
		if( !isset( $_SESSION['dims']['permanent_data']['mb_classes'] ) ){
			$_SESSION['dims']['permanent_data']['mb_classes'] = array();
			$this->initPermanentMBClasses($_SESSION['dims']['permanent_data']['mb_classes']);
		}
		else $this->setMBClasses($_SESSION['dims']['permanent_data']['mb_classes']);

		//--- TO_INDEX_DONE
		if( !isset( $_SESSION['dims']['permanent_data']['to_index_done'] ) ){
			$_SESSION['dims']['permanent_data']['to_index_done'] = array();
		}
		$this->initPermanentToIndexDone($_SESSION['dims']['permanent_data']['to_index_done']);

		//--- MB_RELATIONS
		if( !isset( $_SESSION['dims']['permanent_data']['mb_object_relations'] ) ){
			$_SESSION['dims']['permanent_data']['mb_object_relations'] = array();
			if( _DIMS_DEBUGMODE && ! _DIMS_BLOCK_MBR_PURGE ) mb_object_relation::init_all_relations();  //c'est pour que si le développeur s'est viandé dans une relation, en la modifiant ça la restaure proprement
			$this->initPermanentMBObjectRelations($_SESSION['dims']['permanent_data']['mb_object_relations']);
		}
		else $this->setMBObjectRelations($_SESSION['dims']['permanent_data']['mb_object_relations']);

		if ((defined(_DIMS_SYNC_ACTIVE) && _DIMS_SYNC_ACTIVE)) {
			//initialisation du Dims Courant
			if( !isset( $_SESSION['dims']['permanent_data']['current_dims'] ) ){
				$_SESSION['dims']['permanent_data']['current_dims'] = array();
				$this->initCurrentDimsID($_SESSION['dims']['permanent_data']['current_dims']);
			}
			else $this->setCurrentDimsID($_SESSION['dims']['permanent_data']['current_dims']);

			//initialisation du Dims Root, celui du dims_courant
			if( !isset( $_SESSION['dims']['permanent_data']['root_dims'] ) ){
				$_SESSION['dims']['permanent_data']['root_dims'] = array();
				$this->initCurrentRoootDimsID($_SESSION['dims']['permanent_data']['root_dims']);
			}
			else $this->setCurrentRootDimsID($_SESSION['dims']['permanent_data']['root_dims']);


			//initialisation des dims avec leur fields pour éviter des faire des SELECT SQL à foison dans l'export xml
			if( !isset( $_SESSION['dims']['permanent_data']['synchronized_dims'] ) ){
				$_SESSION['dims']['permanent_data']['synchronized_dims'] = array();
				$this->initSynchronizedDims($_SESSION['dims']['permanent_data']['synchronized_dims']);
			}
			else $this->setSynchronizedDims($_SESSION['dims']['permanent_data']['synchronized_dims']);


			//initialisation de la matrice de synchronisation
			if( !isset( $_SESSION['dims']['permanent_data']['sync_matrix'] ) ){
				$_SESSION['dims']['permanent_data']['sync_matrix'] = array();
				$this->initSyncMatrix($_SESSION['dims']['permanent_data']['sync_matrix']);
			}
			else $this->setSyncMatrix($_SESSION['dims']['permanent_data']['sync_matrix']);
		}
	}

	//Cyril - 16/03/2012 - Initialisation du tableau des mb_objects courants et connus dans le système
	public function initPermanentMBObjects(&$table){
	$this->mbobjects = array();
	$db = $this->getDb();
	$res = $db->query("SELECT * FROM dims_mb_object");
	while($fields = $db->fetchrow($res)){
		$table[$fields['id_module_type']][$fields['id']] = $fields;
	}
	$this->mbobjects = &$table;
	}
	public function setMbObjects(&$table){
	$this->mbobjects = &$table;
	}
	public function getMBObjects(){
	return $this->mbobjects;
	}

	public function getMBObjectFields($id_mt, $id_mbo){
	if( isset( $this->mbobjects[$id_mt][$id_mbo] ) ) return $this->mbobjects[$id_mt][$id_mbo]; else return null;
	}

	public function addNewMBObject($id_module_type, $id_object, $fields){
	$this->mbobjects[$id_module_type][$id_object] = $fields;
	}

	//Cyril - pour la description des tables
	public function initTableDescriptions(&$table){
	$this->table_descriptions = &$table;
	}

	public function setTableDescriptions(&$table){
	$this->table_descriptions = &$table;
	}

	public function addTableDescription($tablename, $data){
	$this->table_descriptions[$tablename] = $data;
	}

	public function getTableDescription($tablename) {
		$tabledescription = null;
		if(isset($this->table_descriptions[$tablename])) {
			$tabledescription = $this->table_descriptions[$tablename];
		} else {
			if($this->getDb()->tableexist($tablename)) {
				$sql = "DESCRIBE $tablename";
				$result = $this->db->query($sql);
				$description = array();
				while ($fields = $this->db->fetchrow($result)) {
					$description[] = $fields;
				}
				$this->addTableDescription($tablename, $description);
			}
			if (isset($this->table_descriptions[$tablename]))
				$tabledescription = $this->table_descriptions[$tablename];
		}
		return $tabledescription;
	}

	public function tableexist($tablename, $database = _DIMS_DB_DATABASE) {
		$tabledescription = $this->getTableDescription($tablename, $database);
		return !empty($tabledescription);
	}

	//Cyril permet de stocker et de restituer en permanance le l'id_module_type sans appeler $_SESSION['dims']['moduletypeid']
	public function setCurrentModuleTypeID($value){

	}
	public function getCurrentModuleTypeID(){
		return $this->currentmoduletypeid;
	}

	 //Cyril - 17/04/2012 - Initialisation du tableau des mbrelations courants et connus dans le système
	public function initPermanentMBObjectRelations(&$table){
		$this->mbrelations = array();
		$db = $this->getDb();
		$res = $db->query("SELECT * FROM dims_mb_object_relation");
		while($fields = $db->fetchrow($res)){
			$table['onto'][$fields['id_class_on']][$fields['id_class_to']][$fields['on']][$fields['to']] = $fields;
			if (isset($fields['alias'])) $table['as'][$fields['id_class_on']][$fields['alias']] = $fields;
			if($fields['extended_indexation']) $table['exindex'][$fields['id_class_on']][$fields['on']] = $fields['on'];
		}
		$this->mbrelations = &$table;
	}
	public function setMbObjectRelations(&$table){
		$this->mbrelations = &$table;
	}

	public function getMBObjectRelations(){
		return $this->mbrelations['onto'];
	}

	public function getMBObjectRelationsOn($classname){
		$id = $this->getMBClasseID($classname);
		return isset($this->mbrelations['onto'][$id])?$this->mbrelations['onto'][$id]:array();
	}

	public function addNewMBObjectRelation($id_class_on, $id_class_to, $on, $to, $fields){
		$this->mbrelations['onto'][$id_class_on][$id_class_to][$on][$to] = $fields;
		if(isset($fields['alias']))
			$this->mbrelations['as'][$fields['id_class_on']][$fields['alias']] = $fields;
		if($fields['extended_indexation']) $this->mbrelations['exindex'][$fields['id_class_on']][$fields['on']] = $fields['on'];

	}

	public function isRelationExisting($id_class_on, $id_class_to, $on, $to){
		return isset( $this->mbrelations['onto'][$id_class_on][$id_class_to][$on][$to] );
	}

	public function getRelationAs($classname, $alias){
		$id = $this->getMBClasseID($classname);
		return isset($this->mbrelations['as'][$id][$alias]) ? $this->mbrelations['as'][$id][$alias] : null;
	}

	public function getExternalIndexedRelation($classname){
		if(isset($this->mbrelations['exindex'][$classname])) return $this->mbrelations['exindex'][$classname];
		else return null;
	}

	//même choses pour les classes (optimisation)
	public function initPermanentMBClasses(&$table){
		$this->mbclasses = array();
		$db = $this->getDb();
		$res = $db->query("SELECT c.*, t.name as tablename FROM dims_mb_classes c INNER JOIN dims_mb_table t ON t.id = c.id_table");
		while($fields = $db->fetchrow($res)){
			$table['classes'][$fields['classname']] = $fields;
			$table['ids'][$fields['id']] = $fields;
		}
		$this->mbclasses = &$table;
	}

	public function setMBClasses(&$table){
		$this->mbclasses = &$table;
	}

	public function getMBClasse($classname){
		return (isset($this->mbclasses['classes'][$classname])) ? $this->mbclasses['classes'][$classname] : null;
	}

	public function getMBClasseID($classname){
		return (isset($this->mbclasses['classes'][$classname]['id'])) ? $this->mbclasses['classes'][$classname]['id'] : null;
	}

	public function getMBClassDataFromID($class_id){
		return (isset($this->mbclasses['ids'][$class_id])) ? $this->mbclasses['ids'][$class_id] : null;
	}

	public function addMBClasse($fields){
		$this->mbclasses['classes'][$fields['classname']] = $fields;
		$this->mbclasses['ids'][$fields['id']] = $fields;
	}

	//même choses pour les classes (optimisation)
	public function initPermanentMBTables(&$table){
		$this->mbtables = array();
		$db = $this->getDb();
		$res = $db->query("SELECT * FROM dims_mb_table");
		while($fields = $db->fetchrow($res)){
			$table['tables'][$fields['name']] = $fields;
			$table['ids'][$fields['id']] = $fields;
		}
		$this->mbtables = &$table;
	}

	public function setMBTables(&$table){
	$this->mbtables = &$table;
	}

	public function getMBTable($tablename){
		if(isset($this->mbtables['tables'][$tablename])){
			return $this->mbtables['tables'][$tablename];
		}
		else if($this->tableexist($tablename)){//il faut l'enregistrer dans la metabase parce qu'on ne la connait pas encore
			return mb_table::create($tablename);//se charge de mettre en session la mbtable
		}
		else return null;
	}

	public function getMBTablenameForID($table_id){
		return (isset($this->mbtables['ids'][$table_id]['name'])) ? $this->mbtables['ids'][$table_id]['name'] : null;
	}

	public function addMBTable($fields){
		$this->mbtables['tables'][$fields['name']] = $fields;
		$this->mbtables['ids'][$fields['id']] = $fields;
	}

	//même choses pour les classes (optimisation)
	public function initCurrentDimsID(&$table){
	$table = dims_sync::getCurrentDimsID();
	$this->current_dims = &$table;
	}

	public function setCurrentDimsID(&$table){
	$this->current_dims = &$table;
	}

	public function getCurrentDimsID(){
	return $this->current_dims;
	}

   //pour le dims root du dims courant
	public function initCurrentRoootDimsID(&$table){
	$table = dims_sync::getCurrentRootDimsID();
	$this->root_dims = &$table;
	}


	public function setCurrentRootDimsID(&$table){
	$this->root_dims = &$table;
	}

	public function getCurrentRootDimsID(){
	return $this->root_dims;
	}

	//initialisation de la matrice de synchronisation
	public function initSyncMatrix(&$table){
		//le but du jeu c'est de remonter tous les dims vers lesquels un dims donné envoie de la data, et de choper également les classes liées
		$res = $this->db->query("SELECT m.id_dims_from, m.id_dims_to
								 FROM dims_sync_matrix m
								 WHERE m.status = :status
								 ORDER BY m.id_dims_from",
								 array(':status' => sync_matrix::DIMS_SYNCHRO_ACTIVE));

		while($fields = $this->db->fetchrow($res)){
			$from = $fields['id_dims_from'];
			$to = $fields['id_dims_to'];

			if( !isset($table[$from]) ){
				$table[$from] = array();
			}
			$table[$from][$to] = $to;
		}
		$this->sync_matrix = &$table;
	}

	public function setSyncMatrix(&$table){
	$this->sync_matrix = &$table;
	}

	public function getSyncMatrix(){
	return $this->sync_matrix;
	}

	public function getDestsFor($id_dims_from){
	return ( isset ($this->sync_matrix[$id_dims_from]) ) ?	$this->sync_matrix[$id_dims_from] : null;
	}

	public function isSyncExists($id_from, $id_to){
	return isset($this->sync_matrix[$id_from][$id_to]);
	}

	public function addDestInSyncMatrix($id_from, $id_to){
	$this->sync_matrix[$id_from][$id_to] = $id_to;
	}

	public function deleteDestInSyncMatrix($id_from, $id_to){
	if(isset($this->sync_matrix[$id_from][$id_to] )) unset($this->sync_matrix[$id_from][$id_to] );
	}

	public function initSynchronizedDims(&$table){
	$res = $this->db->query("SELECT * FROM dims_sync");
	while($fields = $this->db->fetchrow($res) ){
		$table['ids'][$fields['id']] = $fields;
		$table['parents'][$fields['root_reference']][$fields['id']] = $fields;
	}
	$this->synchronized_dims = &$table;
	}

	public function setSynchronizedDims(&$table){
	$this->synchronized_dims = &$table;
	}

	public function getSynchronizedDimsFields($id_sync_dims){
	return ( isset($this->synchronized_dims['ids'][$id_sync_dims]) ) ? $this->synchronized_dims['ids'][$id_sync_dims] : null;
	}

	public function getSynchronizedDimsChildren($id_sync_dims){
	return ( isset($this->synchronized_dims['parents'][$id_sync_dims]) ) ? $this->synchronized_dims['parents'][$id_sync_dims] : null;
	}

	/*
	 * Function which get Meta fields array for indexing contents
	 * No entries
	 * Result : Array metadata
	 */
	public function prepareMetaData( &$tab = array()) {
		$res=$this->db->query("DESCRIBE dims_matrix");

		$this->metadata = &$tab;
		if (empty($this->metadata)) {
			// Recherche des champs e indexer
			$sql="SELECT	mbf.id,
							mbf.indexed,
							mbf.name,
							mbf.label,
							mbf.`type`,
							mbf.from_model,

							mbt.name as tablename,
							mbt.`sql`,
							mbt.`db`

				FROM		dims_mb_field mbf
				INNER JOIN	dims_mb_table mbt
				ON			mbt.id=mbf.id_table

				WHERE		(
							indexed=1
				OR			mbf.label LIKE 'id_module'
				OR			mbf.label LIKE 'id_workspace'
				OR			mbf.label LIKE 'id_user'
				OR			mbf.label LIKE 'id'
				OR			mbf.label LIKE 'id_globalobject'
				)

				ORDER BY mbt.name";

			$res = $this->db->query($sql);
			$ismodulepresent=false;
			$isworkspacepresent=false;
			$isidpresent=false;
			$isuserpresent=false;
		$isgopresent=false;

			$tablecour="";
			$sql="";
			$obj=array();
			$obj['fields']=array();
			$cpte=0;
			$sqlcomp="-";
			$dbtable="-";

			if ($this->db->numrows($res)>0) {
				while($field=$this->db->fetchrow($res)) {
					if ($sqlcomp=="-") $sqlcomp=$field['sql'];
					if ($dbtable=="-") $dbtable=$field['db'];
					if ($tablecour=="") $tablecour=$field['tablename'];

					if ($tablecour!=$field['tablename']) {
						if ($sqlcomp!="") {
							// requete etendue
							if ($sql!="" && $ismodulepresent && $isworkspacepresent && $isidpresent && $isuserpresent && $isgopresent) {
								//gestion particulière pour le $sql distant - pour une bonne intégration de la jointure avec dims_globalobject
								$complement = $sqlcomp = strtolower($sqlcomp);
								if(strpos($sqlcomp, 'table_cur') != false){ //sinon la requête ne pourra jamais fonctionner. Il faut impérativement que la table sur laquelle on fait le FROM ait cet alias
									if(strpos($sqlcomp, 'where') !== false){
										$before_where = substr($sqlcomp, 0, strpos($sqlcomp, 'where'));
										$after_where = substr($sqlcomp, strpos($sqlcomp, 'where') );

										$complement = $before_where.' INNER JOIN dims_globalobject go ON go.id = id_globalobject '.$after_where;
									}
									else $complement .= ' INNER JOIN dims_globalobject go ON go.id = id_globalobject ';
									if(strpos(strtoupper($sql), 'FROM') !== false || strpos(strtoupper($complement), 'FROM') !== false)
										$sql="SELECT table_cur.id_workspace,table_cur.id_module,table_cur.id_user, table_cur.".$obj['id_label'].",table_cur.id_globalobject, go.id_module_type, go.id_object,".$sql." ".$complement; //." order by ".$obj['id_label'];
									else
										$sql="SELECT table_cur.id_workspace,table_cur.id_module,table_cur.id_user, table_cur.".$obj['id_label'].",table_cur.id_globalobject, go.id_module_type, go.id_object,".$sql."
											 FROM	".$tablecour. " table_cur ".$complement; //." order by ".$obj['id_label'];
									$obj['sql']=$sql;
									$obj['cpte']=$cpte;

									if( ! isset($this->metadata[$tablecour])) $this->metadata[$tablecour] = array();
									$this->metadata[$tablecour] = $obj;
								}
							}
						}
						elseif ($sql!="" && $ismodulepresent && $isworkspacepresent && $isidpresent && $isuserpresent && $isgopresent) {
							// on peut ajouter cette requete
							if ($dbtable!="") $tablecour=$dbtable.".".$tablecour;
							$sql="SELECT table_cur.id_workspace,table_cur.id_module,table_cur.id_user, table_cur.".$obj['id_label'].",table_cur.id_globalobject, go.id_module_type, go.id_object, ".$sql.
								 " FROM ".$tablecour. " table_cur
								   INNER JOIN dims_globalobject go ON go.id = table_cur.id_globalobject"; // ." order by ".$obj['id_label'];

							$obj['sql']=$sql;
							$obj['cpte']=$cpte;

							if( ! isset($this->metadata[$tablecour])) $this->metadata[$tablecour] = array();
								$this->metadata[$tablecour] = $obj;

						}
						$sql="";
						$tablecour=$field['tablename'];
						$sqlcomp=$field['sql'];
						$dbtable=$field['db'];
						$ismodulepresent=false;
						$isworkspacepresent=false;
						$isidpresent=false;
						$isuserpresent=false;
						$isgopresent=false;
						$obj=array();
						$obj['fields']=array();
						$obj['from_model']=array();
						$cpte=0;
					}

					if ($field['label']=='id') {
						$isidpresent=true;
						$obj['id_label']=$field['name'];

						if ($field['indexed']) {
							array_push($obj['fields'],$field['name']);
							// correspondance de champ et id
							$obj['corresp'][$field['name']]=$field['id'];
							if($field['from_model']) $obj['from_model'][$field['name']] = $field['from_model'];
							$cpte++;
						}
					}
					elseif ($field['name']=='id_user') $isuserpresent=true;
					elseif ($field['name']=='id_module') $ismodulepresent=true;
					elseif ($field['name']=='id_workspace') $isworkspacepresent=true;
					elseif ($field['name']=='id_globalobject') $isgopresent=true;
					else {
						if(strpos($field['name'],".") === false){
							if ($sql=="") $sql.= "table_cur.".$field['name'];
							else $sql.=", table_cur.".$field['name'];
						}else{ // on récupère les données dans une autre table
							if ($sql=="") $sql.= $field['name'];
							else $sql.=", ".$field['name'];
						}

						array_push($obj['fields'],$field['name']);
						// correspondance de champ et id
						$obj['corresp'][$field['name']]=$field['id'];
						if($field['from_model']) $obj['from_model'][$field['name']] = $field['from_model'];
						$cpte++;
					}
					// test si type
				}
			}

			if ($sql!="") {
				if ($sqlcomp!="") {
					// requete etendue
					if ($sql!="" && $ismodulepresent && $isworkspacepresent && $isidpresent && $isuserpresent && $isgopresent) {
						//gestion particulière pour le $sql distant - pour une bonne intégration de la jointure avec dims_globalobject
						$complement = $sqlcomp = strtolower($sqlcomp);
						if(strpos($sqlcomp, 'table_cur') != false){ //sinon la requête ne pourra jamais fonctionner. Il faut impérativement que la table sur laquelle on fait le FROM ait cet alias
							if(strpos($sqlcomp, 'where') !== false){
								$before_where = substr($sqlcomp, 0, strpos($sqlcomp, 'where'));
								$after_where = substr($sqlcomp, strpos($sqlcomp, 'where') );

								$complement = $before_where.' INNER JOIN dims_globalobject go ON go.id = id_globalobject '.$after_where;
							}
							else $complement .= ' INNER JOIN dims_globalobject go ON go.id = id_globalobject ';
							if(strpos(strtoupper($sql), 'FROM') !== false || strpos(strtoupper($complement), 'FROM') !== false)
								$sql="SELECT table_cur.id_workspace,table_cur.id_module,table_cur.id_user, table_cur.".$obj['id_label'].",table_cur.id_globalobject, go.id_module_type, go.id_object,".$sql." ".$complement; //." order by ".$obj['id_label'];
							else
								$sql="SELECT table_cur.id_workspace,table_cur.id_module,table_cur.id_user, table_cur.".$obj['id_label'].",table_cur.id_globalobject, go.id_module_type, go.id_object,".$sql."
									 FROM	".$tablecour. " table_cur ".$complement; //." order by ".$obj['id_label'];
							$obj['sql']=$sql;
							$obj['cpte']=$cpte;

							if( ! isset($this->metadata[$tablecour])) $this->metadata[$tablecour] = array();
							$this->metadata[$tablecour] = $obj;
						}
					}
				}
				elseif ($sql!="" && $ismodulepresent && $isworkspacepresent && $isidpresent && $isuserpresent && $isgopresent) {
					// on peut ajouter cette requete
					if ($dbtable!="") $tablecour=$dbtable.".".$tablecour;
					$sql="SELECT table_cur.id_workspace,table_cur.id_module,table_cur.id_user, table_cur.".$obj['id_label'].",table_cur.id_globalobject, go.id_module_type, go.id_object, ".$sql.
						 " FROM ".$tablecour. " table_cur
						   INNER JOIN dims_globalobject go ON go.id = table_cur.id_globalobject"; // ." order by ".$obj['id_label'];

					$obj['sql']=$sql;
					$obj['cpte']=$cpte;

					if( ! isset($this->metadata[$tablecour])) $this->metadata[$tablecour] = array();
						$this->metadata[$tablecour] = $obj;

				}
			}
		}
		return $this->metadata;
	}

	public function initPermanentToIndexDone(&$tab){
		$this->to_index_done = &$tab;
	}

	public function setToIndexDoneOn($classname, $checksum){
		$this->to_index_done[$classname] = $checksum;
	}

	public function getToIndexDoneOn($classname){
		return isset($this->to_index_done[$classname]) ? $this->to_index_done[$classname] : null;
	}

	public function isToIndexDoneOn($classname){
		return isset($this->to_index_done[$classname]);
	}

	public function setMetadata(&$tab){
		$this->metadata = &$tab;
	}

	public function getMetadata(){
		return $this->metadata;
	}

	public function getMetafieldsOf($tablename){
	return ( isset($this->metadata[$tablename])) ? $this->metadata[$tablename]['fields'] : null;
	}

	public function getMetaFieldID($tablename, $fieldname){
	return ( isset($this->metadata[$tablename]['corresp'][$fieldname])) ? $this->metadata[$tablename]['corresp'][$fieldname] : null;
	}

	public function addMetaField($tablename, mb_field $field){
		$this->metadata[$tablename]['fields'][] = $field->get('name');
		$this->metadata[$tablename]['corresp'][$field->get('name')] = $field->get('id');
		if($field->get('from_model')) $this->metadata[$tablename]['from_model'][$field->get('name')] = $field->get('from_model');
	}

	public function getMetaFieldFromModel($tablename){
		return isset($this->metadata[$tablename]['from_model']) ? $this->metadata[$tablename]['from_model'] : null;
	}

	public function isMetaFieldFromModel($tablename, $fieldname){
		return isset($this->metadata[$tablename]['from_model'][$fieldname]) && $this->metadata[$tablename]['from_model'][$fieldname];
	}

	public function setMetaFieldFromModel($tablename, $fieldname, $value){
		if(isset($this->metadata[$tablename]['from_model'][$fieldname])){
			$this->metadata[$tablename]['from_model'][$fieldname] = $value;
		}
	}

	public function dropMetaField($tablename, $fieldname){
		foreach($this->metadata[$tablename]['fields'] as $idx => $name){
			if($fieldname == $name){
				unset($this->metadata[$tablename]['fields'][$idx]);
				break;
			}
		}
		if(isset($this->metadata[$tablename]['corresp'][$fieldname])) unset($this->metadata[$tablename]['corresp'][$fieldname]);
		if(isset($this->metadata[$tablename]['from_model'][$fieldname])) unset($this->metadata[$tablename]['from_model'][$fieldname]);

	}

	public function cleanMetaFieldsOf($tablename){
		if(isset($this->metadata[$tablename])) unset($this->metadata[$tablename]);
	}

	public function getXmppObject() {
		if(is_null($this->xmppObject)) {
			$this->xmppObject = self::factoryXmmpObject(_DIMS_XMPP_HOST, _DIMS_XMPP_PORT, _DIMS_XMPP_USER, _DIMS_XMPP_PASSWORD, _DIMS_XMPP_RESOURCE, _DIMS_XMPP_SERVER);
		}
		return $this->xmppObject;
	}

	public function disconnectXmppObject(){
	if(!is_null($this->xmppObject)) {
			$this->xmppObject->disconnect();
		$this->xmppObject = null;
		}
	}
	public static function factoryXmmpObject($host, $port, $user, $password, $resource, $server) {
		require_once DIMS_APP_PATH . '/lib/XMPPHP/XMPP.php';

		$printlog = _DIMS_DISPLAY_ERRORS;
	$loglevel =XMPPHP_Log::LEVEL_ERROR;

		//$loglevel = 0;
		if(_DIMS_ERROR_REPORTING == E_ALL) $loglevel = XMPPHP_Log::LEVEL_VERBOSE;
		if(_DIMS_ERROR_REPORTING == E_ERROR) $loglevel = XMPPHP_Log::LEVEL_ERROR;
		if(_DIMS_ERROR_REPORTING == E_WARNING) $loglevel = XMPPHP_Log::LEVEL_WARNING;

		return new XMPPHP_XMPP($host, $port, $user, $password, $resource, $server, $loglevel, $printlog);
	}

	/* 08/08/2012 - Retourne le chemin appelé derrière le nom de domaine */
	public function getPathAfterDomain($url){
		if(substr($url, 0, 4) == 'http'){
			$pos = strpos($url, '//');
			return substr($url, strpos($url, '/', $pos+2) + 1 );
		}
		else{
			return substr($url, strpos($url, '/') + 1 );
		}
	}

	/*
	 * Fonction permettant la construction du tableau d'espaces de travail courants
	 */
	public function getArrayWorkspaces() {
		$lstw=array();
		if (isset($this->workspaces)) {
			foreach ($this->workspaces as $w) {
				$lstw[] = $w['id'];
			}
		}

		return $lstw;
	}

	public function addshutdownquery($query, $params) {
		$this->shutdownqueries[] = array('query' => $query, 'params' => $params);
	}

	public function execshutdownqueries() {
		if(!empty($this->db) && $this->db->isConnected()){
			foreach($this->shutdownqueries as $record) {
				$this->db->query($record['query'], $record['params']);
			}
		}
	}
}
