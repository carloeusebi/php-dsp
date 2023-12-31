<?php

namespace app\core;

use app\app\App;

abstract class Model
{
    protected array $fields_to_decode;

    public function load(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($value) {
                    $this->$key = is_string($value) ? html_entity_decode($value) : $value;
                }
            }
        }
    }


    /**
     * Decodes fields stored as json
     */
    protected function decodeOne(array $item): array
    {
        $item['id'] = (int) $item['id'];
        foreach ($this->fields_to_decode as $key) {
            if (isset($item[$key]) && $item[$key])
                $item[$key] = json_decode($item[$key], true);
        }
        return $item;
    }


    /**
     * Decodes fields stored as json for each retrieved item, based on the attribute ```$fields_to_decode``` of the Model
     * @param array $data The array with the items to decode
     * @return array The array with the decoded items
     */
    protected function decodeMany(array $data): array
    {
        // if model has no field to decode, just return the original data
        if (empty($data)) return $data;
        return array_map(function ($item) {
            return $this->decodeOne($item);
        }, $data);
    }
}
