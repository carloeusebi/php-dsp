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
        $this->registerMiddleware(new AdminMiddleware([]));
        $this->model = $this->getModel();
    }

    abstract protected function getModel(): DbModel;


    public function index()
    {
        $resources = $this->model->get();
        $labels = $this->model->labels();

        if (empty($labels)) {
            $json = $resources;
        } else {
            $json = ['labels' => $labels, 'list' => $resources];
        }
        return Response::json(200, $json);
    }


    public function show(int $id)
    {
        $resource = $this->model->getById($id);
        if (!$resource) {
            return Response::json(404, ['error' => "No resource found with id $id"]);
        }
        return Response::json(200, $resource);
    }


    public function store(Request $request)
    {
        $errors = [];
        $data = $request->getBody();
        if (!$data) {
            return Response::json(400, ['error' => 'No Body']);
        }

        $this->model->load($data);
        $errors = $this->model->save();
        if ($errors) {
            return Response::json(422, ['errors' => $errors]);
        }

        $inserted_id = Database::getLastInsertId();
        $just_inserted_item = $this->model->getById($inserted_id);

        return Response::json(201, $just_inserted_item);
    }


    public function update(int $id, Request $request)
    {
        $errors = [];
        $data = $request->getBody();
        if (!$data) {
            return Response::json(400, ['errors' => 'No Body']);
        }

        $this->model->load($data);
        $errors = $this->model->save();
        if ($errors) {
            return Response::json(422, ['errors' => $errors]);
        }

        $updated_item = $this->model->getById($id);

        return Response::json(201, $updated_item);
    }


    public function destroy(int $id)
    {
        if (!$id) {
            return Response::json(400, ['Error' => 'No ID provided']);
        }

        $itemToDelete = $this->model->getById($id);

        if (!$itemToDelete) {
            return Response::json(400, ["Error" => "Invalid ID"]);
            exit();
        }

        $this->model->delete($id);
        return Response::statusCode(204);
    }
}
