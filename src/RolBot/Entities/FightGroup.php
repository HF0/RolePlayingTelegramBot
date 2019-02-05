<?php
namespace RolBot\Entities;

use \RedBeanPHP\R as R;
use RolBot\Entities\FightGroup;
use RolBot\Entities\Fighter;
use RolBot\Entities\NpcFighter;
use RolBot\Entities\FightStatus;
use RolBot\Entities\PlayerFighter;
use RedBeanPHP\OODBBean;

class FightGroup
{
	const FIGHT_GROUP_TABLE_NAME = 'fightgroup';
	const NPC_TABLE_NAME = 'fightgroupcharacter';
	const CHARACTER_STATUS_TABLE_NAME = 'characterstatus';

	protected $fightGroupBean;

	public function __construct(OODBBean $fightGroupBean)
	{
		if (!$fightGroupBean) {
			throw new \InvalidArgumentException("Error creating fight");
		}
		$this->fightGroupBean = $fightGroupBean;
	}

	public static function createFromEnabledFightGroup()
	{
		$fightGroupBean = R::findOne(
			self::FIGHT_GROUP_TABLE_NAME,
			' enabled = 1 ',
			array()
		);
		if (!$fightGroupBean) {
			throw new \InvalidArgumentException("No active fight");
		}
		$instance = new self($fightGroupBean);
		return $instance;
	}

	public static function createFromFightGroupName($fightgroupname)
	{
		$fightgroupbean = FightGroup::getFightGroupBean($fightgroupname);
		if (!$fightgroupbean) {
			throw new \InvalidArgumentException("Fight does not exist");
		}
		$instance = new self($fightgroupbean);
		return $instance;
	}

	private static function getFightGroupBean($fightgroupname)
	{
		$fightgroupbean = R::findOne(self::FIGHT_GROUP_TABLE_NAME, ' name = ? ', array($fightgroupname));
		return $fightgroupbean;
	}

	public static function hasFightGroup($name)
	{
		$res = R::count(self::FIGHT_GROUP_TABLE_NAME, ' name = ? ', array($name));
		return $res > 0;
	}

	public static function getAllFightGroup()
	{
		$res = R::findAll(self::FIGHT_GROUP_TABLE_NAME);
		$data = array();
		foreach ($res as $fightgroup) {
			array_push($data, new FightGroup($fightgroup));
		}
		return $data;
	}

	public static function addFightGroup($name, $description)
	{
		if (FightGroup::hasFightGroup($name)) {
			throw new \InvalidArgumentException("Fight already exists");
		}

		$fightGroupBean = R::dispense(self::FIGHT_GROUP_TABLE_NAME);
		$fightGroupBean->name = $name;
		$fightGroupBean->numenemies = 0;
		$fightGroupBean->description = $description;
		$fightGroupBean->round = 0;
		$fightGroupBean->enabled = false;
		$id = R::store($fightGroupBean);

		$fightGroup = new FightGroup($fightGroupBean);

		// all players participate in fight by default
		$characters = Character::getAll();
		foreach ($characters as $character) {
			$fightGroup->addCharacter($character);
		}
	}

	public function getNumEnemies()
	{
		return intval($this->getBean()->numenemies);
	}

	private function getId()
	{
		return $this->getBean()->id;
	}

	private function getBean()
	{
		return $this->fightGroupBean;
	}

	public function getName()
	{
		return $this->fightGroupBean->name;
	}

	public function getDescription()
	{
		return $this->getBean()->description;
	}

	public function getRound()
	{
		return intval($this->getBean()->round);
	}

	public function setRound($round)
	{
		$this->getBean()->round = $round;
		R::store($this->getBean());
	}

	public function setEnabled($enable)
	{
		$this->getBean()->enabled = $enable;
		R::store($this->getBean());
	}

	public function isEnabled()
	{
		return boolval($this->getBean()->enabled);
	}

