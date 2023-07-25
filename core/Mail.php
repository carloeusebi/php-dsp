<?php

namespace core;

use app\App;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Verifalia\VerifaliaRestClient;


class Mail
{
    private const SUBJECT = "Un cliente ti ha scritto";
    private const CONFIRMATION_SUBJECT = "Grazie per avermi Contattato";
    private const CONFIRMATION_BODY = " per avermi contatto, ho ricevuto la tua mail e ti contatter&ograve; al pi&ugrave; presto.";

    public static string $EMAIL_FROM;
    public static string $EMAIL_NAME;
    public static string $EMAIL_MAIN;
    public static string $EMAIL_SUPPORT;

    public string $name;
    public string $phone;
    public string $email_from;
    public string $message;
    public string $email_to;
    public string $subject;
    public string $body;


    public function __construct()
    {
        self::$EMAIL_FROM = $_ENV['EMAIL_FROM'] ?? '';
        self::$EMAIL_NAME = $_ENV['EMAIL_NAME'] ?? '';
        self::$EMAIL_MAIN = $_ENV['EMAIL_MAIN'] ?? '';
        self::$EMAIL_SUPPORT = $_ENV['EMAIL_SUPPORT'] ?? '';
    }


    /**
     * @param string[] $form_data
     */
    public function prepareFromContactForm(array $form_data, bool $needs_validation = false): ?string
    {
        $this->name = $form_data['name'];
        $this->phone = $form_data['phone'];
        $this->email_from = $form_data['mail'];
        $this->message = $form_data['message'];

        $this->email_to = self::$EMAIL_MAIN;
        $this->subject = self::SUBJECT;
        $this->body = "Da: $this->name <br>
            Numero di telefono: $this->phone <br>
            Email: $this->email_from <br><br>
            Messaggio:<br> $this->message";

        if ($needs_validation) {
            return $this->validate($form_data);
        }
    }


    public function send(): ?string
    {
        $mail = new PHPMailer(true);

        if (isset($this->email_from) && isset($this->name))
            $mail->AddReplyTo($this->email_from, $this->name);

        try {
            $mail->SMTPAuth = true;

            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];

            $mail->Port = 587;
            $mail->isSMTP();
            $mail->isHTML(true);

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom(self::$EMAIL_FROM, self::$EMAIL_NAME);
            $mail->addAddress($this->email_to);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;

            $mail->send();

            $mail->ClearAllRecipients();

            return null;
        } catch (Exception) {

            $error = $mail->ErrorInfo;
            App::$app->logIssueToDb(4, $error);

            return "Qualcosa è andato storto, per favore riprovare più tardi";
        }
    }


    public function sendConfirmation(): void
    {
        $this->email_to = $this->email_from;
        $this->email_from = self::$EMAIL_FROM;
        $this->subject = self::CONFIRMATION_SUBJECT;
        $this->body = "Grazie " . $this->name . self::CONFIRMATION_BODY;
        $this->name = self::$EMAIL_NAME;

        $this->send();
    }


    public function validate(array $data = []): ?string
    {
        // It means it is a bot who checked the honey box
        if (isset($data['miele-cb'])) {
            App::$app->logIssueToDb(2);
            return "Qualcosa è andato storto, riprovare";
        }

        $verifalia_username = $_ENV['VERIFALIA_USERNAME'] ?? '';
        $verifalia_password = $_ENV['VERIFALIA_PASSWORD'] ?? '';

        if (!$verifalia_username || !$verifalia_password) return false;

        $verifalia = new VerifaliaRestClient([
            'username' => $verifalia_username,
            'password' => $verifalia_password
        ]);


        // check if have verifalia credits
        $balance = $verifalia->credits->getBalance();
        if ($balance->freeCredits > 0) {
            $validation = $verifalia->emailValidations->submit($this->email_from, true);
            $entry = $validation->entries[0];

            if ($entry->classification === 'Undeliverable') {
                App::$app->logIssueToDb(3);
                return "Email non valida, per favore riprovare con un indirizzo valido";
            }
        }

        return null;
    }
}
