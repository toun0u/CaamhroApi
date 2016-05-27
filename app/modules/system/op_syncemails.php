<?php
require_once DIMS_APP_PATH.'modules/system/class_webmail_email.php';
$domain_code=dims_load_securvalue('key', dims_const::_DIMS_CHAR_INPUT, true,true,true);
ob_end_clean();
if ($dims->getEnabledBackoffice()) {
	if (!isset($_SESSION['dims']['moduleid']) || $_SESSION['dims']['moduleid'] <=0) {
		$_SESSION['dims']['moduleid'] =1;
	}

	$res=$db->query("select * from dims_domain where webmail_http_code= :webmailhttpcode ", array(
		':webmailhttpcode' => $domain_code
	));
	if ($db->numrows($res)==1) {

		//echo "ok workspace identifie et code accepte<br>\n";
		//echo $_POST['message'];
		//$content = ob_get_contents();
		//ob_end_clean();

		$temp_dir = DIMS_TMP_PATH . '/webmail/'.date('YmdHis').'/';

		if (!file_exists($temp_dir)) {
				dims_makedir($temp_dir);
		}

		$domain_code=substr( str_pad( dechex( mt_rand() ), 6, '0',STR_PAD_LEFT ), -1*6 );
		//$domain_code="c93355"; // for debug
		$fileindex=$temp_dir.$domain_code.'.eml';

		if (!isset($_POST['message'])) {
			echo "nok";
		}
		//echo "Create file : ".$fileindex;

		file_put_contents($fileindex, dims_load_securvalue($_POST['message'], dims_const::_DIMS_CHAR_INPUT, true, true, true));
		//dims_print_r($_POST['message']);
		$mail = new webmail_email();
		//$mail->SetFileAttachFolder($temp_dir);
		$mail->fields['id_inbox'] = 0;
		$mail->fields['uid'] = 0;
		$mail->fields['read'] = 0;
		$mail->fields['id_module'] = $_SESSION['dims']['moduleid'];
		$mail->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$mail->fields['id_user'] = $_SESSION['dims']['userid'];

		//$content=$mail->getEmailContent($fileindex);
				$id_user_from=0;
		$adr='';
		$fp = fopen($fileindex, 'r');
		$test_content = false ;
		$boundaries=array();

		$contenttype=false;
		$contentboundary=false;
		$started_boundary=false;
		$content_boundary='';
		$current_boundary = "";

		while($ligne = fgets($fp)){
			//print "l =>".$ligne."<br>";
			$boundary='';
			if (substr($ligne,0,5) == "Date:"){
				$date = trim(substr($ligne,5));
				$date = strtotime($date);
				$mail->fields['date'] = date('YmdHis',$date);
			// recherche From
			}
			elseif(substr($ligne,0,5) == "From:"){
				$adr = trim(substr($ligne,5));
				$mail->addFrom($adr);
				$id_user_from=$mail->getUserFrom();
				if ($id_user_from>0) {
					$mail->fields['id_user'] = $id_user_from;
				}
			}
			elseif(substr($ligne,0,5) == "From "){
				$adr = trim(substr($ligne,5));
				$res=explode(" ",$adr);

				if (isset($res[0])) $adr=$res[0];

				$mail->addFrom($adr);
				$id_user_from=$mail->getUserFrom();
				if ($id_user_from>0) {
					$mail->fields['id_user'] = $id_user_from;
				}
			}
			elseif(substr($ligne,0,9) == "X-Sender:"){
				$adr = trim(substr($ligne,9));
				$mail->addFrom($adr);
				$id_user_from=$mail->getUserFrom();

				if ($id_user_from>0) {
					$mail->fields['id_user'] = $id_user_from;
				}
			}elseif(substr($ligne,0,3) == "To:"){
				$adr = trim(substr($ligne,3)) ;
				while (substr($adr,-1) == ','){
					$ligne = fgets($fp);
					$adr .= trim(substr($ligne,3)) ;
				}
				$mail->addDestTo($adr);
			// recherche Cc
			}elseif(substr($ligne,0,3) == "Cc:"){
				$adr = trim(substr($ligne,3)) ;
				while (substr($adr,-1) == ','){
					$ligne = fgets($fp);
					$adr .= trim(substr($ligne,3)) ;
				}
				$mail->addDestCc($adr);
			// recherche Sujet
			}elseif(substr($ligne,0,8) == "Subject:"){
				$sujet = trim(substr($ligne,8)) ;
				$ligne = fgets($fp);
				$deb_sujet = ftell($fp);
				while((trim($ligne) == "") || (!preg_match("/^[[:upper:]][\-A-Za-z]*: /",trim($ligne)))){
					$sujet .= ' '.trim($ligne) ;
					$ligne = fgets($fp);
				}
				$mail->fields['subject'] = $sujet;
				fseek($fp,$deb_sujet);
			}elseif(substr($ligne,0,26) == "Content-Transfer-Encoding:"){
				$test_content = true ;
			// recherche body + attachement
			}elseif(substr($ligne,0,13) == "Content-Type:"){

				if(strpos($ligne,"multipart") > 0){

					$contenttype=true;
					if (strpos($ligne,"boundary=") > 0 ){
						$ligne=str_replace('boundary=','',trim($ligne));
						$ligne=str_replace('\";','',$ligne);
						$boun=str_replace('\"','',$ligne);
						//$boun = explode('"',$ligne);
						//$boundary = '--'.$boun[1];
						$boundary = '--'.$boun;
						$contentboundary=true;

						// ajout de la balise ds le tableau des boundary
						//$boundaries[$boundary]=array();
						$boundaries[$boundary]['start']=false;
						$boundaries[$boundary]['end']=false;
						$boundaries[$boundary]['content']=array();
					}
				}
			}
			elseif(strpos($ligne,"boundary=") > 0 && $contenttype && trim($ligne)!=''){
				$ligne=str_replace('boundary=','',trim($ligne));
				$ligne=str_replace('\";','',$ligne);
				$boun=str_replace('\"','',$ligne);

				//$boun = explode('"',$ligne);
				/*if (sizeof($boundaries)>0) {
					dims_print_r($boun);die();
				}*/
				//$boundary = '--'.$boun[1];
				$boundary = '--'.$boun;
				$contentboundary=true;

				// ajout de la balise ds le tableau des boundary
				//$boundaries[$boundary]=array();
				$boundaries[$boundary]['start']=false;
				$boundaries[$boundary]['end']=false;
				$boundaries[$boundary]['content']=array();
				if (sizeof($boundaries)>1) {
					//dims_print_r($boundaries);die();
				}
			}


			// on dispose d'un boundary
			/*if ($boundary != "") {
				if(strpos($ligne,$boundary) > 0) {
					echo "<br> End of boundary<br>";
					// si ok on a un contenu de boundary à stocker
					if ($started_boundary && $content_boundary!='') {
						$boundaries[$boundary]['end']=true;
						$boundaries[$boundary]['content'][]=$content_boundary;
					}
				}



			}*/

			// check for boundary pattern
			if (isset($boundaries[trim($ligne)]) && trim($ligne)!='') {
				$ligne=trim($ligne);
				// si ok on a un contenu de boundary à stocker
				if ($started_boundary && $content_boundary!='') {
					//echo "<br> End of boundary<br>";
					$boundaries[$current_boundary]['end']=true;
					$boundaries[$current_boundary]['content'][]=$content_boundary;
				}

				// on a un debut de boundary
				$boundaries[$ligne]['start']=true;
				$started_boundary=true;
				$content_boundary='';
				$current_boundary=$ligne;
				//echo "<br> Begin boundary<br>";
			}
			else {
				// on construit le contenu du boundary
				if (strlen(trim($ligne))>2) {
					$subligne=substr(trim($ligne),0,strlen(trim($ligne))-2);
					//if ($subligne=="--_006_A8ACE35E4D93654589B6C43CFA60B93B5B31739CF4msex1MINECOlo_") {
					//	dims_print_r($boundaries[trim($subligne)]);
					//	die();
					//}
				}
				else {
					$subligne=$ligne;
				}
				//echo $subligne."<br>";
				if ($started_boundary && !isset($boundaries[trim($ligne)])	&& !isset($boundaries[trim($subligne)])) {
					$content_boundary.=$ligne;
				}
			}

		}

		// fin du dernier boundary
		if ($started_boundary && $content_boundary!='') {
			//echo "<br> End of boundary<br>";
			$boundaries[$current_boundary]['end']=true;
			$boundaries[$current_boundary]['content'][]=$content_boundary;
		}
		//dims_print_r($boundaries);die();

		// définition des boundaries
		if (isset($boundaries)) {
			foreach ($boundaries as $k =>$boundary) {
				if (isset($boundary['content']) && $boundary['content']!='') {
					foreach ($boundary['content'] as $ind => $content) {
						$test_content_type = false ;
						$test_content_transfer = false ;
						$test_content_dispo = false ;
						$name_attachement = '';
						$encoding = false ;
						$body = false ;
						//dims_print_r($content);die();
						// ecriture de chaque boundary pour debug
						$fileattach=$temp_dir.$domain_code.'_attach'.$k."_".$ind.'.eml';
						file_put_contents($fileattach, $content);

						// lecture de chaque fichier
						$fp = fopen($fileattach, 'r');
						while ($ligne = fgets($fp)){
							$ligne=trim($ligne);
							//echo "New ligne ".$ligne."<br>";
							if (substr($ligne,0,8) == "Content-"){
								$type = explode(':',$ligne);
								$type_content = substr($type[0],7);

								switch($type_content){
									case "-Transfer-Encoding" :
										$test_content_transfer = true ;
										if (strpos($ligne,"base64") > 0) {
											$encoding = true ;
										}

										break ;
									case "-Type" :
										$test_content_type = true ;
										//echo "<br>TYPE :".$ligne."<br>";
										if (strpos($ligne,"text/plain;") > 0){
											$test_content_dispo = true ;
											$body = true ;
										}
										elseif (strpos($ligne,"text/vcard;") > 0){
											$test_content_dispo = true ;
											$body=false;
										}
										elseif (strpos($ligne,"text/x-vcard;") > 0){
											$test_content_dispo = true ;
											$body=false;

										}
										break ;
									case "-Disposition" :
										$test_content_dispo = true ;
										if (strpos($ligne,"name=") > 0){
											$ligne=str_replace('\"','"',$ligne);
											$name = explode('"',$ligne);
											if ($name[1]!='')
												$name_attachement = $name[1];
										}
										else {
											while (($ligne = fgets($fp)) && ($name_attachement == '')){
												if (strpos($ligne,"name=") > 0){
													$ligne=str_replace('\"','"',$ligne);
													$name = explode('"',$ligne);
													if ($name[1]!='')
														$name_attachement = $name[1];
												}

											}
										}
										break ;
									default :
										/*if ($test_content_type && $test_content_transfer && $test_content_dispo){
											$content .= $ligne;
										}*/
										break ;
								}
								if (strpos($ligne,"name=") > 0){
									$ligne=str_replace(array('\\\"','\\"','\"'),'"',$ligne);
									$name = explode('"',$ligne);
									if ($name[1]!='') {
										$name_attachement = $name[1];

									}
								}
								//echo $test_content_type."-".$test_content_transfer."-".$test_content_dispo."-".$name_attachement."-".$boundary."<br>";
							}

							// on a le separateur strlen($ligne)==0 &&
							if( $test_content_type && $test_content_transfer && $test_content_dispo && $name_attachement!=''){
								$boundarytiret=$boundary."--";
								$content=''; // on vide pour ne prendre que le contenu du texte
								$ligne=fgets($fp);
								$content .= $ligne;
								while (($ligne = fgets($fp)) && (trim($ligne) != $boundary) && (trim($ligne) != $boundarytiret)){
									$content .= $ligne;
								}

								if($body){
									// correspond au texte dans le mail
									$mail->fields['content'] = $content ;
								}else{
									//echo "l ".$name_attachement." ".$encoding;
									// correspond ? un fichier joint
									// cr√©ation d'un fichier ...

									if ($encoding)
										$content = base64_decode($content);

									if ($name_attachement!='') {

										file_put_contents(DIMS_WEB_PATH.'/webmail/'.$name_attachement, $content);

										// cr√©ation du do file associ√©
										$doc_file = new docfile();
										$doc_file->fields['id_module'] = $_SESSION['dims']['moduleid'] ;
										$doc_file->fields['id_workspace'] = $_SESSION['dims']['workspaceid'] ;
										$doc_file->fields['id_user'] = $mail->fields['id_user'];
										$doc_file->tmpuploadedfile = DIMS_TMP_PATH . '/webmail/'.$name_attachement ;
										$doc_file->fields['name'] = $name_attachement ;
										$doc_file->fields['size'] = filesize(DIMS_TMP_PATH . '/webmail/'.$name_attachement);

										$error = $doc_file->save() ;
										$id_doc = $doc_file->fields['id'] ;

										// on lie le fichier au mail
										$mail->addFilesAttached($id_doc);
									}
								}

								$test_content_type = false ;
								$test_content_transfer = false ;
								$test_content_dispo = false ;
								$encoding = false ;
								$content = "" ;
								$body = false ;
								$name_attachement = "" ;
							}
						}
					}
				}
			}
		}

		$mail->save();
		dims_print_r($mail->fields);
		if (file_exists($fileindex)) {
			//unlink($fileindex);
		}
		echo "Import completed succefully";

		//dims_print_r($mail);die();
	}
}
die();
?>
