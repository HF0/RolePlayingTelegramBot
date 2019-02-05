<?php
namespace RolBot\Utils;

class Dice
{
	public static function diceResultString($sides)
	{
		$result = Dice::throwDice($sides);
		return Dice::diceResultStringFromInt($sides, $result);
	}

	public static function throwDice($sides)
	{
		$sides = max(1, $sides);
		return rand(1, $sides);
	}

	public static function diceResultStringFromInt($dicesides, $diceresult)
	{
		$text = '1d' . $dicesides . '=🎲' . $diceresult;
		return $text;
	}

	public static function d20DiceResultString()
	{
		$pifia_text = ' Fail';
		$epic_text = ' Epic';
		$success_text = ' Success';

		$result = Dice::throwDice(20);
		$text = '1d20=🎲' . strval($result);
		if ($result <= 4) {
			$text .= $pifia_text;
		} else if ($result >= 18) {
			$text .= $epic_text;
		} else {
			$text .= $success_text;
		}

		return $text;
	}
}
