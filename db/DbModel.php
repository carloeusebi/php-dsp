<?php

namespace app\db;

use app\app\App;
use app\core\Model;
use app\core\utils\Request;
use PDO;

abstract class DbModel extends Model
{

    abstract static function attributes(): array;
    abstract static function tableName(): string;
    abstract static function labels(): array;
    abstract public function save(): array;

    /**
     * Retrieve data from the database based on specified columns and conditions.
     *
     * This function constructs and executes an SQL query to retrieve data from a database table.
     * It allows specifying the desired columns, filtering conditions, and ordering criteria.
     *
     * @param array $columns An array of column names to be retrieved from the table. Default is an empty array.
     * @param array $where An array of conditions to filter the query results. Default is an empty array.
     * @param string $joins A string containing the table joins;
     * @param string $order A string containing the order, default is `ORDER BY id ASC`;
     *
     * @return array|null An array containing the retrieved data as associative arrays or null if no data found.
     */
    public function get(array $columns = [], array $where = [], string $joins = '', string $order = 'ORDER BY `id` ASC')
    {
        $table_name = $this->tableName();

        $where_params = array_merge($where, $this->getQueryParams());
        $where_params_keys = array_keys($where_params);

        $columns = empty($columns) ? '*' : implode(', ', $columns);
        $where = $where_params ? "WHERE " . implode('AND', array_map(fn ($param) => "$param = :$param", $where_params_keys)) : '';

        $sql = "SELECT $columns FROM $table_name $joins $where $order";

        $statement = $this->prepare($sql);

        foreach ($where_params as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result ? $this->decodeMany($result) : $result;
    }

    /**
     * Retrieve a single record from the database based on the given ID.
     *
     * This function constructs and executes an SQL query to retrieve a single record from a database table based on the provided ID.
     *
     * @param int $id The unique identifier used to retrieve the record from the table.
     * @param string $joins Optional SQL JOIN clauses to enhance the query results. Default is an empty string.
     *
     * @return array|null An associative array containing the retrieved record's data or null if no record found.
     */
    public function getById(int $id, string $joins = '')
    {
        $table_name = $this->tableName();

        $sql = "SELECT * FROM $table_name $joins WHERE $table_name.id = :id";

        $statement = $this->prepare($sql);
        $statement->bindValue('id', $id);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ? $this->decodeOne($result) : $result;
    }

    /**
     * @return int|false PDO::exec returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, PDO::exec returns 0.
     */
    public function create(): bool
    {
        $table_name = $this->tableName();
        $attributes = $this->attributes();

        $params = array_map(fn ($attr) => ":$attr", $attributes);
        $sql = "INSERT INTO $table_name (" . implode(',', $attributes) . ') VALUES (' . implode(',', $params) . ')';

        $statement = $this->prepare($sql);
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        return $statement->execute();
    }

    /**
     * @return int|false PDO::exec returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, PDO::exec returns 0.
     */
    public function update(): bool
    {
        $table_name = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn ($attr) => ":$attr", $attributes);

        $sql = '';
        $sql = implode(', ', array_map(fn ($attr, $param) => "$attr = $param", $attributes, $params));
        $sql = "UPDATE $table_name SET $sql WHERE id = :id";

        $statement = $this->prepare($sql);
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->$attribute);
        }

        return $statement->execute();
    }

    /**
     * @return int|false PDO::exec returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, PDO::exec returns 0.
     */
    public function delete(int $id)
    {
        $table_name = $this->tableName();
        $sql = "DELETE FROM $table_name WHERE id=:id";
        $statement = $this->prepare($sql);
        $statement->bindValue('id', $id);
        return $statement->execute();
    }

    /**
     *  Prepares a statement for execution and returns a statement object
     * @return PDOStatement|false If the database server successfully prepares the statement, PDO::prepare returns a PDOStatement object. If the database server cannot successfully prepare the statement, PDO::prepare returns FALSE or emits PDOException (depending on error handling).
     */
    protected function prepare(string $sql)
    {
        return App::$app->db->prepare($sql);
    }

    /**
     * Retrieve and filter valid query parameters based.
     *
     * This function retrieves query parameters from the $_GET superglobal array and filters them based on the model's attributes.
     *
     * @return array An associative array containing the valid query parameters and their values.
     */
    protected function getQueryParams(): array
    {
        $params = $_GET;
        $attributes = $this->attributes();

        $valid_params = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $attributes) && $key !== 'id') {
                $valid_params[$key] = $value;
            }
        }
        return $valid_params;
    }


    /**
     * Reads order if specified in the query string
     * @return string A string containing the order if specified in the query string, a default of 'ORDER BY id ASC' otherwise
     */
    protected function getOrder(): string
    {
        $data = Request::getBody();
        $attributes = $this->attributes();

        $order_by = $data['order_by'] ?? 'id';
        $direction = isset($data['order_direction']) ? strtoupper($data['order_direction']) : 'ASC';

        if (!in_array($order_by, $attributes)) $order_by = 'id';
        if (!in_array($direction, ['ASC', 'DESC'])) $direction = 'ASC';

        return "$order_by $direction";
    }
}
