<?php

namespace app\controllers;

use app\app\App;
use app\db\DbModel;

class QuestionsController extends AdminController
{
    protected function getModel(): DbModel
    {
      return App::$app->question;
    }
}
