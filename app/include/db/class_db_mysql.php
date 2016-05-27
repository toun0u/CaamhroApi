<?php
include_once DIMS_APP_PATH.'include/class_exception.php';
include_once DIMS_APP_PATH.'include/class_debug.php';

class dims_db {
	const ROW_TABLE_FIELD_ROTATION = 1; //permet de renvoyer dans le split du result set les lignes d'abord, puis par table, et par champ la valeur
	const TABLE_ROW_FIELD_ROTATION = 2; //permet de renvoyer dans le split du result set la table d'abord, puis la ligne puis le fields dans lequel on a la valeur
	const TABLE_FIELD_ROW_ROTATION = 3; //permet de renvoyer dans le split du result set la table d'abord, le fields, et pour chaque fields la ligne dans laquelle on a la valeur

	const ERR_DUPLICATE_KEY = 1062;

	/**
	* @var boolean database connection persistance
	* @access public
	*/
	var $persistency = true;

	/**
	* @var string db user login
	* @access public
	*/
	var $user = '';

	/**
	* @var string db user password
	* @access public
	*/
	var $password = '';

	/**
	* @var string database server address
	* @access public
	*/
	var $server = '';

	/**
	* @var string database name
	* @access public
	*/
	var $database = '';

	/**
	* @var int database connection id
	* @access public
	*/
	var $connection_id;

	/**
	* @var int last query resultset id
	* @access public
	*/
	var $query_result;

	/**
	* @var array resultset
	* @access public
	*/
	var $row = array();

	/**
	* @var array
	* @access public
	*/
	var $rowset = array();

	/**
	* @var int number of queries
	* @access private
	*/
	var $num_queries = 0;

	/**
	* @var int execution time took by queries
	* @access private
	*/
	var $exectime_queries = 0;

	/**
	* @var array result
	* @access public
	*/
	var $array = array();

	/**
	* @var Affiche les erreurs MySQL
	* @access public
	*/
	var $mod_debug	= false;

	var $db_timer;
	private $link_identifier= true;
	private $memory		= array();

	private $pdo = null;
	private $pdostatement = null;

	/*
	* constructor
	*
	* @param string $server database server address
	* @param string $user user login
	* @param string password user password
	* @param boolean persistency tells if the db connection should be persistant or not
	*
	* @return mixed db connection id if successful, FALSE if not.
	*
	* @access public
	*/
	function dims_db($server, $user, $password, $database = '', $persistency = false, $link_identifier = true){
		//Mise en place du mode de debugage MySQL.
		$this->mod_debug	= _DIMS_DEBUGMODE;

		$this->persistency		= $persistency;
		$this->user				= $user;
		$this->password			= $password;
		$this->server			= $server;
		$this->database			= $database;
		$this->connection_id	= 0;
		$this->link_identifier	= $link_identifier;

		$this->open();

	}

	/**
	* ouverture de la connexion
	*/
	function open() {
		try {
			if(!empty($this->database)) {
				$pdooptions = array();

				$dsn = 'mysql:host='.$this->server.';dbname='.$this->database;

				if (_DIMS_ENCODING=='UTF-8') {
					$dsn .= ';charset=utf8';
					// needed before PHP 5.3.6 http://php.net/manual/en/ref.pdo-mysql.connection.php
					$pdooptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
				}

				$pdooptions[PDO::ATTR_PERSISTENT] = $this->persistency;
				$pdooptions[PDO::MYSQL_ATTR_LOCAL_INFILE] = 1;

				$this->timer_start();
				$this->pdo = new PDO($dsn, $this->user, $this->password, $pdooptions);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->timer_stop();

				// supprime le password de la memoire au cas ou l'objet serait
				// malencontreusement affiche (ex. getLightAttributes renvoie $db)
				$this->password = '';

				return $this->pdo;
			}
		}

		catch(Error_Fatal $e){
			global $dims;
			if (!file_exists('./install.php')) {
				$dims->setError(2);
				//Gestion des erreurs fatal.
				$e->getError();
			}
		}
	}

	public function isconnected() {
		return !is_null($this->pdo);
	}

	/**
	* free result pointers (if applicable) and close connection to database
	*
	* @return boolean
	*
	* @access public
	*
	* @uses connection_id
	*
	*/
	public function close() {
		unset($this->pdo);
		$this->pdo = null;
		return true;
	}

	public function isPdoEnabled(){
		return !empty($this->pdo);
	}

