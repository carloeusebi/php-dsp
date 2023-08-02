<?php

namespace app\db\factories;

abstract class BaseFactory
{

    /**
     * Generates and stores to the database the requested entity
     */
    abstract function generateAndInsert(): void;


    /**
     * Generates 100 random entries
     */
    protected function randomDate(int $start, int $end): string
    {
        $timestamp = mt_rand($start, $end);
        return date('Y-m-d', $timestamp);
    }

    /**
     * Return a random item fro an array
     */
    protected function randomItem(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    /**
     * Function to print a simple text-based progress bar
     */
    protected function printProgressBar(int $current, int $total, int $barWidth = 50): void
    {
        $progress = $current / $total;
        $barLength = (int) ($progress * $barWidth);

        echo "\r[";
        echo str_repeat('#', $barLength);
        echo str_repeat(' ', $barWidth - $barLength);
        echo "] " . round($progress * 100, 2) . "%";
    }
}
