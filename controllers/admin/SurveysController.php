<?php

namespace app\controllers\admin;

use app\app\App;
use app\db\DbModel;
use app\core\utils\Request;
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
      return Response::json(400, ['Error' => 'No ID']);
    }

    $survey = App::$app->survey->getById($id);
    if (!$survey) {
      return Response::json(404, ['Error' => "No Test found with id $id"]);
    }
    if (!$survey['completed']) {
      return Response::json(422, ['Error' => 'Test not completed yet']);
    }

    try {
      $scores = Scores::calculateScores($survey);
    } catch (\Exception $e) {
      return Response::json(422, ['message' => $e->getMessage()]);
    }

    return Response::json(200, ['survey' => $survey, 'scores' => $scores]);
  }
}
