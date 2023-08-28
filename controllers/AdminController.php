<?php

namespace app\controllers;

use app\app\App;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\core\utils\Request;
use app\core\utils\Response;
use app\db\Database;
use app\db\DbModel;

abstract class AdminController extends Controller
{
    protected DbModel $model;

    public function __construct()
    {
        // $this->registerMiddleware(new AdminMiddleware([]));
        $this->model = $this->getModel();
    }

    abstract protected function getModel(): DbModel;


    public function index(): void
    {
        $resources = $this->model->get();
        $labels = $this->model->labels();

        if (empty($labels)) {
            $response = $resources;
        } else {
            $response = ['labels' => $labels, 'list' => $resources];
        }
        Response::response(200, $response);
    }


    public function show(int $id): void
    {
        $resource = $this->model->getById($id);
        if (!$resource) {
            Response::response(404, ['error' => "No resource found with id $id"]);
        }
        Response::response(200, $resource);
    }


    public function store(): void
    {
        $errors = [];
        $data = Request::getBody();
        if (!$data) {
            Response::response(400, ['error' => 'No Body']);
        }

        $this->model->load($data);
        $errors = $this->model->save();
        if ($errors) {
            Response::response(422, ['errors' => $errors]);
        }

        $inserted_id = Database::getLastInsertId();
        $just_inserted_item = $this->model->getById($inserted_id);

        Response::response(201, $just_inserted_item);
    }


    public function update(int $id): void
    {
        $errors = [];
        $data = Request::getBody();
        if (!$data) {
            Response::response(400, ['errors' => 'No Body']);
        }

        $this->model->load($data);
        $errors = $this->model->save();
        if ($errors) {
            Response::response(422, ['errors' => $errors]);
        }

        $updated_item = $this->model->getById($id);

        Response::response(201, $updated_item);
    }


    public function destroy(int $id): void
    {
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
