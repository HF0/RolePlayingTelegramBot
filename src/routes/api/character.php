<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Entities\Character;

$app->group('/character', function () use ($app) {
	$app->get('/getAll', function (Request $request, Response $response) {
		$newResponse = $response->withJson(Character::getAll());
		return $newResponse;
	});
	$app->get('/delete/{name}', function (Request $request, Response $response, array $args) {
		try {
			$name = $args['name'];
			$fileDeleted = Character::deleteIfNotUsed($name);
			$newResponse = $response->withJson($fileDeleted);
			$data['ok'] = true;
		} catch (\Exception $e) {
			$data['ok'] = false;
			$data['message'] = $e->getMessage();
		}
		$newResponse = $response->withJson($data);
		return $newResponse;
	});
	$app->post('/add', function (Request $request, Response $response, array $args) {
		$input = json_decode($request->getBody());
		$name = JsonUtils::getJsonProperty($input, 'name');
		$life = JsonUtils::getJsonProperty($input, 'life');
		$description = JsonUtils::getJsonProperty($input, 'description');
		$attack = JsonUtils::getJsonProperty($input, 'attack');
		$defense = JsonUtils::getJsonProperty($input, 'defense');
		$dexterity = JsonUtils::getJsonProperty($input, 'dexterity');
		$level = JsonUtils::getJsonProperty($input, 'level');
		$control = JsonUtils::getJsonProperty($input, 'control');
		$data = array('ok' => true);
		try {
			Character::add(
				$name,
				$life,
				$description,
				$attack,
				$defense,
				$dexterity,
				$level,
				$control
			);
		} catch (\Exception $e) {
			$data['ok'] = false;
			$data['message'] = $e->getMessage();
		}

		$newResponse = $response->withJson($data);
		return $newResponse;
	});

	$app->post('/update', function (Request $request, Response $response, array $args) {
		$characterJson = json_decode($request->getBody());
		$name = JsonUtils::getJsonProperty($characterJson, 'name');
		try {
			if (!$name) {
				throw new \InvalidArgumentException("Character name not found");
			}
			$character = Character::getByName($name);
			$newMaxlife = JsonUtils::getJsonProperty($characterJson, 'maxlife');
			$life = JsonUtils::getJsonProperty($characterJson, 'life');
			if ($life) {
				$updatedMaxlife = $newMaxlife ? $newMaxlife : $character->getMaxlife();
				if ($life > $updatedMaxlife) {
					throw new \InvalidArgumentException("Life cannot be higher than maxlife");
				} else {
					$character->setMaxlife($updatedMaxlife);
					$character->setLife($life);
				}
			}
			$description = JsonUtils::getJsonProperty($characterJson, 'description');
			if ($description) {
				$character->setDescription($description);
			}
			$attack = JsonUtils::getJsonProperty($characterJson, 'attack');
			if ($attack) {
				$character->setAttack($attack);
			}
			$defense = JsonUtils::getJsonProperty($characterJson, 'defense');
			if ($defense) {
				$character->setDefense($defense);
			}
			$dexterity = JsonUtils::getJsonProperty($characterJson, 'dexterity');
			if ($dexterity) {
				$character->setDexterity($dexterity);
			}
			$level = JsonUtils::getJsonProperty($characterJson, 'level');
			if ($level) {
				$character->setLevel($level);
			}
			$control = JsonUtils::getJsonProperty($characterJson, 'control');
			if ($control) {
				$character->setControl($control);
			}
			$data['ok'] = true;
			$data['message'] = 'ok';
		} catch (\Exception $e) {
			$data['ok'] = false;
			$data['message'] = $e->getMessage();
		}
		$newResponse = $response->withJson($data);
		return $newResponse;
	});

});
