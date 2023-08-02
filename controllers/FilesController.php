<?php

namespace app\controllers;

use app\app\App;
use app\core\utils\Request;
use app\core\utils\Response;
use app\db\DbModel;

class FilesController extends AdminController
{
    protected function getModel(): DbModel
    {
        return App::$app->file;
    }


    public function viewFile(): void
    {
        $data = Request::getBody();

        if (!isset($data['file_name'])) {
            Response::response(400, ['Error' => 'No file name']);
        }

        $file_path = App::$ROOT_DIR . '/storage/' . $data['file_name'];

        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $data['file_name'] . '"');

            readfile($file_path);
        } else {
            Response::response(404, ['Error' => 'File not found']);
        }
    }


    public function save(): void
    {
        $data = Request::getBody();

        if (!$data['patient_id']) {
            Response::response(400, ['Error' => 'Missing patient ID']);
        }

        $this->model->load($data);
        $errors = $this->model->save();

        if ($errors) {
            Response::response(422, $errors);
        }

        $last_insert_id = intval(App::$app->db->getLastInsertId());
        $last_insert_item = $this->model->getById($last_insert_id);

        Response::response(201, ['last_insert' => $last_insert_item]);
    }


    public function delete(): void
    {
        $data = Request::getBody();

        $file_name = App::$ROOT_DIR . '/storage/' . $data['name'];

        try {
            unlink($file_name);
        } catch (\Exception $exception) {
            \app\core\exceptions\ErrorHandler::log($exception);
        }


        parent::delete();
    }
}
