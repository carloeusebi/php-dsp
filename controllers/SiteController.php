<?php

namespace controllers;

use app\App;
use core\Controller;
use core\Mail;
use controllers\helpers\Site;
use core\utils\Request;
use core\utils\Response;

class SiteController extends Controller
{
    public function index(): void
    {
        $view = Request::getPath();
        $view = $view === '/' ? '/home' : $view;

        $pageTitle = Site::getPageTitle($view);

        $status = App::$app->session->getFlash('status');
        $errors = App::$app->session->getFlash('errors');
        $form = App::$app->session->getFlash('form');

        $this->addToParams('pageTitle', $pageTitle)
            ->addToParams('status', $status)
            ->addToParams('errors', $errors)
            ->addToParams('form', $form);

        $this->render($view, $this->params);
    }


    public function post()
    {
        $view = Request::getPath();
        $form_data = Request::getBody();

        if (!isset($form_data['submit'])) {
            Response::response(400);
        }

        $mail = new Mail();
        $errors = $mail->prepareFromContactForm($form_data, true);

        if (!$errors) {
            $errors = $mail->send();
        }
        if (!$errors) {
            $mail->sendConfirmation();
            App::$app->session->setFlash('status', 'success');
        } else {
            App::$app->session->setFlash('errors', $errors);
            App::$app->session->setFlash('form', $form_data);
        }

        Response::redirect($view);
    }
}
