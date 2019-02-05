<?php
namespace RolBot\Repository;

use \RedBeanPHP\R as R;

class InfoRepository
{
	private $filePath;
	const TABLE_NAME = 'infocommand';
	private $ALLOWED_FILE_TYPES = array("jpg", "png", "jpeg", "gif", "mp3");
	const MB = 1048576;
	const MAX_FILE_SIZE_MB = 15;

	function __construct($fileFolderPath)
	{
		$this->filePath = $fileFolderPath;
	}

	public function getInfoList()
	{
		$info = R::getAll('SELECT * FROM ' . self::TABLE_NAME);
		return $info;
	}

	public function getByName($name)
	{
		$entry = R::findOne(self::TABLE_NAME, 'name = ? ', [$name]);
		return $entry;
	}

	public function delete($name)
	{
		$fileDeleted = true;

		$entry = R::findOne(self::TABLE_NAME, 'name = ? ', [$name]);
		if ($entry) {
			$fileName = $entry->file;
			if ($fileName) {
				$fileDeleted = unlink($this->filePath . '/' . $fileName);
			}
			R::trash($entry);
		}
		return $fileDeleted;
	}

	public function uploadFileFromPost($nameKey, $textKey, $fileUploadKey)
	{
		$result = array();
		$result['ok'] = false;

		if (!isset($_POST['submit'])) {
			return $result;
		}

		if (!isset($_POST[$nameKey]) || trim($_POST[$nameKey]) === '') {
			$result['message'] = "Invalid name";
			return $result;
		}
		$name = $_POST[$nameKey];

		$description = null;
		if (isset($_POST[$textKey])) {
			$description = $_POST[$textKey];
		}

		$entry = R::findOne(self::TABLE_NAME, 'name = ? ', [$name]);
		if ($entry != null) {
			$result['message'] = "Info already exists";
			return $result;
		}

		$fileName = null;
		if (isset($_FILES[$fileUploadKey]) && isset($_FILES[$fileUploadKey]["name"]) &&
			trim($_FILES[$fileUploadKey]["name"]) !== '') {

			$fileName = strtolower(basename($_FILES[$fileUploadKey]["name"]));
			$targetFile = $this->filePath . '/' . $fileName;

			$uploadOk = true;
			$imageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);

			$tempFileName = $_FILES[$fileUploadKey]["tmp_name"];
			// $check = getimagesize($tempFileName);
			// if($check === false) {
				// $result['message'] = "File is not an image";
				// return $result;
			// }

			if (file_exists($targetFile)) {
				$result['message'] = "File already exists";
				return $result;
			}

			$file_size = $_FILES[$fileUploadKey]["size"];
			if ($file_size > self::MAX_FILE_SIZE_MB * self::MB) {
				$result['message'] = "File is too big";
				return $result;
			}

			if (!in_array($imageFileType, $this->ALLOWED_FILE_TYPES)) {
				$result['message'] = "Supported files: " . implode(" ", $this->ALLOWED_FILE_TYPES);
				return $result;
			}

			$fileMoved = move_uploaded_file($tempFileName, $targetFile);
			if (!$fileMoved) {
				$result['message'] = "Error moving file";
				return $result;
			}
		}

		if (!$fileName && !$description) {
			$result['message'] = "No file and no description";
			return $result;
		}

		$result['ok'] = true;
		$this->insertFleAlreadyUploaded($name, $description, $fileName);

		return $result;
	}

	private function insertFleAlreadyUploaded($name, $text = null, $file = null)
	{
		$table = R::dispense(self::TABLE_NAME);
		$table->name = $name;
		if ($text) {
			$table->text = $text;
		}
		if ($file) {
			$table->file = $file;
		}
		$id = R::store($table);
	}

}
