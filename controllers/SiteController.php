<?php

namespace app\controllers;

use app\app\App;
use app\core\Controller;
use app\controllers\helpers\Site;
use app\core\utils\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->getPath();
        $view = $view === '/' ? '/home' : $view;

        $pageTitle = Site::getPageTitle($view);

        $status = App::$app->session->getFlash('status');
        $errors = App::$app->session->getFlash('errors');
        $form = App::$app->session->getFlash('form');

        $this->addToParams('pageTitle', $pageTitle)
            ->addToParams('status', $status)
            ->addToParams('errors', $errors)
            ->addToParams('form', $form);

        return $this->render($view, $this->params);
    }
}
