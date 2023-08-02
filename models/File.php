<?php

namespace app\models;

use app\app\App;
use app\db\DbModel;

class File extends DbModel
{
    public int $id;
    public int $patient_id;
    public string $path;
    public string $type;

    protected array $fields_to_decode = [];

    static function tableName(): string
    {
        return 'files';
    }

    static function attributes(): array
    {
        return ['id', 'patient_id', 'path', 'type'];
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

        $file_to_upload = $_FILES['file'];

        if (!$file_to_upload) $errors['not-file-sent'] = "Nessun file inviato";

        $this->type = pathinfo($file_to_upload, PATHINFO_EXTENSION);

        if (empty($errors)) {
            try {
                $this->path = $this->uploadFile($file_to_upload);
            } catch (\Exception) {
            }
        }

        return $errors;
    }

    protected function uploadFile($file): string
    {
        $filename = preg_replace("/\s+/", "_", $file['name']);
        $filename = rand(1000, 10000) . "-" . $filename;
        $filepath = App::$app::$ROOT_DIR . "/storage/" .  $filename;

        move_uploaded_file($file['tmp_name'], $filepath);

        return $filename;
    }
}
