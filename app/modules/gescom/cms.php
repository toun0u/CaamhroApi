<?php
dims_init_module('gescom');

$view = view::getInstance();
$view->set_tpl_webpath('modules/gescom/views/');
$view->setLayout('layouts/empty.tpl.php'); //déclaration du layout principal

$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, true);

switch($op){
	case 'web_ask':
		$t = dims_load_securvalue('t',dims_const::_DIMS_NUM_INPUT,true,true); // type
		$o = dims_load_securvalue('o',dims_const::_DIMS_NUM_INPUT,true,true); // famille ou article

		// type & id doivent être présents: lien vers article ou famille
		// test si l'objet est publié
		switch ($t) {
			case article::MY_GLOBALOBJECT_CODE:
				$art = article::find_by(array('id'=>$o,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(empty($art) || !$art->get('published')){
					break 2;
				}
				break;
			case cata_famille::MY_GLOBALOBJECT_CODE:
				$famile = cata_famille::find_by(array('id'=>$o,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
				if(empty($famile) || !$famile->get('published')){
					break 2;
				}
				break;
			default:
				break 2;
		}

		// test connexion
		if(!isset($_SESSION['dims']['connected']) || !$_SESSION['dims']['connected']){
			global $article;
			$urlrewrite = $article->get('urlrewrite');
			$get = array(
				'o' => $o,
				't' => $t,
			);
			if(empty($urlrewrite)){
				$get['articleid'] = $articleid;
				$_SESSION['dims']['before_connexion_url'] = Gescom\get_path($get);
			}else{
				$_SESSION['dims']['before_connexion_url'] = "/".$urlrewrite.".html?".http_build_query($get);
			}
			dims_redirect("/index.php?op=connexion");
			break;
		}

		$gform = gescom_form::find_by(array('type'=>$t,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);

		if(empty($gform)){
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}else{
			$form = forms::find_by(array('id'=>$gform->get('id_form')),null,1);
			if(empty($form)){
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			} else {
				$a = dims_load_securvalue('a',dims_const::_DIMS_CHAR_INPUT,true,true);
				switch ($a) {
					default:
					case 'display': // afficher le formulaire
						$get = array(
							'articleid' => $articleid,
							'a' => 'save',
							'o' => $o,
							't' => $t,
						);

						$view->assign('action_form_'.$form->get('id'),Gescom\get_path($get));
						$view->assign('form',$form);
						$view->assign('t',$t);
						$view->assign('o',$o);
						$view->assign('values',array());
						$view->assign('reply_id',0);
						$view->render('demandes_web/_site_display.tpl.php');
						break;
					case 'save':
						if(isset($_POST['comment']) && empty($_POST['comment'])){
							$reply = new reply();
							if($form->replyTo($reply,(isset($_POST)?$_POST:array()),(isset($_FILES)?$_FILES:array()))){
								//$reply->sendEmail(true);

								$wa = web_ask::build(array(
									'type' => $t,
									'id_object' => $o,
									'id_account' => $_SESSION['dims']['userid'],
									'id_reply' => $reply->get('id'),
									'id_form' => $form->get('id'),
									'id_param_form' => $gform->get('id'),
									'timestp_create' => dims_createtimestamp(),
									'timestp_modify' => dims_createtimestamp(),
								));
								$wa->save();
								$view->flash(nl2br($form->get('cms_response')), 'bg-success');
							}else{
								$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
							}
						} // on a affaire à un bot

						global $article;
						$urlrewrite = $article->get('urlrewrite');
						$get = array(
							'a' => 'ok',
							'o' => $o,
							't' => $t,
						);
						if(empty($urlrewrite)){
							$get['articleid'] = $articleid;
							dims_redirect(Gescom\get_path($get));
						}else{
							dims_redirect("/".$urlrewrite.".html?".http_build_query($get));
						}
						break;
					case 'ok':
						$view->assign('formresponse', $form->fields['cms_response']);
						$view->render('demandes_web/_site_validate.tpl.php');
						break;
				}
			}
		}
		break;
}
$view->compute();
