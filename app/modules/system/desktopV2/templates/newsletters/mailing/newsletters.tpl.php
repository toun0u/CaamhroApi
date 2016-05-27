<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$opt_news = '';
	$form_news = '';
	$tab_news = '';
	$params = array();
	$sql = 'SELECT		n.id,
						n.label,
						mn.id as id_link,
						mn.id_newsletter
			FROM		dims_mod_newsletter n
			LEFT JOIN	dims_mod_newsletter_mailing_news mn
			ON			mn.id_newsletter = n.id
			AND			mn.id_mailing = :idmailing
			WHERE		n.id_workspace in ('.$db->getParamsFromArray(explode(',', $listworkspace_nl ), 'idworkspace', $params).')
			ORDER BY	n.timestp_create DESC';
	$params[':idmailing'] = array('type' => PDO::PARAM_INT, 'value' => $id_mail);

	$res = $db->query($sql, $params);

	if($db->numrows($res) > 0) {
		//construction du tableau de donnees
		while($tab_res = $db->fetchrow($res)) {
			$tab_news[$tab_res['id']] = $tab_res;
		}

		//mise en forme des donnees
		$view_news = '';
		$class = 'class="trl1"';
		$tab_rattach = '';
		foreach($tab_news as $id_n => $news) {

			$opt_news .= '<option value="'.$news['id'].'">'.$news['label'].'</option>';
			//si $news['id_newsletter'] != '', c'est un rattachement
			if($news['id_link'] != '') {
				if($class == 'class="trl1"') $class = 'class="trl2"';
				else $class = 'class="trl1"';

				$tab_rattach .= '<tr '.$class.'>
									<td>'.$news['label'].'</td>
									<td><a href="admin.php?news_op='.dims_const_desktopv2::_NEWSLETTER_ACTION_SUPP_LIST.'&id_link='.$news['id_link'].'&id_mail='.$id_mail.'"><img src="./common/img/delete.png"/></a></td>
								</tr>';
				$view_news = 1;
			}
		}
		//on insere $form_news (1) uniquement si l'utilisateur a deja des newsletters
		$form_news = '<table><tr>
						<td>
							<form id="link_news" name="link_news" action="admin.php?news_op='.dims_const_desktopv2::_NEWSLETTER_SAVE_RATTACH_NEWS.'" method="POST">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("id_mail", $id_mail);
		$token->field("news_linked");
		$tokenHTML = $token->generate();
		$form_news .= $tokenHTML;
		$form_news .=		'<input type="hidden" name="id_mail" value="'.$id_mail.'"/>
							<table>
								<tr>
									<td colspan="2" style="font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_LINK_NEWSLETTER_MAILING'].'</td>
								</tr>
								<tr>
									<td>
										<select id="news_linked" name="news_linked">
											<option value="">--</option>
											'.$opt_news.'
										</select>
									</td>
									<td>';
		$form_news .=  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.link_news.submit();');
		$form_news .= '				</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>';
		if($view_news != '') {
			//on affiche le tableau des news rattachees a la liste
			$form_news .= '<tr>
						<td>
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr class="trl1">
									<td colspan="2">'.$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER_LIST_LINKEDTO'].'</td>
								</tr>
								'.$tab_rattach.'
							</table>
						</td>
					</tr></table>';
		}
	}
		echo $form_news;
?>
