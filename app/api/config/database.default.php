<?php
// Database information
$settings = array(
	'driver' 	=> 'mysql',
	'host' 		=> '127.0.0.1',
	'database' 	=> 'caahmromobile',
	'username'	=> 'root',
	'password' 	=> 'root',
	'charset'	=> "utf8",
	'collation' => 'utf8_general_ci',
	'prefix' 	=> ''
);

// Bootstrap Eloquent ORM
$connFactory = new \Illuminate\Database\Connectors\ConnectionFactory(new Illuminate\Container\Container);
$conn = $connFactory->make($settings);
$resolver = new \Illuminate\Database\ConnectionResolver();
$resolver->addConnection('default', $conn);
$resolver->setDefaultConnection('default');
\Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);