	public function getFightStatus()
	{
		$character = $this->countAliveAndEnabledCharacters();
		$npc = $this->countAliveAndEnabledNpc();
		$finished = $character == 0 || $npc == 0;

		$fightStatus = new FightStatus();
		$fightStatus->setFinished($finished);
		if ($finished) {
			$fightStatus->setDescription($character == 0 ? "Defeat" : "Victory");
		} else {
			$fightStatus->setDescription("Active fight");
		}
		return $fightStatus;
	}

	public function resetTurnIfAllRoundOver()
	{
		$next = $this->getNextPlayer();

		if (!$next) {
			$players = $this->getAllPlayer();
			foreach ($players as $player) {
				$player->setRoundOver(false);
			}
			$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;

			$npcs = $this->getAllNpc();
			foreach ($npcs as $npc) {
				$npc->setRoundOver(false);
			}
			$this->setRound($this->getRound() + 1);
		}
	}

	private function getCharacterWithHighestDexterityNotDeadAndWithRound()
	{
		$tableCharacterStatus = self::CHARACTER_STATUS_TABLE_NAME;
		$tableCharacter = Character::CHARACTER_TABLE;
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;

		$query = "select s.* from {$tableCharacterStatus} as s " .
			"inner join {$tableCharacter} as c on " .
			"s.{$tableCharacter}_id = c.id " .
			"where s.{$fightGroupTable}_id = ? " .
			"and s.enabled and c.life > 0 and not s.round_over " .
			"order by dexterity desc";
		$row = R::getRow($query, [$this->fightGroupBean->id]);
		if (!$row) {
			return null;
		}
		$fighterCharacterStatusBean = R::convertToBean($tableCharacterStatus, $row);
		return new PlayerFighter($fighterCharacterStatusBean);
	}

	public function getNpcWithHighestDexterityNotDeadAndWithRound()
	{
		$npctable = self::NPC_TABLE_NAME;
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;

		$query = "select * from {$npctable} where " .
			"{$fightGroupTable}_id = ? " .
			"and life > 0 and not round_over " .
			"order by dexterity desc";
		$row = R::getRow($query, [$this->getId()]);
		if (!$row) {
			return null;
		}
		$fighter = R::convertToBean($npctable, $row);
		return new NpcFighter($fighter);
	}

	public function countAliveAndEnabledCharacters()
	{
		$tableCharacterStatus = self::CHARACTER_STATUS_TABLE_NAME;
		$tableCharacter = Character::CHARACTER_TABLE;
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;
		$query = "select count(*) from {$tableCharacterStatus} as s " .
			"inner join {$tableCharacter} as c on " .
			"s.{$tableCharacter}_id = c.id " .
			"where s.enabled and s.{$fightGroupTable}_id = ? " .
			"and c.life > 0;";
		$row = R::getCol($query, [$this->getId()])[0];
		return intval($row);
	}

	public function countAliveAndEnabledNpc()
	{
		$npcTable = FightGroup::NPC_TABLE_NAME;
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;
		$query = "select count(*) from {$npcTable} where " .
			"{$fightGroupTable}_id = {$this->getId()} " .
			"and life > 0; ";
		$row = R::getCol($query)[0];
		return intval($row);
	}

	public function getNextPlayer()
	{
		$player = $this->getCharacterWithHighestDexterityNotDeadAndWithRound();
		$npc = $this->getNpcWithHighestDexterityNotDeadAndWithRound();
		if (!$player) {
			return $npc;
		} else if (!$npc) {
			return $player;
		}
		$next = ($player->getDexterity() >= $npc->getDexterity()) ? $player : $npc;
		return $next;
	}

	public function getRandomPartnerCharacter($character)
	{
		$players = $this->getAllPlayer();
		$list = array();
		foreach ($players as $partner) {
			if ($partner->isEnabled() && $partner->getName() !== $character->getName()) {
				array_push($list, $partner);
			}
		}
		if (count($list) == 0) {
			return $character;
		}
		return $list[array_rand($list)];
	}

