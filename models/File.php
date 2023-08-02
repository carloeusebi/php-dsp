<?php

namespace app\models;

use app\app\App;
use app\db\DbModel;

class File extends DbModel
{
    public int $id;
    public int $patient_id;
    public string $name;
    public string $type;

    protected array $fields_to_decode = [];

    static function tableName(): string
    {
        return 'files';
    }

    static function attributes(): array
    {
        return ['patient_id', 'name', 'type'];
    }

    static function labels(): array
    {
        return [];
    }

    protected static function joins(): string
    {
        return '';
    }


    /**
     * Fetches a single record from the database table associated with this model based on the provided patient ID.
     *
     * This method retrieves a single row from the database table associated with the model, where the 'patient_id'
     * column matches the given $patient_id parameter.
     *
     * @param int $patient_id The ID of the patient whose record needs to be fetched.
     *
     * @return array An associative array representing the fetched records if found, 
     */
    public function getByPatientId(int $patient_id): array
    {
        $table_name = $this->tableName();

        $statement = $this->prepare("SELECT * FROM $table_name WHERE `patient_id` = $patient_id");

        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function save(): array
    {
        $errors = [];

        $file_to_upload = $_FILES['file'];

        if (!$file_to_upload) $errors['not-file-sent'] = "Nessun file inviato";

        $this->type = pathinfo($file_to_upload['name'], PATHINFO_EXTENSION);

        if (empty($errors)) {
            try {
                $this->name = $this->uploadFile($file_to_upload);

                parent::create();
            } catch (\Exception $exception) {
                \app\core\exceptions\ErrorHandler::log($exception);
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
