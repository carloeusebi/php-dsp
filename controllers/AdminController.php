<?php

namespace app\controllers;

use app\app\App;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\core\utils\Request;
use app\core\utils\Response;
use app\db\DbModel;

abstract class AdminController extends Controller
{
    protected DbModel $model;

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware(['get', 'delete']));
        $this->model = $this->getModel();
    }


    abstract protected function getModel(): DbModel;


    public function get(): void
    {
        $data = Request::getBody();

        $id = isset($data['id']) ? intval($data['id']) : null;
        $labels = $this->model->labels();

        if ($id) {
            $items = $this->model->getById($id);
            if (!$items) {
                Response::response(404, 'The given patient ID does not exists');
            }
        } else {
            $items = $this->model->get();
        }

        $message = [
            'labels' => $labels,
            'list' => $items
        ];

        Response::response(200, $message);
    }


    public function save(): void
    {
        $errors = [];
        $data = Request::getBody();

        if (!$data) {
            Response::response(400, ['Error' => 'No Body']);
        }

        $this->model->load($data);
        $errors = $this->model->save();

        if ($errors) {
            Response::response(422, $errors);
        }

        // recover saved entry, and sends it back
        $id = $data['id'] ?? intval(App::$app->db->getLastInsertId());
        $last_insert_item = $this->model->getById($id);
        Response::response(201, ["last_insert" => $last_insert_item]);
    }


    public function delete(): void
    {
        $id = Request::getBody()['id'] ?? NULL;

        if (!$id) {
            Response::response(400, ['Error' => 'No ID provided']);
        }

        $itemToDelete = $this->model->getById($id);

        if (!$itemToDelete) {
            Response::response(400, ["Error" => "Invalid ID"]);
            exit();
        }

        $this->model->delete($id);
        Response::statusCode(204);
    }
}
