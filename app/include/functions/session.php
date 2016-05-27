<?
function dims_session_reset()
{
	global $dims_initsession;
	global $scriptenv;
	$securule=false;

	$dims_initsession = true;
	if (isset($_SESSION['dims']['security'])) {
		$securule=true;
		$security=$_SESSION['dims']['security'];
	}
	// session_destroy();
	$_SESSION['dims'] = array(
					'login' 		=> '',
					'password'		=> '',
					'userid'		=> '',
					'workspaceid'	=> '',
					'webworkspaceid'	=> '',
					'adminlevel'	=> 0,

					'connected' 	=> false,
					'loginerror' 	=> false,
					'paramloaded' 	=> false,
					'mode'			=> 'admin',

					'remoteip'		=> dims_getip(),
					'host' 			=> $_SERVER['HTTP_HOST'],
					'scriptname'	=> $scriptenv,

					'wcemoduleid' 	=> 0,

					'hosts'			=> array(),
					'groups' 		=> array(),
					'modules' 		=> array(),
					'allworkspaces' => '',

					'currentrequesttime' 	=> time(),
					'lastrequesttime' 		=> time(),

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

					'newtickets'	=> 	0,
					'ssl'		=> (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? true : false,
					'browser' 		=> dims_detect_browser($_SERVER['HTTP_USER_AGENT'])
				);

	if ($securule) $_SESSION['dims']['security']=$security;
	$_SESSION['dims']['browser']['pda'] = ($_SESSION['dims']['browser']['PDA_NAME'] != '');

}

function dims_session_update()
{
	global $scriptenv;

	$_SESSION['dims']['currentrequesttime'] = time();

	if (!isset($_SESSION['dims']['lastrequesttime'])) {
		$_SESSION['dims']['lastrequesttime']=$_SESSION['dims']['currentrequesttime'];
		dims_session_reset();
	}

	$diff = $_SESSION['dims']['currentrequesttime'] - $_SESSION['dims']['lastrequesttime'];

	if ($diff > _DIMS_SESSIONTIME && _DIMS_SESSIONTIME != '' && _DIMS_SESSIONTIME != 0) dims_session_reset();
	else
	{
		$_SESSION['dims']['lastrequesttime'] = $_SESSION['dims']['currentrequesttime'];
		$_SESSION['dims']['remoteip'] = dims_getip();
	}

	$_SESSION['dims']['scriptname'] = $scriptenv;
	$_SESSION['dims']['ssl'] =(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? true : false;
}

function dims_getiprules($rules)
{
	$intervals = array();
	$iprules = array();
	$ip1 = 0;
	$ip2 = 0;


	if ($rules == '')
	{
		return FALSE;
	}

	//------------------------
	// string conversion
	//------------------------
	$intervals = explode(';',$rules);

	foreach ($intervals as $interval)
	{
		$ips = explode('-',trim($interval));

		if (count($ips) == 1)
		{
			$ips[0] = trim($ips[0]);
			if (strpos($ips[0],"*") !== false)
			{
				$ip1 = str_replace('*','0',$ips[0]);
				$ip2 = str_replace('*','255',$ips[0]);
			}
			else
			{
				$ip1 = $ip2 = $ips[0];
			}
		}
		elseif (count($ips) == 2)
		{
			$ip1 = trim($ips[0]);
			$ip2 = trim($ips[1]);
		}

		$ip1 = ip2long($ip1);
		$ip2 = ip2long($ip2);

			$iprules[$ip1] = $ip2;
	}


	return $iprules;
}

?>
