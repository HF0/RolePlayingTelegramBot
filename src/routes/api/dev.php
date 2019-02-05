<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Service\ErrorLog;

$app->group('/dev', function () use ($app) {
	$app->get('/getphperror', function (Request $request, Response $response, array $args) {
		$filename = './error_log';
		$result = array('hasErrors' => false);
		if (file_exists($filename)) {
			$file = fopen($filename, "r");
			$text = fread($file, filesize($filename));
			$text = nl2br($text);
			$result['hasErrors'] = true;
			$result['errorlog'] = $text;
			fclose($file);
		}
		$newResponse = $response->withJson($result);
		return $newResponse;
	});
});
