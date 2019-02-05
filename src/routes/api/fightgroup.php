<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use RolBot\Utils\JsonUtils;
use RolBot\Entities\FightGroup;
use RolBot\Telegram\Mappers\FighterMapper;
use RolBot\Telegram\Mappers\FightGroupMapper;

$app->group('/fightgroup', function () use ($app) {
	$app->group('/group', function () use ($app) {
		$app->get('/enable/{fightgroupname}/{enable:true|false}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];
			$enable = $args['enable'] === 'true';
			$data = array('ok' => true);
			try {
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$data['data'] = $fightGroup->setFightGroupEnableOnlyOne($enable);
			} catch (\Exception $e) {
				$data['ok'] = false;
				$data['mesasge'] = $e->getMessage();
			}
			$newResponse = $response->withJson($data);
			return $newResponse;
		});

		$app->get('/getdetails/{name}', function (Request $request, Response $response, array $args) {
			$name = $args['name'];
			$data = array('ok' => true);
			try {
				$fightGroup = FightGroup::createFromFightGroupName($name);
				$data['data'] = FightGroupMapper::toArray($fightGroup);
			} catch (\Exception $e) {
				$data['ok'] = false;
				$data['message'] = $e->getMessage();
			}
			$newResponse = $response->withJson($data);
			return $newResponse;
		});
		$app->get('/getAll', function (Request $request, Response $response) {
			$data = FightGroupMapper::listToArray(FightGroup::getAllFightGroup());
			$newResponse = $response->withJson($data);
			return $newResponse;
		});
		$app->get('/delete/{name}', function (Request $request, Response $response, array $args) {
			$name = $args['name'];

			try {
				$fightGroup = FightGroup::createFromFightGroupName($name);
				$fileDeleted = FightGroup::deleteFightGroup($fightGroup);
			} catch (\Exception $e) {
				$fileDeleted = false;
			}
			$newResponse = $response->withJson($fileDeleted);
			return $newResponse;
		});
		$app->post('/add', function (Request $request, Response $response, array $args) {
			$input = json_decode($request->getBody());
			$name = JsonUtils::getJsonProperty($input, 'name');
			$description = JsonUtils::getJsonProperty($input, 'description');
			$data = array('ok' => true);
			try {
				FightGroup::addFightGroup($name, $description);
			} catch (\Exception $e) {
				$data['ok'] = false;
				$data['message'] = $e->getMessage();
			}
			$newResponse = $response->withJson($data);
			return $newResponse;
		});
	});

	$app->group('/character', function () use ($app) {
		$app->get('/getAll/{fightgroupname}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];
			$data = array('ok' => true);
			try {
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$data['data'] = FighterMapper::fighterListToArray($fightGroup->getAllPlayer());
			} catch (\Exception $e) {
				$data['ok'] = false;
			}
			$newResponse = $response->withJson($data);
			return $newResponse;
		});
		$app->get('/enable/{fightgroupname}/{charactername}/{enable:true|false}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];
			$charactername = $args['charactername'];
			$enable = $args['enable'] === 'true';
			$data = array('ok' => true);
			try {
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$playerFighter = $fightGroup->getPlayerFighter($charactername);
				if (!$playerFighter) {
					throw new \InvalidArgumentException("Jugador no encontrado");
				}
				$playerFighter->setEnabled($enable);
			} catch (\Exception $e) {
				$data['ok'] = false;
			}
			$newResponse = $response->withJson($data);
			return $newResponse;
		});
	});

	$app->group('/npc', function () use ($app) {
		$app->get('/getAll/{fightgroupname}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];
			$data = array('ok' => true);
			try {
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$data['data'] = FighterMapper::fighterListToArray($fightGroup->getAllNpc());
			} catch (\Exception $e) {
				$data['ok'] = false;
			}
			$newResponse = $response->withJson($data);
			return $newResponse;
		});
		$app->get('/delete/{fightgroupname}/{npc}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];
			$npc = $args['npc'];
			try {
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$result = $fightGroup->deleteNpc($npc);
			} catch (\Exception $e) {
				$result = false;
			}
			$newResponse = $response->withJson($result);
			return $newResponse;
		});
		$app->post('/update/{fightgroupname}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];
			$result = array('ok' => true);
			try {
				$data = json_decode($request->getBody());
				$name = JsonUtils::getJsonProperty($data, 'name');
				if (!$name) {
					throw new \InvalidArgumentException("Npc name not found");
				}
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$npc = $fightGroup->getNpcFighter($name);
				$newMaxlife = JsonUtils::getJsonProperty($data, 'maxlife');
				$life = JsonUtils::getJsonProperty($data, 'life');
				if ($life) {
					$updatedMaxlife = $newMaxlife ? $newMaxlife : $npc->getMaxlife();
					if ($life > $updatedMaxlife) {
						throw new \InvalidArgumentException("Life cannot be higher than maxlife");
					} else {
						$npc->setMaxlife($updatedMaxlife);
						$npc->setLife($life);
					}
				}
				$description = JsonUtils::getJsonProperty($data, 'description');
				if ($description) {
					$npc->setDescription($description);
				}
				$attack = JsonUtils::getJsonProperty($data, 'attack');
				if ($attack) {
					$npc->setAttack($attack);
				}
				$defense = JsonUtils::getJsonProperty($data, 'defense');
				if ($defense) {
					$npc->setDefense($defense);
				}
				$dexterity = JsonUtils::getJsonProperty($data, 'dexterity');
				if ($dexterity) {
					$npc->setDexterity($dexterity);
				}
				$result['message'] = $npc->getName();
			} catch (\Exception $e) {
				$result['ok'] = false;
				$result['message'] = $e->getMessage();
			}
			$newResponse = $response->withJson($result);
			return $newResponse;
		});
		$app->post('/add/{fightgroupname}', function (Request $request, Response $response, array $args) {
			$fightgroupname = $args['fightgroupname'];

			$data = json_decode($request->getBody());
			$name = JsonUtils::getJsonProperty($data, 'name');
			$attack = JsonUtils::getJsonProperty($data, 'attack');
			$defense = JsonUtils::getJsonProperty($data, 'defense');
			$dexterity = JsonUtils::getJsonProperty($data, 'dexterity');
			$life = JsonUtils::getJsonProperty($data, 'life');
			$description = JsonUtils::getJsonProperty($data, 'description');

			$result = array('ok' => true);
			try {
				if (!$fightgroupname || !$name ||
					!$description || !$attack || !$defense || !$dexterity || !$life) {
					throw new \InvalidArgumentException("Faltan datos");
				}
				$fightGroup = FightGroup::createFromFightGroupName($fightgroupname);
				$fightGroup->addNpc(
					$name,
					$description,
					$attack,
					$defense,
					$dexterity,
					$life
				);
			} catch (\Exception $e) {
				$result['ok'] = false;
				$result['message'] = $e->getMessage();
			}
			$newResponse = $response->withJson($result);
			return $newResponse;
		});
	});
});
