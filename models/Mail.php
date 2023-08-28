<?php

namespace app\models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Verifalia\VerifaliaRestClient;

use app\app\App;
use app\core\Model;
use app\db\Database;

class Mail extends Model
{
    public const UNDELIVERABLE_ERROR_MESSAGE = "Email non valida, per favore riprovare con un indirizzo valido";
    private const SUBJECT = "Un cliente ti ha scritto";
    private const MAIL_BOT = "A form was submitted by a bot";
    private const MAIL_INVALID = "A form was submitted with an undeliverable email";

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
     * Load the email fields from the website's contact form
     * @param string[] $form_data An assoc array containing the data loaded from the form
     * @param bool $needs_validation True to validate the email using Verifalia's api
     * @return null|string Null if the email is loaded correctly, a string with the error message if the email is considered undeliverable or if an attempt from a bot was caught
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

        // It means it is a bot who checked the honey box
        if (isset($form_data['miele-cb'])) {
            self::log(self::MAIL_BOT);
            return "Qualcosa è andato storto, riprovare";
        }

        if ($needs_validation && self::isUndeliverable($this->email_from, true)) {
            return self::UNDELIVERABLE_ERROR_MESSAGE;
        }

        return '';
    }


    /**
     * Send the email using PHPMailer's api
     * @return null|string null if the email was sent, a string with the error message otherwise
     */
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
            self::log($error);

            return "Qualcosa è andato storto, per favore riprovare più tardi";
        }
    }

    /**
     * Checks if email address is deliverable using Verifalia api
     * @param string $email The email address to validate
     * @param bool $should_log_invalid True if a log should be made in case email is invalid
     * @param bool $should_log_valid True if a log should be made in case email is valid; Should not log email if it doesn't belong to a registered patient
     * @return bool False is the email address is valid and deliverable
     */
    static function isUndeliverable(string $email, bool $should_log_invalid = false, bool $should_log_valid = false): bool
    {

        error_reporting(E_ALL & ~E_DEPRECATED);

        // if an invalid email address passed the frontend barriers immediately return the email as undeliverable
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return true;

        // if the email was already validated return false
        if (self::emailWasAlreadyValidated($email)) return false;

        // try the validation through Verifalia
        try {
            $verifalia_username = $_ENV['VERIFALIA_USERNAME'] ?? '';
            $verifalia_password = $_ENV['VERIFALIA_PASSWORD'] ?? '';

            $verifalia = new VerifaliaRestClient([
                'username' => $verifalia_username,
                'password' => $verifalia_password
            ]);

            // check if have verifalia credits
            $balance = $verifalia->credits->getBalance();
            if ($balance->freeCredits > 0) {
                $validation = $verifalia->emailValidations->submit($email, true);
                $entry = $validation->entries[0];


                // if the email passed the Verifalia test is considered valid and deliverable 
                if ($entry->classification === 'Undeliverable') {
                    if ($should_log_invalid) self::log(self::MAIL_INVALID . ": $email");
                    return true;
                }

                // if the flag should_log_valid is true, probably because the email belongs to one of the registered patients, it is logged to the database
                // EMAILS FROM NON PATIENTS ARE NOT SAVED
                if ($should_log_valid)
                    self::saveValidEmail($email);
            }
        } catch (Exception $e) {
            // if there is an error with Verifalia, just pretend the email address is deliverable
            \app\core\exceptions\ErrorHandler::log($e);
            return false;
        }

        // email is considered as valid, but since it bypassed the Verifalia validation, either because there were no more credits available or because Verifalia api failed email is not logged in the database as valid
        return false;
    }

    /**
     * Checks if the email passed as param was already validated
     */
    private static function emailWasAlreadyValidated(string $email_to_validate): bool
    {
        $matches = Database::table('emails')
            ->where('email', '=', $email_to_validate, '')
            ->get();
        return count($matches);
    }


    /**
     * Saves in the database the newly validate email address
     */
    private static function saveValidEmail(string $email)
    {
        Database::table('emails')->insert(['email' => $email]);
    }


    /**
     * Log in the database a failed attempt to validate an email address
     */
    public static function log(string $message): void
    {
        App::logToDb($message);
    }
}
