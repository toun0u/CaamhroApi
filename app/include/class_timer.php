<?
class timer {
	var $start;

	function __construct(){
            $this->start = 0;
	}

	public function start() {
            $this->start = $this->getmicrotime();
	}

	public function getmicrotime() {
            list($usec, $sec) = explode(" ",microtime());

            return ((float)$usec + (float)$sec);
	}

	public function getexectime() {
            return($this->getmicrotime() - $this->start);
	}

}
?>
