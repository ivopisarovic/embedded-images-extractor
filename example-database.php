<?php

include "DatabaseExtractor.php";
include "database.config.php";

$grabber = new EmbeddedImagesExtractor\DatabaseExtractor($db);
$grabber->run("tablename", "id", array("column1", "column2"), "./target-path", "/base/url/to/the-target-path/");

