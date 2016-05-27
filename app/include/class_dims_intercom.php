<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class dims_intercom extends dims_data_object {
	public $db;						// connector to database abstraction layer : Mysql, Oracle

	function __construct($db){
		parent::dims_data_object('dims_intercom','host','dims','ip');
		$this->db=$db;
	}

        function verifyConnexion($ip,$hostname,$dims) {
            $status=0;
            $ip=dims_sql_filter($ip);
            $hostname=dims_sql_filter($hostname);
            $dims=dims_sql_filter($dims);

            $status=$this->open($hostname,$dims,$ip);

            if ($status==0) {
                $this->save();
            }

            return $status;
        }
}
?>
