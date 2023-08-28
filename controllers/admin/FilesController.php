<?php

namespace app\controllers\admin;

use app\app\App;
use app\core\utils\Request;
use app\core\utils\Response;
use app\db\DbModel;
use app\controllers\AdminController;

class FilesController extends AdminController
{

    protected function getModel(): DbModel
    {
        return App::$app->file;
    }

    public function upload(): void
    {
        $file_to_upload = $_FILES['file'];
        if (!$file_to_upload) {
            Response::response(400, ['Error' => 'Missing file to upload']);
        }

        $data = Request::getBody();
        if (!$data['patient_id']) {
            Response::response(400, ['Error' => 'Missing patient ID']);
        }

        $data['name'] = $file_to_upload['name'];
        $data['path'] = $this->uploadFile($file_to_upload);
        $data['type'] = pathinfo($file_to_upload['name'], PATHINFO_EXTENSION);

        $this->model->load($data);
        $errors = $this->model->save();

        if ($errors) {
            Response::response(422, $errors);
        }

        $last_insert_id = intval(App::$app->db->getLastInsertId());
        $last_insert_item = $this->model->getById($last_insert_id);

        Response::response(201, $last_insert_item);
    }

    public function download(int $id): void
    {
        if (!$id) {
            Response::response(404);
        }
        $file = App::$app->file->getById($id);
        if (!$file) {
            Response::response(404, ['error' => "No file found with id $id"]);
        }

        $file_path = $file['path'];

        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $file['name'] . '"');

            readfile($file_path);
        } else {
            Response::response(404, ['Error' => 'File not found']);
        }
    }




    public function delete(int $id): void
    {
        $file = App::$app->file->getById($id);
        if (!$file) {
            Response::response(404, ['error' => "No file found with id $id"]);
        }
        $file_path = $file['path'];

        try {
            unlink($file_path);
        } catch (\Exception $exception) {
            \app\core\exceptions\ErrorHandler::log($exception);
        }
        parent::destroy($id);
    }


    /**
     * Uploads a file to the server and returns the generated filename.     *
     * @param array $file The uploaded file data.
     * @return string The generated filename after successful upload.
     * @throws RuntimeException If the file upload fails or encounters an error.
     */
    protected function uploadFile(array $file): string
    {
        $filename = preg_replace("/\s+/", "_", $file['name']);
        $filename = rand(1000, 10000) . "-" . $filename;
        $filepath = App::$app::$ROOT_DIR . "/storage/uploads/" .  $filename;

        // Create the uploads directory if it doesn't exist
        $storage_folder = App::$ROOT_DIR . '/storage';
        if (!file_exists($storage_folder))
            mkdir($storage_folder);

        $uploads_folder = $storage_folder . '/uploads';
        if (!file_exists($uploads_folder)) {
            mkdir($uploads_folder);
        }

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \RuntimeException('Failed to upload the file. Please try again.');
        }

        return $filepath;
    }
}
