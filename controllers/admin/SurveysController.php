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


  public function getScores(int $id): void
  {
    if (!$id) {
      Response::response(400, ['Error' => 'No ID']);
    }

    $survey = App::$app->survey->getById($id);
    if (!$survey) {
      Response::response(404, ['Error' => 'No Test found']);
    }
    if (!$survey['completed']) {
      Response::response(422, ['Error' => 'Test not completed yet']);
    }

    $scores = Scores::calculateScores($survey);

    Response::response(200, ['survey' => $survey, 'scores' => $scores]);
  }
}
