<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 22.08.16
 * Time: 23:19
 *
 * @var $crossings array of \app\models\Appointment
 * @var $hourMode integer
 */
use \app\utility\DateHelper;

echo "Can't add appointment, it crosses existing appointments: ";
echo implode(', ', array_map(function($app) use ($hourMode) {
    /* @var $app \app\models\Appointment */
    $result = $app->employee->name
        . ' ' . $app->getTimeStart()->format('Y-m-d')
        . ' ' . DateHelper::FormatTimeAccordingRule($app->getTimeStart(), $hourMode)
        . '-' . DateHelper::FormatTimeAccordingRule($app->getTimeEnd(), $hourMode);
    return $result;
}, $crossings));

