<?php

namespace app\controllers\admin;

use app\app\App;
use app\db\DbModel;
use app\controllers\AdminController;


class QuestionsController extends AdminController
{
  protected function getModel(): DbModel
  {
    return App::$app->question;
  }
}
