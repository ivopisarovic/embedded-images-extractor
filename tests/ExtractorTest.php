<?php

include __DIR__ . "/../Extractor.php";

/**
 * @author Ivo
 */
class ExtractorTest {

	public function testExtractingFromString() {

		$inputHTML = '<img alt="test-image.png" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAYAAABzJ5OsAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAADmSURBVGiB7dQxDgFBGIbhd4Q4hEKBSvSiVUskCqVb0OqJ3g3cQsMZ6DQSiYLQqVZBRLZZMmP/rHxPMs1m5p+3mCxkmANgToEDlR/ec2TMKfTQPAAHSsAm9PAXxxCYhB6bCz0wTYq3ongrireieCuKt6J4K3mfw809jFbJ+ypnBg1o+dwFTB2s3z94xZeu0N1+tLX+XD4W8Q+ZfjaKt6J4K4+/TZELN2bfHq6eqQGd0FGpiKAXQZTS6sfvz/SzUbwVxVtRvBXFW1G8FcVbUbwVxVvJdLzzORxBGWgHakmydLBL6S75X3fDl01iE28P4AAAAABJRU5ErkJggg=="/>';

		$extractor = new EmbeddedImagesExtractor\Extractor();
		$targetPath = __DIR__ . "/test-extracted-images";
		$result = $extractor->extractImagesFromString($inputHTML, $targetPath, "./target-path/");

		// check extracted images count
		assert($extractor->getExtractedCount() == 1);

		// check replacement
		$matches = array();
		assert(preg_match('/\<img alt=\"test-image.png\" src=\"\.\/target-path\/([^\"]+)\"\/\>/', $result, $matches));

		// check content
		$path = $targetPath . DIRECTORY_SEPARATOR . $matches[1];
		$content = file_get_contents($path);
		assert($content == file_get_contents(__DIR__ . "/test-image.png"));
		
		// clean
		unlink($path);
	}

	public function run() {
		echo "TEST BEGIN<br>\n";
		$this->testExtractingFromString();
		echo "<br>\nTEST END";
	}

}

// run
$test = new ExtractorTest();
$test->run();
