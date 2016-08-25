<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 10.07.16
 * Time: 23:22
 */

namespace app\tests\codeception\unit\models;

use app\models\Room;
use app\tests\codeception\unit\fixtures\AppointmentFixture;
use app\tests\codeception\unit\fixtures\EmployeesFixture;
use app\tests\codeception\unit\fixtures\RoomsFixture;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use Codeception\Util\Debug;
use app\models\Appointment;

class AppointmentDbTest extends DbTestCase
{
    use Specify;
    /**
     * @var \UnitTester
     */
    protected $tester;
    public function fixtures()
    {
        return [
            'employees' => EmployeesFixture::className(),
            'rooms' => RoomsFixture::className(),
            'appointments' => AppointmentFixture::className(),
        ];
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testDayAppointmentsSelection()
    {
        $room1 = Room::findOne(['room_name' => 'Room 1']);
        $this->specify('one day appointments load', function () use ($room1) {
            /* @var Room $room1 */
            $dateToSearch = new \DateTime('2016-06-02 00:00');
            $dayApps = Appointment::getDayAppointments($room1->id, $dateToSearch);
            expect('load two appointments at ' . $dateToSearch->format('Y-m-d H:i') . ' for ' . $room1->room_name , count($dayApps) == 2)->true();
        });
    }

}