	public function setFightGroupEnableOnlyOne($enable)
	{
		if ($enable) {
			$fightGroups = FightGroup::getAllFightGroup();
			foreach ($fightGroups as $fightGroup) {
				if ($fightGroup->isEnabled()) {
					$fightGroup->setEnabled(false);
				}
			}
		}
		return $this->setEnabled($enable);
	}

	public function getPlayerFighter($name)
	{
		$tableCharacter = Character::CHARACTER_TABLE;
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;

		try {
			$character = Character::getByName($name);
			$characterStatus = R::findOne(
				self::CHARACTER_STATUS_TABLE_NAME,
				" {$tableCharacter}_id = ? and {$fightGroupTable}_id = ?",
				array($character->getBean()->id, $this->getId())
			);
			if ($characterStatus !== null) {
				return new PlayerFighter($characterStatus);
			}
		} catch (\InvalidArgumentException $e) {
			// do nothing and return null
		}
		return null;
	}

	public function getNpcFighter($name)
	{
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;
		$npc = R::findOne(
			self::NPC_TABLE_NAME,
			"name = ? and {$fightGroupTable}_id = ?",
			array($name, $this->getId())
		);
		if (!$npc) {
			return null;
		}
		return new NpcFighter($npc);
	}

	public function getFighter($charactername)
	{
		$fighter = $this->getPlayerFighter($charactername);
		if (!$fighter) {
			$fighter = $this->getNpcFighter($charactername);
		}
		return $fighter;
	}

	public function getAllPlayer()
	{
		$characterStatusList = $this->fightGroupBean->xownCharacterstatusList;
		$data = array();
		foreach ($characterStatusList as $characterStatus) {
			array_push($data, new PlayerFighter($characterStatus));
		}
		return $data;
	}

	public function getAllNpc()
	{
		$npcList = $this->fightGroupBean->xownFightgroupcharacterList;
		$result = array();
		foreach ($npcList as $npc) {
			array_push($result, new NpcFighter($npc));
		}
		return $result;
	}

	public function deleteNpc($name)
	{
		$fightGroupTable = self::FIGHT_GROUP_TABLE_NAME;
		$result = R::findOne(
			self::NPC_TABLE_NAME,
			" name = ? and {$fightGroupTable}_id = ?",
			array($name, $this->getId())
		);

		if ($result) {
			R::trash($result);
			$this->getBean()->numenemies -= 1;
			$id = R::store($this->getBean());
		}
		return true;
	}

	public function addNpc(
		$name,
		$description,
		$attack,
		$defense,
		$dexterity,
		$life
	) {
		if ($this->getFighter($name)) {
			throw new \InvalidArgumentException("Character already exists");
		}
		$npc = R::dispense(self::NPC_TABLE_NAME);
		$npc->name = $name;
		$npc->description = $description;
		$npc->attack = $attack;
		$npc->defense = $defense;
		$npc->dexterity = $dexterity;
		$npc->life = $life;
		$npc->maxlife = $life;
		$npc->round_over = false;
		// one to many (x means cascade on delete)
		$this->getBean()->xownFightgroupcharacterList[] = $npc;
		$this->getBean()->numenemies += 1;
		$id = R::store($this->getBean());
	}

	private function addCharacter($characterbean)
	{
		$characterStatus = R::dispense(self::CHARACTER_STATUS_TABLE_NAME);
		$characterStatus->rolcharacter = $characterbean;
		$characterStatus->fightgroup = $this->getBean();
		$characterStatus->enabled = true;
		$characterStatus->round_over = false;

		$this->getBean()->xownCharacterstatusList[] = $characterStatus;
		R::store($this->getBean());
	}

	public static function deleteFightGroup($fightRepository)
	{
		R::trash($fightRepository->getBean());
		return true;
	}
}
