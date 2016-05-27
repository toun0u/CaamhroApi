<?php
// NEEDS PEAR PACKAGE
@require_once('Cache/Lite/Output.php');

class cache {
	var $activated;
	var $output;
	var $lifetime;
	var $options;
	var $written;
	var $read;


	function cache($lifetime = dims_const::_DIMS_CACHE_DEFAULT_LIFETIME) {
		$this->activated =false;

		if (isset($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_set_cache']))
			$this->activated = $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_set_cache'] && !_DIMS_DEBUGMODE && class_exists('Cache_Lite_Output');

		if ($this->activated) {
			$this->lifetime = $lifetime;

			$this->options = array(
				'cacheDir' => DIMS_TMP_PATH,
				'lifeTime' => $this->lifetime
				);

			$this->output = new Cache_Lite_Output($this->options);

			$this->written = 0;
			$this->read = 0;
		}
	}


	function start($id, $lifetime = -1) {
		if ($this->activated) {
			if ($lifetime > -1) {
				$this->lifetime = $lifetime;

				$this->options = array(
					'cacheDir' => DIMS_TMP_PATH,
					'lifeTime' => $this->lifetime
					);

				$this->output = new Cache_Lite_Output($this->options);
			}

			$id .= "_{$_SESSION['dims']['workspaceid']}";

			// add userid for connected users
			if ($_SESSION['dims']['connected']) $id .= "_{$_SESSION['dims']['userid']}";



			$start_cache = $this->output->start($id);

			if ($start_cache) $this->read++;
			else $this->written++;

			return($start_cache);
		}
		else return(false); // no cache

	}

	function end() {
		if ($this->activated) {
			$this->output->end();
		}
	}
}

