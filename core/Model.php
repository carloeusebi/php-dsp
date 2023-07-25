<?php

namespace core;

use app\App;

abstract class Model
{
    protected array $fields_to_decode;


    public function load($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($value) {
                    $this->$key = is_string($value) ? html_entity_decode($value) : $value;
                }
            }
        }
    }


    protected function decodeOne(array $item): array
    {
        foreach ($this->fields_to_decode as $key) {
            if ($item[$key])
                $item[$key] = json_decode($item[$key]);
        }
        return $item;
    }


    protected function decodeMany(array $data): array
    {
        // if model has no field to decode, just return the original data
        if (empty($this->fields_to_decode)) return $data;
        return array_map(function ($item) {
            return $this->decodeOne($item);
        }, $data);
    }


    protected function uploadFile($file): string
    {
        $filename = preg_replace("/\s+/", "_", $file['name']);
        $filename = '/uploads/' . rand(1000, 10000) . "-" . $filename;
        $filepath = App::$app::$ROOT_DIR . "/public" .  $filename;

        move_uploaded_file($file['tmp_name'], $filepath);

        return $filename;
    }
}
