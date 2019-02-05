<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/login.html', function (Request $request, Response $response) {
	$response = $this->view->render($response, 'login.phtml', []);
	return $response;
})->setName('loginpage');
