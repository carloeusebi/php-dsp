<?php

namespace app\db;

use Exception;
use PDO;
use PDOStatement;

class Database
{
    private PDO $pdo;
    public static Database $db;

    protected static string $table;
    protected static array $clauses = [];

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
            // $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            self::$db = $this;
        } catch (Exception $e) {
            // since database is used only on api and to log errors on the front site, we don't want to raise any errors on failed db connection
            \app\core\exceptions\ErrorHandler::log($e);
        }
    }

    static function table(string $table)
    {
        self::$table = $table;
        self::$clauses = []; //empties the clauses
        return self::$db;
    }

    /**
     * Insert new records in the database.
     */
    public function insert(array $values)
    {
        $table = self::$table;
        $attributes = implode(', ', array_keys($values));
        $params = implode(',', array_map(fn ($attr) => ":$attr", array_keys($values)));

        $sql = "INSERT INTO `$table` ($attributes) VALUES ($params)";
        $stmt = $this->prepare($sql);

        foreach ($values as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        try {
            $stmt->execute();
        } catch (\Exception $e) {
            \app\core\exceptions\ErrorHandler::log($e);
        }
    }


    /**
     * Updates a record in the database based on the ID.
     */
    public function update(int $id, array $values)
    {
        $table = self::$table;
        $set = implode(',', array_map(fn ($attr) => "`$attr` = :$attr", array_keys($values)));

        $where = empty(self::$clauses) ? 'id = :id' : $this->buildWhereClause(self::$clauses);

        $sql = "UPDATE $table SET $set WHERE $where";
        $stmt = $this->prepare($sql);

        // bind values
        foreach ($values as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        //bind clauses
        if (empty(self::$clauses))
            $stmt->bindValue(":id", $id);
        else {
            foreach (self::$clauses as $clause) {
                if (!isset($clause['raw_sql'])) {
                    extract($clause);
                    $stmt->bindValue(":$column", $value);
                }
            }
        }

        $stmt->execute();
    }

    /**
     * Adds a basic where clause to the query.
     */
    public function where(string $column, string $operator, string $value, $boolean = 'AND')
    {
        self::$clauses[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];
        return self::$db;
    }

    /**
     * Adds a basic where clause to the query.
     */
    public function whereRaw(string $sql, $boolean = 'AND')
    {
        self::$clauses[] = [
            'raw_sql' => $sql,
            'boolean' => $boolean,
        ];
        return self::$db;
    }


    /**
     * Retrieves data from the database.
     */
    public function get(string $columns = '*')
    {
        $table = self::$table;
        $clauses = self::$clauses ?? [];
        $sql = "SELECT $columns FROM $table ";
        if (!empty($clauses)) {
            $sql .= " WHERE " . $this->buildWhereClause($clauses);
        }
        $stmt = self::prepare($sql);
        foreach ($clauses as $clause) {
            if (!isset($clause['raw_sql'])) {
                extract($clause);
                $stmt->bindValue(":$column", $value);
            }
        }

        self::$clauses = []; // empties the clauses for the next query;

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function buildWhereClause(array $clauses): string
    {
        $whereClause = "";
        foreach ($clauses as $clause) {
            if (isset($clause['raw_sql'])) {
                $whereClause .= ' ' . $clause['raw_sql'] . ' ' . $clause['boolean'];
            } else {
                extract($clause);
                $whereClause .= " $column $operator :$column $boolean ";
            }
        }
        return $whereClause;
    }


    /**
     *  Prepares a statement for execution and returns a statement object
     * @return PDOStatement|false If the database server successfully prepares the statement, PDO::prepare returns a PDOStatement object. If the database server cannot successfully prepare the statement, PDO::prepare returns FALSE or emits PDOException (depending on error handling).
     */
    static function prepare(string $query): PDOStatement
    {
        return self::$db->pdo->prepare($query);
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     * @return int|false PDO::exec returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, PDO::exec returns 0.
     */
    static function execute(string $query)
    {
        try {
            return self::$db->pdo->exec($query);
        } catch (\Exception $e) {
            \app\core\exceptions\ErrorHandler::log($e);
        }
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     * @return string|false If a sequence name was not specified for the name parameter, PDO::lastInsertId returns a string representing the row ID of the last row that was inserted into the database
     */
    static function getLastInsertId()
    {
        return self::$db->pdo->lastInsertId();
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

            require_once __DIR__ . "/migrations/$migration";
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


    public function applyFactories()
    {
        $files = scandir(__DIR__ . '/factories');
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'sql' || $file === 'BaseFactory.php')
                continue;

            require_once __DIR__ . "/factories/$file";
            $className = pathinfo($file, PATHINFO_FILENAME);

            $instance = new $className;
            try {
                $instance->generateAndInsert();
            } catch (Exception $exception) {
                $code = $exception->getCode();
                if ($code === '42S02') {
                    $this->log("No `" . $instance::TABLE_NAME . "` table found, make sure to run php database migrate first");
                    exit;
                }
            }
        }
    }


    public function dropTables()
    {
        $tables = $this->getTables();

        if (count($tables) > 0) {

            // prepare sql
            $sql = 'DROP TABLES ';
            foreach ($tables as $table) {
                $sql .= $table[0] . ", ";
            }
            $sql = substr($sql, 0, -2);  // removes last ', '
            $this->pdo->exec($sql);

            $this->log(count($tables) . " tables dropped.");
        } else {
            $this->log('No tables to drop');
        }
    }


    protected function createMigrationsTable()
    {
        $SQL = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($SQL);
    }


    protected function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }


    protected function saveMigrations(array $newMigrations)
    {
        $str = implode(',', array_map(fn ($m) => "('$m')", $newMigrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }


    protected function getTables(): array
    {
        // fetch tables
        $statement = $this->pdo->prepare('SHOW TABLES');
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_NUM);
    }


    protected function log(string $message)
    {
        echo "[" . date("d-m-y H:i:s") . "] - " . $message . PHP_EOL;
    }
}
