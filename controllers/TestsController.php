<?php

namespace app\controllers;

use app\app\App;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\core\utils\Response;
use app\core\utils\Request;

class TestsController extends Controller
{

  public function __construct()
  {
    $this->registerMiddleware(new AdminMiddleware(['result']));
  }


  public function get()
  {
    $token = Request::getBody()['token'] ?? '';
    if (!$token) {
      Response::response(400, ['error' => 'No Token provided']);
    }

    $survey = App::$app->survey->getByToken($token);
    if (!$survey) {
      Response::response(404, ['error' => 'No Test found']);
    }

    if (isset($survey['completed']) && $survey['completed']) {
      Response::response(403, ['error' => 'Test is completed']);
    }

    $patient = App::$app->patient->getById($survey['patient_id']);
    $message = [
      'survey' => $survey,
      'patient' => $patient
    ];
    Response::response(200, $message);
  }


  public function save(): void
  {
    $errors = [];
    $data = Request::getBody();

    if (!$data) {
      Response::statusCode(400);
      exit();
    }

    App::$app->survey->load($data);
    $errors = App::$app->survey->save();

    if (empty($errors)) {
      Response::response(204);
    } else {
      Response::response(422, $errors); // HTTP 422 - Unprocessable Entity
    }
  }
}
