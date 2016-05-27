<?php
session_cache_limiter(false);
session_start();//POUR PERMETTRE LES $_SESSION['dims']['workspaceid']
require_once '../vendor/autoload.php';// Autoload our dependencies with Composer
require_once '../config/config.php';// config APP

require_once(APP_PATH . "/lib/BaseController.php");
require_once(APP_PATH . "/lib/APIController.php");

include_dir(APP_PATH . '/controllers');
include_dir(APP_PATH . '/helpers');
$app = new \Slim\Slim(array(
	'mode' => (_DIMS_DEBUGMODE?'development':'production'),
	'debug' => _DIMS_DEBUGMODE,
	'log.enabled' => false,
	'http.version' => '1.1',
));
//print_r($_SERVER);
//die();
$app->hook('slim.before', function() use ($app) {
	//print_r($app->request);
	if(strtolower($app->request->getMethod())=="options"){
		$app->halt(200, json_encode(array('status'=>'success')));
	}
	$app->contentType('application/json');//pour dire qu'on ne renvoie que du Json
	auth_client($app);
	validCustomerAuth($app, $id = false, ($app->request->get('Token') ? $app->request->get('Token') : $app->request->headers('Token')), $emptyId = true, $sendHalt = true);
}, 1);

\Slim\Route::setDefaultConditions(array(
	'id' => '[0-9]{1,}',
	'lastupdate' => '[0-9]{14}'
));

// System
$app->group('/sys',function() use ($app){
	$app->get('/getversion', function () use ($app) {
		$c = new SysController($app);
		$c->getVersion();
	});
	$app->get('/test(/:param)', function ($param=null) use ($app) {
		$c = new SysController($app);
		$c->test($param);
	});	
});

// Famille
$app->group('/families',function() use ($app){
	$app->get('(/:lastupdate)', function ($lastupdate = null) use ($app) {
		$c = new FamiliesController($app);
		$c->getAllFamily($lastupdate);
	});
});

// Article [check]
$app->group('/articles',function() use ($app){
	$app->get('(/:lastupdate)', function ($lastupdate = null) use ($app) { 
		$c = new ArticlesController($app);
		$c->getAllArticles($lastupdate);
	});

	$app->get('/getfamillearticle', function () use ($app) {
		$c = new ArticlesController($app);
		$c->getArticleFamily();
	});
});

//Clients --modif base de donnée à faire--
$app->group('/clients',function() use ($app){
	$app->get('', function () use ($app) {
		$c = new ClientsController($app);
		$c->getClients();
	});
	$app->get('/update/:lastupdate', function ($lastupdate) use ($app) {
		$c = new ClientsController($app);
		$c->updateClients($lastupdate);
	});
});

// Tarifs
$app->group('/tarifs',function() use ($app){ //récupère les tarifs quantité et les prix nets. Fonctionne mais tests limités du fait des capacité de ma machine
	$app->get('', function () use ($app) {
		$c = new TarifsController($app);
		$c->getAllTarifs();
	});
	$app->get('/tarifsqte(/:lastupdate)', function ($lastupdate = null) use ($app) {
		$c = new TarifsController($app);
		$c->getTarQte($lastupdate);
	});
	$app->get('/prixnets', function () use ($app) {
		$c = new TarifsController($app);
		$c->getPrixNets();
	});
	$app->get('/updateprixnets/:lastupdate', function ($lastupdate) use ($app) { 
		$c = new TarifsController($app);
		$c->updatePrixNets($lastupdate);
	});
});

// Utilisateurs
$app->group('/user',function() use ($app){
	$app->post('', function () use ($app) {
		$c = new UserController($app);
		$c->authUser(); 
	});
	$app->delete('/unauth', function () use ($app) {
		$c = new UserController($app);
		$c->unauthUser(); 
	});
});

// Commandes
$app->group('/commandes',function() use ($app){
	$app->get('(/:lastupdate)', function ($lastupdate = null) use ($app) {
		$c = new CommandesController($app);
		$c->getAllCde($lastupdate);
	});
	$app->get('/getcde', function () use ($app) {
		$c = new CommandesController($app);
		$c->getCde(); 
	});
	$app->get('/getcdecontent', function () use ($app) {
		$c = new CommandesController($app);
		$c->getCdeContent();
		//l'update du contenu des commandes se faire via /commandes(/:lastupdate); 
	});
});

// Facture
$app->group('/facture',function() use ($app){ 
	$app->get('', function () use ($app) { //on laisse cette route, mais reste à voir si on l'utilise
		$c = new FactureController($app);
		$c->getAllFacture(); // getFacture et getFactureDet sont trop gourmandes pour les appeler en une fois sur ma machine
	});
	$app->get('/getfacture(/:lastupdate)', function ($lastupdate = null) use ($app) {
		$c = new FactureController($app);
		$c->getFacture($lastupdate); 
	});
	$app->get('/getfacturedet(/:lastupdate)', function ($lastupdate = null) use ($app) { 
		$c = new FactureController($app);
		$c->getFactureDet($lastupdate); 
	});
});

//update
$app->group('/update',function() use ($app){
	$app->get('/deleted/:lastupdate', function ($lastupdate) use ($app) {
		$c = new UpdateController($app);
		$c->getDeleted($lastupdate);
	});	
	$app->get('/updatedrow/:lastupdate', function ($lastupdate) use ($app){
		$c = new UpdateController($app);
		$c->getUpdatedRows($lastupdate);
	});
});

// 404
$app->notFound(function () use ($app){
	$app->halt(404,json_encode(array('status' => array('statusCode' => 404, 'statusMessage' => 'Not found'))));
});

$app->run();
