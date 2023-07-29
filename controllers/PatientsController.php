<?php

namespace app\controllers;

use app\app\App;
use app\db\DbModel;

class PatientsController extends AdminController
{
    protected function getModel(): DbModel
    {
        return App::$app->patient;
    }
}
