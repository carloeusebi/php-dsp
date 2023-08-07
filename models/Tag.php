<?php

namespace app\models;

use app\db\DbModel;

class Tag extends DbModel
{
    public $id;
    public $tag;
    public $color;

    protected array $fields_to_decode = [];

    public static function tableName(): string
    {
        return 'tags';
    }

    static function attributes(): array
    {
        return ['id', 'tag', 'color'];
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
        $errors = [];

        if (!$this->tag) $errors['tag'] = "Manca il nome del tag";
        if (!$this->color) $errors['color'] = "Manca il colore del tag";

        if (empty($errors))
            if ($this->id)
                $this->update();
            else
                $this->create();

        return $errors;
    }
}
