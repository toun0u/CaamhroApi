<?
switch($this->fields['type']){
	case WCE_OBJECT_TYPE_NEWS:
		$this->setLightAttribute('breves',$this->getContent(WCE_BREVE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('articles',$this->getContent(WCE_ARTICLE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('rss',$this->getLinkedFeeds());
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_articles.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_rss.tpl.php"));
		break;
	case WCE_OBJECT_TYPE_UNE:
		$this->setLightAttribute('breves',$this->getContent(WCE_BREVE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('articles',$this->getContent(WCE_ARTICLE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('rss',$this->getLinkedFeeds());
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_articles.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_rss.tpl.php"));
		break;
	case WCE_OBJECT_TYPE_NEWSLETTER:
		$this->setLightAttribute('breves',$this->getContent(WCE_BREVE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('articles',$this->getContent(WCE_ARTICLE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('rss',$this->getLinkedFeeds());
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_articles.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_rss.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_envois.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_signatures.tpl.php"));
		break;
	case WCE_OBJECT_TYPE_SONDAGE:
		$this->setLightAttribute('breves',$this->getContent(WCE_BREVE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('articles',$this->getContent(WCE_ARTICLE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_articles.tpl.php"));
		break;
	case WCE_OBJECT_TYPE_ALL_BREVES:
		$this->setLightAttribute('breves',$this->getContent(WCE_ALL_BREVE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		break;
	case WCE_OBJECT_TYPE_ALERTES:
		$this->setLightAttribute('breves',$this->getContent(WCE_ALERTE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		break;
	default:
		$this->setLightAttribute('breves',$this->getContent(WCE_BREVE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->setLightAttribute('articles',$this->getContent(WCE_ARTICLE, false, false,'', false, _WCE_OBJECT_VIEW_NO_RSS));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_breves.tpl.php"));
		$this->display(module_wce::getTemplatePath("obj_dynamics/display_object_articles.tpl.php"));
		break;
}
