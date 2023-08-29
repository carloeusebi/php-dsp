<?php

namespace app\controllers;

use app\app\App;
use app\models\Mail;
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
        $data['email_to'] = $form_data['mail'];
        $data['subject'] = 'Grazie per avermi Contattato';
        $data['body'] = "Grazie " . $form_data['name'];
        $data['body'] .= " per avermi contatto, ho ricevuto la tua mail e ti contatter&ograve; al pi&ugrave; presto.";

        $confirmation_mail = new Mail();

        $confirmation_mail->load($data);
        // don't care about errors
        $confirmation_mail->send();
    }

    /**
     * When admin, using Vue3.js admin panel, is sending email to its patients
     */
    public function sendEmailWithTestLink()
    {
        $errors = [];
        $data = Request::getBody();
        $template_file = App::$ROOT_DIR . '/resources/views/mails/link-to-test.html';

        extract($data);

        // checks if inputs are valid
        if (!isset($email_to) || !$email_to) $errors['no-email'] = "Nessuna email inserita";
        if (!isset($subject) || !$subject) $errors['no-subject'] = "Nessun soggetto inserito";
        if (!isset($link) || !$link) $errors['no-link'] = "Nessun corpo della mail inserito";

        $html_template = file_get_contents($template_file);
        $data['body'] = str_replace('{{link}}', $link, $html_template);

        // if there errors sends response 400 with errors as body
        if (!empty($errors)) {
            Response::response(400, $errors);
        }

        // loads and sends the email
        $this->mail->load($data);
        $error = $this->mail->send();

        // if there are errors sending the email it returns those error in the response
        if ($error) {
            Response::response(500, ['error' => $error]);
        }

        //everything was successful
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
        $email_from = $email ?? '-'; // renames the variable for clearer use

        // checks
        if (!isset($name) || !$name) $errors['no-name'] = "Nessun nome inserito";
        if (!isset($issue) || !$issue) $errors['no-issue'] = "Nessun problema specificato";

        if (!empty($errors)) {
            Response::response(400, $errors);
        }

        // only if all checks are passed log the issue to the database (it could be a bot completing the form we don't want to log junks)

        //TODO LOG TO DATABASE

        // build the email body
        $data['subject'] = 'E\' stato contattato il supporto';
        $data['body'] = "Da: $name<br>
            Email: $email_from<br>
            Ha contattato il supporto per il seguente motivo:<br> $issue";

        // send email both to admin's panel user and the support team
        $data['email_to'] = Mail::$EMAIL_MAIN;
        $this->mail->load($data);
        $this->mail->send();

        $data['email_to'] = Mail::$EMAIL_SUPPORT;
        $this->mail->load($data);
        $this->mail->send();

        Response::response(204);
    }
}
