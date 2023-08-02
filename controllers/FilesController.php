<?php

namespace app\controllers;

use app\app\App;
use app\db\DbModel;

class FilesController extends AdminController
{
    protected function getModel(): DbModel
    {
        return App::$app->file;
    }
}
