<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Utils\Dice;

$app->get('/dice/{sides}', function (Request $request, Response $response, array $args) {
	$sides = $args['sides'];
	$resultNumber = Dice::throwDice($sides);
	$resultString = Dice::diceResultStringFromInt($sides, $resultNumber);
	$response->getBody()->write($resultString);
	return $response;
});
