<?php
class APIController extends BaseController{
	public function __construct(\Slim\Slim $app){
		$this->view = null;
		$this->app = $app;
		$this->request = $app->request;
	}
}
