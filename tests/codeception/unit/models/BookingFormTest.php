<?php
namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use Codeception\Specify;
use app\models\BookingForm;


class BookingFormTest extends TestCase
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
    public function testTimeBeginEndAsDateTime()
    {
        $model = new BookingForm([
            'date' => '2016-07-07',
            'timeBegin' => '11:15',
            'timeEnd' => '12:45',
            'timeFormat' => 24,
        ]);
        expect("getTimeBeginAsDateTime contain date and time parts", $model->getTimeBeginAsDateTime()->getTimestamp())->equals((new \DateTime('2016-07-07 11:15'))->getTimestamp());
        expect("getTimeEndAsDateTime contain date and time parts", $model->getTimeEndAsDateTime()->getTimestamp())->equals((new \DateTime('2016-07-07 12:45'))->getTimestamp());
    }

    public function testTimeBeginEndAsDateTimeAmPm()
    {
        $model = new BookingForm([
            'date' => '2016-07-07',
            'timeBegin' => '11:15AM',
            'timeEnd' => '12:45PM',
            'timeFormat' => 12,
        ]);
        expect("getTimeBeginAsDateTime contain date and time parts", $model->getTimeBeginAsDateTime()->getTimestamp())->equals((new \DateTime('2016-07-07 11:15AM'))->getTimestamp());
        expect("getTimeEndAsDateTime contain date and time parts", $model->getTimeEndAsDateTime()->getTimestamp())->equals((new \DateTime('2016-07-07 12:45PM'))->getTimestamp());
    }
}