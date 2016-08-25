<?php
namespace tests\codeception\unit\models;

use Codeception\Util\Debug;
use Yii;
use yii\codeception\DbTestCase;
use Codeception\Specify;
use app\models\BookingForm;
use app\models\AppointmentChain;
use app\models\Room;
use app\tests\codeception\unit\fixtures\AppointmentFixture;
use app\tests\codeception\unit\fixtures\EmployeesFixture;
use app\tests\codeception\unit\fixtures\RoomsFixture;

class AppointmentChainTest extends DbTestCase
{
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
    public function testNotRecursive()
    {
        $booking = new BookingForm([
            'employeeCode' => 1,
            'date' => '2016-06-10',
            'timeBegin' => '08:10',
            'timeEnd' => '09:15',
            'comment' => 'some comment',
            'recurring' => BookingForm::RECURRING_NO,
            'repeatInterval' => BookingForm::NOT_RECURRING,
            'timeFormat' => 24,
        ]);
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 1', $chain->count())->equals(1);
    }

    public function testWeekly()
    {
        $booking = new BookingForm([
            'employeeCode' => 1,
            'date' => '2016-06-10',
            'timeBegin' => '08:10',
            'timeEnd' => '09:15',
            'comment' => 'some comment',
            'recurring' => BookingForm::RECURRING_YES,
            'repeatInterval' => BookingForm::WEEKLY,
            'duration' => 24,
            'timeFormat' => 24,
        ]);
        expect('validation should be ok', $booking->validate())->true();
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 5', $chain->count())->equals(5);

        $booking->duration = 2;
        expect('validation should be ok', $booking->validate())->true();
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 3', $chain->count())->equals(3);
    }

    public function testBiWeekly()
    {
        $booking = new BookingForm([
            'employeeCode' => 1,
            'date' => '2016-06-10',
            'timeBegin' => '08:10',
            'timeEnd' => '09:15',
            'comment' => 'some comment',
            'recurring' => BookingForm::RECURRING_YES,
            'repeatInterval' => BookingForm::BIWEEKLY,
            'duration' => 24,
            'timeFormat' => 24,
        ]);
        expect('validation should be ok', $booking->validate())->true();
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 3', $chain->count())->equals(3);

        $booking->duration = 3;
        expect('validation should be ok', $booking->validate())->true();
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 2', $chain->count())->equals(2);

        $booking->duration = 1;
        expect('validation should be ok', $booking->validate())->true();
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 1', $chain->count())->equals(1);

        $booking->duration = 2;
        expect('validation should be ok', $booking->validate())->true();
        $chain = AppointmentChain::make($booking, 2, 3);
        expect('Chain length is 2', $chain->count())->equals(2);
    }

    public function testGetCrossingAppointments()
    {
        $booking = new BookingForm([
            'employeeCode' => 1,
            'date' => '2016-06-01',
            'timeBegin' => '09:30',
            'timeEnd' => '10:30',
            'comment' => 'some comment',
            'recurring' => BookingForm::RECURRING_NO,
            'timeFormat' => 24,
        ]);
        expect('validation should be ok', $booking->validate())->true();
        /* @var Room $room1*/
        $room1 = Room::findOne(['room_name' => 'Room 1']);
        Debug::debug('room1->id = ' . $room1->id);
        $chain = AppointmentChain::make($booking, 2, $room1->id);
        $chain->setChainId(3);
        $cross = $chain->getCrossingAppointments();
        expect('crossing count should be 1', count($cross))->equals(1);
        expect('it should be app13', $cross[0]->notes)->equals('app13');
    }

    public function testChainLoad()
    {
        $chain = AppointmentChain::loadChain(5);
        expect('chain length of chainId == 5 should be 2', $chain->count())->equals(2);
        $chain->applyFilter(new \DateTime('2016-06-05 00:00'));
        expect('chain length after filtering of chainId == 5 should be 1', $chain->count())->equals(1);
    }

}