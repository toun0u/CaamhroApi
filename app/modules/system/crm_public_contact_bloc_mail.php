<?php

	if(!empty($ct)) {
		echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_EMAIL'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_email.png','26px', '26px', '-17px', '-5px', $tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct, '', '');

		/*$sql = 'SELECT
					email.*,
					COUNT(mail_doc.id) AS nb_mailDoc
				FROM
					dims_user user
				LEFT JOIN
					dims_mod_business_contact_mail ct_mail
					ON
						ct_mail.id_user = user.id
				INNER JOIN
					dims_mod_webmail_email email
					ON
						email.id = ct_mail.id_email
				LEFT JOIN
					dims_mod_webmail_email_docfile mail_doc
					ON
						mail_doc.id_email = email.id
				WHERE
					user.id_contact = '.$contact_id.'
				GROUP BY
					mail_doc.id_email
				ORDER BY
					email.date DESC
				LIMIT 3;';*/

		$sql = '' ;
		if ($_SESSION['dims']['user']['id_contact'] == $contact_id){
		    $sql = "SELECT 	COUNT(a.id_mail) AS nb_mail
			    FROM 	dims_mod_webmail_email_adresse a
			    INNER JOIN 	dims_mod_webmail_email_link lk
				ON 	lk.id_mail = a.id
				AND 	lk.id_contact = :idcontact
			    INNER JOIN	dims_mod_webmail_email e
				ON	e.id = a.id_mail
				AND	e.read = 0
			    WHERE	(a.type = 2 OR a.type = 3)";
		}else{
		    $sql = "SELECT 	COUNT(a.id_mail) AS nb_mail
			    FROM 	dims_mod_webmail_email_adresse a
			    INNER JOIN 	dims_mod_webmail_email_link lk
				ON 	lk.id_mail = a.id
				AND 	lk.id_contact = :idcontact
			    INNER JOIN	dims_mod_webmail_email e
				ON	e.id = a.id_mail
				AND	e.read = 0
			    WHERE	a.type = 1";
		}


		$ress = $db->query($sql, array(
			':idcontact' => $contact_id
		));

		echo '<table width="100%" cellspacing="0" cellpadding="1">';

		if($db->numrows($ress) > 0) {
			/*$class = 'trl1';
			while($result = $db->fetchrow($ress)) {

				echo '<tr class="'.$class.'">';
				echo '<td width="75%">';
				if(strlen($result['subject']) > 25)
					echo substr($result['subject'],0,25).'[...]';
				else
					echo $result['subject'];
				echo '</td>';
				echo '<td width="15%">';
				$dateLocal = dims_timestamp2local($result['date']);
				echo $dateLocal['date'];
				echo '</td>';
				echo '<td width="10%">';
				if($result['nb_mailDoc'] > 0)
					echo '<img src="./common/img/attachment.png" alt="Fichier(s) attaché(s)" title="Fichier(s) attaché(s)" />';
				echo '</td>';
				echo '</tr>';

				$class = ($class == 'trl1') ? 'trl2' : 'trl1';
			}*/
			$result = $db->fetchrow($ress) ;

			// nombre de nouveaux messages
			echo '<tr><td>';
			//echo '<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct.'">';
			echo $_DIMS['cste']['_DIMS_LABEL_NEW_MAIL_RECEIVED']." : ".$result['nb_mail'];
			//echo '</a>';
			echo '</td></tr>';

			if ($result['nb_mail'] > 0){
				if ($_SESSION['dims']['user']['id_contact'] == $contact_id){
					$sql = "SELECT 	e.id, e.subject
						FROM 	dims_mod_webmail_email_adresse a
						INNER JOIN 	dims_mod_webmail_email_link lk
						    ON 	lk.id_mail = a.id
						    AND 	lk.id_contact = :idcontact
						INNER JOIN	dims_mod_webmail_email e
						    ON	e.id = a.id_mail
						    AND	e.read = 0
						WHERE	(a.type = 2 OR a.type = 3)
						LIMIT	3";
				}else{
					$sql = "SELECT 	e.id, e.subject
						FROM 	dims_mod_webmail_email_adresse a
						INNER JOIN 	dims_mod_webmail_email_link lk
						    ON 	lk.id_mail = a.id
						    AND 	lk.id_contact = :idcontact
						INNER JOIN	dims_mod_webmail_email e
						    ON	e.id = a.id_mail
						    AND	e.read = 0
						WHERE	a.type = 1
						LIMIT	3";
				}
				// affichage des 3 derniers messages non lus
				$res = $db->query($sql, array(
					':idcontact' => $contact_id
				));
				while ($mail = $db->fetchrow($res)){
					echo '<tr>';
					echo 	'<td>';
					echo 		'<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct.'&subaction='._DIMS_MENU_MAIL_RECEIVED.'&mail_id='.$mail['id'].'">';
					if(strlen($mail['subject']) > 25)
						echo substr($mail['subject'],0,25).'[...]';
					else
						echo $mail['subject'];
					echo		'</a>';
					echo 	'</td>';
					echo '</tr>';
				}

			}

			$sql = '' ;
			if ($_SESSION['dims']['user']['id_contact'] == $contact_id){
			    $sql = "SELECT 	COUNT(a.id_mail) AS nb_mail
				    FROM 	dims_mod_webmail_email_adresse a
				    INNER JOIN 	dims_mod_webmail_email_link lk
					ON 	lk.id_mail = a.id
					AND 	lk.id_contact = :idcontact
				    INNER JOIN	dims_mod_webmail_email e
					ON	e.id = a.id_mail
				    WHERE	(a.type = 2 OR a.type = 3)";
			}else{
			    $sql = "SELECT 	COUNT(a.id_mail) AS nb_mail
				    FROM 	dims_mod_webmail_email_adresse a
				    INNER JOIN 	dims_mod_webmail_email_link lk
					ON 	lk.id_mail = a.id
					AND 	lk.id_contact = :idcontact
				    INNER JOIN	dims_mod_webmail_email e
					ON	e.id = a.id_mail
				    WHERE	a.type = 1";
			}

			// nombre de messages reçus
			$ress = $db->query($sql, array(
				':idcontact' => $contact_id
			));
			if($db->numrows($ress) > 0) {
				$result = $db->fetchrow($ress) ;
				if ($result['nb_mail'] > 0){
					echo '<tr><td>';
					//echo '<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct.'">';
					echo $_DIMS['cste']['_DIMS_LABEL_MAIL_RECEIVED']." : ".$result['nb_mail'];
					//echo '</a>';
					echo '</td></tr>';
				}

				$sql = '' ;
				if ($_SESSION['dims']['user']['id_contact'] == $contact_id){
				    $sql = "SELECT 	COUNT(a.id_mail) AS nb_mail
					    FROM 	dims_mod_webmail_email_adresse a
					    INNER JOIN 	dims_mod_webmail_email_link lk
						ON 	lk.id_mail = a.id
						AND 	lk.id_contact = :idcontact
					    INNER JOIN	dims_mod_webmail_email e
						ON	e.id = a.id_mail
					    WHERE	a.type = 1";
				}else{
				    $sql = "SELECT 	COUNT(a.id_mail) AS nb_mail
					    FROM 	dims_mod_webmail_email_adresse a
					    INNER JOIN 	dims_mod_webmail_email_link lk
						ON 	lk.id_mail = a.id
						AND 	lk.id_contact = :idcontact
					    INNER JOIN	dims_mod_webmail_email e
						ON	e.id = a.id_mail
					    WHERE	(a.type = 2 OR a.type = 3)";
				}

				// nombre de messages envoyés
				$ress = $db->query($sql, array(
					':idcontact' => $contact_id
				));
				if($db->numrows($ress) > 0) {
					$result = $db->fetchrow($ress) ;
					if ($result['nb_mail'] > 0){
						echo '<tr><td>';
						//echo '<a href="'.$tabscriptenv."&action="._BUSINESS_TAB_CONTACT_MAIL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct.'">';
						echo $_DIMS['cste']['_FAQ_SEND_MESSAGE']." : ".$result['nb_mail'];
						//echo '</a>';
						echo '</td></tr>';
					}
				}else{
				echo '<tr><td>';
				echo $_DIMS['cste']['_DIMS_LABEL_MAIL_NONE'];
				echo '</td></tr>';
			}


			}else{
				echo '<tr><td>';
				echo $_DIMS['cste']['_DIMS_LABEL_MAIL_NONE'];
				echo '</td></tr>';
			}
		}
		else {
			echo '<tr><td>';
			echo $_DIMS['cste']['_DIMS_LABEL_MAIL_NONE'];
			echo '</td></tr>';
		}

		echo '</table>';

		echo $skin->close_widgetbloc();
	}

?>
