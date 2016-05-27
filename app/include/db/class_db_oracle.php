<?php
include_once DIMS_APP_PATH.'/include/class_exception.php';

class dims_db{
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
    private $link_identifier	= true;
    private $memory		= array();

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
    function dims_db($server, $user, $password, $osef='', $persistency = false, $link_identifier = true){
        $this->persistency 		= $persistency;
        $this->user 			= $user;
        $this->password 		= $password;
        $this->server 			= $server;
        $this->connection_id 	= 0;
        $this->link_identifier	= $link_identifier;
        //Mise en place du mode de debugage MySQL.
        //$this->mod_debug = _DIMS_DEBUGMODE;
        $this->open();
    }

	function open() {
		try {
            if($this->persistency) {
                $this->timer_start();
                $this->connection_id = oci_connect($this->user, $this->password,$this->server);
                $this->timer_stop();
            }else{
                $this->timer_start();
                $this->connection_id = oci_pconnect($this->user, $this->password,$this->server);
                $this->timer_stop();
            }
            if (oci_error() != 0){
                throw new Error_Fatal(100);
            }
            if($this->connection_id){
                return $this->connection_id;
            }else{
                throw new Error_Fatal(103);
            }
        }catch(Error_Fatal $e){
            global $dims;
            $dims->setError(2);
            //Gestion des erreurs fatal.
            $e->getError();
        }
	}

    function isconnected(){
        return ($this->connection_id!=0);
    }

    /**
    * free result pointers (if applicable) and close connection to database
    *
    * @return boolean
    *
    * @access public
    *
    * @uses connection_id
    */
    function close() {
            if($this->connection_id) {
                    $this->timer_start();
                    if(is_resource($this->query_result)){
                        oci_free_statement($this->query_result);
                    }
                    oci_close($this->connection_id);
                    $this->timer_stop();
                    $this->connection_id	= false;
                    return true;
            }else{
                    return false;
            }
    }

