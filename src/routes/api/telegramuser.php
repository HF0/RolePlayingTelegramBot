<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Repository\TelegramUserRepository;

$app->group('/user', function () use ($app) {
	$app->get('/getall', function (Request $request, Response $response) {
		$repository = new TelegramUserRepository();
		$data = $repository->getall();
		$newResponse = $response->withJson($data);
		return $newResponse;
	});
	$app->post('/add', function (Request $request, Response $response) {
		$result['ok'] = false;
		try {
			$data = json_decode($request->getBody());
			$name = JsonUtils::getJsonProperty($data, 'name');
			$description = JsonUtils::getJsonProperty($data, 'description');
			$userid = JsonUtils::getJsonProperty($data, 'userid');
			$ismaster = JsonUtils::getJsonProperty($data, 'ismaster');
			$repository = new TelegramUserRepository();
			$repository->add($userid, $name, $ismaster, $description);
			$result['ok'] = true;
		} catch (\Exception $e) {
			$result['message'] = $e->getMessage();
		}
		$newResponse = $response->withJson($result);
		return $newResponse;
	});
	$app->get('/delete/{name}', function (Request $request, Response $response, array $args) {
		$result['ok'] = false;
		try {
			$name = $args['name'];
			$repository = new TelegramUserRepository();
			$repository->delete($name);
			$result['ok'] = true;
		} catch (\Exception $e) {
			$result['message'] = $e->getMessage();
		}
		$newResponse = $response->withJson($result);
		return $newResponse;
	});
});
