<?
class docfolder extends dims_data_object {
	/**
	* Class constructor
	*
	* @access public
	**/

	function __construct() {
		parent::dims_data_object('dims_mod_doc_folder');
		$this->fields['timestp_create'] = dims_createtimestamp();
		$this->fields['timestp_modify'] = $this->fields['timestp_create'];
		$this->fields['parents']=0;
	}

	function save($id_object=0) {
		if($id_object === 0){
			$id_object = dims_const::_SYSTEM_OBJECT_DOCFOLDER;
		}
		if ($this->fields['id_folder'] != 0) {
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$this->fields['parents'] = "{$docfolder_parent->fields['parents']},{$this->fields['id_folder']}";
			$ret = parent::save($id_object);
			$docfolder_parent->fields['nbelements'] = doc_countelements($this->fields['id_folder']);
			$docfolder_parent->save($id_object);
		}
		else{
			$ret = parent::save($id_object);
		}

		return ($ret);
	}

	function setid_object() {
		$this->id_globalobject = dims_const::_SYSTEM_OBJECT_DOCFOLDER;
	}
	function settitle(){
		$this->title = $this->fields['name'];
	}

	function delete() {
		$db = dims::getInstance()->getDb();

		// on recherche tous les fichiers pour les supprimer
		$rs = $db->query("SELECT id FROM dims_mod_doc_file WHERE id_folder = :idfolder",
						array(':idfolder' => $this->fields['id']) );
		while($row = $db->fetchrow($rs)) {
			$file = new docfile();
			$file->open($row['id']);
			$file->delete();
		}

		// on recherche tous les dossiers fils pour les supprimer
		$rs = $db->query("SELECT id FROM dims_mod_doc_folder WHERE id_folder = :idfolder",
						array(':idfolder' => $this->fields['id']) );
		while($row = $db->fetchrow($rs)) {
			$folder = new docfolder();
			$folder->open($row['id']);
			$folder->delete();
		}

		parent::delete();

		if ($this->fields['id_folder'] != 0) {
			$docfolder_parent = new docfolder();
			$docfolder_parent->open($this->fields['id_folder']);
			$docfolder_parent->fields['nbelements'] = doc_countelements($this->fields['id_folder']);
			$docfolder_parent->save();
		}
	}

	function moveto($docfolder) {
			// verify if moduleid egals
			if($this->fields["id_module"]==$docfolder->fields['id_module']) {
				// update id_folder parent + parents
				$this->fields['id_folder']=$docfolder->fields['id'];
				$this->fields['parents']=$docfolder->fields['parents'];
				$this->save();
			}
	}

		/*
		 * fonction permettant de scanner les dossiers en lien avec un lecteur réseau
		 */
		public function scanNetworkFolders() {
			require_once DIMS_APP_PATH . '/modules/doc/include/global.php';
			require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';
			global $dims;
			//$gencode = date(Ymd).rand(1,200);

			//creation dossier temp
			//dims_makedir($gencode);
			// chargement des correspondances de fichier
			$currentpath=realpath(".");

			$rs = $this->db->query("SELECT * FROM dims_mod_doc_folder WHERE foldertype like 'network'");
			if ($this->db->numrows($rs)>0) {
				while($row = $this->db->fetchrow($rs)) {

					$id_docfolder=$row['id'];
					$objdocfolder = new docfolder();
					$objdocfolder->open($id_docfolder);

					$_SESSION['dims']['userid']=$objdocfolder->fields['id_user'];
					$_SESSION['dims']['workspaceid']=$objdocfolder->fields['id_workspace'];
					$_SESSION['dims']['moduleid']=$objdocfolder->fields['id_module'];
					$networkdir = $row['networkpath'];
					echo "\n".$networkdir." : ";

					if ($networkdir=='') {
						echo "Directory is empty ";
					}
					elseif (is_dir($networkdir)) {
						echo $networkdir." is directory <br>";

						if (substr($networkdir,strlen($networkdir)-1,1)!="/") $networkdir.="/";
						$_SESSION['dims']['docs']['pathdest']=$networkdir;
						//chdir($networkdir);
						$this->createRecursiveFilesFromScan($networkdir,$networkdir._DIMS_SEP,"777",$objdocfolder);
						echo " ok";

					} else {
						echo "Acces error";
					}
				}
			}
			chdir ($currentpath);
			// suppression dossier temp
			//dims_deletedir($gencode);
		}

