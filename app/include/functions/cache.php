<?

function dims_cache_start($id, $lifetime = dims_const::_DIMS_CACHE_DEFAULT_LIFETIME)
{

	if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_set_cache'])
	{
		global $dims_cache_output;
		require_once('Cache/Lite/Output.php');

		$id .= "_{$_SESSION['dims']['workspaceid']}";

		// add userid for connected users
		if ($_SESSION['dims']['connected']) $id .= "_{$_SESSION['dims']['userid']}";

		$options = array(
			'cacheDir' => DIMS_TMP_PATH,
			'lifeTime' => $lifetime
			);

		$dims_cache_output = new Cache_Lite_Output($options);

		return($dims_cache_output->start($id));
	}
	else return(false); // no cache

}

function dims_cache_end()
{
	if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_set_cache'])
	{
		global $dims_cache_output;
		$dims_cache_output->end();
	}
}

?>
