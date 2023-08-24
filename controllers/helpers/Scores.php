<?php

namespace app\controllers\helpers;

use app\app\App;
use app\controllers\TestsController;
use Exception;

class Scores extends TestsController
{

    /**
     * Calculate scores for the given survey questions.
     *
     * @param array $survey The survey data containing questions and answers.
     * @return array An associative array containing question scores.
     */
    public static function calculateScores(array $survey): array
    {
        $scores = [];

        foreach ($survey['questions'] as $question) {
            $scores[$question['question']] = self::calculateQuestionScore($question);
        }

        return $scores;
    }


    /**
     * Calculate scores for a specific question.
     *
     * @param array $question The question data.
     * @return array An associative array containing variable scores for the question.
     */
    private static function calculateQuestionScore(array $question): array
    {
        $scores = [];

        $variables = $question['variables'] ? $question['variables'] : self::getQuestionVariables($question['id']);

        if (empty($variables)) return [];

        foreach ($variables as $variable) {
            $scores[$variable['name']] = self::calculateVariableScore($question, $variable);
        }

        return $scores;
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
        }

        foreach ($question['items'] as $item) {
            if ($item['id'] === $id) {
                $item['answer'];
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
}
