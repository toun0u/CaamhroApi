<?

include DIMS_APP_PATH.'modules/user/class_user.php';

/*******************************************************************************************************
---> Constantes
*******************************************************************************************************/
define ("LDAP_HOST", "213.41.145.227");
define ("LDAP_PORT", "389");
define ("LDAP_BASE", "o=emiic-controle,c=fr");


/**
*
* Access LDAP function / regroupe les fonctionnalités d'accès ldap
*
*
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package 	ldap
* @access  	public
*
**/
class ldap
{

	/**
	* Name of the host
	* @var string
	**/
	var $host;
	/**
	* Number of the port
	* @var int
	**/
	var $port;
	/**
	* Name of the base
	* @var string
	**/
	var $base;
	/**
	* Name of the connexion
	* @var string
	**/
	var $conn;
	/**
	* BIND
	* @var string
	**/
	var $bind;
	/**
	* Error message
	* @var string
	**/
	var $msgerror;
	/**
	* Result
	* @var string
	**/
	var $result;
	/**
	* Number of result
	* @var int
	**/
	var $nbresult;
	/**
	* Name of the server
	* @var string
	**/
	var $gidnumber;
	/**
	* Name of the server
	* @var string
	**/
	var $uidnumber;

	/**
	* Class constructor
	*
	* @access public
	**/
	function ldap()
	{
		$this->host = LDAP_HOST;
		$this->port = LDAP_LOGIN;
		$this->base = LDAP_BASE;
		$this->nbresult=0;
		$this->gidnumber = 0;
		$this->uidnumber = 0;
	}

	/**
	*
	* @return string
	*
	* @access public
	*
	**/
	function connect()
	{
		$this->msgerror = "";
		$this->conn = ldap_connect($this->host, $this->port);
		// bind anonyme pour tester la connexion
		$this->bind = ldap_bind($this->conn);
		if(!$this->bind) $this->msgerror = ldap_error($this->conn);

		return($this->msgerror=='');
	}

	/**
	*
	*
	* @param int $dbconn
	* @access public
	**/
	function ldap2db($dbconn)
	{
		//echo "<FONT COLOR=RED><B>SYNCHRO LDAP</B></FONT>";

		$this->result = ldap_search($this->conn, $this->base, "uid=*");
		$this->info = ldap_get_entries($this->conn, $this->result);

		// et on insert les nouveaux (si existe pas)
		for ($i=0; $i<$this->info['count']; $i++)
		{
				$userdata['utilisateur_id'] = -1;
			$userdata['utilisateur_nom'] = $this->info[$i]['givenname'][0];
			$userdata['utilisateur_prenom'] = $this->info[$i]['sn'][0];
			$userdata['utilisateur_login'] = $this->info[$i]['uid'][0];
			$userdata['utilisateur_password'] = "";
			$userdata['utilisateur_idgroup'] = $this->info[$i]['gidnumber'][0];
			$userdata['utilisateur_email'] = $this->info[$i]['mail'][0];
			$userdata['utilisateur_telephone'] = "";
			$userdata['utilisateur_fax'] = "";
			$userdata['utilisateur_ldap'] = $this->info[$i]['uidnumber'][0];

			//print_r($userdata);
			$user = new utilisateur($dbconn);
			$user->setvalues($userdata);
			if (!$user->existldap()) $user->save();
		}
	}

	/**
	*
	* @param string $user
	* @param string $pwd
	*
	* @return bool
	*
	* @access public
	*
	**/
	function search($user,$pwd)
	{
		$this->result = ldap_search($this->conn, $this->base, "uid=$user");
		$this->nbresult = ldap_count_entries($this->conn,$this->result);
		if ($this->nbresult==1)
		{

			$this->info = ldap_get_entries($this->conn, $this->result);
			$this->dn = $this->info[0]['dn'];
			$this->disconnect();
			$this->connect();

			$this->bind = @ldap_bind($this->conn, $this->dn, $pwd);

			// Invalid credentials => user/pwd incorrect
			if (ldap_error($this->conn)!='Success') return(false);

			$result = @ldap_search($this->conn, $this->base, "uid=$user");
			$this->nbresult = ldap_count_entries($this->conn,$this->result);
			if ($this->nbresult==1) // user/pass ok
			{
				$this->info = ldap_get_entries($this->conn, $this->result);
				$this->uidnumber = $this->info[0]['uidnumber'][0];
				$this->gidnumber = $this->info[0]['gidnumber'][0];
			}
			else return(false);

			return(true);
		}
		else return(false);
	}

	/**
	*
	* @access public
	*
	**/
	function disconnect ()
	{
			ldap_close($this->conn);
	}

}
?>
