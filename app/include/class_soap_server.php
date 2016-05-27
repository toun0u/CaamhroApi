<?php
class dims_soap_server {

	private $instance;
	private $wsdl;
	private $class;

	public function __construct() {
		$this->instance = null;
		$this->wsdl = null;
		$this->class = null;
	}

	public function start() {
		if ($this->getWSDL()) {
			$this->instance = new SoapServer($this->getWSDL());

			if ($this->getMyClass()) {
                            $this->getMyInstance()->setClass($this->getMyClass());
			}
		}
		else {
                    throw new Exception("Error Processing Request", 1);
		}
	}

	public function listen() {
		if (!$this->getMyInstance()) {
			$this->start();
		}
		$this->getMyInstance()->handle();
	}

	// ----------------

	private function getMyInstance() {
		return $this->instance;
	}

	private function getWSDL() {
		return $this->wsdl;
	}

	private function getMyClass() {
		return $this->class;
	}


	public function setWSDL($wsdl) {
		$this->wsdl = $wsdl;
	}

	public function setMyClass($class) {
		$this->class = $class;
	}

}
