<?php
use Slim\Extras\Log\DateTimeFileWriter;

use Slim\Extras\Views\Twig;

use Slim\Slim;

define('__ROOT_DIR__', __DIR__);

setlocale(LC_ALL, NULL);
setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');

$classLoader = (require 'vendor/autoload.php');
$classLoader->add('', 'classes');

$app = new Slim(array(
	'mode' => preg_match('/centraldoveiculo.com.br$/', $_SERVER['HTTP_HOST']) ? 'production' : 'development',
	'view' => new Twig
));

$app->configureMode('development', function() use($app) {
	$app->getLog()->setWriter(new \CV\Logger('erros.log'));
	$app->getLog()->setWriter(new DateTimeFileWriter(array(
			'path' => __DIR__ . '/logs',
			'name_format' => 'Y-m-d',
			'extension' => 'log',
			'message_format' => '%label% %date% %message%'			
	)));
	$app->config(array(
			'debug' => true,
			'log.enable' => true,
			'templates.path' => './templates',
			'database.host' => 'localhost',
			'database.user' => 'root',
			'database.password' => '',
			'database.dbname' => 'centraldoveicu'
	));
});

$app->configureMode('production', function() use($app) {
	$app->config(array(
		'debug' => true,
		'log.enable' => true,
		'templates.path' => './templates',
		'database.host' => 'mysql.centraldoveiculo.com.br',
		'database.user' => 'centraldoveicu',
		'database.password' => 's3nh4central',
		'database.dbname' => 'centraldoveicu'
	));
});

Twig::$twigExtensions = array(
	'Twig_Extensions_Slim',
	'Twig\Extensions\CV'
);
Twig::$twigDirectory = $app->config('templates.path');

ORM::configure('mysql:host=' . $app->config('database.host') . ';dbname=' . $app->config('database.dbname'));
ORM::configure('username', $app->config('database.user'));
ORM::configure('password', $app->config('database.password'));
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

require 'routes/portal.php';
require 'routes/painel.php';

$app->get('/', function() use($app) {
	echo 'root';
})->name('root');

$app->get('/editor', function() use($app) {
	$app->render('editor.twig');
})->name('editor');

session_start();
$app->run();