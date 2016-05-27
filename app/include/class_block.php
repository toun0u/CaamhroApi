<?
class block {
	var $menu;
	var $content;
	var $menuportal;
	var $admin;

	function block()
	{
		$this->menu = array();
		$this->content = '';
		$this->menuportal = '';
		$this->admin = false;
	}

	function addmenu($label, $url, $target = '')
	{
		$this->menu[] = array (	'label' => $label,
								'url' => $url,
								'target' => $target
								);
	}

	function addcontent($content)
	{
		$this->content = $content;
	}

	function getmenu()
	{
		return($this->menu);
	}

	function getcontent()
	{
		return($this->content);
	}

	function getadmin()
	{
		return($this->admin);
	}

	function getmenuportal()
	{
		return($this->menuportal);
	}

}
?>
