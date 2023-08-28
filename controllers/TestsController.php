<?php

namespace app\controllers;

use app\app\App;
use app\controllers\helpers\Scores;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\core\middlewares\PatientMiddleware;
use app\core\utils\Response;
use app\core\utils\Request;

class TestsController extends Controller
{

  public function __construct()
  {
    // $this->registerMiddleware(new PatientMiddleware([]));
  }


  public function show(string $token)
  {
    $survey = App::$app->survey->getByToken($token);
    if (!$survey) {
      Response::response(404, ['error' => 'No Test found']);
    }

    if (isset($survey['completed']) && $survey['completed']) {
      Response::response(403, ['error' => 'Test is completed']);
    }

    Response::response(200, $survey);
  }


  public function update(): void
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


  public function updatePatientInfo(int $id): void
  {
    $model = App::$app->patient;
    $updated_patient_info = Request::getBody();

    if (!$updated_patient_info) {
      Response::response(400);
    }

    $patient_to_update = $model->getById($id);

    // updates patient infos with the ones sent from the form
    foreach ($patient_to_update as $key => $value) {
      if ($updated_patient_info[$key])
        $patient_to_update[$key] = $updated_patient_info[$key];
    }

    $model->load($patient_to_update);
    $errors = $model->save();

    $errors ? Response::response(422, ['errors' => $errors]) : Response::response(204);
  }
}