	/**
	 * execute a SQL query only if we don't have the key already in cache, and that cache has
	 * been loaded less than $timeout seconds ago. The key is appended with a unique key based
	 * on the provided bound parameters.
	 *
	 * @return an array with all the rows of the query response
	 */
	function fetchAllCached($key, $timeout = 60, $query = '', $bindedparams = null) {
		$cached = false;
		$allRows = array();
		$key = $this->database . "_" . $key;
		$key .= md5(serialize($bindedparams));

		if(APC_EXTENSION_LOADED)
			$timeCache = apc_fetch($key."_TIME", $cached);
		if (!$cached || (time()-$timeCache) > $timeout) {
			// query is not cached or too old, query!
			$res = $this->query($query, $bindedparams);
			while ($fields = $this->fetchrow($res)) { $allRows[] = $fields; }
			if(APC_EXTENSION_LOADED) {
				apc_store($key, $allRows);
				apc_store($key."_TIME", time());
			}
		} else {
			if(APC_EXTENSION_LOADED)
				$allRows = apc_fetch($key);
		}

		return $allRows;
	}



	/**
	* execute a SQL query
	*
	* @return mixed If successful : resultset id, if no result : FALSE, else : die()
	*
	* @access public
	*
	* @param string query sql query to database
	* @param array bindedparams param for in-query replacement eg: array(':identifier' => $value,) XOR array(1, $value,)
	*
	* @uses connection_id
	* @uses num_queries
	* @uses query_result
	* @uses row
	* @uses rowset
	*/
	function query($query = '', $bindedparams = null) {
		unset($this->query_result);
		try{
			$successquery = false;
			if($query != '') {
				$this->num_queries++;
				$this->timer_start();
				// $this->query_result = $this->pdo->prepare($query);
				$this->pdostatement = $this->pdo->prepare($query);

				if(!is_null($bindedparams) && is_array($bindedparams)) {
					foreach($bindedparams as $identifier => $value) {
						if(is_numeric($identifier)) $identifier++; // avoid 0 started array

						if(is_array($value)) {

							$this->pdostatement->bindValue($identifier, $value['value'], $value['type']);
						} else {

							$this->pdostatement->bindValue($identifier, $value, PDO::PARAM_STR);
						}
					}
				}

				$successquery = $this->pdostatement->execute();

				$time_exec = $this->timer_stop();
				if ( _DIMS_DISPLAY_ERRORS == true) {
					if (!$successquery){
						throw new Error_MySQL($query);
					}
					dims_debug::setRequet(array("time" => round($time_exec, 3), "requet" => $query));
				}
			}

			if($successquery) {
				return $this->pdostatement;
			} else {
				return false;
			}
		}
		catch(Error_MySQL $e) {
			$throw_err = false;
			// FIXME : Error case may not match new pdo error codes
			switch($this->pdostatement->errorCode()){
				case self::ERR_DUPLICATE_KEY:
					//On détermine sur quelle colonne la duplicate key entry porte
					$err = $this->pdostatement->errorCode();
					$matches = array();
					if(preg_match('/^Duplicate entry.*for key \'(.*)\'/', $err, $matches)) {
						if(!empty($matches[1]) && defined('_DIMS_CATCH_DUPLICATE_ENTRIES') && _DIMS_CATCH_DUPLICATE_ENTRIES) {

							return array(self::ERR_DUPLICATE_KEY, $matches[1]);
						}
						else $throw_err = true;
					} else {
						$throw_err = true;
					}
					break;

				default:
					$throw_err = true;
					break;
			}

			if($throw_err) {
				echo '<pre>'.$query.'<br/>';
				throw new Exception(mysql_error());
				echo '</pre>';
			}
						die("error");
			return false;
		}
		catch(PDOException $e){
			if(defined('_DIMS_DEBUGMODE') && _DIMS_DEBUGMODE){
				echo $e->getMessage();
			}
		}
	}

	/**
	* execute a SQL query
	*
	* @access public
	*
	*/
	function multiplequeries($queries) {
		$queries	= trim($queries);
		$array_query	= explode(';',$queries);

		foreach ($array_query AS $key => $query) {
			$query = trim($query);
			if ($query != '') {
				$this->query($query);
			}
		}
	}

	/**
	* execute a SQL query
	*
	* @return mixed If successful : number of elements in resultset, else : FALSE
	*
	* @param PDOStatement $pdostatement object
	* @access public
	*
	* @uses pdostatement
	*/
	function numrows($pdostatement = null) {
		if(is_null($pdostatement)) {
			$pdostatement = $this->pdostatement;
		}

		if(!is_null($pdostatement)) {
			return $pdostatement->rowCount();
		} else {
			return false;
		}
	}

	/**
	* retrieves the resultset
	*
	* @return mixed If successful : array containing the query resultset, else : FALSE
	*
	* @param PDOStatement $pdostatement object
	* @param string opt fetching method
	*
	* @access public
	*
	* @uses pdostatement
	*/
	function fetchrow($pdostatement = null, $opt = PDO::FETCH_ASSOC) {
		if(is_null($pdostatement)) {
			$pdostatement = $this->pdostatement;
		}

		if(!is_null($pdostatement)) {
			return $pdostatement->fetch($opt);
		} else {
			return false;
		}
	}

