<?php
class BaseController{
	protected $view;
	protected $app;
	protected $request;

	public function __construct(\Slim\Http\Request $r, \MFWK\lib\View $v){
		$this->view = $v;
		$this->app = $this->view->getApp();
		$this->request = $r;
	}
}
