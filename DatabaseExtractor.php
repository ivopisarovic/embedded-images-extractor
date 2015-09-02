<?php

namespace EmbeddedImagesExtractor;

include __DIR__ . "/Extractor.php";

/**
 * Extracts HTML embedded images from a database table to files.
 *
 * @author Ivo Pisarovic
 */
class DatabaseExtractor extends Extractor {

	private $dbConn, $condition;

	/**
	 * Assigns an existing PDO instance.
	 * @param PDO $pdo
	 */
	public function __construct($pdo) {
		$this->dbConn = $pdo;
	}

	/**
	 * Can be used for restricting extracted rows.
	 * @param type $whereClause
	 */
	public function setCondition($whereClause) {
		$this->condition = $whereClause;
	}

	/**
	 * Iterates all rows in the given table and extract embedded images to files.
	 * @param string $table Table name.
	 * @param array $columns Searched columns.
	 * @param type $targetPath Target path in the file system for saving images.
	 */
	public function run($table, $idColumn, $columns, $targetPath, $targetURL) {
		$countRows = 0;
		$countChanges = 0;

		$sql = $this->prepareStatement($table, $idColumn, $columns);

		foreach ($this->dbConn->query($sql) as $row) {
			$newValues = array();
			foreach ($columns as $column) {
				$newContent = $this->extractImagesFromString($row[$column], $targetPath, $targetURL);
				if ($newContent != $row[$column]) {
					$newValues[$column] = $newContent;
					$countChanges+=$this->getExtractedCount();
				}
			}
			$countRows++;
			$this->updateItem($table, $idColumn, $row[$idColumn], $newValues);
		}

		echo $countRows . " rows, " . $countChanges . " extracted images.";
	}

	/**
	 * Creates a statement for retrieving rows with embedded images.
	 * @param string $table Table name.
	 * @param string $idColumn Primary column name.
	 * @param array $columns Columns to fetch.
	 * @return string SQL.
	 */
	protected function prepareStatement($table, $idColumn, $columns) {
		array_unshift($columns, $idColumn);
		$sql = 'SELECT ' . join(', ', $columns) . ' FROM ' . $table . ' ' . $this->condition . ';';
		return $sql;
	}

	/**
	 * Updates row with extracted image - replaces embedded code by URL.
	 * @param type $table Table name.
	 * @param string $idColumn Primary column name.
	 * @param int $id Primary column value of the edited row.
	 * @param array $values Values to update.
	 */
	protected function updateItem($table, $idColumn, $id, $values) {
		if (count($values) > 0) {
			$sql = "UPDATE $table SET ";
			foreach ($values as $k => $v) {
				$sql.="$k = ?, ";
			}
			$sql = substr($sql, 0, -2); //remove last comma
			$sql.=" WHERE $idColumn = ?;";

			$statement = $this->dbConn->prepare($sql);
			array_push($values, $id);
			$statement->execute(array_values($values));
		}
	}

}
