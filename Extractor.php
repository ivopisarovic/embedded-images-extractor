<?php

namespace EmbeddedImagesExtractor;

/**
 * Extracts images from embedded images in HTML string to image files in the file system. 
 *
 * @author Ivo Pisarovic
 */
class Extractor {

	const PATTERN = '/\<img [^\>]*src=[\"\']?(data:(image\/[a-z]+);base64,([^\"\' \>]+))[\"\' \>]/';

	private $count;

	/**
	 * Extract images from a string and saves them to the file system.
	 * @param string $str Input string.
	 * @param string $targetPath Target path for saving new files.
	 * @param string $targetURL Used as a replacement for the embedded image.
	 * @return string String with embedded code replaced by URLs to new extracted files.
	 */
	public function extractImagesFromString($str, $targetPath, $targetURL) {
		$images = $this->getImagesFromString($str);
		$this->count = 0;

		foreach ($images as $image) {
			$result = $this->processImage($image, $str, $targetPath, $targetURL);
			if ($result !== false) {
				$str = $result;
				$this->count++;
			}
		}

		return $str;
	}

	/**
	 * Creates a new image file and replaces embedded image by URL if saving succeed.
	 * @param string $str Input HTML string.
	 * @param array $image Array of a regular search match.
	 * @param string $targetPath
	 * @param string $targetURL
	 * @return mixed Replaced string if success, FALSE if saving failed.
	 */
	protected function processImage($image, $str, $targetPath, $targetURL) {
		// get info about the file
		$dynamicPath = $this->generatePath();
		$path = $targetPath . DIRECTORY_SEPARATOR . $dynamicPath;

		$ext = $this->getFileExt($image[2]);
		$fileName = $this->generateFileName($path, $ext);
		$fileContent = base64_decode($image[3]);

		$url = $targetURL . $dynamicPath . $fileName;

		// save the file
		$a = $this->saveAsImage($fileContent, $path, $fileName);
		$b = $this->verifyFile($path, $fileName, $fileContent);

		// result
		if ($a and $b) {
			// Replace embedded image in the string only if the file was correctly saved.
			$str = $this->replaceByURL($str, $image[1], $url);
			echo "File " . $fileName . " extracted to ".$path.".<br>\n";
		} else {
			echo "Error! File " . $fileName . " cannot be correctly saved to " . $path . "!<br>\n";
			return false;
		}

		return $str;
	}

	/**
	 * Return the count of extracted images from the last call of extractImagesFromString.
	 * @return int Count.
	 */
	public function getExtractedCount() {
		return $this->count;
	}

	/**
	 * Performs a search for embedded images in a HTML string.
	 * @param string $str Input string.
	 * @return array Matches.
	 */
	public function getImagesFromString($str) {
		$matches = array();
		preg_match_all(self::PATTERN, $str, $matches, PREG_SET_ORDER);
		return $matches;
	}

	/**
	 * Returns a file extension for the given mime type.
	 * @param string $mimeType
	 * @return string Appropriate extension.
	 * @throws \Exception If mime extension is not supported.
	 */
	protected function getFileExt($mimeType) {
		$mimeType = strtolower($mimeType);
		if ($mimeType == "image/jpeg") {
			return "jpg";
		} elseif ($mimeType == "image/png") {
			return "png";
		} elseif ($mimeType == "image/gif") {
			return "gif";
		} else {
			throw new \Exception("Unsupported image type!");
		}
	}

	/**
	 * Generates the dynamic part of path for saving image files.
	 * @return string Path, ending by a slash.
	 */
	protected function generatePath() {
		return date("Y") . "/" . date("m") . "/";
	}

	/**
	 * Generate an unique file name for a new file.
	 * @param string $path Target directory. Returned file name must be unique for this directory!
	 * @param string $extension File extrnsion.
	 * @return string File name with an extension.
	 */
	protected function generateFileName($path, $extension) {
		$filename = uniqid();
		while (file_exists($path . DIRECTORY_SEPARATOR . $filename)) {
			$filename = uniqid();
			$i++;
		}
		return $filename . '.' . $extension;
	}

	/**
	 * Saves the given image string to a new file to the given directory. 
	 * If the target directory doen not exists, creates it.
	 * @param string $image Image as a string.
	 * @param string$targetPath
	 * @param string $fileName
	 * @return bool True on success, false on saving failure.
	 */
	protected function saveAsImage($image, $targetPath, $fileName) {
		// check if target directory exists
		if (!file_exists($targetPath)) {
			mkdir($targetPath, 0777, true);
		}

		// save to the file
		$result = file_put_contents($targetPath . DIRECTORY_SEPARATOR . $fileName, $image);

		// result
		if ($result !== false) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Verifies if a new image file was correctly saved.
	 * @param string $targetPath
	 * @param string $fileName
	 * @param string $expected Expected content of the file.
	 * @return boolean True if file is OK.
	 */
	protected function verifyFile($targetPath, $fileName, $expected) {
		$file = $targetPath . DIRECTORY_SEPARATOR . $fileName;
		if (file_exists($file)) {
			$content = file_get_contents($file);
			return ($content == $expected);
		} else {
			return false;
		}
	}

	/**
	 * Replaces embedded image by a replacement.
	 * @param string $str Input string.
	 * @param string $search Value to be replaced.
	 * @param string $replacement
	 * @return string
	 */
	protected function replaceByURL($str, $search, $replacement) {
		return str_replace($search, $replacement, $str);
	}

}
