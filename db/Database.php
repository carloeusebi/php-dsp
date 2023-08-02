<?php

namespace app\db;

use Exception;
use PDO;

class Database
{
    public PDO $pdo;
    public static Database $db;

    public function __construct()
    {
        $db_name = $_ENV['DB_NAME'] ?? '';
        $host = $_ENV['DB_HOST'] ?? '';
        $port = $_ENV['DB_PORT'] ?? '';
        $user = $_ENV['DB_USERNAME'] ?? '';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $dsn = "mysql:host=$host;port=$port;dbname=$db_name";

        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

            self::$db = $this;
        } catch (Exception $e) {
            // since database is used only on api and to log errors on the front site, we don't want to raise any errors on failed db connection
            \app\core\exceptions\ErrorHandler::log($e);
        }
    }

    /**
     *  Prepares a statement for execution and returns a statement object
     * @return PDOStatement|false If the database server successfully prepares the statement, PDO::prepare returns a PDOStatement object. If the database server cannot successfully prepare the statement, PDO::prepare returns FALSE or emits PDOException (depending on error handling).
     */
    public function prepare(string $query)
    {
        return $this->pdo->prepare($query);
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     * @return int|false PDO::exec returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, PDO::exec returns 0.
     */
    public function execute(string $query)
    {
        return $this->pdo->exec($query);
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     * @return string|false If a sequence name was not specified for the name parameter, PDO::lastInsertId returns a string representing the row ID of the last row that was inserted into the database
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }


    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(__DIR__ . '/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {

            if ($migration === '.' || $migration === '..')
                continue;

            require_once __DIR__ . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();
            $instance->up();
            $this->log("Applied migration $migration");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("There are no migrations to apply");
        }
    }


    public function createMigrationsTable()
    {
        $SQL = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;";

        $this->pdo->exec($SQL);
    }


    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }


    public function saveMigrations(array $newMigrations)
    {
        $str = implode(',', array_map(fn ($m) => "('$m')", $newMigrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }


    public function log(string $message)
    {
        echo "[" . date("d-m-y H:i:s") . "] - " . $message . PHP_EOL;
    }
}
