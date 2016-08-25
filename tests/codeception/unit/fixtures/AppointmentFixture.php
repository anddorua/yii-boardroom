<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 10.07.16
 * Time: 23:33
 */

namespace app\tests\codeception\unit\fixtures;

use yii\test\ActiveFixture;

class AppointmentFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Appointment';
    public $dataFile = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'appointment.php';
    public $depends = [
        'app\tests\codeception\unit\fixtures\RoomsFixture',
        'app\tests\codeception\unit\fixtures\EmployeesFixture',
    ];
}