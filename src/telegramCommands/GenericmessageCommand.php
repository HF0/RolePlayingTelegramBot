<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;
use RolBot\Commands\RolCommand;

use RolBot\Utils\Dice;

class GenericmessageCommand extends RolCommand
{

	protected $name = 'genericmessage';

	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$text = trim($message->getText(true));

		// command
		if ($this->hasWord($text, 'lol')) {
			$data = [
				'chat_id' => $chat_id,
				'text' => '🤢 -> lol',
			];
			return Request::sendMessage($data);
		// command
		} else if ($this->hasWord($text, 'happiness')) {
			$data = [
				'chat_id' => $chat_id,
				'text' => 'Easter egg!! Happiness for everyone',
			];
			return Request::sendMessage($data);
		// command
		} else {
			$dice_result = $this->parse_dice_command($text);
			if ($dice_result !== null) {
				$data = [
					'chat_id' => $chat_id,
					'text' => $dice_result,
				];
				return Request::sendMessage($data);
			}
		}

		return Request::emptyResponse();
	}


	private function hasWord($text, $word)
	{
		return preg_match('/\b' . $word . '\b/i', $text);
	}

	private function parse_dice_command($msg)
	{

		$msg = trim(preg_replace('/\s+/', ' ', $msg));
		$allowed_chars = preg_split('//u', '1234567890d ', -1, PREG_SPLIT_NO_EMPTY);
		$chars = preg_split('//u', $msg, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($chars as $char) {
			if (!in_array($char, $allowed_chars)) {
				return null;
			}
		}

		$dice_regexp = "/^([1-9]+[0-9]*)d([1-9]+[0-9]*)$/";
		$tokens = preg_split("/[ \t]+/", $msg);
		// filter empty tokens
		$tokens = array_filter($tokens, function ($token) {
			return $token !== '';
		});

		$result = '';
		$num_tokens = 0;
		foreach ($tokens as $token) {
			$num_tokens += 1;
			$is_dice = preg_match_all($dice_regexp, $token, $matchdice);
			if (!$is_dice) {
				return null;
			} else if ($num_tokens > 6) {
				return "Not that much man....";
			}

			$command = $matchdice[0][0];
			$number_dices = intval($matchdice[1][0]);
			$dice_sides = intval($matchdice[2][0]);

			if ($number_dices > 5 || $dice_sides > 2000) {
				return "Not that much man....";
			}

			// special case
			if ($number_dices === 1 && $dice_sides === 20) {
				$dice1d20 = Dice::d20DiceResultString();
				$result .= $dice1d20;
				continue;
			}

			$answer = '';
			$total = 0;
			for ($i = 1; $i <= $number_dices; $i++) {
				$num = rand(1, $dice_sides);
				$total += $num;
				$answer .= strval($num) . '+';
			}
			if ($number_dices > 1) {
				$answer[strlen($answer) - 1] = '=';
				$answer .= '🎲' . $total;
				$result .= $command . '=' . $answer . ' ';
			} else {
				$answer = substr($answer, 0, strlen($answer) - 1);
				$result .= $command . '=🎲' . $answer . ' ';
			}
		}
		return $result;
	}
}
