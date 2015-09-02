<?php

include __DIR__ . "/../DatabaseExtractor.php";
include __DIR__ . "/../database.config.php";

/**
 * @author Ivo
 */
class DatabaseExtractorTest {

	const TABLE = "embedded_images_extractor_test";

	/** @var \PDO */
	private $dbConn;

	public function __construct($db) {
		$this->dbConn = $db;
	}

	private function prepareDB() {
		$inputHTML = '<img alt="test-image.png" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAYAAABzJ5OsAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAADmSURBVGiB7dQxDgFBGIbhd4Q4hEKBSvSiVUskCqVb0OqJ3g3cQsMZ6DQSiYLQqVZBRLZZMmP/rHxPMs1m5p+3mCxkmANgToEDlR/ec2TMKfTQPAAHSsAm9PAXxxCYhB6bCz0wTYq3ongrireieCuKt6J4K3mfw809jFbJ+ypnBg1o+dwFTB2s3z94xZeu0N1+tLX+XD4W8Q+ZfjaKt6J4K4+/TZELN2bfHq6eqQGd0FGpiKAXQZTS6sfvz/SzUbwVxVtRvBXFW1G8FcVbUbwVxVvJdLzzORxBGWgHakmydLBL6S75X3fDl01iE28P4AAAAABJRU5ErkJggg=="/>';

		$this->dbConn->exec("DROP TABLE IF EXISTS " . self::TABLE);
		$this->dbConn->exec("CREATE TABLE " . self::TABLE . " ("
				. "id INTEGER(4), content TEXT);");
		$this->dbConn->exec("INSERT INTO " . self::TABLE . " VALUES (1, '$inputHTML')");
	}

	public function testExtracting() {
		$this->prepareDB();

		$extractor = new EmbeddedImagesExtractor\DatabaseExtractor($this->dbConn);
		$targetPath = __DIR__ . "/test-extracted-images";
		$extractor->run(self::TABLE, "id", array("content"), $targetPath, "./target-path/");

		// check extracted images count
		assert($extractor->getExtractedCount() == 1);

		// check replacement
		$dbContent = $this->dbConn->query("SELECT content FROM " . self::TABLE . " WHERE id=1;")->fetchColumn();
		$matches = array();
		assert(preg_match('/\<img alt=\"test-image.png\" src=\"\.\/target-path\/([^\"]+)\"\/\>/', $dbContent, $matches));

		// check content
		$path = $targetPath . DIRECTORY_SEPARATOR . $matches[1];
		$content = file_get_contents($path);
		assert($content == file_get_contents(__DIR__ . "/test-image.png"));

		// clean
		unlink($path);
		$this->dbConn->exec("DROP TABLE " . self::TABLE);
	}

	public function run() {
		echo "TEST BEGIN<br>\n";
		$this->testExtracting();
		echo "<br>\nTEST END";
	}

}

// run
$test = new DatabaseExtractorTest($db);
$test->run();
