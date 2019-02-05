<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Request;

use RolBot\Commands\RolCommand;
use RolBot\Repository\InfoRepository;
use RolBot\Config\Configuration;

class InfoCommand extends RolCommand
{
	protected $name = 'info';

	public function execute()
	{
		$message = $this->getMessage();
		$info_name = trim($message->getText(true));

		if (!$info_name || $info_name === '') {
			$info_name = "zona";
		}

		$base_path = Configuration::get('folderServerPrivateUploadPath');
		$infoRepository = new InfoRepository($base_path);
		$entry = $infoRepository->getByName($info_name);

		$data = [
			'chat_id' => $message->getChat()->getId(),
		];

		// photo or sound
		$file = $entry->file;
		$description = $entry->text;
		if ($file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			$image_full_path = $base_path . '/' . $file;
			if ($description) {
				$data['caption'] = $description;
			}

			$is_image = $extension === 'jpg' || $extension === 'png';
			if ($is_image) {
				if ($description) {
					$data['caption'] = $description;
				}
				$data['photo'] = Request::encodeFile($image_full_path);
				return Request::sendPhoto($data);
			}
			$is_sound = $extension === 'mp3';
			if ($is_sound) {
				$data['audio'] = Request::encodeFile($image_full_path);
				return Request::sendAudio($data);
			}
			$is_gif = $extension === 'gif';
			if ($is_gif) {
				$data['document'] = Request::encodeFile($image_full_path);
				return Request::sendDocument($data);
			}
		}

		if ($description) {
			$data['text'] = $description;
		} else {
			$data['text'] = '❌';
		}
		return Request::sendMessage($data);
	}

}
