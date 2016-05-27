<?php


class dims_process extends dims_data_object {
	private $dbprocess;
	private $pgpused;
	private $status;
	private $timeexec;
	public function __construct () {
		$this->pgpused=1;
		$this->status=0;
		$this->timeexec=0;
	}

	public function connect() {
		if(defined('_DIMS_DB_PROCESS_SERVER') && defined('_DIMS_DB_PROCESS_LOGIN') && defined('_DIMS_DB_PROCESS_PASSWORD') && defined('_DIMS_DB_PROCESS_DATABASE')){
			if (file_exists(DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php';

			// INIT VARIABLES
			$this->dbprocess = new dims_db(_DIMS_DB_PROCESS_SERVER, _DIMS_DB_PROCESS_LOGIN, _DIMS_DB_PROCESS_PASSWORD, _DIMS_DB_PROCESS_DATABASE);
			if(!$this->dbprocess->isconnected()) {
				trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);
				return false;
			}
			else return true;
		}else
			return false;
	}


	public function insert($id_dims,$program,$cmd,$pathfile,$extension,$resultpath='') {
                // verification du programme
                $id_progr=$this->verifCommand($program);
                if ($id_progr>0 && file_exists($pathfile)) {
                        // on peut faire la demande
                       $sql="INSERT INTO `dims_process` (`id`, `id_dims_origin`, `id_cluster`, `id_programme`, `date_update`, `status`, `path_local_init`, `path_local_final`, `path_dest`, `ext_file_init`, `url_download_init`, `url_download_res`, `commande`, `pgp_encrypt`, `tps_execute`, `md5sum_init`, `md5sum_res`, `priority`) VALUES (";
                        $sql.="null,".$id_dims.",0,".$id_progr.",null,".$this->status.",'".$pathfile."',null,'".$resultpath."','".$extension."',null,null,'".$cmd."',".$this->pgpused.",".$this->timeexec.",'','',0);";
                //      echo $sql;die();
                        // on execute la commande
                        $this->dbprocess->query($sql);
                        // on recupere l'id du process
                        return$this->dbprocess->insertid();
                }
                else return 0;

        }

	/*
	 * Fonction de controle du programme
	 */
	private function verifCommand($program) {
		$id_progr=0;

		$sql="select id from dims_programme where nom ='".dims_sql_filter($program)."'";
		$res=$this->dbprocess->query($sql);

		if ($this->dbprocess->numrows($res)>0) {
			if ($f=$this->dbprocess->fetchrow($res)) {
				$id_progr=$f['id'];
			}
		}
		return $id_progr;
	}

	public function getStatus($id_process) {
		$sql="select status from dims_process where id =".intVal($id_process);
		$res=$this->dbprocess->query($sql);
		$status=-1;

		if ($this->dbprocess->numrows($res)>0) {
			if ($f=$this->dbprocess->fetchrow($res)) {
				$status=$f['status'];
			}
		}
		return $status;
	}

	public function getContent($id_process) {
                $sql="select path_local_final,path_dest from dims_process where id =".intVal($id_process);
                $res=$this->dbprocess->query($sql);
                $content="";

                if ($this->dbprocess->numrows($res)>0) {
                        if ($f=$this->dbprocess->fetchrow($res)) {
                                $path=$f['path_local_final'];
                                $pathdest=$f['path_dest'];
                                $copyfiles=false;
                                if (file_exists($pathdest)) echo file_get_contents($pathdest);
                                else {

					if (is_dir($path)) {
						// on recupere tout le contenu
						// deux cas, soit des fichiers soit un seul
						if ($pathdest ===  basename($pathdest)) $copyfiles=true;
						$folder=opendir($path);
						// on boucle sur le resultat
						while ($file = readdir($folder)) {
							$l = array('.', '..');

							if (!in_array( $file, $l)) {
								if (!is_dir($path.$file)) {
									// on prend les fichiers
									//echo $path.$file." ->".$pathdest.$file."<br>";
									if ($copyfiles)
										rename($path.$file, $pathdest.$file);
									else {// on a un fichier de destination
										copy($path.$file, $pathdest);
										unlink ($path.$file);
										echo file_get_contents($pathdest);
									}
								}
							}
						}
					}
                                }

                        }
                }
                return $content;
        }
}
?>
