<?php

namespace app\models;

use app\app\App;
use app\db\DbModel;

class File extends DbModel
{
    public int $id;
    public int $patient_id;
    public string $name;
    public string $path;
    public string $type;

    protected array $fields_to_decode = [];

    static function tableName(): string
    {
        return 'files';
    }

    static function attributes(): array
    {
        return ['patient_id', 'name', 'path', 'type'];
    }

    static function labels(): array
    {
        return [];
    }

    protected static function joins(): string
    {
        return '';
    }


    public function save(): array
    {
        try {
            parent::create();
        } catch (\Exception $exception) {
            \app\core\exceptions\ErrorHandler::log($exception);
        }
        return [];
    }
}
