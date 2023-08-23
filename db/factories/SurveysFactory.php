<?php

use app\app\App;
use app\db\factories\BaseFactory;

class SurveysFactory extends BaseFactory
{
    public const TABLE_NAME = 'surveys';

    private array $titles = ['Test di inizio terapia', 'Test di metà terapia', 'Test di fine terapia'];

    /**
     * Fetches patients from db
     */
    private function fetchPatients(): array|false
    {
        $statement = App::$app->db->prepare('SELECT * FROM `patients`');
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetches questionnaires from db
     */
    private function fetchQuestions(): array|false
    {
        $statement = App::$app->db->prepare('SELECT * FROM `questions`');
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Generates random answer for the Questionnaire's items
     * @return array The array of answered items
     */
    private function answerItems(array $items, string $type): array
    {
        if ($type === 'MUL') return $items;

        $min = intval(substr($type, 0, 1));
        $max = intval(substr($type, -1));

        if ($type === 'EDI') {
            $min = 0;
            $max = 5;
        }

        return array_map(function ($item) use ($min, $max) {
            $random_answer = random_int($min, $max);
            return [
                'id' => $item['id'],
                'text' => $item['text'],
                'reversed' => $item['reversed'] ?? false,
                'answer' => $random_answer,
            ];
        }, $items);
    }

    /**
     * Generates a random survey, with randoms fields, random questionnaires, a 75% chance of being completed and in case it also generates random answers
     * @return array The newly random generated Survey, ready to be stored in the database
     */
    private function generateSurvey(int $patient_id, array $questions): array
    {
        // generates random dates
        $created_at = $this->randomDate(strtotime('-5 years'), strtotime('-5 days'));
        $last_update = $this->randomDate(strtotime($created_at), strtotime('now'));

        // removes empty Questionnaires, if any, and MUL types Questionnaires
        $questions = array_values(array_filter($questions, fn ($quest) => $quest['items'] !== 'null' && $quest['type'] !== 'MUL'));

        // generates a 75% chance that the survey is completed
        $completed = rand(1, 100) <= 75 ? 1 : null;

        // if completed, fake answers 
        $questions = array_map(function ($question) use ($completed) {
            $question['items'] = json_decode($question['items'], true);
            if ($completed) {
                $question['items'] = $this->answerItems($question['items'], $question['type']);
            }
            $question['legend'] = json_decode($question['legend'], true);
            $question['variables'] = json_decode($question['variables'], true);
            return $question;
        }, $questions);


        return [
            'patient_id' => $patient_id,
            'title' => $this->randomItem($this->titles),
            'questions' => json_encode($questions),
            'created_at' => $created_at,
            'last_update' => $last_update,
            'completed' => $completed,
            'token' => App::$app->survey->generateToken()
        ];
    }

    /**
     * Store the randomly generated Survey in the database
     * @param array $survey The Survey to be stored in the database
     */
    private function insertSurvey(array $survey): void
    {
        extract($survey);

        $sql = "INSERT INTO " . self::TABLE_NAME . "(patient_id, title, questions, created_at, last_update, completed, token) VALUES (:patient_id, :title, :questions, :created_at, :last_update, :completed, :token)";

        $statement = App::$app->db->prepare($sql);
        $statement->bindValue(':patient_id', $patient_id);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':questions', $questions);
        $statement->bindValue(':created_at', $created_at);
        $statement->bindValue(':last_update', $last_update);
        $statement->bindValue(':completed', $completed);
        $statement->bindValue(':token', $token);

        $statement->execute();
    }

    /**
     * The only public function that will get called by the factory
     */
    public function generateAndInsert(): void
    {
        $patients = $this->fetchPatients();
        $questions = $this->fetchQuestions();

        $number_of_patients = count($patients);

        $avg_surveys_per_patient = (float) readline("Average surveys per patients (float): ");

        $total_num_of_surveys =  $number_of_patients * $avg_surveys_per_patient;

        for ($i = 1; $i <= $total_num_of_surveys; $i++) {
            $patient = $this->randomItem($patients);
            $survey = $this->generateSurvey($patient['id'], $questions);
            $this->insertSurvey($survey);

            $this->printProgressBar($i, (int) $total_num_of_surveys);
        }
        echo "\n$total_num_of_surveys Surveys generated.\n";
    }
}
