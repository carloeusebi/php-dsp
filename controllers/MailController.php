<?php

namespace app\controllers;

use app\app\App;
use app\core\Mail;
use app\core\Controller;
use app\core\utils\Request;
use app\core\utils\Response;
use app\core\middlewares\AdminMiddleware;

class MailController extends Controller
{
    protected Mail $mail;

    public function __construct()
    {
        $this->mail = new Mail();
        $this->registerMiddleware(new AdminMiddleware(['sendFromAdmin']));
    }


    public function sendFromForm()
    {
        $view = Request::getPath();
        $form_data = Request::getBody();

        if (!isset($form_data['submit'])) {
            Response::response(400);
        }

        $errors = $this->mail->prepareFromContactForm($form_data, true);

        if (!$errors) {
            $errors = $this->mail->send();
        }
        if (!$errors) {
            $this->mail->sendConfirmation();
            App::$app->session->setFlash('status', 'success');
        } else {
            App::$app->session->setFlash('errors', $errors);
            App::$app->session->setFlash('form', $form_data);
        }

        Response::redirect($view);
    }


    public function sendFromAdmin()
    {
        $errors = [];
        $data = Request::getBody();

        extract($data);

        if (!isset($email) || !$email) $errors['no-email'] = "Nessuna email inserita";
        if (!isset($subject) || !$subject) $errors['no-subject'] = "Nessun soggetto inserito";
        if (!isset($body) || !$body) $errors['no-body'] = "Nessun corpo della mail inserito";

        if (!empty($errors)) {
            Response::response(400, $errors);
        }

        $this->mail->email_to = $email;
        $this->mail->subject = $subject;
        $this->mail->body = $body;

        $error = $this->mail->send();

        if ($error) {
            Response::response(500, ['error' => $error]);
        }

        Response::response(204);
    }


    public function contactSupport()
    {
        $errors = [];
        $data = Request::getBody();

        extract($data);

        if (!isset($name) || !$name) $errors['no-name'] = "Nessun nome inserito";
        if (!isset($email) || !$email) $errors['no-email'] = "Nessuna email inserita";
        if (!isset($issue) || !$issue) $errors['no-issue'] = "Nessun problema specificato";

        if (!empty($errors)) {
            Response::response(400, $errors);
        }

        $this->mail->email_from = $email;

        // validate email
        if (Mail::isUndeliverable($email))
            Response::response(400, ['error' => Mail::UNDELIVERABLE_ERROR_MESSAGE]);

        $message = "Da: $name<br>
            Email: $email<br>
            Ha contattato il supporto per il seguente motivo:<br> $issue";

        App::$app->logIssueToDb(5, $message);

        $this->mail->name = $name;
        $this->mail->subject = 'E\' stato contattato il supporto';
        $this->mail->body = $message;

        // send email to owner
        $this->mail->email_to = $this->mail::$EMAIL_MAIN;
        $error = $this->mail->send();
        if ($error) $errors[] = $error;

        // send email to webmaster
        $this->mail->email_to = $this->mail::$EMAIL_SUPPORT;
        $error = $this->mail->send();
        if ($error) $errors[] = $error;

        if (!empty($errors)) {
            Response::response(500, $errors[0]);
        }

        Response::response(204);
    }
}
