<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Repository\InfoRepository;
use RolBot\Config\Configuration;

$app->group('/info', function () use ($app) {
	$app->get('/get/{name}', function (Request $request, Response $response, array $args) {
		$name = $args['name'];
		$infoRepository = new InfoRepository(Configuration::get('folderServerPrivateUploadPath'));
		$entry = $infoRepository->getByName($name);
		$newResponse = $response->withJson($entry);
		return $newResponse;
	});
	$app->get('/getAll', function (Request $request, Response $response) {
		$infoRepository = new InfoRepository(Configuration::get('folderServerPrivateUploadPath'));
		$data = $infoRepository->getInfoList();
		foreach ($data as &$entry) {
			$entry['fullPath'] = $this->get('router')->pathFor('downloadInfo', ['name' => $entry['name']]);
		}
		$newResponse = $response->withJson($data);
		return $newResponse;
	});
	$app->get('/delete/{name}', function (Request $request, Response $response, array $args) {
		$name = $args['name'];
		$infoRepository = new InfoRepository(Configuration::get('folderServerPrivateUploadPath'));
		$fileDeleted = $infoRepository->delete($name);
		$newResponse = $response->withJson($fileDeleted);
		return $newResponse;
	});

	$app->get('/show/{name}', function (Request $request, Response $response, array $args) {
		$name = $args['name'];
		$uploadFolder = Configuration::get('folderServerPrivateUploadPath');
		$infoRepository = new InfoRepository($uploadFolder);
		$info = $infoRepository->getByName($name);
		if (is_null($info)) {
			return $response->withStatus(404);
		}
		$filename = $info->file;
		$filepath = "{$uploadFolder}/{$filename}";
		$fh = fopen($filepath, 'rb');
		$stream = new \Slim\Http\Stream($fh);
		return $response
			->withHeader('Content-Type', mime_content_type($filepath))
			->withHeader('Content-Disposition', 'inline; filename="' . basename($filename) . '"')
			->withBody($stream);
	})->setName('downloadInfo');
});
