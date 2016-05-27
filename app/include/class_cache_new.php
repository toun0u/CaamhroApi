<?
// NEEDS PEAR PACKAGE
@require_once('Cache/Lite/Output.php');

global $dims_cache_activated;
global $dims_cache_written;
global $dims_cache_read;

$dims_cache_activated = $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_set_cache'] && !_DIMS_DEBUGMODE && class_exists('Cache_Lite_Output');
$dims_cache_written = 0;
$dims_cache_read = 0;

class dims_cache  extends Cache_Lite_Output
{
	var $cache_id;

	function dims_cache($id, $lifetime = dims_const::_DIMS_CACHE_DEFAULT_LIFETIME)
	{
		global $dims_cache_activated;

		if ($dims_cache_activated)
		{
			$this->cache_id = $id;
			$this->Cache_Lite_Output(array( 'cacheDir' => DIMS_TMP_PATH, 'lifeTime' => $lifetime));
		}
	}

	function get_lastmodified()
	{
		global $dims_cache_activated;

		if ($dims_cache_activated)
		{
			$this->_setFileName($this->cache_id, 'default');
			if (file_exists($this->_file)) return($this->lastModified());
			else return(0);
		}

		return(0);
	}

	function start($force_caching = false)
	{
		global $dims_cache_activated;
		global $dims_cache_written;
		global $dims_cache_read;

		if ($dims_cache_activated)
		{
			if ($force_caching) $this->setOption('lifeTime', 0);
			$cache_content = parent::start($this->cache_id);

			if ($cache_content) $dims_cache_read++;
			else $dims_cache_written++;

			return($cache_content);
		}
		else return(false); // no cache

	}

	function end()
	{
		global $dims_cache_activated;

		if ($dims_cache_activated)
		{
			parent::end();
		}
	}
}

?>
