<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 04.07.16
 * Time: 23:56
 */

namespace app\models;

use Yii;
use yii\db\Query;
use app\models\BookingForm;
use app\models\Appointment;
use Codeception\Util\Debug;


/**
 * Class AppointmentChain
 * @package app\models
 * @property \DateTime $timeFilter
 */
class AppointmentChain implements \Iterator
{
    private $members = array();
    private $position;
    private $timeFilter;


    public function __construct()
    {
        $this->position = 0;
        $this->timeFilter = null;
    }

    public function add(Appointment $item)
    {
        $this->members[] = $item;
    }

    /**
     * отсекает все букинги, которые закончились на момент фильтра
     * @param Appointment $member
     * @return bool
     */
    public function isMeetFilter(Appointment $member)
    {
        if (is_null($this->timeFilter)) {
            return true;
        } else {
            $end = $member->getTimeEnd()->getTimestamp();
            $test = $this->timeFilter->getTimestamp();
            return $end > $test;
        }
    }

    /**
     * @return Appointment
     */
    public function current()
    {
        return $this->members[$this->position];
    }

    public function next()
    {
        Debug::debug('chain next');
        ++$this->position;
        while ($this->valid() && !$this->isMeetFilter($this->members[$this->position])) {
            ++$this->position;
        }
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->members[$this->position]);
    }

    public function rewind()
    {
        Debug::debug('chain rewind');
        $this->position = 0;
        while ($this->valid() && !$this->isMeetFilter($this->members[$this->position])) {
            ++$this->position;
        }
    }

    /**
     * устанавливает новый chainId для всех членов цепочки. Не подвержена фильтрации.
     * @param $newId integer
     */
    public function setChainId($newId)
    {
        for ($i = 0; $i < count($this->members); $i++) {
            $this->members[$i]->chain = $newId;
        }
    }

    /**
     * Counts chain length
     * @return int
     */
    public function count()
    {
        if ($this->timeFilter) {
            Debug::debug("time filter is " . $this->timeFilter->format('c'));
            $filteredMembers = array_filter($this->members, function(Appointment $member) {
                return $this->isMeetFilter($member);
            });
            return count($filteredMembers);
        } else {
            return count($this->members);
        }
    }

    public function applyFilter(\DateTime $time)
    {
        $this->timeFilter = $time;
    }
    public function dropFilter()
    {
        $this->timeFilter = null;
    }

    public function applyChange(BookingForm $bookingData)
    {
        $this->rewind();
        foreach($this as $member) {
            /* @var Appointment $member */
            $this->applyChangeToAppointment($member, $bookingData);
        }
    }

    public function applyChangeToMember($empId, BookingForm $bookingData)
    {
        $this->rewind();
        foreach($this as $member) {
            /* @var Appointment $member*/
            if ($member->id == $empId) {
                $this->applyChangeToAppointment($member, $bookingData);
                break;
            }
        }
    }
    
    private function applyChangeToAppointment(Appointment $app, BookingForm $bookingData)
    {
        $app->setNewTime($bookingData->getTimeBeginAsDateTime(), $bookingData->getTimeEndAsDateTime());
        $app->notes = $bookingData->comment;
        $app->emp_id = $bookingData->employeeCode;
    }

    public function get($empId)
    {
        foreach($this->members as $member) {
            if ($member->id == $empId) {
                return $member;
            }
        }
        return null;
    }

    /**
     * return array of dates according to recurring period and duration
     * @param \DateTime $start_date
     * @param integer $recurring_period
     * @param integer $duration
     * @return array of \DateTime
     */
    private static function makeDatesChain(\DateTime $start_date, $recurring_period, $duration)
    {
        $event_dates = array($start_date);
        $step = null;
        $duration_to_count = $duration;
        switch ($recurring_period) {
            case BookingForm::WEEKLY:
                $step = new \DateInterval('P7D');
                break;
            case BookingForm::BIWEEKLY:
                $step = new \DateInterval('P14D');
                $duration_to_count = $duration / 2;
                break;
            case BookingForm::MONTHLY:
                $step = new \DateInterval('P1M');
                break;
        }
        /* @var \DateTime $event_date */
        $event_date = clone $event_dates[0];
        for ($i = 1; $i <= $duration_to_count && $recurring_period != BookingForm::NOT_RECURRING; $i++) {
            $event_date->add($step);
            $event_dates[] = clone $event_date;
        }
        return $event_dates;
    }



    /**
     * Returns new Appointments chain according to bookingData, user entered, room
     * @param $booking
     * @param $creatorId
     * @param $roomId
     * @return AppointmentChain
     */
    public static function make(BookingForm $booking, $creatorId, $roomId)
    {
        $result = new AppointmentChain();
        //Debug::debug("isRecurring:" . ($booking->isRecurring() ? "true" : "false"));
        //Debug::debug("recurring period:" . ($booking->isRecurring() ? $booking->repeatInterval : BookingForm::NOT_RECURRING));
        //Debug::debug("duration:" . ($booking->duration));
        $event_dates = self::makeDatesChain($booking->getTimeBeginAsDateTime(), $booking->isRecurring() ? $booking->repeatInterval : BookingForm::NOT_RECURRING, $booking->duration);
        //Debug::debug("\nEvent dates count:" . count($event_dates) . "\n");
        $event_duration = $booking->getTimeEndAsDateTime()->getTimestamp() - $booking->getTimeBeginAsDateTime()->getTimestamp();
        foreach($event_dates as $event_date) {
            /* @var \DateTime $event_date */
            $appItem = new Appointment([
                'emp_id' => $booking->employeeCode,
                'notes' => $booking->comment,
                'creator_id' => $creatorId,
                'room_id' => $roomId,
            ]);
            $appItem->setTimeStart($event_date);
            $appItem->setTimeEnd((new \DateTime())->setTimestamp($event_date->getTimestamp() + $event_duration));
            $result->add($appItem);
            //\Codeception\Util\Debug::debug("Event added");
        }
        return $result;
    }

    /**
     * @return array Appointment
     */
    public function getCrossingAppointments()
    {
        $result = array();
        //Debug::debug('appointment chain count ' . $this->count());
        foreach($this as $appointment) {
            /* @var Appointment $appointment*/
            //Debug::debug('looking for day appointments with room_id=' . $appointment->room_id . ' and time ' . $appointment->getTimeStart()->format('c'));
            $day_apps = Appointment::getDayAppointments($appointment->room_id, $appointment->getTimeStart());
            foreach($day_apps as $match) {
                /* @var Appointment $match*/
                if (!is_null($appointment->chain) && $appointment->chain == $match->chain) {
                    continue; // пропускаем, если из той же цепочки
                }
                if ($appointment->isCrossing($match->getTimeStart(), $match->getTimeEnd())) {
                    $result[] = $match;
                }
            }
        }
        return $result;
    }

    /**
     * @param $chainId
     * @return AppointmentChain
     */
    public static function loadChain($chainId)
    {
        $chainMembers = Appointment::find()->where(['chain' => $chainId])->orderBy(['time_start' => SORT_ASC])->all();
        $result = new self();
        foreach ($chainMembers as $member) {
            /* @var $member Appointment*/
            $result->add($member);
        }
        return $result;
    }

    public function saveChain()
    {
        foreach($this as $appointment) {
            $appointment->save();
        }
    }

    /**
     * deletes all appointments in chain $appId belongs to
     * but preserve members earlier $preserve time
     * @param $appId
     * @param \DateTime $preserve
     * @return integer
     */
    public static function deleteAppChain($appId, \DateTime $preserve)
    {
        $command = Yii::$app->db->createCommand()->delete('appointments', [
            'and',
            ['in', 'chain', (new Query())->select('chain')->from('(select chain from appointments where id=:id) as p')],
            ['>=', 'time_end', $preserve->format('Y-m-d H:i:s')],
        ])->bindParam(':id', $appId);
        Yii::trace('del sql:' . $command->rawSql);

        return $command->execute();
    }


}