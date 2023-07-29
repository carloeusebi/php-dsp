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


    /**
     * Handles website contact form's submission
     */
    public function sendFromForm()
    {
        $view = Request::getPath();
        $form_data = Request::getBody();

        // if somehow this code is being executed without the form submission return invalid requests
        if (!isset($form_data['submit'])) {
            Response::response(400);
        }

        // load the mail with data from the form, and collects errors
        $errors = $this->mail->prepareFromContactForm($form_data, true);

        if (!$errors) {
            $errors = $this->mail->send();
        }
        if (!$errors) {
            App::$app->session->setFlash('status', 'success');
            $this->sendConfirmationMailAfterContactForm($form_data);
        } else {
            App::$app->session->setFlash('errors', $errors);
            App::$app->session->setFlash('form', $form_data);
        }

        Response::redirect($view);
    }


    /**
     * @param string[] $form_data The Form of data collected from the contact form
     */
    private function sendConfirmationMailAfterContactForm(array $form_data): void
    {
        $email_to = $form_data['mail'];
        $subject = 'Grazie per avermi Contattato';
        $body = "Grazie " . $form_data['name'];
        $body .= " per avermi contatto, ho ricevuto la tua mail e ti contatter&ograve; al pi&ugrave; presto.";

        $this->mail->load($email_to, $subject, $body);
        // don't care about errors
        $this->mail->send();
    }

    /**
     * When admin, using Vue3.js admin panel, is sending email to its patients
     */
    public function sendFromAdmin()
    {
        $errors = [];
        $data = Request::getBody();

        extract($data);

        // checks if inputs are valid
        if (!isset($email) || !$email) $errors['no-email'] = "Nessuna email inserita";
        if (!isset($subject) || !$subject) $errors['no-subject'] = "Nessun soggetto inserito";
        if (!isset($body) || !$body) $errors['no-body'] = "Nessun corpo della mail inserito";

        // if there errors sends response 400 with errors as body
        if (!empty($errors)) {
            Response::response(400, $errors);
        }

        // loads and sends the email
        $this->mail->load($email, $subject, $body);
        $error = $this->mail->send();

        // if there are errors sending the email it returns those error in the response
        if ($error) {
            Response::response(500, ['error' => $error]);
        }

        //everything was successfull
        Response::response(204);
    }


    /**
     * If patients encounter issues during the completion of the survey they can contact the support
     */
    public function contactSupport()
    {
        $errors = [];
        $data = Request::getBody();

        extract($data);
        $email_from = $email; // renames the variable for clearer use

        // checks
        if (!isset($name) || !$name) $errors['no-name'] = "Nessun nome inserito";
        if (!isset($email_from) || !$email_from) $errors['no-email'] = "Nessuna email inserita";
        if (!isset($issue) || !$issue) $errors['no-issue'] = "Nessun problema specificato";

        if (!empty($errors)) {
            Response::response(400, $errors);
        }

        // validate email
        if (Mail::isUndeliverable($email_from, true))
            Response::response(400, ['error' => Mail::UNDELIVERABLE_ERROR_MESSAGE]);

        // only if all checks are passed log the issue to the database (it could be a bot completing the form we don't want to log junks)

        App::$app->logIssueToDb($issue, $name, $email_from);

        // build the email body
        $subject = 'E\' stato contattato il supporto';
        $body = "Da: $name<br>
            Email: $email_from<br>
            Ha contattato il supporto per il seguente motivo:<br> $issue";

        // send email both to admin's panel user and the support team
        $this->mail->load(Mail::$EMAIL_MAIN, $subject, $body, $name, $email_from);
        $this->mail->send();

        $this->mail->load(Mail::$EMAIL_SUPPORT, $subject, $body, $name, $email_from);
        $this->mail->send();

        Response::response(204);
    }
}
