<?php
require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
require_once(DIMS_APP_PATH . '/include/functions/image.php');

switch($dims_op) {
	case  'events_xsd':
		header("Content-type: text/xml");
		echo '<?xml version="1.0"?>
				<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">

				<xs:element name="event">
				  <xs:complexType>
					<xs:sequence>
					  <xs:element name="id" type="xs:numeric"/>
					  <xs:element name="title" type="xs:string"/>
					  <xs:element name="description" type="xs:string"/>
					  <xs:element name="typeaction" type="xs:string"/>
					  <xs:element name="date" type="xs:date"/>
					  <xs:element name="date_end" type="xs:date"/>
					  <xs:element name="hour_begin" type="xs:string"/>
					  <xs:element name="hour_end" type="xs:string"/>
					  <xs:element name="target" type="xs:string"/>
					  <xs:element name="teaser" type="xs:string"/>
					  <xs:element name="place" type="xs:string"/>
					  <xs:element name="price" type="xs:string"/>
					  <xs:element name="conditions" type="xs:string"/>
					  <xs:element name="lastname" type="xs:string"/>
					  <xs:element name="firstname" type="xs:string"/>
					  <xs:element name="url" type="xs:string"/>
					</xs:sequence>
				  </xs:complexType>
				</xs:element>

				</xs:schema>';
				die();

	break;

	case 'events':
	default:

		$id_module = 1;
		$id_object = dims_const::_SYSTEM_OBJECT_EVENT;
		$id_object_part=dims_const::_SYSTEM_OBJECT_EVENT_PARTNERS;

		// on va r�cup�rer la liste des events rattach�s
		$workspace_code=dims_load_securvalue('workspace_code', dims_const::_DIMS_CHAR_INPUT, true);
		$front=$dims->getWebWorkspaces();
		$id_workspace=0;

		// v�rification de l'existence du code
		foreach($front as $id=>$worksp) {
			if ($worksp['code']==$workspace_code) {
				$id_workspace=$worksp['id'];
			}
		}

		if ($id_workspace==0) {
			$back=$dims->getAdminWorkspaces();
			foreach($back as $id=>$worksp) {
				if ($worksp['code']==$workspace_code) {
					$id_workspace=$worksp['id'];
				}
			}
		}

		$picture_extension = array('jpeg'=>'jpeg', 'jpg'=>'jpg', 'gif'=>'gif', 'png'=>'png', 'bmp'=>'bmp');
		// construction de la liste des events
		$events=array();
		$galery = array();
		$partners=array();

		// desactivation avant correction : 16/03/2010
		//$id_workspace=0;
		$params = array();
		if ($id_workspace==0) {
		 $sql = 'SELECT		distinct a.id AS id_evt
				FROM		dims_mod_business_action a
				 WHERE		a.type = :type

				AND			a.supportrelease != 0
				AND			a.id_parent = 0
				AND			a.timestamp_release <= '.date('Ymd000000');
				$params[':type'] = dims_const::_PLANNING_ACTION_EVT;
		}
		else {
			$sql = 'SELECT	   distinct a.id AS id_evt
				FROM		dims_mod_business_action a
				 WHERE		a.type = :type
				AND			a.id_workspace= :idworkspace
				AND			a.supportrelease != 0
				AND			a.id_parent = 0
				AND			a.timestamp_release <= '.date('Ymd000000');
				$params[':idworkspace'] = $id_workspace;
				$params[':type'] = dims_const::_PLANNING_ACTION_EVT;
		}

		$res = $db->query($sql, $params);

		if($db->numrows($res)) {
			while ($f=$db->fetchrow($res)) {
				$events[$f['id_evt']]=$f['id_evt'];
			}
		}

		// selection des documents galeries
		if (!empty($events)) {
			$params = array();
			$sql = 'SELECT		doc.id,
								doc.id_record,
								doc.name,
								doc.description,
								doc.extension,
								doc.id_module,
								doc.timestp_create,
								doc.version
					FROM		dims_mod_doc_file as doc
					WHERE		doc.id_module= :idmodule
					AND			doc.id_object= :idobject
					AND			doc.id_record in ('.$db->getParamsFromArray($events, 'idworkspace', $params).')
					ORDER BY	id_record,id,version';
			$params[':idobject'] = $id_object;
			$params[':idmodule'] = $id_module;

			$res = $db->query($sql, $params);

			if($db->numrows($res)) {
				while ($f=$db->fetchrow($res)) {
					$galery[$f['id_record']][$f['name']]=$f;
				}
			}
		}

		// selection des documents partenaires
		if (!empty($events)) {
			$params = array();
			$sql = 'SELECT		doc.id,
								doc.id_record,
								doc.name,
								doc.description,
								doc.extension,
								doc.id_module,
								doc.timestp_create,
								doc.version
					FROM		dims_mod_doc_file as doc
					WHERE		doc.id_module= :idmodule
					AND			doc.id_object= :idobject
					AND			doc.id_record in ('.$db->getParamsFromArray($events, 'idworkspace', $params).')
					ORDER BY	id_record,id,version';
			$params[':idobject'] = $id_object_part;
			$params[':idmodule'] = $id_module;

			$res = $db->query($sql, $params);

			if($db->numrows($res)) {
				while ($f=$db->fetchrow($res)) {
					$partners[$f['id_record']][$f['name']]=$f;
				}
			}
		}

		$params = array();
		$sql = 'SELECT
					a.id AS id_evt,
					a.libelle,
					a.description,
					a.typeaction,
					a.datejour,
					a.datefin,
					a.heuredeb,
					a.heurefin,
					a.timestp_modify,
					a.target,
					a.teaser,
					a.lieu,
					a.prix,
					a.conditions,
					a.close,
					a.banner_path,
					a.matchmaking_path,
					a.preview_path,
					a.allow_fo,
					a.display_hp,
					u.id AS id_user,
					u.lastname,
					u.firstname,
					u.id_contact
				FROM
					dims_mod_business_action a
				LEFT JOIN
					dims_user u
					ON
						u.id = a.id_user
				WHERE
					a.type = :type
					and a.typeaction != :typeaction
				/*AND
					a.allow_fo = 1
				AND
					a.close = 0*/
				AND
					a.supportrelease != 0
				/*AND
					a.datejour > CURDATE()*/
				AND
					a.id_parent = 0
				AND
					a.timestamp_release <= '.date('Ymd000000').'
				';
		$params[':type'] = dims_const::_PLANNING_ACTION_EVT;
		$params[':typeaction'] = _DIMS_PLANNING_FAIR_STEPS;

		if ($id_workspace>0) {
			$sql.=" AND			a.id_workspace= :idworkspace ";
			$params[':idworkspace'] = $id_workspace;
		}

		$ressource = $db->query($sql, $params);

		if($db->numrows($ressource)) {
			$tab_evt = array();

			while($result = $db->fetchrow($ressource)) {
				$tab_info = array();

				//on remplace les caracteres cassant le flux
				$result['libelle'] = "<![CDATA[".$result['libelle']."]]>";
				$result['lieu'] = "<![CDATA[".$result['lieu']."]]>";
				$result['teaser'] = "<![CDATA[".$result['teaser']."]]>";

				// on reconstruit l'URL complete sur chaque lien
				//on construit la chaine de remplacement
				$replacing_url = $dims->getUrlPath();
				$pat = '@^(?:http[s]?://)?[^/]+@i';
				preg_match($pat, $replacing_url, $matches);

				$replace = 'a href="'.$matches[0].'/';

				//on insere la chaine de remplacement � l'endroit d�sir�
				$pattern = '/a href="\.\//';

				$result['description'] = preg_replace($pattern, $replace, $result['description']);

				//idem pour les images eventuellement ins�r�es dans le descriptif
				$pattern = './common/img src="\.\//';
				$replace = 'img src=""'.$matches[0].'/';

				$result['description'] = preg_replace($pattern, $replace, $result['description']);

				if(!isset($tab_info['id'])) {
					$tab_info['id']			= $result['id_evt'];
					$tab_info['title']		= $result['libelle'];
					//$tab_info['type']		= '<![CDATA['.$_DIMS['cste'][$result['typeaction']].']]>';
					$tab_info['type']		= $result['typeaction']; //on envoie la global pour qu'il n'y ait pas d'erreur en fonction de la langue.
					$tab_info['date']		= $result['datejour'];
					$tab_info['date_end']	= $result['datefin'];
					$tab_info['hour_begin']	= $result['heuredeb'];
					$tab_info['hour_end']	= $result['heurefin'];
					$tab_info['target']		= '<![CDATA['.$result['target'].']]>';
					$tab_info['summary']	= (!empty($result['teaser'])) ? $result['teaser'] : '<![CDATA['.strip_tags($result['description']).']]>';
					$tab_info['body']		= '<![CDATA['.$result['description'].']]>';
					$tab_info['place']		= $result['lieu'];
					//$tab_info['price']	= $result['prix'];
					$tab_info['conditions'] = '<![CDATA['.$result['conditions'].']]>';
					$tab_info['lastname']	= '<![CDATA['.$result['lastname'].']]>';
					$tab_info['firstname']	= '<![CDATA['.$result['firstname'].']]>';
					$tab_info['closed']		= ($result['close']) ? 'true' : 'false';
					$tab_info['display_hp']	= $result['display_hp'];
					//$tab_info['banner']	= $result['banner_path'];

					if ($result['matchmaking_path']!="" && file_exists(realpath("./").str_replace("./","/",$result['matchmaking_path']))) {
						$webmatchpath='http://'.$http_host.substr($result['matchmaking_path'],1);
						$tab_info['matchmaking']= $webmatchpath;
					}

					$link=array();
					$link['name']=$result['libelle'];
					if($result['allow_fo'] == 1 && $tab_info['closed']=='false') {
						$link['link']=dims_urlencode('http://'.$http_host.'/index.php?id_event='.$result['id_evt'].'&action=form_niv1',true);
					}
					else {
						$link['link'] = '';
					}

					$tab_info['urls'][] = array("url"=> $link);

					// construction de la banner
					if ($result['banner_path']!="" && file_exists(realpath("./").str_replace("./","/",$result['banner_path']))) {
						$result['banner_path']= realpath("./").str_replace("./","/",$result['banner_path']);

						$extension=substr(strrchr($result['banner_path'], "."),1);
						$pathtemp=realpath("./")."/data/event_file/banner_".$result['id_evt'].".".$extension;

						if (!file_exists($pathtemp)) {
							dims_resizeimage($result['banner_path'], 0, 0, 0,'',0,$pathtemp,620,190);
						}
						$webbannerpath='http://'.$http_host.'/'._DIMS_WEBPATHDATA.'event_file/banner_'.$result['id_evt'].'.'.$extension;
						$tab_info['banner'] = $webbannerpath;

					}
					// construction de la galerie
					if (isset($galery[$result['id_evt']])) {
						foreach ($galery[$result['id_evt']] as $pict) {
							if (isset($picture_extension[$pict['extension']])) {
								$lk_booklet=array();
								$lk_booklet['name'] = $pict['name'];
								$lk_booklet['description'] = $pict['description'];
								$path=realpath("./").'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'.'.$pict['extension'];
								$pathtemp=realpath("./").'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'_gal.'.$pict['extension'];
								$pathtempbig=realpath("./").'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'_biggal.'.$pict['extension'];
								// generation des images pour galeries en 140x90, reste a faire les partenaires
								if (file_exists($path)) {
									if (!file_exists($pathtemp)) {
										//echo $path."<br>".$pathtemp;
										dims_resizeimage($path, 0, 0, 0,'',0,$pathtemp,140,90);
									}
									if (!file_exists($pathtempbig)) {
										//echo $path."<br>".$pathtemp;
										dims_resizeimage($path, 0, 0, 0,'',0,$pathtempbig,600,500);
									}
									$lk_booklet['link_mini'] = 'http://'.$http_host.'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'_gal.'.$pict['extension'];

									$lk_booklet['link'] = 'http://'.$http_host.'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'_biggal.'.$pict['extension'];
									$tab_info['galery'][] = array("image"=> $lk_booklet);
								}
							}
							else {

								//creation du booklet
								$lk_booklet=array();
								$lk_booklet['name'] = $pict['name'];
								$lk_booklet['description'] = $pict['description'];
								$lk_booklet['link'] = 'http://'.$http_host.'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'.'.$pict['extension'];

								// construction de la preview du document d'annonce (booklet)
								if ($result['preview_path']!="" && file_exists(realpath("./").str_replace("./","/",$result['preview_path']))) {
									$result['preview_path']= realpath("./").str_replace("./","/",$result['preview_path']);

									$extension=substr(strrchr($result['preview_path'], "."),1);
									$pathtemp=realpath("./")."/data/event_file/preview_".$result['id_evt'].".".$extension;

									if (!file_exists($pathtemp)) {
										dims_resizeimage($result['preview_path'], 0, 0, 0,'',0,$pathtemp,70,100);
									}
									$webpreviewpath='http://'.$http_host.'/'._DIMS_WEBPATHDATA.'event_file/preview_'.$result['id_evt'].'.'.$extension;
									$lk_booklet['link_mini'] = $webpreviewpath;

								}

								$tab_info['booklets'][] = array("booklet"=> $lk_booklet);
							}
						}
					}

					// construction des partenaires
					if (isset($partners[$result['id_evt']])) {
						foreach ($partners[$result['id_evt']] as $pict) {
							$partner=array();
							$partner['name'] = $pict['name'];
							$partner['description'] = $pict['description'];

							$path=realpath("./").'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'.'.$pict['extension'];
							$pathtemp=realpath("./").'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'_160.'.$pict['extension'];
							// generation des images pour galeries en max 160 de largeur pour les partenaires
							if (file_exists($path)) {
								if (!file_exists($pathtemp)) {
									echo $path."<br>".$pathtemp;
									dims_resizeimage($path, 0, 160, 0,'',0,$pathtemp);
									$partner['link'] = 'http://'.$http_host.'/'._DIMS_WEBPATHDATA.'doc-'.$pict['id_module']._DIMS_SEP.substr($pict['timestp_create'],0,8)._DIMS_SEP.$pict['id'].'_'.$pict['version'].'_160.'.$pict['extension'];
								}
							}
							$tab_info['partners'][] = array("partner"=> $partner);
						}
					}
				}

				$tab_evt[] = $tab_info;
			}
   //dims_print_r($tab_evt);die();
			ob_end_clean();
			header("Content-type: text/xml");
			echo '<?xml version="1.0" encoding="UTF-8"?>';

			if (sizeof($tab_evt)>0) {
				echo '<events>';
				// on construit la balise de tableaux
				foreach($tab_evt as $k => $event) {
					echo '<event>';

					foreach($event as $k => $elem) {
						echo '<'.$k.'>';

						if (is_array($elem)) {
								foreach($elem as $ki =>$elemi) {
										if (is_array($elemi)) {

												foreach($elemi as $kj =>$elemj) {
														echo '<'.$kj.'>';
														foreach($elemj as $kk => $elemk) {
																echo '<'.$kk.'>';
																echo utf8_encode($elemk);
																echo '</'.$kk.'>';
														}
														echo '</'.$kj.'>';
												}

										}
								}
						}
						else {
								echo utf8_encode($elem);
						}

						echo '</'.$k.'>';
					}

					echo '</event>';
				}
				echo '</events>';
			}
		}
		die();
		break;
}
?>
