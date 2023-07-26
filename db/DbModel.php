<?php

namespace app\db;

use app\app\App;
use app\core\Model;
use app\core\utils\Request;
use PDO;

abstract class DbModel extends Model
{

    abstract public static function tableName(): string;

    abstract public function attributes(): array;

    abstract public function labels(): array;

    abstract public function save(): array;


    public function get()
    {
        $order = $this->getOrder();

        $tableName = $this->tableName();
        $query = "SELECT * FROM $tableName ORDER BY {$order}";
        $statement = $this->prepare($query);
        $statement->execute();;

        return $this->decodeMany($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getById(int $id)
    {
        $tableName = $this->tableName();
        $statement = $this->prepare("SELECT * FROM $tableName WHERE id = :id");
        $statement->bindValue('id', $id);
        $statement->execute();

        return $this->decodeOne($statement->fetch(PDO::FETCH_ASSOC));
    }


    public function create(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();

        $params = array_map(fn ($attr) => ":$attr", $attributes);
        $query = "INSERT INTO $tableName (" . implode(',', $attributes) . ') VALUES (' . implode(',', $params) . ')';

        $statement = $this->prepare($query);
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        return $statement->execute();
    }


    public function update(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn ($attr) => ":$attr", $attributes);

        $query = '';
        $query = implode(', ', array_map(fn ($attr, $param) => "$attr = $param", $attributes, $params));
        $query = "UPDATE $tableName SET $query WHERE id = :id";

        $statement = $this->prepare($query);
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->$attribute);
        }

        return $statement->execute();
    }


    public function delete(int $id)
    {
        $tableName = $this->tableName();
        $query = "DELETE FROM $tableName WHERE id=:id";
        $statement = $this->prepare($query);
        $statement->bindValue('id', $id);
        return $statement->execute();
    }


    protected function prepare($query)
    {
        return App::$app->db->prepare($query);
    }


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
