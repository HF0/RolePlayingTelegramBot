<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;
use RolBot\Commands\RolCommand;
use RolBot\Entities\FightGroup;

class AdminfightCommand extends RolCommand
{
    protected $name = 'adminfight';
    protected $requireMaster = true;

    public function execute()
    {
        $message = $this->getMessage();
        $params_string = trim($message->getText(true));

        $data = [
            'chat_id' => $message->getChat()->getId(),
            'parse_mode' => 'MARKDOWN',
        ];

        try {
            if (!$params_string) {
                $fightGroups = FightGroup::getAllFightGroup();
                $data['text'] = "Fights\n";
                foreach ($fightGroups as $fightGroup) {

                    $data['text'] .= "- {$fightGroup->getName()}";
                    if ($fightGroup->isEnabled()) {
                        $data['text'] .= " ✅ ";
                    }
                    $data['text'] .= "\n";
                }
            } else {
                $params = explode(' ', $params_string);
                $params = array_filter($params, function ($value) {
                    return $value !== '';
                });
                $params = array_values($params);
                $numParams = count($params);
                if ($numParams < 2) {
                    $data['text'] = '❌';
                    return Request::sendMessage($data);
                }

                $fightgroupnameArray = array_slice($params, 1);
                $fightgroupname = join(' ', $fightgroupnameArray);

                $action = $params[0];

                if (strcasecmp($action, 'on') !== 0 && strcasecmp($action, 'off') !== 0) {
                    $data['text'] = '❌';
                    return Request::sendMessage($data);
                }

                $enable = strcasecmp($action, 'on') === 0 ? true : false;

                $fightgroup = FightGroup::createFromFightGroupName($fightgroupname);
                $res = $fightgroup->setFightGroupEnableOnlyOne($enable);
                $enableMessage = $enable ? "ON" : "OFF";
                $data['text'] .= "Fight {$fightgroup->getName()} {$enableMessage}";
            }
            return Request::sendMessage($data);
        } catch (\Exception $e) {
            $errorMessage = [
                'chat_id' => $message->getChat()->getId(),
                'text' => "❌" . $e->getMessage()
            ];
            return Request::sendMessage($errorMessage);
        }
    }
}