    /**
    * execute a SQL query
    *
    * @return mixed If successful : resultset id, if no result : FALSE, else : die()
    *
    * @access public
    *
    * @uses connection_id
    * @uses num_queries
    * @uses query_result
    * @uses row
    * @uses rowset
    */
    function query($query = ''){
        $query = str_replace("	"," ",$query);
        $query = str_replace("
"," ",$query);
        $query = str_replace("`","",$query);
        $query = trim($query,";");
        $query = str_replace("\"","'",$query);
        $query = str_replace("distinct","",$query);
        $query = str_replace("DISTINCT","",$query);
        $query = str_replace("group by","GROUP BY",$query);
        $query = str_replace("insert","INSERT",$query);
        $query = str_replace(" limit "," LIMIT ",$query);
        $query = str_replace("update","UPDATE",$query);
        $query = str_replace(" from "," FROM ",$query);
        $query = str_replace("set","SET",$query);
        $query = str_replace("where","WHERE",$query);
        $query = str_replace("WHERE 1 ","WHERE 1=1 ",$query);
        $query = str_replace("order by","ORDER BY",$query);
        $query = str_replace("select","SELECT",$query);
        $query = str_replace("dims_mod_business","dmb",$query);
        $query = str_replace("dims_mod_elearning","dme",$query);
        $query = str_replace("dims_mod_newsletter","dmn",$query);
        $query = str_replace("dims_mod_wce","dmw",$query);
        $query = str_replace("dims_mod_webmail","dmwebmail",$query);
        $query = str_replace("dmb_contact_import_ent_similar","dmb_cont_import_ent_simil",$query);
        $query2 = $query;
        $query2 = str_replace(" as "," AS ",$query2);
        $query2 = str_replace(" As "," AS ",$query2);

        $query = str_replace(" AS "," ",$query);
        $query = str_replace(" as "," ",$query);
        $query = str_replace(" As "," ",$query);
        $liste_col = array("comment","share","size","access","public","level","date","view","validate","values","default","mode","uid","number","group");
        foreach($liste_col as $mot){
            $query = str_replace(".$mot ",".\"$mot\" ",$query);
            $query = str_replace(".$mot,",".\"$mot\",",$query);
            $query = str_replace(",$mot,",",\"$mot\",",$query);
            $query = str_replace(".$mot>",".\"$mot\">",$query);
            $query = str_replace(".$mot<",".\"$mot\"<",$query);
            $query = str_replace(".$mot=",".\"$mot\"=",$query);
            $query = str_replace(" $mot "," \"$mot\" ",$query);
        }
        if(strstr($query,"GROUP BY")){
            $champs = strstr($query,"SELECT");
            $champs = strstr($champs,"FROM",true);
            $champs = trim($champs,"SELECT");
            $list_champs = array();
            while(strstr($champs,",")){
                $champ = strstr($champs,",",true);
                $champs = str_replace("$champ,","",$champs);
                $champ = trim($champ," ");
                if(strstr($champ," ")){
                    $champ = str_replace(strstr($champ," "),"",$champ);
                }
                if(strstr($champ,".*")){
                    $table = strstr($champ,".*",true);
                    $table = str_replace(" ","",$table);
                    $from = strstr($query2,"FROM");
                    $table2 = $table;
                    if (strstr($from," AS $table")){
                        $table = strstr($from," AS $table",true);
                        $table = trim($table," ");
                        $table = strrchr($table," ");
                    }
                    $table = str_replace(" ","",$table);
                    $q = "Select COLUMN_NAME from USER_TAB_COLUMNS where TABLE_NAME = '".strtoupper($table)."'";
                    $res = oci_parse($this->connection_id,$q);
                    ociexecute($res);
                    while($row = oci_fetch_assoc($res)){
                        if(strtolower($row['COLUMN_NAME']) == $row['COLUMN_NAME'])
                            $row['COLUMN_NAME'] = '"'.$row['COLUMN_NAME'].'"';
                        $list_champs[] = $table2.".".$row['COLUMN_NAME'];
                    }
                }
                if(!strstr($champ,"count(") && !strstr($champ,"max(") && !strstr($champ,"min(") && !strstr($champ,"avg(") && !strstr($champ,"*"))
                    $list_champs[] = $champ;
            }
            $champs = str_replace(' ','',$champs);
            if(strlen($champs) > 0){
                if(!strstr($champs,"count(") && !strstr($champs,"max(") && !strstr($champs,"min(") && !strstr($champs,"avg(") && !strstr($champs,"*"))
                    $list_champs[] = $champs;
            }
            if (!empty($list_champs)){
                $champs = " GROUP BY ".implode(",",$list_champs)." ";
            }else{
                $champs = "";
            }
            $del = strstr($query,"GROUP BY");
            if (strstr($del,"ORDER BY",true))
                $del = strstr($del,"ORDER BY",true);
            $query = str_replace($del,$champs,$query);
        }
        if(strstr($query,"LIMIT")){
            $limit = strstr($query,"LIMIT");
            $query = str_replace($limit,"",$query);
            $limit = trim($limit,"LIMIT");
            $limit = str_replace(" ","",$limit);
            if(strstr($query,"WHERE"))
                $l = "AND ";
            else
                $l = "WHERE ";
            if(strstr($limit,",")){
                $start = strstr($limit,",",true);
                $nb = strstr($limit,",");
            }else{
                $start = 0;
                $nb = $limit;
            }
            $l .= "rownum>$start AND rownum<".($nb+1);
            $query .= $l;
        }
        if(strstr($query,"INSERT") && strstr($query,"SET")){
            $modifs = strstr($query,"SET");
            $table = strstr($query,'SET',true);
            $table = str_replace("INSERT INTO ","",$table);
            $table = str_replace(" ","",$table);
            $champs = array();
            $vals = array();
            while(strstr($modifs,$table)){
                $modif = strstr($modifs,$table);
                if(strstr($modif,",",true)) $modif = strstr($modif,",",true);

                $champ = strstr($modif,"=",true);
                $champ = str_replace("$table.","",$champ);
                $champ = str_replace(" ","",$champ);
                $champs[] = $champ;

                $val = strstr($modif,"=");
                $val= str_replace("=","",$val);
                $val = trim($val," ");
                $vals[] = $val;

                $modifs = str_replace("$modif","",$modifs);
            }
            $query = "INSERT INTO $table (";
            $end = count($champs)-1;
            foreach($champs as $i => $champ){
                $query .= $champ;
                if($i != $end) $query .= ",";
            }
            $query .= ") VALUES (";
            $end = count($vals)-1;
            foreach($vals as $i => $val){
                $query .= $val;
                if($i != $end) $query .= ",";
            }
            $query .= ")";
        }
        else if(strstr($query,"UPDATE")){
            //$modifs = strstr($query,"SET");
            //$modifs = strstr($modifs,"WHERE",true);
            //$extra = strstr($query,"WHERE");
            //$table = strstr($query,'SET',true);
            //$table = str_replace("UPDATE ","",$table);
            //$table = str_replace(" ","",$table);
            //$champs = array();
            //$vals = array();
            //while(strstr($modifs,",")){
            //    $modif = strstr($modif,",",true);
            //    echo $modif;
            //
            //    $champ = strstr($modif,"=",true);
            //    $champs[] = $champ;
            //
            //    $val = strstr($modif,"=");
            //    $val= str_replace("=","",$val);
            //    $vals[] = $val;
            //
            //    $modifs = str_replace("$modif","",$modifs);
            //}
            //$query = "UPDATE $table SET ";
            //$end = count($champs)-1;
            //foreach($champs as $i => $champ){
            //    $val = $vals[$i];
            //    $query .= $champ."=".$val;
            //    if($i != $end) $query .= ",";
            //}
            //$query .= $extra;
        }
        unset($this->query_result);
        try{
            $this->memory["query"]	= $query;
            if($query != ''){
                $this->num_queries++;

                $this->timer_start();
                $this->query_result = oci_parse($this->connection_id,$query);
                ociexecute($this->query_result);

                $time_exec	= $this->timer_stop();

                if (oci_error($this->query_result) != 0){
                    throw new Error_MySQL(oci_error($this->query_result));
                }
            }
            if($this->query_result){
                unset($this->row[$this->query_result]);
                unset($this->rowset[$this->query_result]);
                return $this->query_result;
            }else{
                return (false);
            }
        }
       catch(Error_MySQL $e){
            //Gestion des erreurs dans les class.
            echo $query2;
            $e->getError();
        }
    }

    /**
    * execute a SQL query
    *
    * @access public
    *
    */
    function multiplequeries($queries){
            $queries 		= trim($queries);
            $array_query 	= explode(';',$queries);

            foreach ($array_query AS $key => $query){
                    $query 	= trim($query);
                    if ($query != ''){
                            $this->query($query);
                    }
            }
    }



    /**
    * execute a SQL query
    *
    * @return mixed If successful : number of elements in resultset, else : FALSE
    *
    * @param int query id
    * @access public
    *
    * @uses query_result
    */
    function numrows($query_id = 0){
            if(!$query_id){
                    $query_id = $this->query_result;
            }
            if($query_id){
                    if($this->numfields($query_id))
                        return true;
                    else
                        return false;
            }else{
                    return false;
            }
    }


    /**
    * retrieves the resultset
    *
    * @return mixed If successful : array containing the query resultset, else : FALSE
    *
    * @param int query id
    * @param string opt fetching method
    *
    * @access public
    *
    * @uses query_result
    * @uses row
    */
    function fetchrow($query_id = 0, $opt = OCI_ASSOC){
            if(!$query_id){
                $query_id = $this->query_result;
                if($query_id) {
                    $r = oci_fetch_array($query_id, $opt);
                }else{
                    return false;
                }
            }else{
                $r = oci_fetch_array($query_id, $opt);
            }
            foreach($r as $keys => $val){
                unset($r[$keys]);
                $r[strtolower($keys)] = $val;
            }
            return $r;
    }

    /**
    * retrieves last database insert id
    *
    * @return mixed If successful : last inserted id, else : FALSE
    *
    * @access public
    *
    * @uses connection_id
    */
    //function insertid(){
    //        if($this->connection_id){
    //                $result = @mysql_insert_id($this->connection_id);
    //                return $result;
    //        }else{
    //                return false;
    //        }
    //}

    /**
    * retrieves a list of database tables
    *
    * @return mixed If successful : result ressource id on database tables list, else : FALSE
    *
    * @access public
    *
    * @uses database
    * @uses connection_id
    */
    //function listtables(){
    //        if($this->connection_id){
    //                $result = @mysql_list_tables($this->database, $this->connection_id);
    //                return $result;
    //        }else{
    //                return false;
    //        }
    //}

    /**
    * retrieves a table name
    *
    * @return mixed If successful : table name, else : FALSE
    *
    * @param int result ressource id from the mysql_list_tables()
    * @param int index of table
    *
    * @access public
    *
    * @uses connection_id
    */
    function tablename($tables, $i){
            if($this->connection_id){
                    $result = @mysql_tablename($tables, $i);
                    return $result;
            }else{
                    return false;
            }
    }

    /**
    * retrieves the number of fields in a resultset
    *
    * @return mixed If successful : number of fields in the resultset, else : FALSE
    *
    * @param int result ressource id
    *
    * @access public
    *
    * @uses query_result
    */
    function numfields($query_id = 0){
            if(!$query_id){
                    $query_id = $this->query_result;
            }

            if($query_id){
                    $result = oci_num_fields($query_id);
                    return $result;
            }else{
                    return false;
            }
    }

    /**
    * retrieves name of a fields in a resultset
    *
    * @return mixed If successful : field name name, else : FALSE
    *
    * @param int result ressource id
    * @param int field index
    *
    * @access public
    *
    * @uses query_result
    */
    function fieldname($query_id = 0, $i){
            if(!$query_id){
                    $query_id = $this->query_result;
            }

            if($query_id){
                    $result = oci_field_name($query_id, $i);
                    return $result;
            }else{
                    return false;
            }
    }


    /**
    * retrieves the resultset in an array
    *
    * @return mixed If successful : array containing the query resultset, else : FALSE
    *
    * @param int query id
    *
    * @access public
    *
    * @uses query_result
    */
    function getarray($query_id = 0, $shrink = TRUE){
            $this->array	= array();

            if(!$query_id){
                    $query_id = $this->query_result;
            }

            if($query_id){
                    $this->dataseek($query_id, 0);
                    while ($fields = $this->fetchrow($query_id)){
                            if (sizeof($fields) == 1) $this->array[] = $fields[key($fields)];
                            else $this->array[] = $fields;
                    }

                    return $this->array;
            }else{
                    return false;
            }
    }



    function dataseek($query_id = 0, $pos = 0){
            if(!$query_id){
                    $query_id = $this->query_result;
            }

            if($query_id){
                    return $query_id->seek($pos);
            }else{
                    return(false);
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

    private function view_mysq_error($query=false){
            $res		= debug_backtrace();
            $res		= array_reverse($res);
            $iderror	= "error".rand();
            $cpte		= 0;
            $result;

            foreach($res as $line) {
                    $result.=str_repeat("&nbsp;",$cpte)."Fichier ".$line['file']." : ".$line['line']." fonction (".$line['function'].")<br>";
                    $cpte++;
            }

            return '<div style="border:0.1em solid #FF0000; margin: 5px; padding: 10px 10px 10px 10px; background-color: #FFFFCC; color: red;">
                            <span style="font-style: bold; cursor : pointer;" onclick="switchElementDisplay(\''.$iderror.'\');">
                            <img id="img'.$iderror.'" src="./common/img/plus.gif">Context :</span>
                            <br/>
                            <span style="display:none;visibility:hidden;" id="'.$iderror.'">'.$result.'</span>
                            <br />
                            <span style="font-style: bold;">Requet MySQL</span> :
                            <br />'.
                            $query.
                            '<br /><br />
                            <span style="font-style: bold;">Error MySQL</span> : <br />'.
                            oci_error().
                            " - ".
                            oci_error().
                            "</div>\n";
    }
}
?>
