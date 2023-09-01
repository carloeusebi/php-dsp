<?php

namespace app\controllers\admin;

use app\app\App;
use app\db\DbModel;
use app\core\utils\Response;
use app\controllers\helpers\Scores;
use app\controllers\AdminController;


class SurveysController extends AdminController
{
  protected function getModel(): DbModel
  {
    return App::$app->survey;
  }


  public function getScores(int $id)
  {
    if (!$id) {
      return Response::json(400, ['message' => 'Nessun ID fornito']);
    }

    $survey = App::$app->survey->getById($id);
    if (!$survey) {
      return Response::json(404, ['message' => "Nessun sondaggio trovato con ID $id"]);
    }
    if (!$survey['completed']) {
      return Response::json(404, ['message' => 'Questo sondaggio non è stato ancora completato.']);
    }

    try {
      $scores = Scores::calculateScores($survey);
    } catch (\Exception $e) {
      return Response::json(422, ['message' => $e->getMessage()]);
    }

    return Response::json(200, $scores);
  }
}
