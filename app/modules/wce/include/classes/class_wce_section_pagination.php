<?php
class wce_section_pagination extends dims_data_object {
		var $sectionpagination;
		var $idelement;
		var $dims;
		var $selectedItem;
		var $maxItem;

		function __construct($idelement) {
		$sectionpagination=array();
				$this->idelement=$idelement;
				$this->dims=dims::getInstance();
	}

		public function setMaxItem($maxItem) {
			$this->maxItem=$maxItem;
		}

		public function getMaxItem() {
			return ($this->maxItem);
		}

		public function setSelectedItem($selectedItem) {
			$this->selectedItem=$selectedItem;
		}

	public function display($template='') {
			$content='';

			if ($template=='') {
				// on regarde template courant, sinon module
				// commenté par Kévin car bug "unable to find" avec le nouveau template (cette ligne repète 2 fois /var/www/ dans l'url avec realpath) :
				//$tmppath=realpath('.').str_replace("./","/",$_SESSION['dims']['front_template_path']).'/section_pagination.tpl.php';
				$tmppath= str_replace("./","/",$_SESSION['dims']['front_template_path']).'/section_pagination.tpl.php';

				if (file_exists($tmppath)) {
					$template=$tmppath;
				}
				else {
					$tmppath=DIMS_APP_PATH.'modules/wce/wce/common/section_pagination.tpl.php';
					if (file_exists($tmppath)) {
						$template=$tmppath;
					}
				}
			}

			if (file_exists($template)) {
				ob_start();
				include $template;
				$content = ob_get_contents();
				ob_end_clean();
			}
			else {
				echo 'Unable to find '.$template;
			}

			return $content;
	}
}
?>
