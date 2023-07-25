<?php

namespace controllers;

use app\App;
use core\Controller;
use core\middlewares\AdminMiddleware;
use core\utils\Request;
use core\utils\Response;
use db\DbModel;

abstract class AdminController extends Controller
{
    protected DbModel $model;
    protected string $model_name;

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware([]));
        $this->model = App::$app->{$this->model_name};
    }


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

        if (empty($errors)) {
            // recover saved entry, and sends it back
            $id = $data['id'] ?? intval(App::$app->db->getLastInsertId());
            $lastInsertItem = $this->model->getById($id);
            Response::response(201, ["$this->model_name" => $lastInsertItem]);
        } else {
            Response::response(422, $errors);
        }
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
