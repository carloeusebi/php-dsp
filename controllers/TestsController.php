<?php

namespace app\controllers;

use app\app\App;
use app\controllers\helpers\Scores;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\core\middlewares\PatientMiddleware;
use app\core\utils\Response;
use app\core\utils\Request;
use app\models\Mail;

class TestsController extends Controller
{

  public function __construct()
  {
    $this->registerMiddleware(new PatientMiddleware([]));
  }


  public function show(string $token)
  {
    $survey = App::$app->survey->getByToken($token);
    if (!$survey) {
      Response::response(404, ['error' => 'No Test found']);
    }

    if (isset($survey['completed']) && $survey['completed']) {
      Response::response(423, ['error' => 'Test is completed']);
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

    if (isset($data['justCompleted']) && $data['justCompleted']) {
      $this->sendCompletionEmail($data);
    }

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


  private function sendCompletionEmail(array $survey)
  {
    $mail = new Mail();
    $patient = $survey['patient'];
    $patient_name = $patient['fname'] . ' ' . $patient['lname'];
    $now = date('d-m-Y H:i', time());

    $data['email_to'] = Mail::$EMAIL_MAIN;
    $data['subject'] = "$patient_name ha completato il test";
    $data['body'] = "$patient_name ha completato il test " . $survey['title'] . " il $now.<br>
        <p><a href=\"https://www.dellasantapsicologo.it/admin/risposte/{$survey['id']}\">Link per vedere le risposte</a></p>
        <p><a href=\"https://www.dellasantapsicologo.it/admin/risultati/{$survey['id']}\">Link per vedere i risultati</a></p>";

    $mail->load($data);
    $mail->send();
  }
}
