<?php

namespace app\db;

use app\app\App;
use app\core\Model;
use app\core\utils\Request;
use PDO;

abstract class DbModel extends Model
{

    abstract static protected function joins(): string;
    abstract static function attributes(): array;
    abstract static function tableName(): string;
    abstract static function labels(): array;
    abstract public function save(): array;


    public function get(string $fields = '*')
    {
        $table_name = $this->tableName();
        $joins = $this->joins();

        $order = $this->getOrder();
        $params = $this->getQueryParams();
        $params_keys = array_keys($params);

        $where = $params ? "WHERE " . implode('AND', array_map(fn ($param) => "$param = :$param", $params_keys)) : '';

        $sql = "SELECT $fields FROM $table_name $joins $where ORDER BY $table_name.{$order}";

        $statement = $this->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result ? $this->decodeMany($result) : $result;
    }

    public function getById(int $id)
    {
        $table_name = $this->tableName();
        $joins = $this->joins();

        $statement = $this->prepare("SELECT * FROM $table_name $joins WHERE $table_name.id = :id");
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


    protected function getQueryParams(): array
    {
        $params = Request::getBody();
        $attributes = $this->attributes();

        $valid_params = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $attributes)) {
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
