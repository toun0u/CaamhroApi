<?php

// TODO : Static method to build an dims_urlBuilder from a given url... Could be nice :o)

class dims_urlBuilder {
	private $protocol;
	private $domain;
	private $port;
	private $script;
	private $scriptPath;
	private $params;

	public function __construct($script = 'index.php', $scriptPath = '/', $domaine = '', $port = '80', $protocol = 'http://') {
		$this->protocol 	= $protocol;
		$this->domain 		= (!empty($domaine)) ? $domaine : $_SERVER['HTTP_HOST'];
		$this->port 		= $port;
		$this->script 		= $script;
		$this->scriptPath 	= $scriptPath;
		$this->params 		= array();
	}

	public function getUrl() {
		return $this->protocol
				.$this->domain
				.$this->getPort()
				.$this->scriptPath
				.$this->script
				.$this->getParamsAsString();
	}

	public function __toString() {
		return $this->getUrl();
	}

	public function getParamsAsString() {
		if(empty($this->params))
			return '';
		else {
			return '?'.http_build_query($this->params);
		}
	}

	public function getPort() {
		return (($this->port == '80' && ($this->protocol == 'http://' || $this->protocol == '')) || ($this->port == '443' && $this->protocol == 'https://')) ? '': ':'.$this->port;
	}

	public function getParam($name) {
		return $this->param[$name];
	}

	public function addParam($name, $value) {
		$this->params[$name] = $value;
		return $this;
	}

	public function addParams($params) {
		$this->params = array_merge($this->params, $params);
		return $this;
	}

	public function setPort($port) {
		$this->port = $port;
		return $this;
	}

	public function setProtocol($protocol) {
		$this->protocol = $protocol;
		return $this;
	}
}
