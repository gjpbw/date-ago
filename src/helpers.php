<?php
function dateAgo($date, $dateFormat = null)
{
    $DateAgo = app('\Gjpbw\DateAgo\DateAgo');
    return $DateAgo->dateFormat($date, $dateFormat);
}