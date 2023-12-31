<?php

namespace app\controllers\helpers;

use app\app\App;
use Exception;

class Scores
{
    private static array $patient;

    /**
     * Calculate scores for the given survey questions.
     *
     * @param array $survey The survey data containing questions and answers.
     * @return array An associative array containing question scores.
     */
    public static function calculateScores(array $survey): array
    {
        self::$patient = $survey['patient'];

        foreach ($survey['questions'] as &$question) {
            $question['variables'] = self::calculateQuestionScore($question);
        }
        return $survey;
    }


    /**
     * Calculate scores for a specific question.
     *
     * @param array $question The question data.
     * @return array An associative array containing variable scores for the question.
     */
    private static function calculateQuestionScore(array $question): array
    {
        $variables = $question['variables'] ? $question['variables'] : self::getQuestionVariables($question['id']);

        if (empty($variables)) return [];

        foreach ($variables as &$variable) {
            $score = self::calculateVariableScore($question, $variable);
            $variable['score'] = $score;
            foreach ($variable['cutoffs'] as &$cutoff) {
                $cutoff['scored'] = self::checkIfScored($score, $variable, $cutoff);
            }
        }
        return $variables;
    }


    /**
     * Calculate score for a specific variable within a question.
     *
     * @param array $question The question data.
     * @param array $variable The variable data.
     * @return int The calculated variable score.
     */
    private static function calculateVariableScore(array $question, array $variable): int
    {
        $score = 0;

        foreach ($variable['items'] as $itemId) {
            $score += self::getItemAnswer($question, $itemId);
        }

        return $score;
    }


    /**
     * Get the answer for a specific item within a question.
     *
     * @param array $question The question data.
     * @param int $id The ID of the item.
     * @return int The item's answer score.
     * @throws Exception If the item's answer is not found.
     */
    private static function getItemAnswer(array $question, int $id): int
    {
        if ($question['type'] === 'EDI') {
            $min = 0;
            $max = 5;
        } elseif ($question['type'] !== 'MUL') {
            $min = intval(substr($question['type'], 0, 1));
            $max = intval(substr($question['type'], -1));
        } else {
            //Questionnaire's type is MUL
            $item_answers = $question['items'][0]['multipleAnswers'];
            $min = $item_answers[0]['points'];
            $max = $item_answers[array_key_last($item_answers)]['points'];
        }

        foreach ($question['items'] as $item) {
            if ($item['id'] === $id) {
                $answer = $item['answer'];

                if ($answer === -1) return 0;

                //answer validation
                if ($answer < $min || $answer > $max)
                    throw new Exception("Le risposta all'item \"{$item['id']}.{$item['text']}\" del questionario {$question['question']} contiene un punteggio non valido: {$answer}");

                // if item has a reversed score, it reverts the score
                $answer =  array_key_exists('reversed', $item) && $item['reversed'] ? self::reverseScore($min, $max, $item['answer']) : $item['answer'];
                if ($question['type'] === 'EDI') $answer = $answer - 2 < 0 ? 0 : $answer - 2;
                elseif ($question['type'] === 'MUL') {
                    $answer = $item['multipleAnswers'][$answer]['points'];
                }
                return $answer;
            }
        }

        throw new Exception("Le risposte del questionario {$question['question']} non sono sincronizzate con il questionario");
    }


    /**
     * Reverse the score.
     */
    private static function reverseScore(int $min, int $max, int $answer): int
    {
        return $min + $max - $answer;
    }


    /**
     * Get variables for a specific question by ID.
     *
     * @param int $id The ID of the question.
     * @return array The question's variables.
     */
    private static function getQuestionVariables(int $id): array
    {
        return App::$app->question->getById($id)['variables'] ?? [];
    }


    private static function checkIfScored(int $score, array $variable, array $cutoff): bool
    {
        $patient = self::$patient ?? [];

        $from = $cutoff['from'];
        $to = $cutoff['to'];

        if (isset($variable['genderBased']) && $variable['genderBased'] && !isset($patient['sex'])) {
            if (!isset($patient['sex'])) {
                throw new Exception("Uno dei questionari ha cutoffs basati sul sesso ma {$patient['fname']} {$patient['lname']} non ha nessun sesso assegnato.");
            }

            if ($patient['sex'] === 'F') {
                $from ?? $cutoff['femFrom'];
                $to ?? $cutoff['femTo'];
            }
        }

        $type = $cutoff['type'];

        if ($type === 'greater-than')
            return $score > $from;
        if ($type === 'lesser-than')
            return $score < $from;
        else
            return $score >= $from && $score <= $to;
    }
}
