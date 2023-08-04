<?php

namespace app\controllers;

use app\app\App;
use app\core\Controller;
use app\core\middlewares\PatientMiddleware;
use app\core\utils\Response;
use app\core\utils\Request;

class TestsController extends Controller
{

  public function __construct()
  {
    $this->registerMiddleware(new PatientMiddleware([]));
  }


  public function get()
  {
    $token = Request::getBody()['token'] ?? '';

    $survey = App::$app->survey->getByToken($token);
    if (!$survey) {
      Response::response(404, ['error' => 'No Test found']);
    }

    if (isset($survey['completed']) && $survey['completed']) {
      Response::response(403, ['error' => 'Test is completed']);
    }
    Response::response(200, $survey);
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


  public function getPatient(): void
  {
    $patient_id = Request::getBody()['id'] ?? '';
    if (!$patient_id) {
      Response::response(400, ['Error' => 'No Patient ID']);
    }

    $patient = $this->fetchPatient($patient_id);

    Response::response(200, ['patient' => $patient]);
  }


  public function updatePatientInfo(): void
  {
    $model = App::$app->patient;

    $updated_patient_info = Request::getBody();
    if (!$updated_patient_info['id']) {
      Response::response(400, ['Error' => 'No patient ID']);
    }

    $patient_to_update = $this->fetchPatient($updated_patient_info['id']);


    // updates patient infos with the ones sent from the form
    foreach ($patient_to_update as $key => $value) {
      if ($updated_patient_info[$key])
        $patient_to_update[$key] = $updated_patient_info[$key];
    }

    $model->load($patient_to_update);
    $errors = $model->save();

    $errors ? Response::response(422, $errors) : Response::response(204);
  }


  protected function fetchPatient(int $id): array
  {
    $patient = App::$app->patient->getById($id);
    if (!$patient) {
      // There should always be a match in the database, if there is not a match this is a Bad Request code and not a Not Found
      Response::response(400, ['Error' => 'Invalid patient ID']);
    }
    return $patient;
  }
}
