<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Repository\LoginRepository;

$app->post('/login', function ($request, $response, $args) {
	$data = json_decode($request->getBody());
	$user = JsonUtils::getJsonProperty($data, 'user');
	$password = JsonUtils::getJsonProperty($data, 'password');
	$repository = new LoginRepository();
	$result = $repository->authorize($user, $password);
	return $this->response->withJson($result);
})->setName('loginapi');

$app->get('/logout', function ($request, $response, $args) {
	$repository = new LoginRepository();
	$result = $repository->logout();
	return $this->response->withJson($result);
})->setName('logoutapi');