	/*
	 * Cyril - 27/07/2011 - Fonction retournant les données du resultset à travers autant d'array que de tables SQL concernées par le SELECT
	 */
	function split_resultset($pdostatement = null, $rotation = self::ROW_TABLE_FIELD_ROTATION) {
		if(is_null($pdostatement)) {
			$pdostatement = $this->pdostatement;
		}

		if(!is_null($pdostatement)) {
			//initialisation de la structure des champs de la requête
			$reference = array();
			$fieldsnum = $this->numfields($pdostatement);
			for ($i = 0; $i < $fieldsnum; ++$i) {
				$columnmeta = $pdostatement->getColumnMeta($i);
				$table = $columnmeta['table'];
				$field = $columnmeta['name'];
				$reference[$i]['table'] = ($table=='') ? 'unknown_table' : $table; // sert si on a fait un sum, count ...
				$reference[$i]['col'] = $field;
			}

			//chargement du tableau composite
			$composite = array();
			$row = 0;
			while($fields = $this->fetchrow($pdostatement, PDO::FETCH_NUM)) {
				$fieldsnum = count($fields);
				for($i=0; $i < $fieldsnum; $i++) {
					switch($rotation) {
						default:
						case self::ROW_TABLE_FIELD_ROTATION:
							$composite[$row][$reference[$i]['table']][$reference[$i]['col']] = $fields[$i];
							break;
						case self::TABLE_ROW_FIELD_ROTATION:
							$composite[$reference[$i]['table']][$row][$reference[$i]['col']] = $fields[$i];
							break;
						case self::TABLE_FIELD_ROW_ROTATION:
							$composite[$reference[$i]['table']][$reference[$i]['col']][$row] = $fields[$i];
							break;
					}
				}
				$row ++;
			}

			return $composite;
		}
		else return null;
	}

	/**
	* retrieves last database insert id
	*
	* @return mixed If successful : last inserted id, else : FALSE
	*
	* @param string $name name of the sequence object from wich the ID should be returned
	*
	* @access public
	*
	* @uses connection_id
	*/
	function insertid($name = null) {
		if(!is_null($this->pdo)) {
			return $this->pdo->lastInsertId($name);
		} else {
			return false;
		}
	}

	/**
	* retrieves a list of database tables
	*
	* @return mixed If successful : result ressource id on database tables list, else : FALSE
	*
	* @access public
	*
	*/
	function listtables() {
		if($this->isconnected()) {
			return $this->query('SHOW TABLES');
		} else {
			return false;
		}
	}

	/**
	* Test if table exist in database
	*
	* @return bool true if table exists else false
	*
	* @access public
	*
	*/
	public function tableexist($tablename) {
		foreach($this->listtables() as $table) {
			if($table[0] == $tablename) return true;
		}
		return false;
	}

	/**
	* Test if field exist in table
	*
	* @return bool true if field exists else false
	*
	* @access public
	*
	*/
	public function fieldexist($tablename, $fieldname) {
		if($this->tablexist($tablename)) {
			foreach(dims::getInstance()->getTableDescription() as $field) {
				if($field['Field'] == $fieldname) return true;
			}
		}
		return false;
	}

	/**
	* retrieves the number of fields in a resultset
	*
	* @return mixed If successful : number of fields in the resultset, else : FALSE
	*
	* @param PDOStatement $pdostatement object
	*
	* @access public
	*
	*/
	function numfields($pdostatement = null){
		if(is_null($pdostatement)) {
			$pdostatement = $this->pdostatement;
		}

		if(!is_null($pdostatement)) {
			return $pdostatement->columnCount();
		} else {
			return false;
		}
	}

	/**
	* retrieves the resultset in an array
	*
	* @return mixed If successful : array containing the query resultset, else : FALSE
	*
	* @param PDOStatement $pdostatement object
	*
	* @access public
	*
	*/
	function getarray($pdostatement = null) {
		if(is_null($pdostatement)) {
			$pdostatement = $this->pdostatement;
		}

		$fieldsarray = array();
		if(!is_null($pdostatement)) {
			while($fields = $this->fetchrow($pdostatement)) {
				if(count($fields) == 1) $fieldsarray[] = $fields[key($fields)];
				else $fieldsarray[] = $fields;
			}
			return $fieldsarray;
		} else {
			return false;
		}
	}

	function timer_start()	{
		if (class_exists('timer')){
			$this->db_timer = new timer();
			$this->db_timer->start();
		}
	}

	function timer_stop(){
		if (class_exists('timer')){
			$timer	= $this->db_timer->getexectime();
			$this->exectime_queries += $timer;
			return $timer;
		}

		return false;
	}

	public function __destruct(){
		$this->close();
	}

	public static function getParamsFromArray (array $arr, $prefix, &$params, $glue=",") {
		$lstreturn='';
		$i=0;
		foreach ($arr as $value) {
			if ($i>0) $lstreturn.=$glue;
			// calcul de l'indice
			$ind=':'.$prefix.$i;
			// concat
			$lstreturn.=$ind;
			// affectation
			$params[$ind]=$value;
			$i++;
		}
		return $lstreturn;
	}

	public function getPdo() {
		return $this->pdo;
	}
}
