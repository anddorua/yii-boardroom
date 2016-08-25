<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 10.07.16
 * Time: 23:37
 */
Codeception\Util\Debug::debug("__appointment fixture code__");
$employees = \app\models\Employee::find()->all();
$emps = array_combine(yii\helpers\ArrayHelper::getColumn($employees, 'login'), $employees);
$roomList = \app\models\Room::find()->all();
$rooms = array_combine(yii\helpers\ArrayHelper::getColumn($roomList, 'room_name'), $roomList);


return [
    'app11' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-01 08:00',
        'time_end' => '2016-06-01 09:00',
        'notes' => 'app11',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app12' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-01 09:00',
        'time_end' => '2016-06-01 10:00',
        'notes' => 'app12',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app13' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-01 10:00',
        'time_end' => '2016-06-01 11:00',
        'notes' => 'app13',
        'creator_id' => $emps['admin']->id,
        'chain' => '4',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app14' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-01 11:00',
        'time_end' => '2016-06-01 12:00',
        'notes' => 'app14',
        'creator_id' => $emps['admin']->id,
        'chain' => '4',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app21' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-02 08:00',
        'time_end' => '2016-06-02 09:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app22' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-02 09:00',
        'time_end' => '2016-06-02 10:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app23' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-02 09:00',
        'time_end' => '2016-06-02 10:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 2']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app31' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-03 08:00',
        'time_end' => '2016-06-03 09:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app32' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-03 09:00',
        'time_end' => '2016-06-03 10:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '3',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app41' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-04 09:00',
        'time_end' => '2016-06-04 10:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '5',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
    'app42' => [
        'emp_id' => $emps['user']->id,
        'time_start' => '2016-06-11 09:00',
        'time_end' => '2016-06-11 10:00',
        'notes' => 'some note',
        'creator_id' => $emps['admin']->id,
        'chain' => '5',
        'room_id' => $rooms['Room 1']->id,
        'submitted' => '2016-06-01 07:00',
    ],
];