		function createRecursiveFilesFromScan($src,$dest,$mask,$objfolder) {
			$ok = true;
			$folder=opendir($src);

			ini_set('max_execution_time',0);
			ini_set('memory_limit',"300M");
			//if (!file_exists($dest)) mkdir($dest, $mask);

			while ($file = readdir($folder)) {
				$l = array('.', '..');

				if (!in_array( $file, $l)) {
					if (is_dir($src.$file)) {
						// on doit recr��er le dosier contenant les �ventuels fichiers en plus
						$docfolder = new docfolder();
						$docfolder->fields['foldertype']=$objfolder->fields['foldertype'];

						if (mb_check_encoding($file,"UTF-8")) $namefile=utf8_decode($file);
						else $namefile=$file;

						$docfolder->fields['name']=$namefile;
						$docfolder->fields['description']="";

						if ($objfolder->fields['id']==0) $docfolder->fields['parents']=$objfolder->fields['id'];
						else $docfolder->fields['parents']=$objfolder->fields['parents'].",".$objfolder->fields['id'];

						$docfolder->fields['readonly']=0;
						$docfolder->fields['readonly_content']=0;
						$docfolder->fields['timestp_create']=dims_createtimestamp();
						$docfolder->fields['timestp_modify']=dims_createtimestamp();
						$docfolder->fields['published']=$objfolder->fields['published'];
						$docfolder->fields['id_folder']=$objfolder->fields['id'];
						$docfolder->setugm();
						//dims_print_r($docfolder);die();
						$docfolder->save();
						$ok = $this->createRecursiveFilesFromScan("$src$file"._DIMS_SEP, "$dest$file"._DIMS_SEP, $mask,$docfolder);

						// maj des �l�ments
						$docfolder->save();
						unset($docfolder);
					}
					else
					{
						// test if writable
						if (!(file_exists("$dest$file") && !is_writable("$dest$file"))) {

							// on commence le traitement du document
							$currentpath=realpath(".");

							//on copie le fichier courant dans un dossier temp
							$temppath=DIMS_TMP_PATH.session_id()."scan/";
							if (is_dir($temppath)) {
								dims_deletedir($temppath);
							}
							dims_makedir($temppath);

							// on copie le fichier courant
							copy($src.$file,$temppath.'file.pdf');

							// appel du script de conversion
							chdir($temppath);
							$exec="bash ".escapeshellarg($currentpath."/scripts/splitpdf.sh")." ".escapeshellarg($temppath."file.pdf")." ".escapeshellarg($temppath);

							shell_exec($exec);
							unlink($temppath."file.pdf");

							chdir($currentpath);

							// on traite les fichiers ds le dossier courant
							echo "\n".$temppath;
							$foldertemp=opendir($temppath);
							while ($fpdf = readdir($foldertemp)) {

								if (!in_array( $fpdf, $l)) {
									if (!is_dir($temppath.$fpdf)) {
										$tabfile=explode(".",$fpdf);

										if (strtolower($tabfile[1])=="pdf") {
											// on a le pdf resultat
											// on regarde si on a le code barre qui va bien ds le fichier resultX.txt
											// on doit enlever EAN-13:
											$code="";
											if (file_exists($temppath.$tabfile[0].".txt")) {
												$code=file_get_contents($temppath.$tabfile[0].".txt");
												$code = str_replace("EAN-13:","",$code);
											}

											// on sauvegarde le fichier avec ou sans code
											$docfile = new docfile();
											$docfile->fields['id_module']=$objfolder->fields['id_module'];
											$docfile->fields['id_workspace']=$objfolder->fields['id_workspace'];
											$docfile->fields['id_user']=$objfolder->fields['id_user'];
											$docfile->fields['id_folder'] = $objfolder->fields['id'];
											$docfile->fields['size'] = filesize($temppath.$tabfile[0].".pdf");

											$namefile="fichier_".date("d-m-Y_His").".pdf";

											$docfile->fields['name'] = $namefile;
											$docfile->fields['description'] = $code;

											$docfile->tmpzipfile =$temppath.$tabfile[0].".pdf";
											echo "\n".$temppath.$tabfile[0].".pdf";
											//dims_print_r($docfile->fields);die();
											chdir($currentpath);
											$erreur=$docfile->save();

											if ($erreur>0)
											{
												echo $erreur;
											}
											//unlink($src.$file);
											unset($docfile);
										}
										//dims_print_r($tabfile);
									}
								}
							}

						}
						else $ok = false;
					}
				}
			}
			return $ok;
		}
		/*
		 * Fonction permettant d'obtenir la liste des lecteurs réseaux
		 */
		public function getNetworkFolders() {
			global $dims;
			$arrayfolders = array();


			foreach($dims->getModuleByType('doc') as $i =>$mod) {
				$rs = $this->db->query("SELECT df.*
										FROM dims_mod_doc_folder as df
										INNER JOIN dims_module as m
										ON m.id=df.id_module
										AND df.foldertype LIKE 'network'
										AND m.id_module_type= :moduleid",
										array(':moduleid' => $mod['id']) );
				if ($this->db->numrows($rs)>0) {
					while($row = $this->db->fetchrow($rs)) {
						$arrayfolders[]=$row;
					}
				}
			}

			return $arrayfolders;
		}

