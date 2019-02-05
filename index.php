<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use RolBot\Middleware\SecurityMiddleware;
use RolBot\Config\Db;
use RolBot\Config\Configuration;

require 'vendor/autoload.php';

session_start();

$host = Configuration::get('host');
$dbName = Configuration::get('db_name');
$user = Configuration::get('user');
$password = Configuration::get('password');
Db::setup($host, $dbName, $user, $password);

$config = array();
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['templateDir'] = __DIR__ . '/src/templates/';
$config['determineRouteBeforeAppMiddleware'] = true;

$app = new \Slim\App(['settings' => $config]);
$securityMiddleware = new SecurityMiddleware();
$securityMiddleware->configure($app);

// dependencies
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer($config['templateDir']);
$container['logger'] = function ($container) {
    $logger = new Logger('logger');
    $fileHandler = new \Monolog\Handler\StreamHandler('./logs/botlog.txt');
    $logger->pushHandler($fileHandler);
    return $logger;
};

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("");
    return $response;
})->setName('root');

require './src/routes/api/telegramHook.php';

// frontend
require './src/routes/frontend/admin.php';
require './src/routes/frontend/login.php';

$app->group('/api', function () use ($app) {
    require './src/routes/api/login.php';
    require './src/routes/api/dice.php';
    require './src/routes/api/character.php';
    require './src/routes/api/telegramuser.php';
    require './src/routes/api/fightgroup.php';
    require './src/routes/api/info.php';
    require './src/routes/api/dev.php';
});

$app->run();
