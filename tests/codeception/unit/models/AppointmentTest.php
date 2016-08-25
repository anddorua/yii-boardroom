<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;
use app\models\Appointment;

class AppointmentTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testTimeBeginCorrect()
    {
        $model = new Appointment(['time_start' => "2016-07-07 18:00:00"]);
        expect('TimeStart as DateTime is correct', $model->getTimeStart()->getTimestamp())->equals((new \DateTime("2016-07-07 18:00:00"))->getTimestamp());
    }


}