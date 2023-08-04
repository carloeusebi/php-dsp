<?php

function dd($value): never
{
    var_dump($value);
    die();
}

function calculateAge(string $birthday): int
{

    $today = new \DateTime();
    $birth_date = new \DateTime($birthday);
    $age = $today->diff($birth_date)->y;

    return $today > $birth_date ? $age : $age * -1;
}
