<?php

include "Extractor.php";

$str = '<img alt="test-image.png" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAYAAABzJ5OsAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAADmSURBVGiB7dQxDgFBGIbhd4Q4hEKBSvSiVUskCqVb0OqJ3g3cQsMZ6DQSiYLQqVZBRLZZMmP/rHxPMs1m5p+3mCxkmANgToEDlR/ec2TMKfTQPAAHSsAm9PAXxxCYhB6bCz0wTYq3ongrireieCuKt6J4K3mfw809jFbJ+ypnBg1o+dwFTB2s3z94xZeu0N1+tLX+XD4W8Q+ZfjaKt6J4K4+/TZELN2bfHq6eqQGd0FGpiKAXQZTS6sfvz/SzUbwVxVtRvBXFW1G8FcVbUbwVxVvJdLzzORxBGWgHakmydLBL6S75X3fDl01iE28P4AAAAABJRU5ErkJggg=="/>';

$extractor = new EmbeddedImagesExtractor\Extractor();
$result = $extractor->extractImagesFromString($str, "./target-path", "./target-path/");

echo $result;