	public static function getElements($id_folder, $order_by_doc='', $order_by_file=''){

		$sql = "SELECT * FROM dims_mod_doc_folder WHERE  id_folder = :idfolder ".$order_by_doc;
		$db = dims::getInstance()->getDb();
		$res = $db->query($sql, array(':idfolder' => $id_folder) );
		$sub_folders = array();
		while($tab = $db->fetchrow($res)){
			$sub_folders[] = $tab;
		}

		//ensuite les fichiers
		$sql = "SELECT * FROM dims_mod_doc_file WHERE id_folder = :idfolder ".$order_by_file;
		$res = $db->query($sql, array(':idfolder' => $id_folder) );
		$sub_files = array();
		while($tab = $db->fetchrow($res)){
			$sub_files[] = $tab;
		}
		$a = array();
		$a[0]= $sub_folders;
		$a[1]= $sub_files;
		return $a;
	}

	/*
	 * Fonction permettant de retrouver les nouveaux dossiers potentiels au regard d'un dossier courant
	 */
	public function getAvailableCategs() {
		require_once DIMS_APP_PATH . 'modules/system/class_category.php';

		$categ = new category();
		$listcategs = $categ->getAllCateg(3);

		// tableau de retour des possibles
		$tabresult = array();

		// on va regarder la liste des dossiers déjà créés, on regarde sur quel niveau on se trouve
		$sql = "SELECT id_category FROM dims_matrix WHERE id_docfolder=:idfolder";

		$db = dims::getInstance()->getDb();

		$res = $db->query($sql, array(':idfolder' => $this->fields['id_globalobject']));

		$categ_cour = 0;
		$categ_cour_go = 0;

		while ($tab = $db->fetchrow($res)){
			$categ_cour_go=$tab['id_category'];

			if ($categ_cour_go>0) {
				// on recherche le globalobject
				$categ->openWithGB($categ_cour_go);
				$categ_cour = $categ->fields['id'];
			}
		}

		if (($categ_cour == 0 && $this->fields['id_folder'] == 0) || ($categ_cour > 0 && isset($listcategs[$categ_cour]))) {
			$tabexists = array();
			// on regarde les fils deja existants en lien avec les
			$sql = "SELECT c.* FROM dims_category as c"
				. " INNER JOIN dims_matrix as m"
				. " ON m.id_category = c.id_globalobject "
				. " INNER JOIN dims_mod_doc_folder as f"
				. " ON f.id_globalobject = m.id_docfolder"
				. " AND f.id_folder = :idfolder_parent";

			$res = $db->query($sql, array(':idfolder_parent' => $this->fields['id']) );

			while ($tab = $db->fetchrow($res)) {
				$tabexists[$tab['id']] = $tab;
			}

			foreach ($listcategs[$categ_cour]['children'] as $child) {
				if (!isset($tabexists[$child])) {
					// on a un element que l'on peut creer
					$tabresult[$child] = $listcategs[$child];
				}
			}
		}

		return $tabresult;
	}
}
