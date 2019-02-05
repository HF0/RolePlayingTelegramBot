<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->group('/admin', function () use ($app) {
	// admin main page
	$app->get('/', function (Request $request, Response $response) {
		$response = $this->view->render($response, 'index.phtml', []);
		return $response;
	})->setName('adminpage');

	$app->get('/util.html', function (Request $request, Response $response) {
		$response = $this->view->render($response, 'util.phtml', []);
		return $response;
	});
	// info command
	$app->map(['GET', 'POST'], '/infoadmin.html', function (Request $request, Response $response) {
		$response = $this->view->render($response, 'infocommand.phtml', []);
		return $response;
	});
	// character
	$app->get('/character.html', function (Request $request, Response $response) {
		$response = $this->view->render($response, 'character.phtml', []);
		return $response;
	});
	// fight group
	$app->get('/fightgroup.html', function (Request $request, Response $response) {
		$response = $this->view->render($response, 'fightgroup.phtml', []);
		return $response;
	});
	$app->get('/fightgroupdetail.html', function (Request $request, Response $response, array $args) {
		$response = $this->view->render($response, 'fightgroupdetail.phtml', []);
		return $response;
	});
	$app->get('/dev.html', function (Request $request, Response $response, array $args) {
		$response = $this->view->render($response, 'dev.phtml', []);
		return $response;
	});
	$app->get('/users.html', function (Request $request, Response $response, array $args) {
		$response = $this->view->render($response, 'users.phtml', []);
		return $response;
	});

});
