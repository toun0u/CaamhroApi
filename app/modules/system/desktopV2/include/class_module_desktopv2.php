<?php

require_once DIMS_APP_PATH.'modules/system/class_module.php';

class module_desktopv2 extends module {
	const TEMPLATE_WEB_PATH = 'modules/system/desktopV2/templates/';
	const STATIC_FILES_VERSION = 'c8cb39400e01695b48c590dc2cc41269fa6bf33e';

	public static function getTemplateWebPath($file = '') {
		$webPath = '/'.self::TEMPLATE_WEB_PATH;

		if(!empty($file)) {
			$webPath .= $file.'?'.self::get_static_version();
		}

		return $webPath;
	}

	public static function getTemplatePath($file = '') {
		return DIMS_APP_PATH.self::TEMPLATE_WEB_PATH.$file;
	}

	public static function get_static_version() {
		return self::STATIC_FILES_VERSION;
	}

	public function display($fileName) {
		return parent::display(self::getTemplatePath().'/'.$fileName);
	}

}
