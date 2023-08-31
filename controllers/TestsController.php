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
      return Response::json(404, ['error' => 'No Test found']);
    }

    if (isset($survey['completed']) && $survey['completed']) {
      return Response::json(423, ['error' => 'Test is completed']);
    }

    return Response::json(200, $survey);
  }


  public function update(Request $request)
  {
    $errors = [];
    $data = $request->getBody();

    if (!$data) {
      return Response::statusCode(404);
    }

    App::$app->survey->load($data);
    $errors = App::$app->survey->save();

    if (isset($data['justCompleted']) && $data['justCompleted']) {
      $this->sendCompletionEmail($data);
    }

    if (empty($errors)) {
      return Response::statusCode(204);
    } else {
      return Response::json(422, $errors); // HTTP 422 - Unprocessable Entity
    }
  }


  public function updatePatientInfo(int $id, Request $request)
  {
    $model = App::$app->patient;
    $updated_patient_info = $request->getBody();

    if (!$updated_patient_info) {
      return Response::statusCode(400);
    }

    $patient_to_update = $model->getById($id);

    // updates patient infos with the ones sent from the form
    foreach ($patient_to_update as $key => $value) {
      if ($updated_patient_info[$key])
        $patient_to_update[$key] = $updated_patient_info[$key];
    }

    $model->load($patient_to_update);
    $errors = $model->save();

    return $errors ? Response::json(422, ['errors' => $errors]) : Response::statusCode(204);
  }


  private function sendCompletionEmail(array $survey): void
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
